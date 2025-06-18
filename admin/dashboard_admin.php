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

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_email = $_POST['email'];
    $input_password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "aide_binan");
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

    $query = "SELECT email, password_hash FROM admin_users";
    $result = $conn->query($query);
    $found = false;
    while ($row = $result->fetch_assoc()) {
        if ($input_email === decrypt_data($row['email'], $encryption_key)) {
            $found = true;
            if (password_verify($input_password, $row['password_hash'])) {
                $_SESSION['admin_otp'] = generateOTP();
                $_SESSION['admin_email'] = $input_email;
                echo "<script>alert('OTP for testing: {$_SESSION['admin_otp']}'); window.location.href='login_otp_admin.php';</script>";
                exit();
            } else {
                $error = "Incorrect password.";
                break;
            }
        }
    }
    if (!$found) $error = "Email not found.";
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login - AIDE Biñan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #fde0e0, #ffe8e8);
            font-family: Arial, sans-serif;
        }
        .blur-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.5;
            z-index: 0;
        }
        .circle1 {
            width: 400px;
            height: 400px;
            background: #f87171;
            top: -100px;
            left: -100px;
        }
        .circle2 {
            width: 300px;
            height: 300px;
            background: #facc15;
            bottom: -80px;
            right: -80px;
        }
    </style>
</head>
<body>
    <div class="blur-circle circle1"></div>
    <div class="blur-circle circle2"></div>

    <header class="bg-red-900 text-white px-6 py-4 flex justify-between items-center relative z-10">
        <h1 class="text-xl font-bold">A.I.D.E. BIÑAN</h1>
        <nav class="space-x-6">
            <a href="#" class="hover:underline">Home</a>
            <a href="#" class="hover:underline">About</a>
        </nav>
    </header>

    <section class="flex flex-col items-center justify-center min-h-screen relative z-10">
        <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md backdrop-blur-md bg-opacity-90">
            <h2 class="text-2xl font-bold text-center mb-2 text-gray-800">Admin Login</h2>
            <form action="" method="POST" class="space-y-4">
                <input type="email" name="email" placeholder="Email" required class="w-full border px-4 py-2 rounded focus:outline-none">
                <input type="password" name="password" placeholder="Password" required class="w-full border px-4 py-2 rounded focus:outline-none">
                <button type="submit" class="w-full bg-red-700 text-white py-2 rounded hover:bg-red-800">Login</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 border border-red-300 p-2 rounded mt-4 text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
