<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Select Registration Type</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #e60000, #cc0000); 
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: #ffe5e5; 
            padding: 50px 60px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        h1 {
            color: #222;
            margin-bottom: 30px;
            font-size: 28px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }

        form {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        button {
            padding: 12px 30px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            background-color: #b30000;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #990000;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>Select Registration Type</h1>

    <form method="POST">
        <button type="submit" name="role" value="rider">Rider</button>
        <button type="submit" name="role" value="admin">Admin</button>
    </form>
    </div>

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
</body>
</html>
