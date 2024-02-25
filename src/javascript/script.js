
function confirmCancellation(appointmentId) {
    if (confirm("Are you sure you want to cancel this appointment?")) {
        window.location.href = "my_appointments.php?cancel=true&appointment_id=" + appointmentId;
    }
}