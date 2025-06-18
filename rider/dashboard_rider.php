<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['rider_email'])) {
    header("Location: login_rider.php");
    exit();
}

function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

$encryption_key = "your-strong-secret-key";
$email = $_SESSION['rider_email'];

$conn = new mysqli("localhost", "root", "", "aide_binan");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM rider_users";
$result = $conn->query($query);

$rider = null;
while ($row = $result->fetch_assoc()) {
    if (decrypt_data($row['email'], $encryption_key) === $email) {
        $row['email'] = $email;
        $row['contact_number'] = decrypt_data($row['contact_number'], $encryption_key);
        $rider = $row;
        break;
    }
}

if (!$rider) {
    echo "<p>User not found.</p>";
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rider Dashboard</title>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($rider['first_name']) ?>!</h1>

<h2>Account Details</h2>
<ul>
    <li><strong>First Name:</strong> <?= htmlspecialchars($rider['first_name']) ?></li>
    <li><strong>Middle Name:</strong> <?= htmlspecialchars($rider['middle_name']) ?></li>
    <li><strong>Last Name:</strong> <?= htmlspecialchars($rider['last_name']) ?></li>
    <li><strong>Birthday:</strong> <?= htmlspecialchars($rider['birthday']) ?></li>
    <li><strong>Contact Number:</strong> <?= htmlspecialchars($rider['contact_number']) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($rider['email']) ?></li>
    <li><strong>E-bike Type:</strong> <?= htmlspecialchars($rider['ebike_type']) ?></li>
    <li><strong>E-bike Model:</strong> <?= htmlspecialchars($rider['ebike_model']) ?></li>
    <li><strong>E-bike Color:</strong> <?= htmlspecialchars($rider['ebike_color']) ?></li>
    <li><strong>Plate Number:</strong> <?= htmlspecialchars($rider['ebike_plate']) ?: 'Not Assigned Yet' ?></li>
    <li><strong>Purchase Date:</strong> <?= htmlspecialchars($rider['ebike_purchased']) ?></li>
    <li><strong>Control Number:</strong> <?= htmlspecialchars($rider['ebike_control_number']) ?></li>
</ul>

<h2>Menu</h2>
<ul>
    <li><a href="#">Violation</a></li>
    <li><a href="#">Penalty</a></li>
    <li><a href="#">E-Bike Laws</a></li>
    <li><a href="#">Notifications</a></li>
    <li><a href="#">About</a></li>
    <li><a href="logout_rider.php">Logout</a></li>
</ul>
</body>
</html>
