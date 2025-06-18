<?php
session_start();
if (!isset($_SESSION['admin_otp']) || !isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_otp = $_POST['otp'];

    if ($input_otp === $_SESSION['admin_otp']) {
        unset($_SESSION['admin_otp']);
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "<p style='color:red;'>Invalid OTP.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Enter OTP</title></head>
<body>
<h2>Enter OTP</h2>
<form method="POST">
    <input type="text" name="otp" placeholder="Enter OTP" required><br>
    <button type="submit">Verify</button>
</form>
</body>
</html>
