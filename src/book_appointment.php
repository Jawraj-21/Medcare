<?php
include "connection_db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

// Check if department ID is provided in the URL
if (!isset($_GET['department_id'])) {
    // Redirect to homepage or error page if department ID is not provided
    header("Location: index.php");
    exit;
}

$department_id = $_GET['department_id'];

// Fetch department details from the database
$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM departments WHERE department_id = :department_id");
$stmt->bindParam(':department_id', $department_id);
$stmt->execute();
$department = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if department exists
if (!$department) {
    // Redirect to homepage or error page if department does not exist
    header("Location: index.php");
    exit;
}

// Get today's date
$current_date = date('Y-m-d');

// Get opening and closing times of the department
$opening_time = strtotime($department['opening_time']);
$closing_time = strtotime($department['closing_time']);

// Generate time slots based on the opening and closing hours
$time_options = '';
$current_time = $opening_time;
while ($current_time <= $closing_time) {
    // Check if the current time plus 30 minutes is still within opening hours
    if ($current_time + 1800 <= $closing_time) {
        $start_time = date("h:i A", $current_time);
        $end_time = date("h:i A", $current_time + 1800); // 1800 seconds = 30 minutes
        $time_options .= '<option value="' . date("H:i", $current_time) . '">' . $start_time . ' - ' . $end_time . '</option>';
    }
    $current_time += 1800; // Increment by 30 minutes (1800 seconds)
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $user_id = $_SESSION['user_id'];
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $doctor_id = isset($_POST['doctor']) ? $_POST['doctor'] : null; // Added to get selected doctor ID

    // Validate form data
    if (empty($date) || empty($time) || empty($doctor_id)) { // Added check for doctor ID
        $error_message = "Please fill in all fields.";
    } elseif ($date < $current_date) {
        // Check if selected date is in the past
        $error_message = "You cannot book appointments for past dates.";
    } else {
        // Check if the selected date is a weekday
        $day_of_week = date('N', strtotime($date)); // 'N' returns the ISO-8601 numeric representation of the day of the week (1 for Monday, 7 for Sunday)
        if ($day_of_week >= 6) { // Saturday or Sunday
            $error_message = "Appointments can only be booked on weekdays.";
        } else {
            // Check if the selected date and time are already booked
            $stmt = $conn->prepare("SELECT * FROM appointments WHERE date = :date AND time = :time");
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->execute();
            $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_appointment) {
                // Appointment already exists for the selected date and time
                $error_message = "The selected date and time are already booked. Please choose a different time.";
            } else {
                // Insert appointment into database
                $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, department_id, date, time) VALUES (:user_id, :doctor_id, :department_id, :date, :time)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':doctor_id', $doctor_id); // Bind doctor ID parameter
                $stmt->bindParam(':department_id', $department_id);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':time', $time);
                $stmt->execute();

                // Redirect to confirmation page after successful booking
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
                                    // Fetch doctors from the same department
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

