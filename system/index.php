<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head><title>A.I.D.E. BIÑAN</title></head>

<body>
<h1>Welcome to A.I.D.E. BIÑAN</h1>

<form action="" method="POST">
    <button type="submit" name="action" value="login_rider">Rider</button>
    <button type="submit" name="action" value="login_admin">Admin</button>
    <button type="submit" name="action" value="register">Register</button>
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($_POST['action']) {
        case "register": header("Location: register.php"); break;
        case "login_rider": header("Location: ../rider/login_rider.php"); break;
        case "login_admin": header("Location: ../admin/login_admin.php"); break;
    }
    exit();
}
?>
</body>
</html>