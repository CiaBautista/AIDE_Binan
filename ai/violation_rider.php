<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['rider_email']) && !isset($_SESSION['admin_email'])) {
    header("Location: login_rider.php");
    exit();
}

// Decrypt function (same as in dashboard)
function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $iv = str_pad($iv, 16, "\0");
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

$encryption_key = "your-strong-secret-key";
$plate = '';

// Admin view (via GET param)
if (isset($_GET['plate'])) {
    $plate = $_GET['plate'];
} else {
    // Rider view using decrypted email
    if (!isset($_SESSION['rider_email'])) {
        header("Location: login_rider.php");
        exit();
    }
    $email = $_SESSION['rider_email'];
    $stmt = $conn->query("SELECT ebike_plate, email FROM rider_users");
    while ($row = $stmt->fetch_assoc()) {
        if (decrypt_data($row['email'], $encryption_key) === $email) {
            $plate = $row['ebike_plate'];
            break;
        }
    }
}

$violations = [];
if ($plate) {
    $stmt = $conn->prepare("SELECT * FROM violations WHERE ebike_plate = ? ORDER BY detected_at DESC");
    $stmt->bind_param('s', $plate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $violations[] = $row;
    }
}

function formatViolation($row) {
    $violations = [];
    if ($row['no_helmet']) $violations[] = 'No Helmet';
    if ($row['no_side_mirror']) $violations[] = 'No Side Mirror';
    if ($row['fake_plate']) $violations[] = 'Fake/Tampered Plate';
    if ($row['unregistered']) $violations[] = 'Unregistered';
    if ($row['expired_registration']) $violations[] = 'Expired Registration';
    return implode(', ', $violations);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Violations</title>
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
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-weight: bold;
            font-size: 28px;
        }

        .back-btn {
            background: #b91c1c;
            color: white;
            border: none;
            padding: 10px 16px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #991b1b;
        }

        .main {
            padding: 30px;
        }

        h2.page-title {
            color: #7f1d1d;
            text-align: center;
            margin: 0 0 30px;
            font-size: 28px;
        }

        .table-container {
            max-height: 500px; 
            overflow-y: auto;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #991b1b;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tr.unregistered {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background-color: #991b1b;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🛵 A.I.D.E. BIÑAN</div>
    <?php if (isset($_SESSION['admin_email'])): ?>
        <a href="/AIDE_Binan/admin/dashboard_admin.php" class="back-btn">⬅ Back to Dashboard</a>
    <?php else: ?>
        <a href="../rider/dashboard_rider.php" class="back-btn">⬅ Back to Dashboard</a>
    <?php endif; ?>
</header>

<div class="main">
    <h2 class="page-title">My Violation Records</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Plate</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Type</th>
                    <th>Violation(s)</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($violations)): ?>
                    <?php foreach ($violations as $v): ?>
                        <tr class="<?= $v['unregistered'] ? 'unregistered' : '' ?>">
                            <td><?= htmlspecialchars($v['ebike_plate']) ?></td>
                            <td><?= htmlspecialchars($v['ebike_model']) ?></td>
                            <td><?= htmlspecialchars($v['ebike_color']) ?></td>
                            <td><?= htmlspecialchars($v['ebike_type']) ?></td>
                            <td><?= formatViolation($v) ?: 'N/A' ?></td>
                            <td><?= htmlspecialchars($v['detected_at']) ?></td>
                            <td><?= isset($v['payment_status']) ? htmlspecialchars($v['payment_status']) : 'Not Paid' ?></td>
                            <td>
                                <?php if (!empty($v['image_path'])): ?>
                                    <img src="<?= htmlspecialchars($v['image_path']) ?>" alt="Evidence">
                                <?php else: ?>
                                    <em>No image</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8"><em>No violation records found.</em></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
