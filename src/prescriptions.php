<?php
ob_start();

include "connection_db.php";

date_default_timezone_set('UTC');

if (!isset($_SESSION['user_id'])) {
    $loginMessage = "You need to log in to access your prescriptions.";
} else {
    $user_id = $_SESSION['user_id'];

    $conn = getDatabase();
    $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $patient_id = $patient['patient_id'];

        $stmt = $conn->prepare("SELECT * FROM prescriptions WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Error: Patient data not found for the logged-in user.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>MedCare | My Prescriptions</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">My Prescriptions</h2>
        <div class="row justify-content-center">
            <?php foreach ($prescriptions as $prescription) : ?>
                <div class="col-md-5 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            $medicine_id = $prescription['medicine_id'];
                            $stmt = $conn->prepare("SELECT name, no_medicine FROM medicine WHERE medicine_id = :medicine_id");
                            $stmt->bindParam(':medicine_id', $medicine_id);
                            $stmt->execute();
                            $medicine = $stmt->fetch(PDO::FETCH_ASSOC);

                            $quantity = $prescription['quantity'];

                            $stmt = $conn->prepare("SELECT taken_at FROM medicine_taken WHERE medicine_id = :medicine_id AND patient_id = :patient_id ORDER BY taken_at DESC LIMIT 1");
                            $stmt->bindParam(':medicine_id', $medicine_id);
                            $stmt->bindParam(':patient_id', $patient_id);
                            $stmt->execute();
                            $last_taken_time = $stmt->fetchColumn();

                            $next_take_time = '';
                            if ($last_taken_time) {
                                $dose_interval = $prescription['dose_interval'];
                                $next_take_time = date('Y-m-d H:i:s', strtotime($last_taken_time) + getSecondsFromTimeString($dose_interval));
                            }

                            $disable_button = $next_take_time > date('Y-m-d H:i:s');

                            if ($quantity == 1) {
                                $no_medicine = $medicine['no_medicine'];
                                $new_quantity = $quantity + $no_medicine;
                                $stmt = $conn->prepare("UPDATE prescriptions SET quantity = :new_quantity WHERE prescription_id = :prescription_id");
                                $stmt->bindParam(':new_quantity', $new_quantity);
                                $stmt->bindParam(':prescription_id', $prescription['prescription_id']);
                                $stmt->execute();
                            }

                            ?>
                            <h5 class="card-text">Medicine: <?php echo $medicine['name']; ?></h5>
                            <p class="card-text">Instructions: <?php echo $prescription['instructions']; ?></p>
                            <p class="card-text">Medicine dosage: <?php echo $prescription['dose']; ?></p>
                            <p class="card-text">Capsules left: <?php echo $quantity; ?></p>
                            <?php if ($last_taken_time) : ?>
                                <p class="card-text">Last taken: <?php echo $last_taken_time; ?></p>
                                <p class="card-text">Next available: <?php echo $next_take_time; ?></p>
                            <?php endif; ?>
                            <form method="post" action="">
                                <input type="hidden" name="prescription_id" value="<?php echo $prescription['prescription_id']; ?>">
                                <input type="hidden" name="dose" value="<?php echo $prescription['dose']; ?>">
                                <div class="btn-group" role="group" aria-label="Actions">
                                    <!-- Trigger confirmation modal on button click -->
                                    <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#takeMedicationModal<?php echo $prescription['prescription_id']; ?>" <?php if ($disable_button) echo 'disabled'; ?>>
                                        Take Medication
                                    </button>
                                    <?php if ($quantity <= 7 && $prescription['reorder_state'] == 1) : ?>
                                        <button type="button" class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#reorderModal<?php echo $prescription['prescription_id']; ?>">Re-order Medicine</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Confirmation modal for taking medication -->
                <div class="modal fade" id="takeMedicationModal<?php echo $prescription['prescription_id']; ?>" tabindex="-1" aria-labelledby="takeMedicationModalLabel<?php echo $prescription['prescription_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="takeMedicationModalLabel<?php echo $prescription['prescription_id']; ?>">Take Medication</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to take this medication?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <!-- Submit form only if user confirms -->
                                <form method="post" action="">
                                    <input type="hidden" name="takeMedication" value="Yes">
                                    <input type="hidden" name="prescription_id" value="<?php echo $prescription['prescription_id']; ?>">
                                    <input type="hidden" name="dose" value="<?php echo $prescription['dose']; ?>">
                                    <button type="submit" class="btn btn-success">Yes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
    if (isset($_POST['takeMedication']) && $_POST['takeMedication'] === 'Yes') {
        $prescription_id = $_POST['prescription_id'];
        $dose = $_POST['dose'];

        $stmt = $conn->prepare("INSERT INTO medicine_taken (patient_id, medicine_id, taken_at) VALUES (:patient_id, :medicine_id, NOW())");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->bindParam(':medicine_id', $prescription['medicine_id']);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE prescriptions SET quantity = quantity - :dose WHERE prescription_id = :prescription_id");
        $stmt->bindParam(':dose', $dose);
        $stmt->bindParam(':prescription_id', $prescription_id);
        $stmt->execute();

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['reorderMedication'])) {
        $prescription_id = $_POST['reorderMedication'];

        $stmt = $conn->prepare("UPDATE prescriptions SET reorder_state = 0 WHERE prescription_id = :prescription_id");
        $stmt->bindParam(':prescription_id', $prescription_id);
        $stmt->execute();

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    function getSecondsFromTimeString($timeString) {
        $timeParts = explode(':', $timeString);
        return ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];
    }
    ?>

    <?php foreach ($prescriptions as $prescription) : ?>
        <div class="modal fade" id="reorderModal<?php echo $prescription['prescription_id']; ?>" tabindex="-1" aria-labelledby="reorderModalLabel<?php echo $prescription['prescription_id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reorderModalLabel<?php echo $prescription['prescription_id']; ?>">Re-order Medicine</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to re-order this medication?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="post" action="">
                            <input type="hidden" name="reorderMedication" value="<?php echo $prescription['prescription_id']; ?>">
                            <button type="submit" class="btn btn-warning">Re-order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>

<?php
ob_end_flush();
?>
