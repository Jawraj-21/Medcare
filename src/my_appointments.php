<?php
include "connection_db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user's appointments from the database
$conn = getDatabase();
$stmt = $conn->prepare("SELECT appointment_id, date, time, doctor_id, department_id FROM appointments WHERE user_id = :user_id ORDER BY date DESC, time ASC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if appointment cancellation is requested
if (isset($_GET['cancel']) && isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];
    
    // Delete the appointment from the database
    $stmt = $conn->prepare("DELETE FROM appointments WHERE user_id = :user_id AND appointment_id = :appointment_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    
    // Redirect back to this page after cancellation
    header("Location: my_appointments.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medcare | My Appointments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <style>
        .card {
            max-width: 400px; /* Adjust the max-width as needed */
            margin: 0 auto; /* Center the card horizontally */
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Add a button to book appointment -->
    <div class="container mt-4">
        <div class="text-end">
            <a href="appointments.php" class="btn btn-primary">Book Appointment</a>
        </div>
    </div>

    <div class="container mt-4">
        <h2 class="text-center">My Appointments</h2>
        <?php if (empty($appointments)) : ?>
            <div class="alert alert-info text-center" role="alert">
                You don't have any appointments yet.
            </div>
        <?php else : ?>
            <?php foreach ($appointments as $appointment) : ?>
                <?php
                // Retrieve doctor details
                $stmt = $conn->prepare("SELECT first_name, last_name FROM doctors WHERE doctor_id = :doctor_id");
                $stmt->bindParam(':doctor_id', $appointment['doctor_id']);
                $stmt->execute();
                $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

                // Retrieve department details
                $stmt = $conn->prepare("SELECT department_name FROM departments WHERE department_id = :department_id");
                $stmt->bindParam(':department_id', $appointment['department_id']);
                $stmt->execute();
                $department = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if appointment date and time have already passed
                $appointment_datetime = strtotime($appointment['date'] . ' ' . $appointment['time']);
                $current_datetime = strtotime(date('Y-m-d H:i:s'));
                $past_appointment = $appointment_datetime < $current_datetime;
                ?>
                <div class="row justify-content-center">
                    <div class="col">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Appointment Date: <?php echo $appointment['date']; ?></h5>
                                <p class="card-text">Appointment Time: <?php echo $appointment['time']; ?></p>
                                <p class="card-text">Department: <?php echo $department['department_name']; ?></p>
                                <p class="card-text">Doctor: <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></p>
                                <?php if (!$past_appointment) : ?>
                                    <a href="change_appointment.php?appointment_id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-success">Change Appointment</a>
                                    <a href="#" class="btn btn-danger" onclick="confirmCancellation(<?php echo $appointment['appointment_id']; ?>)">Cancel Appointment</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Add a confirmation pop-up script -->
    <script>
        function confirmCancellation(appointmentId) {
            if (confirm("Are you sure you want to cancel this appointment?")) {
                window.location.href = "my_appointments.php?cancel=true&appointment_id=" + appointmentId;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
