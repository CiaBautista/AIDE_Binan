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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rider Dashboard</title>
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
            font-size: 24px;
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
        }
        .sidebar ul li:hover {
            background: #ef4444;
            cursor: pointer;
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
            color: #7f1d1d;
        }

        .info-box ul {
            padding: 0;
            list-style: none;
        }

        .info-box li {
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

<header>
    <div class="logo">?? AIDE BIÑAN</div>
    <h1>Rider Dashboard</h1>
    <a href="logout_rider.php"><button class="logout-btn">Logout</button></a>
</header>

<div class="container">
    <div class="sidebar">
        <ul>
            <li>E-Bike Laws</li>
            <li>Penalty</li>
            <li>Notifications</li>
            <li>About</li>
        </ul>
    </div>

    <div class="main">
        <div class="info-box">
            <h2>Welcome, <?= htmlspecialchars($rider['first_name']) ?>!</h2>
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
        </div>
    </div>
</div>

</body>
</html>
