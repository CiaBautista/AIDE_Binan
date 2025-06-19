<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['rider_email'])) {
    header("Location: login_rider.php");
    exit();
}

$email = $_SESSION['rider_email'];
$stmt = $conn->prepare("SELECT ebike_plate FROM rider_users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$plate = $user['ebike_plate'] ?? '';

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Violations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            padding: 20px;
        }
        h2 {
            color: #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
<h2>My Violation Records</h2>

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
            <tr>
                <td><?= htmlspecialchars($v['ebike_plate']) ?></td>
                <td><?= htmlspecialchars($v['ebike_model']) ?></td>
                <td><?= htmlspecialchars($v['ebike_color']) ?></td>
                <td><?= htmlspecialchars($v['ebike_type']) ?></td>
                <td>
                    <?= $v['no_helmet'] ? 'No Helmet<br>' : '' ?>
                    <?= $v['no_side_mirror'] ? 'No Side Mirror<br>' : '' ?>
                    <?= $v['fake_plate'] ? 'Fake Plate<br>' : '' ?>
                    <?= $v['expired_registration'] ? 'Expired Registration<br>' : '' ?>
                    <?= $v['unregistered'] ? 'Unregistered<br>' : '' ?>
                    <?= (!$v['no_helmet'] && !$v['no_side_mirror'] && !$v['fake_plate'] && !$v['expired_registration'] && !$v['unregistered']) ? 'N/A' : '' ?>
                </td>
                <td><?= htmlspecialchars($v['detected_at']) ?></td>
                <td><?= isset($v['payment_status']) ? htmlspecialchars($v['payment_status']) : 'Not Paid' ?></td>
                <td><img src="<?= htmlspecialchars($v['image_path']) ?>" style="max-width:100px;"></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">N/A</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<br><a href="dashboard_rider.php">Dashboard</a>
</body>
</html>