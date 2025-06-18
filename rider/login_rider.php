<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


function generateOTP($length = 6) {
    return str_pad(random_int(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}


function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

$encryption_key = "your-strong-secret-key";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_email = $_POST['email'];
    $input_password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "aide_binan");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT email, password_hash FROM rider_users";
    $result = $conn->query($query);

    $found = false;
    while ($row = $result->fetch_assoc()) {
        $decrypted_email = decrypt_data($row['email'], $encryption_key);

        if ($input_email === $decrypted_email) {
            $found = true;
            if (password_verify($input_password, $row['password_hash'])) {
                // Store OTP and email
                $otp = generateOTP();
                $_SESSION['rider_email'] = $input_email;
                $_SESSION['rider_otp'] = $otp;

                echo "<script>alert('OTP for testing: $otp'); window.location.href = 'login_otp_rider.php';</script>";
                exit();
            } else {
                echo "<p style='color:red;'>Incorrect password.</p>";
                break;
            }
        }
    }

    if (!$found) {
        echo "<p style='color:red;'>Email not found.</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head><title>Rider Login</title></head>
<body>
<h1>Login as Rider</h1>
<form action="" method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
