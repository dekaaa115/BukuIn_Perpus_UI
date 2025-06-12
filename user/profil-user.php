<?php
// Initialize the session
session_start();

// Include database configuration
require_once "../php/config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Prepare a select statement to get user data
$user = []; // Initialize an empty array for user data
$sql = "SELECT full_name, nickname, email, kelas, phone_number, address, gender, profile_image_url FROM users WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    
    // Set parameters
    $param_id = $_SESSION["id"];
    
    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Get result
        $result = mysqli_stmt_get_result($stmt);
        
        // Fetch the data into an associative array
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
        } else {
            echo "Error: User data not found.";
            exit;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profil - Buku in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-[#212121]">

    <div class="flex h-screen bg-[#212121] text-white">
        <!-- Sidebar -->
        <nav class="w-64 bg-[#333333] p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-col items-center mb-10">
                    <img src="../<?php echo htmlspecialchars($user['profile_image_url']); ?>" onerror="this.onerror=null; this.src='https://placehold.co/100x100/A78BFA/FFFFFF?text=<?php echo substr(htmlspecialchars($user['full_name']), 0, 1); ?>'" alt="User Profile" class="rounded-full w-24 h-24 mb-4 border-2 border-purple-400 object-cover">
                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-sm bg-blue-500 px-3 py-1 rounded-full mt-2">Pengguna</p>
                </div>
                <ul>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="daftar-buku.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="book-open" class="mr-3"></i>Daftar Buku
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="data-form.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="file-pen-line" class="mr-3"></i>Data Form
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="transaksi.php" class="flex items-center p-3 rounded-lg">
                           <i data-lucide="qr-code" class="mr-3"></i>Transaksi
                        </a>
                    </li>
                     <li class="nav-item rounded-lg mb-2">
                        <a href="history.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="history" class="mr-3"></i>History
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                 <a href="../php/logout.php" class="flex items-center p-3 rounded-lg nav-item">
                    <i data-lucide="log-out" class="mr-3"></i>Logout
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-[#A78BFA] text-black p-4 flex justify-between items-center shadow-md">
                <div class="flex items-center">
                    <i data-lucide="library" class="mr-3"></i>
                    <h1 class="text-xl font-semibold">Buku in - Sistem Informasi Perpustakaan</h1>
                </div>
                <div class="flex items-center">
                    <span class="mr-4"><?php echo htmlspecialchars($user['full_name']); ?></span>
                    <a href="profil-user.php">
                        <img src="../<?php echo htmlspecialchars($user['profile_image_url']); ?>" onerror="this.onerror=null; this.src='https://placehold.co/40x40/FFFFFF/333333?text=<?php echo substr(htmlspecialchars($user['full_name']), 0, 1); ?>'" alt="User Avatar" class="rounded-full w-10 h-10 cursor-pointer object-cover">
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-8 overflow-y-auto">
                 <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-300">My Profil</h2>
                    </div>
                    <button class="bg-gray-200 text-black font-bold py-2 px-4 rounded-lg hover:bg-gray-300">My Data</button>
                </div>
               <div class="bg-[#333333] p-8 rounded-xl shadow-lg">
                   <div class="flex items-center justify-between mb-8">
                       <div class="flex items-center">
                           <img src="../<?php echo htmlspecialchars($user['profile_image_url']); ?>" onerror="this.onerror=null; this.src='https://placehold.co/100x100/A78BFA/FFFFFF?text=<?php echo substr(htmlspecialchars($user['full_name']), 0, 1); ?>'" alt="User Profile" class="rounded-full w-24 h-24 mr-6 border-2 border-purple-400 object-cover">
                           <div>
                                <h3 class="text-2xl font-bold"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                <p class="text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                           </div>
                       </div>
                       <button class="bg-[#A78BFA] hover:bg-purple-600 text-white font-bold py-2 px-6 rounded-lg">Edit</button>
                   </div>
                   
                   <!-- NEW: Profile Picture Upload Form -->
                   <div class="mt-8 border-t border-gray-600 pt-6">
                        <h4 class="text-lg font-semibold text-white mb-4">Change Profile Picture</h4>
                        <form action="../php/upload_picture_process.php" method="post" enctype="multipart/form-data">
                            <div class="flex items-center gap-4">
                                <input class="w-full text-gray-300 text-sm bg-[#4F4F4F] rounded-lg cursor-pointer focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-200 file:text-purple-700 hover:file:bg-purple-300" name="profile_picture" type="file" required>
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg">Upload</button>
                            </div>
                        </form>
                   </div>


                   <form class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 border-t border-gray-600 pt-6">
                       <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Full Name</label>
                           <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" type="text" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                       </div>
                       <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Nick Name</label>
                           <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" type="text" value="<?php echo htmlspecialchars($user['nickname']); ?>">
                       </div>
                        <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Gender</label>
                           <select class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4 appearance-none">
                               <option <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                               <option <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                           </select>
                       </div>
                         <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Country</label>
                           <select class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4 appearance-none">
                               <option>Indonesia</option>
                           </select>
                       </div>
                       <div>
                            <p class="text-lg font-semibold mb-2">My email Address</p>
                            <div class="flex items-center">
                                 <i data-lucide="mail" class="text-purple-400 mr-3"></i>
                                <div>
                                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-sm text-gray-500">1 month ago</p>
                                </div>
                            </div>
                            <button class="text-purple-400 mt-2 font-semibold">+ Add Email Address</button>
                       </div>
                       <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Address</label>
                           <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" type="text" value="<?php echo htmlspecialchars($user['address']); ?>">
                       </div>
                       <div>
                           <label class="block text-gray-400 text-sm font-bold mb-2">Member ID</label>
                           <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" type="text" value="<?php echo $_SESSION['id']; ?>" readonly>
                       </div>
                   </form>
               </div>
            </div>
        </main>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
