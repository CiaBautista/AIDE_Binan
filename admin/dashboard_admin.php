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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - AIDE Biñan</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fef2f2;
        }
        header {
            background: #7f1d1d;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-weight: bold;
            font-size: 22px;
            letter-spacing: 1px;
        }
        .logout-btn {
            background: #b91c1c;
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        .logout-btn:hover {
            background: #991b1b;
        }
        .container {
            display: flex;
        }
        .sidebar {
            width: 220px;
            background: #991b1b;
            min-height: 100vh;
            padding: 20px 0;
            color: white;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar ul li {
            padding: 12px 20px;
            cursor: pointer;
        }
        .sidebar ul li:hover {
            background: #ef4444;
        }
        .main {
            flex: 1;
            padding: 20px;
        }
        .info-box {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .info-box h2 {
            margin-top: 0;
        }
        .info-box p {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🛵 AIDE BIÑAN</div>
    <h1>Admin Dashboard</h1>
    <a href="logout_admin.php"><button class="logout-btn">Logout</button></a>
</header>

<div class="container">
    <div class="sidebar">
        <ul>
            <li>Dashboard</li>
            <li><a href="../ai/violation.php" style="color: white; text-decoration: none;">Violation</a></li>
            <li>Penalty</li>
            <li>E-Bike Laws</li>
            <li>Notifications</li>
            <li>About</li>
        </ul>
    </div>

    <div class="main">
        <div class="info-box">
            <h2>Welcome, <?= htmlspecialchars($admin['first_name']) ?>!</h2>
            <p><strong>First Name:</strong> <?= htmlspecialchars($admin['first_name']) ?></p>
            <p><strong>Middle Name:</strong> <?= htmlspecialchars($admin['middle_name']) ?></p>
            <p><strong>Last Name:</strong> <?= htmlspecialchars($admin['last_name']) ?></p>
            <p><strong>Birthday:</strong> <?= htmlspecialchars($admin['birthday']) ?></p>
            <p><strong>Contact Number:</strong> <?= htmlspecialchars($admin['contact_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
            <p><strong>Employee Number:</strong> <?= htmlspecialchars($admin['employee_number']) ?></p>
        </div>
    </div>
</div>

</body>
</html>
