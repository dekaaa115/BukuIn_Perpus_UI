<?php
// Start the session to access session variables
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Buku in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-[#212121]">

    <div class="flex h-screen">
        <div class="w-full lg:w-1/2 bg-[#333333] flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <h1 class="text-white text-4xl font-bold mb-2">Buku in</h1>
                <h2 class="text-white text-3xl font-bold mt-8">Login</h2>
                <p class="text-gray-400 mt-2 mb-8">Welcome back! Enter your email/username and password below to sign in.</p>
                
                <?php 
                // Check if there is a login error in the session
                if(isset($_SESSION['login_error'])){
                    // Display the error message
                    echo '<div class="bg-red-500 text-white text-center p-3 rounded-lg mb-4">' . $_SESSION['login_error'] . '</div>';
                    // Unset the error message so it doesn't show again on refresh
                    unset($_SESSION['login_error']);
                }
                ?>

                <form action="php/login_process.php" method="post">
                    <div class="mb-4">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="email">Email</label>
                        <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-purple-400" id="email" type="email" name="email" placeholder="Enter your email/username" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-300 text-sm font-bold mb-2" for="password">Password</label>
                        <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4 mb-3 focus:outline-none focus:ring-2 focus:ring-purple-400" id="password" type="password" name="password" placeholder="Enter your password" required>
                    </div>
                        <button class="w-full bg-[#A78BFA] hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline" type="submit">
                        Sign In
                    </button>
                    <p class="text-center text-gray-400 text-sm mt-6">
                        Don't have an account? <a href="register.html" class="font-bold text-purple-400 hover:text-purple-300">Sign Up</a>
                    </p>
                </form>
            </div>
        </div>
        <div class="hidden lg:flex w-1/2 bg-white items-center justify-center">
             <img src="assets/images/background.png" alt="Library Image" class="h-full w-full object-cover">
        </div>
    </div>

</body>
</html>
