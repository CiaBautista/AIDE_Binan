<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit();
}

function decrypt_data($encrypted_data, $key) {
    if (!$encrypted_data) return false;
    $data = base64_decode($encrypted_data);
    if ($data === false || strlen($data) < 17) return false;
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

$encryption_key = "your-strong-secret-key";
$email = $_SESSION['admin_email'];

$conn = new mysqli("localhost", "root", "", "aide_binan");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM admin_users";
$result = $conn->query($query);

$admin = null;
while ($row = $result->fetch_assoc()) {
    $decrypted_email = decrypt_data($row['email'], $encryption_key);
    if ($decrypted_email === $email) {
        $row['email'] = $email;
        $row['contact_number'] = decrypt_data($row['contact_number'], $encryption_key);
        $admin = $row;
        break;
    }
}

if (!$admin) {
    echo "<p>User not found. Debug: Session Email = $email</p>";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($admin['first_name']) ?>!</h1>

<h2>Account Details</h2>
<ul>
    <li><strong>First Name:</strong> <?= htmlspecialchars($admin['first_name']) ?></li>
    <li><strong>Middle Name:</strong> <?= htmlspecialchars($admin['middle_name']) ?></li>
    <li><strong>Last Name:</strong> <?= htmlspecialchars($admin['last_name']) ?></li>
    <li><strong>Birthday:</strong> <?= htmlspecialchars($admin['birthday']) ?></li>
    <li><strong>Contact Number:</strong> <?= htmlspecialchars($admin['contact_number']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></li>
    <li><strong>Employee Number:</strong> <?= htmlspecialchars($admin['employee_number']) ?></li>
</ul>

<h2>Menu</h2>
<ul>
    <li><a href="#">Violation</a></li>
    <li><a href="#">Penalty</a></li>
    <li><a href="#">E-Bike Laws</a></li>
    <li><a href="#">Notifications</a></li>
    <li><a href="#">About</a></li>
    <li><a href="logout_admin.php">Logout</a></li>
</ul>
</body>
</html>
