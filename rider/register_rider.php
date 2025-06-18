<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AIDE Biñan - Rider Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at center, #b91c1c, #dc2626, #f87171, #ffcccc);
            background-size: 300% 300%;
            animation: moveBG 15s ease infinite;
        }

        @keyframes moveBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-box {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            box-shadow: 
                0 0 20px rgba(255, 0, 0, 0.4),
                0 0 40px rgba(255, 0, 0, 0.3),
                0 0 60px rgba(255, 0, 0, 0.2);
            border-radius: 20px;
            padding: 2rem;
            transition: box-shadow 0.4s ease;
        }

        .glass-box:hover {
            box-shadow: 
                0 0 30px rgba(255, 50, 50, 0.6),
                0 0 50px rgba(255, 50, 50, 0.5),
                0 0 80px rgba(255, 50, 50, 0.4);
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #991b1b;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
        }

        .form-input {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            width: 100%;
        }
    </style>
</head>
<body>


<header class="bg-red-900 text-white px-6 py-4 flex justify-between items-center shadow-lg">
    <h1 class="text-xl font-bold tracking-wide">🛵 A.I.D.E. BIÑAN</h1>
    <nav class="space-x-6">
        <a href="#" class="hover:underline">Home</a>
        <a href="#" class="hover:underline">About</a>
    </nav>
</header>

<main class="flex-grow flex items-center justify-center px-4 py-12">
    <div class="glass-box w-full max-w-4xl">
        
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-1">Register for A.I.D.E. BIÑAN</h2>
            <p class="text-sm text-gray-500">Create your rider account to get started</p>
        </div>

       
        <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
           
            <div>
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" required class="form-input">
            </div>
            <div>
                <label class="form-label">Middle Name</label>
                <input type="text" name="middle_name" class="form-input">
            </div>
            <div>
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" required class="form-input">
            </div>
            <div>
                <label class="form-label">Birthday</label>
                <input type="date" name="birthday" required class="form-input">
            </div>
            <div>
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" required class="form-input">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" required class="form-input">
            </div>
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-input">
            </div>
            <div>
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" required class="form-input">
            </div>

            <div class="col-span-2 section-title">E-Bike Information</div>

            <div>
                <label class="form-label">Type of E-bike</label>
                <input type="text" name="ebike_type" value="2-wheels" readonly class="form-input">
            </div>
            <div>
                <label class="form-label">E-bike Model</label>
                <input type="text" name="ebike_model" required class="form-input">
            </div>
            <div>
                <label class="form-label">E-bike Color</label>
                <input type="text" name="ebike_color" required class="form-input">
            </div>
            <div>
                <label class="form-label">Control Number</label>
                <input type="text" name="ebike_control_number" required class="form-input">
            </div>
            <div>
                <label class="form-label">Purchase Date</label>
                <input type="date" name="ebike_purchased" required class="form-input">
            </div>

           
            <div class="col-span-2 mt-4">
                <button type="submit" name="submit" class="w-full bg-red-700 text-white font-semibold py-3 rounded-lg hover:bg-red-800 transition duration-300">
                    🚴‍♂️ Register as Rider
                </button>
            </div>

            <div class="col-span-2 text-center mt-4 text-sm text-white">
                Already have an account? 
                <a href="#" class="text-red-200 hover:underline">Login</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>
