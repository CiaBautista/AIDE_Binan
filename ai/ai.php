<?php
require_once '../db.php';     

function generateImageHash(string $filePath): string
{
    return hash_file('sha256', $filePath);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir  = '../uploads/';       
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName   = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imageHash = generateImageHash($targetFile);
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Violation Entry</title></head>
        <body>
            <h2>Enter Violation Details</h2>

            <!-- preview -->
            <img src="<?= $targetFile ?>" style="max-width:300px;border:1px solid #ccc"><br><br>

            <form action="ai.php" method="POST">
                <input type="hidden" name="image_path" value="<?= $targetFile ?>">
                <input type="hidden" name="image_hash" value="<?= $imageHash ?>">

                <label>E-bike Plate&nbsp;#:</label><br>
                <input type="text" name="ebike_plate" required><br><br>

                <label>E-bike Model:</label><br>
                <input type="text" name="ebike_model" required><br><br>

                <label>E-bike Color:</label><br>
                <input type="text" name="ebike_color" required><br><br>

                <label>E-bike Type:</label><br>
                <select name="ebike_type" required>
                    <option value="2-wheels">2-wheels</option>
                    <option value="3-wheels">3-wheels</option>
                </select><br><br>

                <label>Detected Violations:</label><br>
                <input type="checkbox" name="no_helmet"> No Helmet<br>
                <input type="checkbox" name="no_side_mirror"> No Side Mirror<br>
                <input type="checkbox" name="fake_plate"> Fake/Tampered Plate<br><br>

                <button type="submit" name="confirm_data">Save Violation</button>
            </form>
        </body>
        </html>
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
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Violation Result</title></head>
    <body>
    <?php
    if ($ok) {
        echo '<h3>✅ Violation saved successfully.</h3>';
        echo "<img src='$imagePath' style='max-width:300px;border:1px solid #ccc'><br>";
        echo "<p><strong>Plate:</strong> $plate<br>";
        echo "<strong>Model:</strong> $model<br>";
        echo "<strong>Color:</strong> $color</p>";


        if ($unregistered) {
            echo "<p style='color:red'><strong>⚠️ Unregistered user</strong></p>";
        } else {
            echo '<h4>Matched Rider Info</h4><ul>';
            echo '<li><strong>Name:</strong> ' . htmlspecialchars($rider['full_name']) . '</li>';
            echo '<li><strong>Email:</strong> ' . htmlspecialchars($rider['email']) . '</li>';
            echo '<li><strong>Plate #:</strong> ' . htmlspecialchars($rider['ebike_plate']) . '</li>';
            echo '<li><strong>Registration Expiry:</strong> ' . htmlspecialchars($rider['registration_expiry']) . '</li>';
            if ($expired_registration) {
                echo '<li style="color:red"><strong>⚠️ Registration expired</strong></li>';
            }
            echo '</ul>';
        }
    } else {
        echo '❌ DB Error: ' . $ins->error;
    }
    ?>
    <br><a href="ai.php">Upload Another</a>
    </body>
    </html>
    <?php
    exit();
}
?>


<!DOCTYPE html>
<html>
<head><title>Upload E-bike Violation Image</title></head>
<body>
    <h2>Upload E-bike Image</h2>
    <form action="ai.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required><br><br>
        <button type="submit">Upload Image</button>
    </form>
</body>
</html>
