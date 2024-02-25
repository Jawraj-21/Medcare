<?php
include "connection_db.php";

$conn = getDatabase();

$stmt = $conn->query("SELECT * FROM departments");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>MedCare | Appointments</title>
  <link rel="stylesheet" type="text/css" href="css/style.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>

<body>
  <?php include 'header.php'; ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <?php foreach ($departments as $department): ?>
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo $department['department_name']; ?></h5>
              <p class="card-text">Opening Time: <?php echo $department['opening_time']; ?></p>
              <p class="card-text">Closing Time: <?php echo $department['closing_time']; ?></p>
              <a href="book_appointment.php?department_id=<?php echo $department['department_id']; ?>" class="btn btn-primary">Book Appointment</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>

</html>
