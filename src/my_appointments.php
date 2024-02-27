<?php
date_default_timezone_set('UTC');

include "connection_db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = getDatabase();
$stmt = $conn->prepare("SELECT appointment_id, date, time, doctor_id, department_id FROM appointments WHERE user_id = :user_id ORDER BY date DESC, time ASC");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['cancel']) && isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];
    
    $stmt = $conn->prepare("DELETE FROM appointments WHERE user_id = :user_id AND appointment_id = :appointment_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    
    header("Location: my_appointments.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medcare | My Appointments</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .card {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <div class="text-end">
            <a href="appointments.php" class="btn btn-primary">Book Appointment</a>
        </div>
    </div>

    <div class="container mt-4">
        <h2 class="text-center">My Appointments</h2>

        <?php
        $past_appointments = array();
        $future_appointments = array();

        // Separate past and future appointments
        foreach ($appointments as $appointment) {
            $appointment_datetime = strtotime($appointment['date'] . ' ' . $appointment['time']);
            $current_datetime = strtotime(date('Y-m-d H:i:s'));
            if ($appointment_datetime < $current_datetime) {
                $past_appointments[] = $appointment;
            } else {
                $future_appointments[] = $appointment;
            }
        }
        ?>

        <!-- Display future appointments -->
        <?php if (!empty($future_appointments)) : ?>
            <h3 class="text-center mt-4">Future Appointments</h3>
            <?php foreach ($future_appointments as $appointment) : ?>
                <?php
                // Retrieve doctor and department information
                $stmt = $conn->prepare("SELECT first_name, last_name FROM doctors WHERE doctor_id = :doctor_id");
                $stmt->bindParam(':doctor_id', $appointment['doctor_id']);
                $stmt->execute();
                $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $conn->prepare("SELECT department_name FROM departments WHERE department_id = :department_id");
                $stmt->bindParam(':department_id', $appointment['department_id']);
                $stmt->execute();
                $department = $stmt->fetch(PDO::FETCH_ASSOC);

                // Checking if the appointment date and time is passed
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

        <!-- Display past appointments -->
        <?php if (!empty($past_appointments)) : ?>
            <h3 class="text-center mt-4">Past Appointments</h3>
            <?php foreach ($past_appointments as $appointment) : ?>
                <?php
                // Retrieve doctor and department information
                $stmt = $conn->prepare("SELECT first_name, last_name FROM doctors WHERE doctor_id = :doctor_id");
                $stmt->bindParam(':doctor_id', $appointment['doctor_id']);
                $stmt->execute();
                $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt = $conn->prepare("SELECT department_name FROM departments WHERE department_id = :department_id");
                $stmt->bindParam(':department_id', $appointment['department_id']);
                $stmt->execute();
                $department = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <div class="row justify-content-center">
                    <div class="col">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Appointment Date: <?php echo $appointment['date']; ?></h5>
                                <p class="card-text">Appointment Time: <?php echo $appointment['time']; ?></p>
                                <p class="card-text">Department: <?php echo $department['department_name']; ?></p>
                                <p class="card-text">Doctor: <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($appointments)) : ?>
            <div class="alert alert-info text-center" role="alert">
                You don't have any appointments yet.
            </div>
        <?php endif; ?>
    </div>
    <script src="javascript/script.js"></script>
</body>

</html>

