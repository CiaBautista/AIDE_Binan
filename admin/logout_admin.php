<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="2;url=../system/index.php" /> 
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Logging Out - AIDE Biñan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(180deg, #fef2f2, #f8fafc);
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

<header class="bg-gradient-to-r from-red-700 to-red-900 text-white px-6 py-4 flex justify-center items-center shadow-md">
    <h1 class="text-xl font-bold tracking-wide">?? A.I.D.E. BIÑAN</h1>
</header>

<main class="flex items-center justify-center py-20 px-4">
    <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-10 w-full max-w-md text-center border border-red-100">
        <h2 class="text-2xl font-extrabold text-red-800 mb-4">Logging you out...</h2>
        <p class="text-gray-600 mb-6">Thank you for using <span class="font-semibold">A.I.D.E. BIÑAN</span>.</p>
        <div class="animate-spin inline-block w-12 h-12 border-[5px] border-red-700 border-t-transparent rounded-full"></div>
    </div>
</main>

</body>
</html>
