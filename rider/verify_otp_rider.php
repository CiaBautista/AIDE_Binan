<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = $_POST['otp'] ?? '';
    $correctOtp = $_SESSION['rider_otp'] ?? '';

    if ($enteredOtp === $correctOtp) {
        
        unset($_SESSION['rider_otp']);
        header("Location: login_rider.php");
        exit();
    } else {
        echo "<p style='color:red;'>Invalid OTP. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Verify OTP</title></head>
<body>
<h1>Verify OTP</h1>
<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required>
    <button type="submit">Verify</button>
</form>
</body>
</html>
