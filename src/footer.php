<footer class="footer " >
  <div class="container">
    <div class="row">
      <div class="col-md-4 text-center">
        <h5>Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="index.php?page=home">Home</a></li>
          <li><a href="my_appointments.php?page=my_appointments">My Appointments</a></li>
          <li><a href="reports.php">My Reports</a></li>
          <li><a href="prescriptions.php">My Prescriptions</a></li>
        </ul>
      </div>
      <div class="col-md-4 text-center">
        <h5>Account</h5>
        <ul class="list-unstyled">
          <li><a href="profile.php">Profile</a></li>
          <li>
            <?php if (isset($_SESSION['user_id'])) { 
              $first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';  
            ?>
              <a href="login.php"><?php echo $first_name . " (Logout)" ?></a>
            <?php } else { ?>
              <a href="login.php">Log In</a>
            <?php } ?>
          </li>
        </ul>
      </div>
      <div class="col-md-4 text-center">
        <h5>Contact Us</h5>
        <p>Aston University<br>Birmingham<br>Phone: 123-456-7890<br>Email: info@medcare.com</p>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <p class="text-center">&copy; <?php echo date('Y'); ?> MedCare. All Rights Reserved.</p>
      </div>
    </div>
  </div>
</footer>
