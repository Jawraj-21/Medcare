<?php
include "connection_db.php";

$conn = getDatabase();

$stmt = $conn->query("SELECT * FROM departments");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Medcare | Appointments</title>
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
<?php include 'header.php'; ?>

<div class="container">
  <div class="row">
    <?php foreach ($departments as $department): ?>
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo $department['department_name']; ?></h5>
            <p class="card-text">Opening Time: <?php echo $department['opening_time']; ?></p>
            <p class="card-text">Closing Time: <?php echo $department['closing_time']; ?></p>
            <a href="book_appointment.php?department_id=<?php echo $department['department_id']; ?>" class="btn btn-primary">Book Appointment</a>
          </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


</body>