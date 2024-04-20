<?php
include 'connection_db.php';

$conn = getDatabase();

if (isset($_POST["user_firstname"]) && isset($_POST["user_lastname"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["retypepassword"]) && isset($_POST["gender"]) && isset($_POST["DOB"]) && isset($_POST["address"]) && isset($_POST["phone_number"])) {
    require_once('connection_db.php');

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
        if ($_POST["password"] != $_POST["retypepassword"]) {
            echo "<script>alert('Passwords must match!')</script>";
        } else {
            $password = $_POST["password"];
            if (!validatePassword($password)) {
                echo "<script>alert('Password must contain at least 8 characters, including one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&')</script>";
                exit;
            }

            $fname = $_POST['user_firstname'];
            $lname = $_POST['user_lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $role = 'Patient';
            $gender = $_POST['gender'];
            $dob = $_POST['DOB'];
            $address = $_POST['address'];
            $phone_number = $_POST['phone_number'];

            try {
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, first_name, last_name, role) VALUES (:username, :password, :email, :first_name, :last_name, :role)");
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hash);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':first_name', $fname);
                $stmt->bindParam(':last_name', $lname);
                $stmt->bindParam(':role', $role);
                $stmt->execute();

                $user_id = $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO patients (user_id, first_name, last_name, gender, DOB, address, phone_number) VALUES (:user_id, :first_name, :last_name, :gender, :DOB, :address, :phone_number)");
                $stmt->bindParam(':user_id', $user_id);
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

function validatePassword($password) {
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($passwordPattern, $password);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medcare | Register</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="card mx-auto mt-5 mb-4 w-50">
            <div class="card-header">
                Register
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <form action="register.php" method="post" id="registerForm">
                    <div class="form-group position-relative">
                        <label for="user_firstname">First Name</label>
                        <input type="text" class="form-control" name="user_firstname" placeholder="First Name" required>
                    </div>
                    <div class="form-group position-relative">
                        <label for="user_lastname">Last Name</label>
                        <input type="text" class="form-control" name="user_lastname" placeholder="Last Name" required>
                    </div>
                    <div class="form-group position-relative">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group position-relative">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group position-relative">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <div id="password-requirements" style="display: none;">
                            <div class="requirement">✘ Minimum of 8 characters</div>
                            <div class="requirement">✘ Uppercase letter</div>
                            <div class="requirement">✘ Lowercase letter</div>
                            <div class="requirement">✘ One number</div>
                        </div>
                        <div id="password-message" class="text-danger"></div>
                    </div>
                    <div class="form-group position-relative">
                        <label for="retypepassword">Retype Password</label>
                        <input type="password" class="form-control" id="retypepassword" name="retypepassword" placeholder="Retype Password" required>
                        <div id="retypepassword-message" class="text-danger"></div>
                    </div>
                    <div class="form-group position-relative">
                        <label for="gender">Gender</label>
                        <select class="form-control" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group position-relative">
                        <label for="DOB">Date of Birth</label>
                        <input type="date" class="form-control" name="DOB" required>
                    </div>
                    <div class="form-group position-relative">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" name="address" placeholder="Address" required>
                    </div>
                    <div class="form-group position-relative">
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

    <script>
        $(document).ready(function() {
            $('#password').on('focus', function() {
                $('#password-requirements').show();
            });

            $('#password').on('blur', function() {
                $('#password-requirements').hide(); 
            });

            $('#password').on('input', function() {
                var password = $(this).val();
                var passwordMessage = $('#password-message');
                var passwordRequirements = $('#password-requirements').children();
                var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
                var requirementsMet = [false, false, false, false];

                if (password.length >= 8) {
                    requirementsMet[0] = true;
                }
                if (password.match(/[A-Z]/)) {
                    requirementsMet[1] = true;
                }
                if (password.match(/[a-z]/)) {
                    requirementsMet[2] = true;
                }
                if (password.match(/\d/)) {
                    requirementsMet[3] = true;
                }

                for (var i = 0; i < passwordRequirements.length; i++) {
                    var requirement = $(passwordRequirements[i]);
                    if (requirementsMet[i]) {
                        requirement.removeClass('text-danger').addClass('text-success').html('✔ ' + requirement.text().substr(2));
                    } else {
                        requirement.removeClass('text-success').addClass('text-danger').html('✘ ' + requirement.text().substr(2));
                    }
                }

                if (passwordPattern.test(password)) {
                    passwordMessage.html("");
                } else {
                    passwordMessage.html("Password does not meet the requirements");
                }
            });

            $('#retypepassword').on('input', function() {
                var password = $('#password').val();
                var retypepassword = $(this).val();
                var retypepasswordMessage = $('#retypepassword-message');

                if (password === retypepassword) {
                    retypepasswordMessage.html(""); 
                } else {
                    retypepasswordMessage.html("Passwords do not match");
                }
            });
        });
    </script>
    <?php include 'footer.php'; ?>
</body>

</html>

