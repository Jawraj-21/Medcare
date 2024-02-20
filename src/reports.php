<?php
include "connection_db.php";

// Retrieve logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch patient ID (patient_id) based on user ID (user_id)
$conn = getDatabase();
$stmt = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if ($patient) {
    $patient_id = $patient['patient_id'];

    // Fetch patient reports for the logged-in user based on patient_id
    $stmt = $conn->prepare("SELECT * FROM patient_reports WHERE patient_id = :patient_id ORDER BY report_date DESC");
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Handle case where patient ID is not found for the logged-in user
    // For example, if the logged-in user is not registered as a patient
    $reports = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medcare | Reports</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Patient Reports</h2>
        <?php if (empty($reports)) : ?>
            <div class="alert alert-info" role="alert">
                No reports available.
            </div>
        <?php else : ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($reports as $report) : ?>
                    <?php
                    // Fetch doctor details
                    $stmt = $conn->prepare("SELECT first_name, last_name FROM doctors WHERE doctor_id = :doctor_id");
                    $stmt->bindParam(':doctor_id', $report['doctor_id']);
                    $stmt->execute();
                    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Report Type: <?php echo $report['report_type']; ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Report Date: <?php echo $report['report_date']; ?></p>
                                <p class="card-text">Doctor: <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></p>
                                <a href="report_data.php?report_id=<?php echo $report['report_id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
