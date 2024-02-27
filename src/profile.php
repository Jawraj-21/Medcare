<?php
include "connection_db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$conn = getDatabase();
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch patient details from the database
$stmt = $conn->prepare("SELECT * FROM patients WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

$dob = date('d/m/Y', strtotime($patient['DOB']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">User Profile</h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" id="username" class="form-control" value="<?php echo $user['username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="first-name" class="form-label">First Name:</label>
                                    <input type="text" id="first-name" class="form-control" value="<?php echo $user['first_name']; ?>" readonly>
                                </div>
                                <div class="col">
                                    <label for="last-name" class="form-label">Last Name:</label>
                                    <input type="text" id="last-name" class="form-control" value="<?php echo $user['last_name']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="dob" class="form-label">Date of Birth:</label>
                                    <input type="text" id="dob" class="form-control" value="<?php echo $dob; ?>" readonly>
                                </div>
                                <div class="col">
                                    <label for="gender" class="form-label">Gender:</label>
                                    <input type="text" id="gender" class="form-control" value="<?php echo $patient['gender']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                        <div class="mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <input type="text" id="address" class="form-control" value="<?php echo $patient['address']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number:</label>
                            <input type="text" id="phone" class="form-control" value="<?php echo $patient['phone_number']; ?>" readonly>
                        </div>
                        <div class="text-center">
                            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>