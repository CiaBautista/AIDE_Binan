<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = $_POST['otp'] ?? '';
    $correctOtp = $_SESSION['admin_otp'] ?? '';

    if ($enteredOtp === $correctOtp) {
        unset($_SESSION['admin_otp']);
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "<p style='color:red;'>Invalid OTP.</p>";
    }
}
?>
<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin OTP Verification - AIDE Biñan</title>
    <style>
        body {
            background-color: #fef2f2; /* light red background */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #000;
        }
        .otp-container {
            background: #fff; /* white form background */
            border-radius: 12px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.6), 0 0 30px rgba(220, 38, 38, 0.4); /* glowing border */
            border: 2px solid rgba(220, 38, 38, 0.7);
            transition: box-shadow 0.3s ease, transform 0.2s ease;
        }
        .otp-container:hover {
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.8), 0 0 40px rgba(220, 38, 38, 0.5);
            transform: scale(1.02);
        }
        .otp-container h1 {
            font-size: 32px;
            color: #b91c1c;
            margin-bottom: 8px;
            text-align: center;
        }
        .otp-container p.subtext {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
            text-align: center;
        }
        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 8px;
            color: #000;
            width: 100%;
            text-align: center;
        }
        input[type="text"], button {
            width: 220px; 
            padding: 10px; 
            font-size: 13px; 
            border-radius: 6px;
            border: 1px solid #000;
            margin-bottom: 15px;
            text-align: center;
            box-sizing: border-box;
        }
        input[type="text"] {
            color: #000;
            height: 36px;
        }
        button {
            background: linear-gradient(to right, #b91c1c, #dc2626);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            border: none;
            height: 36px; 
        }
        button:hover {
            background: linear-gradient(to right, #7f1d1d, #991b1b);
            transform: scale(1.05);
        }
        .message {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        a {
            display: inline-block;
            margin-top: 12px;
            color: #b91c1c;
            text-decoration: underline;
            font-size: 14px;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="otp-container">
    <h1>AIDE Biñan</h1>
    <p class="subtext">Admin OTP Verification</p>

    <form action="" method="POST">
        <label for="otp">Enter the OTP sent to your contact number:</label>
        <input type="text" name="entered_otp" id="otp" placeholder="Enter OTP" required>

        <button type="submit" name="verify">Verify</button>
    </form>