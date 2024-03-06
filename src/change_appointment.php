<?php
include "connection_db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['appointment_id'])) {
    header("Location: index.php");
    exit;
}

$appointment_id = $_GET['appointment_id'];

$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
$stmt->bindParam(':appointment_id', $appointment_id);
$stmt->execute();
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$appointment) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM departments WHERE department_id = :department_id");
$stmt->bindParam(':department_id', $appointment['department_id']);
$stmt->execute();
$department = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM doctors WHERE doctor_id = :doctor_id");
$stmt->bindParam(':doctor_id', $appointment['doctor_id']);
$stmt->execute();
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $doctor_id = isset($_POST['doctor']) ? $_POST['doctor'] : null;

    if (empty($date) || empty($time) || empty($doctor_id)) {
        $error_message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM appointments WHERE date = :date AND time = :time AND appointment_id != :appointment_id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
        $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_appointment) {
            $error_message = "The selected date and time are already booked. Please choose a different time.";
        } else {
            $stmt = $conn->prepare("UPDATE appointments SET date = :date, time = :time, doctor_id = :doctor_id WHERE appointment_id = :appointment_id");
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':appointment_id', $appointment_id);
            $stmt->execute();

            header("Location: my_appointments.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>MedCare | Change Appointment</title>
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
                        <h2 class="card-title">Change Appointment</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $appointment['date']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Select Time</label>
                                <select class="form-select" id="time" name="time">
                                    <option value="" disabled>Select Time</option>
                                    <?php
                                    $opening_time = strtotime($department['opening_time']);
                                    $closing_time = strtotime($department['closing_time']);

                                    $current_time = $opening_time;
                                    while ($current_time <= $closing_time) {
                                        $formatted_time = date("H:i", $current_time);
                                        $selected = ($formatted_time == date("H:i", strtotime($appointment['time']))) ? "selected" : "";
                                        echo "<option value='$formatted_time' $selected>$formatted_time</option>";
                                        $current_time += 1800;
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="doctor" class="form-label">Select Doctor</label>
                                <select class="form-select" id="doctor" name="doctor">
                                    <option value="" selected disabled>Select Doctor</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM doctors WHERE department_id = :department_id");
                                    $stmt->bindParam(':department_id', $department['department_id']);
                                    $stmt->execute();
                                    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($doctors as $doc) {
                                        $selected = ($doc['doctor_id'] == $doctor['doctor_id']) ? "selected" : "";
                                        echo "<option value='" . $doc['doctor_id'] . "' $selected>" . $doc['first_name'] . " " . $doc['last_name'] . "</option>";
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
                                <button type="submit" class="btn btn-success mx-2">Update</button>
                                <button type="button" class="btn btn-danger mx-2" onclick="history.back()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
