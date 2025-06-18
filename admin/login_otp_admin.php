<?php
session_start();
if (!isset($_SESSION['admin_otp']) || !isset($_SESSION['admin_email'])) {
    header("Location: login_rider.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_otp = $_POST['otp'];

    if ($input_otp === $_SESSION['admin_otp']) {
        unset($_SESSION['admin_otp']);
        header("Location: dashboard_admin.php");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin OTP Verification - AIDE Biñan</title>
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
            <h2 class="text-2xl font-bold text-center mb-2 text-gray-800">Admin OTP Verification</h2>
            <p class="text-center text-gray-500 mb-6">Enter the OTP sent to your email</p>

            <form method="POST" class="space-y-4">
                <input type="text" name="otp" maxlength="6" placeholder="One-Time Password" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                <button type="submit" class="w-full bg-red-700 text-white py-2 rounded hover:bg-red-800">Verify OTP</button>
            </form>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 border border-red-300 p-2 rounded mt-4 text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <script>
                alert("OTP for testing: <?= $_SESSION['admin_otp'] ?? '' ?>");
            </script>
        </div>
    </section>
</body>
</html>
