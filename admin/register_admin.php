<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration - AIDE Biñan</title>
</head>
<body>
<h1>Admin Registration Form</h1>
<form action="" method="POST">
    <label>First Name:</label><br><input type="text" name="first_name" required><br>
    <label>Middle Name:</label><br><input type="text" name="middle_name"><br>
    <label>Last Name:</label><br><input type="text" name="last_name" required><br>
    <label>Birthday:</label><br><input type="date" name="birthday" required><br>
    <label>Contact Number:</label><br><input type="text" name="contact_number" required><br>
    <label>Employee Number:</label><br><input type="text" name="employee_number" required><br>
    <label>Email:</label><br><input type="email" name="email" required><br>
    <label>Password:</label><br><input type="password" name="password" required><br>
    <label>Confirm Password:</label><br><input type="password" name="confirm_password" required><br><br>
    <button type="submit" name="submit">Register</button>
</form>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function encrypt_data($data, $key) {
    $iv = random_bytes(16);
    $ciphertext = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $ciphertext);
}

function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = ucwords(strtolower(trim($_POST['first_name'])));
    $middle_name = ucwords(strtolower(trim($_POST['middle_name'])));
    $last_name = ucwords(strtolower(trim($_POST['last_name'])));
    $birthday = $_POST['birthday'];
    $contact_number = trim($_POST['contact_number']);
    $employee_number = strtoupper(trim($_POST['employee_number']));
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

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

    $check = $conn->query("SELECT email, contact_number FROM admin_users");
    while ($row = $check->fetch_assoc()) {
        if ($email === decrypt_data($row['email'], $encryption_key)) {
            echo "<p style='color:red;'>Email already registered.</p>";
            $conn->close(); exit;
        }
        if ($contact_number === decrypt_data($row['contact_number'], $encryption_key)) {
            echo "<p style='color:red;'>Contact number already registered.</p>";
            $conn->close(); exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO admin_users (first_name, middle_name, last_name, birthday, contact_number, employee_number, email, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $birthday, $encrypted_contact, $employee_number, $encrypted_email, $password_hash);

    if ($stmt->execute()) {
        $otp = generateOTP();
        $_SESSION['admin_otp'] = $otp;
        $_SESSION['admin_email'] = $email;

        echo "<script>alert('OTP for testing: $otp'); window.location.href='verify_otp_admin.php';</script>";
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close(); $conn->close();
}
?>
</body>
</html>
