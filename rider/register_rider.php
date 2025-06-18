<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rider Registration - AIDE Biñan</title>
</head>
<body>
    <h1>Rider Registration Form</h1>

    <form action="" method="POST">
        <label>First Name:</label><br><input type="text" name="first_name" required><br>
        <label>Middle Name:</label><br><input type="text" name="middle_name"><br>
        <label>Last Name:</label><br><input type="text" name="last_name" required><br>
        <label>Birthday:</label><br><input type="date" name="birthday" required><br>
        <label>Contact Number:</label><br><input type="text" name="contact_number" required><br>
        <label>Email:</label><br><input type="email" name="email" required><br>
        <label>Password:</label><br><input type="password" name="password" required><br>
        <label>Confirm Password:</label><br><input type="password" name="confirm_password" required><br><br>

        <label>Type of E-bike:</label><br>
        <input type="text" name="ebike_type" value="2-wheels" readonly><br>
        <label>E-bike Model:</label><br><input type="text" name="ebike_model" required><br>
        <label>E-bike Color:</label><br><input type="text" name="ebike_color" required><br>
        <label>Control Number:</label><br><input type="text" name="ebike_control_number" required><br><br>
        <label>Purchase Date:</label><br><input type="date" name="ebike_purchased" required><br>


        <button type="submit" name="submit">Register</button>
    </form>

<?php
function encrypt_data($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

function generateOTP($length = 6) {
    $digits = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $digits[random_int(0, strlen($digits) - 1)];
    }
    return $otp;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = ucwords(strtolower(trim($_POST['first_name'])));
    $middle_name = ucwords(strtolower(trim($_POST['middle_name'])));
    $last_name = ucwords(strtolower(trim($_POST['last_name'])));
    $birthday = $_POST['birthday'];
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $ebike_type = "2-wheels";
    $ebike_model = ucwords(strtolower(trim($_POST['ebike_model'])));
    $ebike_color = ucwords(strtolower(trim($_POST['ebike_color'])));
    $ebike_control_number = $_POST['ebike_control_number'];
    $ebike_purchased = $_POST['ebike_purchased'];
    $ebike_plate = ""; // Assigned by admin later


    if ($password !== $confirm_password) {
        echo "<p style='color:red;'>Passwords do not match.</p>";
        exit;
    }

    $encryption_key = "your-strong-secret-key";
    $encrypted_email = encrypt_data($email, $encryption_key);
    $encrypted_contact = encrypt_data($contact_number, $encryption_key);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "aide_binan");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $check = $conn->query("SELECT email, contact_number FROM rider_users");
    while ($row = $check->fetch_assoc()) {
        $decrypted_email = decrypt_data($row['email'], $encryption_key);
        $decrypted_contact = decrypt_data($row['contact_number'], $encryption_key);
        if ($email === $decrypted_email) {
            echo "<p style='color:red;'>Email is already registered. Please use a different email.</p>";
            $conn->close();
            exit;
        }
        if ($contact_number === $decrypted_contact) {
            echo "<p style='color:red;'>Contact number is already registered. Please use a different number.</p>";
            $conn->close();
            exit;
        }
    }


    $stmt = $conn->prepare("INSERT INTO rider_users 
        (first_name, middle_name, last_name, birthday, contact_number, email, password_hash, ebike_type, ebike_model, ebike_color, ebike_control_number, ebike_plate, ebike_purchased) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss",
        $first_name, $middle_name, $last_name, $birthday,
        $encrypted_contact, $encrypted_email, $password_hash,
        $ebike_type, $ebike_model, $ebike_color, $ebike_control_number,
        $ebike_plate, $ebike_purchased
    );

    if ($stmt->execute()) {
        $otp = generateOTP();
        $_SESSION['rider_otp'] = $otp;
        $_SESSION['rider_email'] = $email;

        echo "<script>
            alert('Registration successful! OTP: $otp');
            window.location.href = 'verify_otp_rider.php';
        </script>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
</body>
</html>
