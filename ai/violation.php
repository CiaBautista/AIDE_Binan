<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_email'])) {
    header("Location: login_admin.php");
    exit();
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

$result = $conn->query("SELECT * FROM violations ORDER BY detected_at DESC");
$violations = [];

while ($row = $result->fetch_assoc()) {
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, ebike_type, ebike_model, ebike_color, ebike_control_number, ebike_plate FROM rider_users WHERE ebike_plate = ?");
    $stmt->bind_param('s', $row['ebike_plate']);
    $stmt->execute();
    $riderResult = $stmt->get_result();
    $rider = $riderResult->fetch_assoc();

    $row['rider'] = $rider ?: null;
    $violations[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Violation Records - Admin View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fef2f2;
            padding: 20px;
        }
        h2 {
            color: #7f1d1d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #991b1b;
            color: white;
        }
        tr.unregistered {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Violation Records – Admin View</h2>

    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Rider Name</th>
                <th>E-bike Type</th>
                <th>Model</th>
                <th>Color</th>
                <th>Control #</th>
                <th>Plate #</th>
                <th>Violation</th>
                <th>Date/Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($violations as $v): ?>
                <tr class="<?= $v['unregistered'] ? 'unregistered' : '' ?>">
                    <td>
                        <?php if (!empty($v['image_path'])): ?>
                            <img src="<?= htmlspecialchars($v['image_path']) ?>" alt="E-bike">
                        <?php else: ?>
                            <em>No image</em>
                        <?php endif; ?>
                    </td>
                    <?php if ($v['rider']): ?>
                        <td><?= htmlspecialchars($v['rider']['first_name'] . ' ' . $v['rider']['middle_name'] . ' ' . $v['rider']['last_name']) ?></td>
                        <td><?= htmlspecialchars($v['rider']['ebike_type']) ?></td>
                        <td><?= htmlspecialchars($v['rider']['ebike_model']) ?></td>
                        <td><?= htmlspecialchars($v['rider']['ebike_color']) ?></td>
                        <td><?= htmlspecialchars($v['rider']['ebike_control_number']) ?></td>
                        <td><?= htmlspecialchars($v['rider']['ebike_plate']) ?></td>
                    <?php else: ?>
                        <td><em>Unknown</em></td>
                        <td><?= htmlspecialchars($v['ebike_type']) ?></td>
                        <td><?= htmlspecialchars($v['ebike_model']) ?></td>
                        <td><?= htmlspecialchars($v['ebike_color']) ?></td>
                        <td><em>Unknown</em></td>
                        <td><?= htmlspecialchars($v['ebike_plate']) ?></td>
                    <?php endif; ?>

                    <td><?= formatViolation($v) ?></td>
                    <td><?= htmlspecialchars($v['detected_at']) ?></td>
                    <td><?= htmlspecialchars($v['payment_status'] ?? 'Not Paid') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><a href="dashboard_admin.php">⬅ Back to Dashboard</a>
</body>
</html>