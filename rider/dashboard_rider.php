﻿    <?php
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
        $iv = str_pad($iv, 16, "\0");  // Pad IV to 16 bytes to fix warning
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
                font-size: 26px;
                font-weight: bold;
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
                color: white;
                padding-top: 30px;
            }

            .sidebar ul {
                list-style: none;
                padding-left: 0;
            }

            .sidebar ul li {
                padding: 14px 20px;
                font-weight: 500;
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

            .info-box ul {
                padding: 0;
                list-style: none;
                line-height: 1.7;
            }

            .info-box li {
                margin-bottom: 6px;
                font-size: 16px;
            }

            .summary {
                display: flex;
                justify-content: space-between;
                gap: 20px;
            }

            .summary-box {
                flex: 1;
                background: #fff;
                border-radius: 10px;
                padding: 20px;
                text-align: center;
                box-shadow: 0 4px 10px rgba(0,0,0,0.08);
                transition: transform 0.2s ease-in-out;
            }

            .summary-box:hover {
                transform: translateY(-5px);
            }

            .summary-box h3 {
                margin: 0;
                color: #991b1b;
                font-weight: bold;
                font-size: 18px;
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
        <div class="logo">🚴 AIDE BIÑAN</div>
        <h1>Rider Dashboard</h1>
        <a href="logout_rider.php"><button class="logout-btn">Logout</button></a>
    </header>

    <div class="container">
        <div class="sidebar">
            <ul>
                <li><a href="#">E-Bike Laws</a></li>
                <li><a href="../ai/violation_rider.php">My Violation</a></li>
                <li><a href="#">Notifications</a></li>
                <li><a href="#">About</a></li>
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

            <div class="summary">
                <div class="summary-box">
                    <h3>About</h3>
                    <p>System Info</p>
                </div>
                <div class="summary-box">
                    <h3>Info</h3>
                    <p>System Details</p>
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