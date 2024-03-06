<?php
include "connection_db.php";

if (!isset($_GET['report_id'])) {
    header("Location: index.php");
    exit;
}

$report_id = $_GET['report_id'];

$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM patient_reports WHERE report_id = :report_id");
$stmt->bindParam(':report_id', $report_id);
$stmt->execute();
$report = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT first_name, last_name FROM doctors WHERE doctor_id = :doctor_id");
$stmt->bindParam(':doctor_id', $report['doctor_id']);
$stmt->execute();
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$report) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medcare | Report Details</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Report Details</h2>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Report Type: <?php echo $report['report_type']; ?></h4>
                        <p class="card-text"><strong>Report Date:</strong> <?php echo $report['report_date']; ?></p>
                        <hr>
                        <h5 class="card-subtitle mb-2 text-muted">Doctor Information</h5>
                        <p class="card-text"><strong>Doctor Name:</strong> <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?></p>
                        <hr>
                        <h5 class="card-subtitle mb-2 text-muted">Patient Health Parameters</h5>
                        <p class="card-text"><strong>Blood Pressure:</strong> <?php echo $report['blood_pressure']; ?></p>
                        <p class="card-text"><strong>Heart Rate:</strong> <?php echo $report['heart_rate']; ?></p>
                        <p class="card-text"><strong>Lipid Profile:</strong> <?php echo $report['lipid_profile']; ?></p>
                        <p class="card-text"><strong>Liver Function Tests:</strong> <?php echo $report['liver_function_tests']; ?></p>
                        <p class="card-text"><strong>Kidney Function Tests:</strong> <?php echo $report['kidney_function_tests']; ?></p>
                        <p class="card-text"><strong>Thyroid Function Tests:</strong> <?php echo $report['thyroid_function_tests']; ?></p>
                        <p class="card-text"><strong>Diabetes Status:</strong> <?php echo $report['diabetes_status']; ?></p>
                        <p class="card-text"><strong>Vitamin D Level:</strong> <?php echo $report['vitamin_d_level']; ?></p>
                        <p class="card-text"><strong>Vitamin B12 Level:</strong> <?php echo $report['vitamin_b12_level']; ?></p>
                        <p class="card-text"><strong>Serum Cholesterol:</strong> <?php echo $report['serum_cholesterol']; ?></p>
                        <p class="card-text"><strong>Serum Sodium:</strong> <?php echo $report['serum_sodium']; ?></p>
                        <p class="card-text"><strong>Serum Potassium:</strong> <?php echo $report['serum_potassium']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
