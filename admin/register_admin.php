<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Registration - AIDE Biñan</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: radial-gradient(circle at top, #ff4d4d, #7f1d1d, #1f1f1f);
            background-size: cover;
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .container {
            max-width: 850px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 0 25px rgba(255, 0, 0, 0.6), 0 0 50px rgba(255, 0, 0, 0.4), 0 0 75px rgba(255, 0, 0, 0.2);
            backdrop-filter: blur(6px);
            animation: glow 4s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from {
                box-shadow: 0 0 25px rgba(255, 0, 0, 0.6), 0 0 50px rgba(255, 0, 0, 0.4), 0 0 75px rgba(255, 0, 0, 0.2);
            }
            to {
                box-shadow: 0 0 35px rgba(255, 100, 100, 0.8), 0 0 60px rgba(255, 100, 100, 0.5), 0 0 90px rgba(255, 100, 100, 0.3);
            }
        }
        .logo {
            text-align: center;
            font-size: 48px;
            color: #b91c1c;
            margin-bottom: 10px;
        }
        h1 {
            text-align: center;
            margin-bottom: 5px;
            color: #b91c1c;
        }
        h2 {
            text-align: center;
            font-weight: normal;
            font-size: 14px;
            color: #444;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .form-group {
            flex: 1 1 45%;
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 6px;
            font-size: 14px;
            color: #000;
        }
        input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #000;
            border-radius: 6px;
            background-color: #fff;
            color: #333;
        }
        .form-group.full-width {
            flex: 1 1 100%;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 14px;
            background-color: #b91c1c;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }
        button:hover {
            background-color: #991b1b;
            transform: scale(1.03);
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">🛵</div>
    <h1>Register for A.I.D.E. BIÑAN</h1>
    <h2>Create your admin account to get started</h2>

    <form action="" method="POST">
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
            <label>Middle Name:</label>
            <input type="text" name="middle_name">
        </div>
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
            <label>Birthday:</label>
            <input type="date" name="birthday" required>
        </div>
        <div class="form-group">
            <label>Contact Number:</label>
            <input type="text" name="contact_number" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <div class="form-group full-width">
            <label>Employee Number:</label>
        <input type="text" name="employee_number" pattern="[0-9]+" inputmode="numeric" title="Please enter numbers only" required>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="submit">Register as Admin</button>
        </div>
    </form>


<?php
mysqli_report(MYSQLI_REPORT_OFF); // Disable exception throwing

function encrypt_data($data, $key) {
    $iv = openssl_random_pseudo_bytes(16); // Generate a 16-byte IV
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);

    if ($encrypted === false) {
        return false; // encryption failed
    }

    // Combine IV + encrypted data and encode for storage
    return base64_encode($iv . $encrypted);
}

function decrypt_data($encrypted_data, $key) {
    $data = base64_decode($encrypted_data);

    // Ensure data is long enough for IV + ciphertext
    if ($data === false || strlen($data) < 17) { // Minimum 17 = 16 IV + 1 byte ciphertext
        return false; // corrupted or invalid
    }

    $iv = substr($data, 0, 16); // First 16 bytes = IV
    $ciphertext = substr($data, 16); // Rest = actual encrypted data

    $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
    return $decrypted !== false ? $decrypted : false;
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

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($birthday)) {
        echo "<div class='error'>Please fill in all required fields.</div>";
        return;
    }

    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/\d/', $password) || 
        !preg_match('/[!@#$%^&*(),.?\":{}|<>]/', $password)) {
        echo "<div class='error'>Password must be at least 8 characters long, include an uppercase letter, a number, and a special character.</div>";
        return;
    }

    if ($password !== $confirm_password) {
        echo "<div class='error'>Passwords do not match.</div>";
        return;
    }

    if (!preg_match('/^09[0-9]{9}$/', $contact_number)) {
        echo "<div class='error'>Contact number must start with 09 and be exactly 11 digits.</div>";
        return;
    }

    $encryption_key = "your-strong-secret-key";
    $encrypted_email = encrypt_data($email, $encryption_key);
    $encrypted_contact = encrypt_data($contact_number, $encryption_key);
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "aide_binan");
    if ($conn->connect_error) {
        die("<div class='error'>Database connection failed.</div>");
    }

    // ✅ Check for duplicate employee number
    $check_emp = $conn->prepare("SELECT employee_number FROM admin_users WHERE employee_number = ?");
    $check_emp->bind_param("s", $employee_number);
    $check_emp->execute();
    $check_emp->store_result();

    $employee_number = strtoupper(trim($_POST['employee_number']));

    if (!preg_match('/^\d+$/', $employee_number)) {
    echo "<div class='error'>Employee number must contain numbers only.</div>";
    return;
    }

    $check_emp->close();

    $check = $conn->query("SELECT email, contact_number FROM admin_users");
    while ($row = $check->fetch_assoc()) {
        $decrypted_email = decrypt_data($row['email'], $encryption_key);
        $decrypted_contact = decrypt_data($row['contact_number'], $encryption_key);

        if ($email === $decrypted_email) {
            echo "<div class='error'>Email already registered.</div>";
            $conn->close(); return;
        }

        if ($contact_number === $decrypted_contact) {
            echo "<div class='error'>Contact number already registered.</div>";
            $conn->close(); return;
        }
    }

    $stmt = $conn->prepare("INSERT INTO admin_users (first_name, middle_name, last_name, birthday, contact_number, employee_number, email, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $birthday, $encrypted_contact, $employee_number, $encrypted_email, $password_hash);

    if ($stmt->execute()) {
        $otp = generateOTP();
        $_SESSION['admin_otp'] = $otp;
        $_SESSION['admin_email'] = $email;

        echo "<script>
            alert('Admin registration successful! OTP for testing: $otp');
            window.location.href = 'verify_otp_admin.php';
        </script>";
    }
    if (str_contains($stmt->error, 'Duplicate entry') && str_contains($stmt->error, 'employee_number')) {
    echo "<div class='error'>Employee number already exists. Please use another one.</div>";
} else {
    echo "<div class='error'>Registration failed: " . htmlspecialchars($stmt->error) . "</div>";
}
    }

    $stmt->close();
    $conn->close();
?>
</div>
</body>
</html>
