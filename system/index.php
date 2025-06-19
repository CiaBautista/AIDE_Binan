<!DOCTYPE html>

<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #b91c1c, #dc2626, #f87171, #ffcccc);
            background-size: 300% 300%;
            animation: moveBG 15s ease infinite;
        }

        @keyframes moveBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            padding: 50px;
            border-radius: 16px;
            box-shadow: 
                0 0 20px rgba(255, 0, 0, 0.5),
                0 0 40px rgba(255, 0, 0, 0.4),
                0 0 60px rgba(255, 0, 0, 0.3);
            transition: box-shadow 0.4s ease;
        }

        .container:hover {
            box-shadow: 
                0 0 30px rgba(255, 50, 50, 0.7),
                0 0 50px rgba(255, 50, 50, 0.5),
                0 0 80px rgba(255, 50, 50, 0.4);
        }

        h1 {
            color: #1f1f1f;
            margin-bottom: 30px;
            font-size: 28px;
            letter-spacing: 1px;
            text-shadow: 1px 1px 3px rgba(255, 0, 0, 0.4);
        }

        form {
            margin-top: 20px;
        }

        button {
            padding: 14px 28px;
            margin: 12px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            background-color: #b91c1c;
            color: white;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #991b1b;
            transform: scale(1.08);
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.7), 0 0 25px rgba(255, 0, 0, 0.5);
        }

        .register-link {
            display: block;
            margin-top: 20px;
            font-size: 15px;
            color: #000;
            text-decoration: none;
            transition: color 0.3s ease, text-decoration 0.3s ease;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .register-link b {
            color: #000;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome to A.I.D.E. BIÑAN</h1>

        <form action="" method="POST">
            <button type="submit" name="action" value="login_rider">Rider</button>
            <button type="submit" name="action" value="login_admin">Admin</button>
        </form>

        <a href="register.php" class="register-link">
            <span>Don’t have an account? </span><b>Register</b>
        </a>
    </div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($_POST['action']) {
        case "register": header("Location: register.php"); break;
        case "login_rider": header("Location: ../rider/login_rider.php"); break;
        case "login_admin": header("Location: ../admin/login_admin.php"); break;
    }
    exit();
}
?>
</body>
</html>
