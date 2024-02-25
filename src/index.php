<?php
include "connection_db.php";

$conn = getDatabase();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("location:login.php");
  exit;
}

// Getting user details from patients or doctors table
$stmt = $conn->prepare("SELECT 'patient' AS type, first_name, last_name, gender, DOB, address, phone_number FROM patients WHERE user_id = :user_id 
  UNION ALL 
  SELECT 'doctor' AS type, first_name, last_name, gender, DOB, address, phone_number FROM doctors WHERE user_id = :user_id 
");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user_details = $stmt->fetch(PDO::FETCH_ASSOC);

// Getting the user's first name
$userFirstName = $user_details['first_name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>MedCare | Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2>Welcome to Medcare, <?php echo $userFirstName; ?></h2>
        <?php if ($user_details) : ?>
            <p>Your details:</p>
            <ul>
                <li><strong>Name:</strong> <?php echo $user_details['first_name'] . ' ' . $user_details['last_name']; ?></li>
                <li><strong>Gender:</strong> <?php echo $user_details['gender']; ?></li>
                <li><strong>Date of Birth:</strong> <?php echo $user_details['DOB']; ?></li>
                <li><strong>Address:</strong> <?php echo $user_details['address']; ?></li>
                <li><strong>Contact Number:</strong> <?php echo $user_details['phone_number']; ?></li>
            </ul>
        <?php else : ?>
            <p>User details not found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
