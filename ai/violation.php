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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Violation Records - Admin View</title>
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
    <a href="/AIDE_Binan/admin/dashboard_admin.php" class="back-btn">⬅ Back to Dashboard</a>
</header>

<div class="main">
    <h2 class="page-title">Violation Records</h2>

    <div class="table-container">
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
    </div>
</div>
</body>
</html>