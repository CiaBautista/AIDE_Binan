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
    </style>
</head>

<body>
    <div class="container">
        <h1>Select Registration Type</h1>

        <form method="POST">
            <button type="submit" name="role" value="admin">Admin</button>
            <button type="submit" name="role" value="rider">Rider</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $role = $_POST['role'];
            if ($role === 'admin') {
                header("Location: ../admin/register_admin.php");
            } elseif ($role === 'rider') {
                header("Location: ../rider/register_rider.php");
            }
            exit();
        }
        ?>
    </div>
</body>
</html>
