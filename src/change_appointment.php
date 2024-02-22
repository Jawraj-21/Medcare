<?php
include "connection_db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit;
}

// Check if appointment ID is provided in the URL
if (!isset($_GET['appointment_id'])) {
    // Redirect to homepage or error page if appointment ID is not provided
    header("Location: index.php");
    exit;
}

$appointment_id = $_GET['appointment_id'];

// Fetch appointment details from the database
$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM appointments WHERE appointment_id = :appointment_id");
$stmt->bindParam(':appointment_id', $appointment_id);
$stmt->execute();
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if appointment exists
if (!$appointment) {
    // Redirect to homepage or error page if appointment does not exist
    header("Location: index.php");
    exit;
}

// Fetch department details from the database
$stmt = $conn->prepare("SELECT * FROM departments WHERE department_id = :department_id");
$stmt->bindParam(':department_id', $appointment['department_id']);
$stmt->execute();
$department = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch doctor details from the database
$stmt = $conn->prepare("SELECT * FROM doctors WHERE doctor_id = :doctor_id");
$stmt->bindParam(':doctor_id', $appointment['doctor_id']);
$stmt->execute();
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $time = isset($_POST['time']) ? $_POST['time'] : null;
    $doctor_id = isset($_POST['doctor']) ? $_POST['doctor'] : null;

    // Validate form data
    if (empty($date) || empty($time) || empty($doctor_id)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Check if the selected date and time are already booked
        $stmt = $conn->prepare("SELECT * FROM appointments WHERE date = :date AND time = :time AND appointment_id != :appointment_id");
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':appointment_id', $appointment_id);
        $stmt->execute();
        $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_appointment) {
            // Appointment already exists for the selected date and time
            $error_message = "The selected date and time are already booked. Please choose a different time.";
        } else {
            // Update appointment details in the database
            $stmt = $conn->prepare("UPDATE appointments SET date = :date, time = :time, doctor_id = :doctor_id WHERE appointment_id = :appointment_id");
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':appointment_id', $appointment_id);
            $stmt->execute();

            // Redirect to confirmation page after successful update
            header("Location: my_appointments.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Appointment</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                                    // Generate time slots based on the opening and closing hours
                                    $opening_time = strtotime($department['opening_time']);
                                    $closing_time = strtotime($department['closing_time']);

                                    $current_time = $opening_time;
                                    while ($current_time <= $closing_time) {
                                        $formatted_time = date("H:i", $current_time);
                                        $selected = ($formatted_time == date("H:i", strtotime($appointment['time']))) ? "selected" : "";
                                        echo "<option value='$formatted_time' $selected>$formatted_time</option>";
                                        $current_time += 1800; // Increment by 30 minutes (1800 seconds)
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="doctor" class="form-label">Select Doctor</label>
                                <select class="form-select" id="doctor" name="doctor">
                                    <option value="" selected disabled>Select Doctor</option>
                                    <?php
                                    // Fetch doctors from the same department
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

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
