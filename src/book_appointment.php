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
    $date = $_POST['date'];
    $time = $_POST['time'];
    $doctor_id = $_POST['doctor']; // Added to get selected doctor ID

    // Validate form data
    if (empty($date) || empty($time) || empty($doctor_id)) { // Added check for doctor ID
        $error_message = "Please fill in all fields.";
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medcare | Book Appointment</title>
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
                        <h2 class="card-title">Book Appointment - <?php echo $department['department_name']; ?></h2>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Time</label>
                                <select class="form-select" id="time" name="time">
                                    <?php echo $time_options; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="doctor" class="form-label">Select Doctor</label>
                                <select class="form-select" id="doctor" name="doctor">
                                    <option value="">Select Doctor</option>
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
                            <button type="submit" class="btn btn-primary">Submit</button>
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
