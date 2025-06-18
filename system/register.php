<!DOCTYPE html>

<html>
<head><title>Register</title></head>

<body>
<h1>Select Registration Type</h1>

<form method="POST">
    <button type="submit" name="role" value="admin">Admin</button>
    <button type="submit" name="role" value="rider">Rider</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    if ($role === 'admin') {
        header("Location: ../admin/register_admin.php");
    } elseif ($role === 'rider') {
        header("Location: ../rider/register_rider.php");
    }
    exit();
}
?>
</body>
</html>