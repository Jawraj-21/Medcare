<?php


include "connection_db.php"; // Include your database connection file

if(isset($_SESSION['user_id'])) {
  session_destroy();
  header("Location: login.php");
  exit();
}

if(isset($_POST['submit'])){
  $username_or_email = $_POST['username'];
  $password = $_POST['password'];

  // Check if the input is an email or username
  $login = filter_var($username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

  $conn = getDatabase(); // Get database connection
  $stmt = $conn->prepare("SELECT user_id, first_name, password FROM users WHERE $login = :login_field");
  $stmt->bindParam(':login_field', $username_or_email);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if($user) {
    if(password_verify($password, $user['password'])){
      $_SESSION['first_name'] = $user['first_name'];
      $_SESSION['user_id'] = $user['user_id']; // Set the user_id in session
      header("location:index.php");
      exit(); // Add this line to prevent further execution
    } else {
      echo "<script>alert('The email or password provided is incorrect')</script>";
    }
  } else {
    echo "<script>alert('User not found')</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Medcare | Login</title>
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

  <div class="screen-container">
    <div class="container">
      <div class="card mx-auto mt-5 mb-4 w-50">
        <div class="card-header">
          Login
        </div>
        <div class="card-body d-flex flex-column align-items-center justify-content-center">
          <form action="login.php" method="post">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" name="username" placeholder="Enter username">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <button type="submit" name="submit" class="btn btn-success btn-block mt-2">Login</button>
            <div class="text-center mt-3">
              <p>Not registered yet? <a href="register.php">Click here</a> to register</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  
</body>
</html>
