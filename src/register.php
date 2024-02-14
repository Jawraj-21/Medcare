<?php
include 'connection_db.php';

$conn = getDatabase();

if (isset($_POST["user_firstname"]) && isset($_POST["user_lastname"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["retypepassword"]) && isset($_POST["gender"]) && isset($_POST["DOB"]) && isset($_POST["address"]) && isset($_POST["phone_number"])) {
    require_once('connection_db.php');
    
    // Check if all fields are filled
    $success = true;
    $requiredFields = array("user_firstname", "user_lastname", "username", "email", "password", "retypepassword", "gender", "DOB", "address", "phone_number");
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $success = false;
            break;
        }
    }
    
    if (!$success) {
        echo "<div class='alert alert-danger'><h1>Error</h1><p>All fields must be filled out</p></div>";
    } else {
        // Check if passwords match
        if ($_POST["password"] != $_POST["retypepassword"]) {
            echo "<script>alert('Passwords must match!')</script>";
        } else {
            $fname = $_POST['user_firstname'];
            $lname = $_POST['user_lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = 'Patient'; // Assign role as 'Patient' by default
            $gender = $_POST['gender'];
            $dob = $_POST['DOB'];
            $address = $_POST['address'];
            $phone_number = $_POST['phone_number'];

            // Inserts the Registered User information to the appropriate table based on the selected role
            try {
                // Insert into users table
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, first_name, last_name, role) VALUES (:username, :password, :email, :first_name, :last_name, :role)");
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':first_name', $fname);
                $stmt->bindParam(':last_name', $lname);
                $stmt->bindParam(':role', $role); // Assign role here
                $stmt->execute();

                // Get the user_id of the inserted user
                $user_id = $conn->lastInsertId();

                // Insert into appropriate role-specific table
                $stmt = $conn->prepare("INSERT INTO patients (user_id, first_name, last_name, gender, DOB, address, phone_number) VALUES (:user_id, :first_name, :last_name, :gender, :DOB, :address, :phone_number)");
                $stmt->bindParam(':user_id', $user_id); // Assign the user_id retrieved from the inserted user record
                $stmt->bindParam(':first_name', $fname);
                $stmt->bindParam(':last_name', $lname);
                $stmt->bindParam(':gender', $gender);
                $stmt->bindParam(':DOB', $dob);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':phone_number', $phone_number);
                $stmt->execute();

                echo "Congratulations! You are now registered.";
                header("Location: ./index.php");
            } catch (PDOException $ex) {
                echo "Sorry, a database error occurred! <br>";
                echo "Error details: <em>" . $ex->getMessage() . "</em>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <title>Medcare | Register</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <!-- header goes here when header is ready -->
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="card mx-auto mt-5 mb-4 w-50">
            <div class="card-header">
                Register
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <form action="register.php" method="post">
                    <div class="form-group">
                        <label for="user_firstname">First Name</label>
                        <input type="text" class="form-control" name="user_firstname" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label for="user_lastname">Last Name</label>
                        <input type="text" class="form-control" name="user_lastname" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                   <div class="form-group">
                        <label for="retypepassword">Retype Password</label>
                        <input type="password" class="form-control" name="retypepassword" placeholder="Retype Password" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="DOB">Date of Birth</label>
                        <input type="date" class="form-control" name="DOB" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" placeholder="Address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" name="phone_number" placeholder="Phone Number" required>
                    </div>

                    <button type="submit" name="submit" value="Register" class="btn btn-success btn-block mt-2">Register</button>
                    <div class="text-center mt-3">
                        <p>Already registered? <a href="login.php">Click here</a> to Login</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
