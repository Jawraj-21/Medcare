<?php
include "connection_db.php";

$conn = getDatabase();

$user_details = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT 'patient' AS type, first_name, last_name, gender, DOB, address, phone_number, patient_id FROM patients WHERE user_id = :user_id 
  UNION ALL 
  SELECT 'doctor' AS type, first_name, last_name, gender, DOB, address, phone_number, null AS patient_id FROM doctors WHERE user_id = :user_id 
");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
}

$userFirstName = isset($user_details['first_name']) ? $user_details['first_name'] : "";
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
        <?php endif; ?>
    </div>

    <?php displayMedicinePopup($conn, $user_details); ?>
    <?php displayAppointmentPopup($conn); ?>

    <script>
        window.onload = function() {
            <?php
            if (isset($_SESSION['medicine_popup_shown']) && $_SESSION['medicine_popup_shown']) {
                echo "var medicinePopup = new bootstrap.Modal(document.getElementById('medicinePopup'));\n";
                echo "medicinePopup.show();\n";
            }

            if (isset($_SESSION['appointment_popup_shown']) && $_SESSION['appointment_popup_shown']) {
                echo "var appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));\n";
                echo "appointmentModal.show();\n";
            }
            ?>
        };
    </script>

</body>
</html>

<?php
function displayMedicinePopup($conn, $user_details)
{
    if ($user_details && !isset($_SESSION['medicine_popup_shown'])) {
        $stmt = $conn->prepare("SELECT *, MAX(mt.taken_at) AS last_taken_time FROM prescriptions p LEFT JOIN medicine_taken mt ON p.medicine_id = mt.medicine_id WHERE p.patient_id = :patient_id GROUP BY p.prescription_id");
        $stmt->bindParam(':patient_id', $user_details['patient_id']);
        $stmt->execute();
        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $showPopup = false;
        foreach ($prescriptions as $prescription) {
            $last_taken_time = $prescription['last_taken_time'];
            $dose_interval = $prescription['dose_interval'];
            $next_take_time = strtotime($last_taken_time) + getSecondsFromTimeString($dose_interval);

            if (time() >= $next_take_time) {
                $showPopup = true;
                break;
            }
        }

        if ($showPopup) {
            echo '<div class="modal fade" id="medicinePopup" tabindex="-1" aria-labelledby="medicinePopupLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="medicinePopupLabel">Medicine Reminder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>It\'s time to take your medicine!</p>
                        </div>
                    </div>
                </div>
            </div>';
            $_SESSION['medicine_popup_shown'] = true;
        }
    }
}

function displayAppointmentPopup($conn)
{
    if (!isset($_SESSION['appointment_popup_shown']) && isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = :user_id AND DATE(date) >= CURDATE() AND CONCAT(date, ' ', time) > NOW() ORDER BY date ASC LIMIT 1");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appointment) {
            echo '<div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Upcoming Appointment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Date: ' . $appointment['date'] . '<br>Time: ' . $appointment['time'] . '</p>
                        </div>
                    </div>
                </div>
            </div>';
            $_SESSION['appointment_popup_shown'] = true;
        }
    }
}

function getSecondsFromTimeString($timeString)
{
    $timeParts = explode(':', $timeString);
    return ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];
}
?>
