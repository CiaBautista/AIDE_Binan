<?php
require_once '../db.php';     

function generateImageHash(string $filePath): string {
    return hash_file('sha256', $filePath);
}

function renderHeader($title = "Violation Entry") {
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{$title}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #fef2f2;
                margin: 0;
                padding: 0;
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
                font-size: 22px;
            }
            .back-btn {
                background-color: #b91c1c;
                border: none;
                color: white;
                padding: 10px 16px;
                font-size: 14px;
                border-radius: 6px;
                cursor: pointer;
                text-decoration: none;
            }
            .back-btn:hover {
                background-color: #991b1b;
            }
            .container {
                padding: 20px;
            }
            input, select, button {
                margin-bottom: 10px;
                padding: 8px;
                width: 100%;
                max-width: 300px;
            }
            label {
                font-weight: bold;
            }
            img {
                max-width: 300px;
                border: 1px solid #ccc;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Violation Entry</h1>
            <a href="http://localhost/AIDE_Binan/system/" class="back-btn">⬅ Back</a>
        </header>
        <div class="container">
HTML;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir  = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName   = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imageHash = generateImageHash($targetFile);
        renderHeader("Violation Details");
        ?>
        <h2>Enter Violation Details</h2>
        <img src="<?= $targetFile ?>"><br>

        <form action="ai.php" method="POST">
            <input type="hidden" name="image_path" value="<?= $targetFile ?>">
            <input type="hidden" name="image_hash" value="<?= $imageHash ?>">

            <label>E-bike Plate #:</label><br>
            <input type="text" name="ebike_plate" required><br>

            <label>E-bike Model:</label><br>
            <input type="text" name="ebike_model" required><br>

            <label>E-bike Color:</label><br>
            <input type="text" name="ebike_color" required><br>

            <label>E-bike Type:</label><br>
            <select name="ebike_type" required>
                <option value="2-wheels">2-wheels</option>
                <option value="3-wheels">3-wheels</option>
            </select><br>

            <label>Detected Violations:</label><br>
            <input type="checkbox" name="no_helmet"> No Helmet<br>
            <input type="checkbox" name="no_side_mirror"> No Side Mirror<br>
            <input type="checkbox" name="fake_plate"> Fake/Tampered Plate<br><br>

            <button type="submit" name="confirm_data">Save Violation</button>
        </form>
        </div></body></html>
        <?php
        exit();
    }
    echo '❌ Failed to upload image.';
    exit();
}

if (isset($_POST['confirm_data'])) {
    $imagePath  = $_POST['image_path'];
    $imageHash  = $_POST['image_hash'];
    $plate      = strtoupper(trim($_POST['ebike_plate']));
    $model      = $_POST['ebike_model'];
    $color      = $_POST['ebike_color'];
    $type       = $_POST['ebike_type'];

    $v_noHelmet      = isset($_POST['no_helmet'])       ? 1 : 0;
    $v_noMirror      = isset($_POST['no_side_mirror'])  ? 1 : 0;
    $v_fakePlate     = isset($_POST['fake_plate'])      ? 1 : 0;

    $stmt = $conn->prepare("SELECT * FROM rider_users WHERE ebike_plate = ?");
    $stmt->bind_param('s', $plate);
    $stmt->execute();
    $userResult = $stmt->get_result();

    $unregistered         = ($userResult->num_rows === 0) ? 1 : 0;
    $expired_registration = 0;
    $rider                = null;

    if (!$unregistered) {
        $rider = $userResult->fetch_assoc();
        if (strtotime($rider['registration_expiry']) < time()) {
            $expired_registration = 1;
        }
    }

    $ins = $conn->prepare("
        INSERT INTO violations (
            image_path, image_hash, ebike_type, ebike_model, ebike_color, ebike_plate,
            no_helmet, no_side_mirror, fake_plate, unregistered, expired_registration,
            detected_at
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW())");
    $ins->bind_param(
        'ssssssiiiii',
        $imagePath,
        $imageHash,
        $type,
        $model,
        $color,
        $plate,
        $v_noHelmet,
        $v_noMirror,
        $v_fakePlate,
        $unregistered,
        $expired_registration
    );
    $ok = $ins->execute();

    renderHeader("Violation Result");

    if ($ok) {
        echo "<h3>✅ Violation saved successfully.</h3>";
        echo "<img src='$imagePath'><br>";
        echo "<p><strong>Plate:</strong> $plate<br>";
        echo "<strong>Model:</strong> $model<br>";
        echo "<strong>Color:</strong> $color</p>";

        if ($unregistered) {
            echo "<p style='color:red'><strong>⚠️ Unregistered user</strong></p>";
        } else {
            echo "<h4>Matched Rider Info</h4><ul>";
            echo "<li><strong>Name:</strong> " . htmlspecialchars($rider['full_name']) . "</li>";
            echo "<li><strong>Email:</strong> " . htmlspecialchars($rider['email']) . "</li>";
            echo "<li><strong>Plate #:</strong> " . htmlspecialchars($rider['ebike_plate']) . "</li>";
            echo "<li><strong>Registration Expiry:</strong> " . htmlspecialchars($rider['registration_expiry']) . "</li>";
            if ($expired_registration) {
                echo '<li style="color:red"><strong>⚠️ Registration expired</strong></li>';
            }
            echo "</ul>";
        }
    } else {
        echo '❌ DB Error: ' . $ins->error;
    }
    echo '<br><a href="ai.php" class="back-btn">Upload Another</a>';
    echo '</div></body></html>';
    exit();
}
?>

<?php renderHeader("Upload Violation Image"); ?>
    <h2>Upload E-bike Image</h2>
    <form action="ai.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required><br>
        <button type="submit">Upload Image</button>
    </form>
</div>
</body>
</html>
