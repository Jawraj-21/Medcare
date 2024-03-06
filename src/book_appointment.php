<?php
include "connection_db.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['department_id'])) {
    header("Location: index.php");
    exit;
}

$department_id = $_GET['department_id'];

$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM departments WHERE department_id = :department_id");
$stmt->bindParam(':department_id', $department_id);
$stmt->execute();
$department = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$department) {
    header("Location: index.php");
    exit;
}

$current_date = date('Y-m-d');

$opening_time = strtotime($department['opening_time']);
$closing_time = strtotime($department['closing_time']);

$time_options = '';
$current_time = $opening_time;
while ($current_time <= $closing_time) {
    if ($current_time + 1800 <= $closing_time) {
        $start_time = date("h:i A", $current_time);
        $end_time = date("h:i A", $current_time + 1800);
        $time_options .= '<option value="' . date("H:i", $current_time) . '">' . $start_time . ' - ' . $end_time . '</option>';
    }
    $current_time += 1800;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $doctor_id = isset($_POST['doctor']) ? $_POST['doctor'] : null;


    if (empty($date) || empty($time) || empty($doctor_id)) {
        $error_message = "Please fill in all fields.";
    } elseif ($date < $current_date) {
        $error_message = "You cannot book appointments for past dates.";
    } else {
        $day_of_week = date('N', strtotime($date));
        if ($day_of_week >= 6) {
            $error_message = "Appointments can only be booked on weekdays.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM appointments WHERE date = :date AND time = :time");
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->execute();
            $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_appointment) {
                $error_message = "The selected date and time are already booked. Please choose a different time.";
            } else {
                $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, department_id, date, time) VALUES (:user_id, :doctor_id, :department_id, :date, :time)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':doctor_id', $doctor_id);
                $stmt->bindParam(':department_id', $department_id);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':time', $time);
                $stmt->execute();

                header("Location: index.php");
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>MedCare | Book Appointment</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Book Appointment - <?php echo $department['department_name']; ?></h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="appointmentForm">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" min="<?php echo $current_date; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Time</label>
                                <select class="form-select" id="time" name="time" required>
                                    <option value="" selected disabled>Select Time</option>
                                    <?php echo $time_options; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="doctor" class="form-label">Select Doctor</label>
                                <select class="form-select" id="doctor" name="doctor" required>
                                    <option value="" selected disabled>Select Doctor</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM doctors WHERE department_id = :department_id");
                                    $stmt->bindParam(':department_id', $department_id);
                                    $stmt->execute();
                                    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($doctors as $doctor) {
                                        echo "<option value='" . $doctor['doctor_id'] . "'>" . $doctor['first_name'] . " " . $doctor['last_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php if (isset($error_message)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Book</button>
                                <button type="button" class="btn btn-danger mx-2" onclick="history.back()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('appointmentForm').addEventListener('submit', function(event) {
            var selectedDate = new Date(document.getElementById('date').value);
            var today = new Date();
            var selectedTime = new Date(selectedDate.toDateString() + ' ' + document.getElementById('time').value);

            if (selectedDate < today || (selectedDate.getTime() === today.getTime() && selectedTime < today)) {
                alert('You cannot book appointments for past dates or times.');
                event.preventDefault();
            }
        });
    </script>
</body>

</html>

