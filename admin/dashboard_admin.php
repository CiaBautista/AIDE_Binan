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

// Count registered riders
$countResult = $conn->query("SELECT COUNT(*) as total FROM rider_users");
$countRow = $countResult->fetch_assoc();
$total_riders = $countRow['total'] ?? 0;

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

        header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
        }

        .logo {
            font-weight: bold;
            font-size: 22px;
        }

        .logout-btn {
            background: #b91c1c;
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 6px;
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
            padding: 14px 20px;
            font-weight: 500;
            cursor: pointer;
        }

        .sidebar ul li:hover {
            background: #ef4444;
            transition: 0.3s;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .main {
            flex: 1;
            padding: 30px;
        }

        .info-box {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .info-box h2 {
            margin-top: 0;
            color: #7f1d1d;
            font-size: 22px;
        }

        .info-box p {
            margin: 6px 0;
            font-size: 16px;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .summary-box {
            flex: 1;
            min-width: 240px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .summary-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .summary-box h3 {
            margin: 0;
            color: #b91c1c;
            font-size: 18px;
            font-weight: bold;
        }

        .summary-box p {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            color: #111;
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
            <li><a href="#">Dashboard</a></li>
            <li><a href="../ai/violation.php">Violation</a></li>
            <li><a href="#">Penalty</a></li>
            <li><a href="#">E-Bike Laws</a></li>
            <li><a href="#">Notifications</a></li>
            <li><a href="#">About</a></li>
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

        <div class="summary">
            <div class="summary-box">
                <h3>E-bike Laws</h3>
                <p>View list of Laws</p>
            </div>
            <div class="summary-box">
                <h3>Registered Riders</h3>
                <p><?= $total_riders ?></p>
            </div>
            <div class="summary-box">
                <h3>Total Penalties</h3>
                <p>List of penalties</p>
            </div>
        </div>

        <br><br><br>

        <div class="summary">
            <div class="summary-box">
                <h3>About</h3>
                <p>System Info</p>
            </div>
            <div class="summary-box">
                <h3>Payment Details</h3>
                <p>Payments List</p>
            </div>
            <div class="summary-box">
                <h3>Alert</h3>
                <p>Notifications</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>