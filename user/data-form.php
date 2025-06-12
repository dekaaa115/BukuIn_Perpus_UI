<?php
// Initialize the session
session_start();
require_once "../php/config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Get user data to pre-fill the form
$user_id = $_SESSION["id"];
$user_data = [];
$sql = "SELECT full_name, kelas, phone_number, address FROM users WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman - Buku in</title>
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
                    <?php
                        // Prepare variables for the image tag to avoid syntax errors and warnings
                        $img_url = isset($_SESSION['profile_image_url']) && !empty($_SESSION['profile_image_url']) ? htmlspecialchars($_SESSION['profile_image_url']) : 'assets/images/default_avatar.png';
                        $fallback_text_initial = substr(htmlspecialchars($_SESSION['full_name']), 0, 1);
                        $fallback_url = "https://placehold.co/100x100/A78BFA/FFFFFF?text=" . $fallback_text_initial;
                    ?>
                    <img src="../<?php echo $img_url; ?>" onerror="this.onerror=null; this.src='<?php echo $fallback_url; ?>'" alt="User Profile" class="rounded-full w-24 h-24 mb-4 border-2 border-purple-400 object-cover">
                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h3>
                    <p class="text-sm bg-blue-500 px-3 py-1 rounded-full mt-2">Pengguna</p>
                </div>
                <ul>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="daftar-buku.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="book-open" class="mr-3"></i>Daftar Buku
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="data-form.php" class="flex items-center p-3 rounded-lg active-nav">
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
                    <span class="mr-4"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="profil-user.php">
                        <?php
                            // Prepare variables for the header image tag
                            $header_fallback_url = "https://placehold.co/40x40/FFFFFF/333333?text=" . $fallback_text_initial;
                        ?>
                        <img src="../<?php echo $img_url; ?>" onerror="this.onerror=null; this.src='<?php echo $header_fallback_url; ?>'" alt="User Avatar" class="rounded-full w-10 h-10 cursor-pointer object-cover">
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-8 overflow-y-auto">
                <div>
                    <h2 class="text-3xl font-bold text-gray-300">Data Form</h2>
                    <p class="text-lg text-gray-400 bg-[#4A4A4A] inline-block px-4 py-1 rounded-full mt-2">Peminjaman</p>
                </div>
                <div class="bg-[#333333] p-8 rounded-xl shadow-lg mt-6">
                    <form action="../php/borrow_process.php" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            <div><label class="text-gray-400">ID Anggota :</label><input type="text" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                            <div><label class="text-gray-400">Nama :</label><input type="text" value="<?php echo htmlspecialchars($user_data['full_name']); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                            <div><label class="text-gray-400">Kelas :</label><input type="text" value="<?php echo htmlspecialchars($user_data['kelas']); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                             <div><label class="text-gray-400">Judul Buku :</label><input type="text" name="book_title" placeholder="Enter exact book title" class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-purple-400 text-white p-2" required></div>
                             <div><label class="text-gray-400">No Telepon :</label><input type="text" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                             <div><label class="text-gray-400">Tanggal Pinjam :</label><input type="date" name="borrow_date" value="<?php echo date('Y-m-d'); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                             <div><label class="text-gray-400">Tanggal Pengembalian (Due Date):</label><input type="date" name="due_date" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" class="w-full bg-transparent border-b border-gray-600 focus:outline-none focus:border-purple-400 text-white p-2"></div>
                        </div>
                        <div class="grid grid-cols-1 mb-6">
                             <div><label class="text-gray-400">Alamat :</label><input type="text" value="<?php echo htmlspecialchars($user_data['address']); ?>" class="w-full bg-transparent border-b border-gray-600 text-white p-2" readonly></div>
                        </div>
                        <div class="text-left">
                            <button type="submit" class="bg-gray-400 hover:bg-gray-500 text-black font-bold py-2 px-8 rounded-lg">Submit</button>
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
