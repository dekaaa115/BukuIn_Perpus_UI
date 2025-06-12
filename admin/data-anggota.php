<?php
// Initialize the session
session_start();

// Include database configuration
require_once "../php/config.php";

// Check if the user is logged in and is an admin, otherwise redirect
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Fetch all users (non-admins) from the database
$users = [];
$sql = "SELECT id, full_name, kelas, address, phone_number FROM users WHERE role = 'user' ORDER BY id ASC";
if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_free_result($result);
    }
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Admin Dashboard</title>
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
                    <img src="https://placehold.co/100x100/A78BFA/FFFFFF?text=A" alt="Admin Profile" class="rounded-full w-24 h-24 mb-4 border-2 border-purple-400">
                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h3>
                    <p class="text-sm bg-green-500 px-3 py-1 rounded-full mt-2">Administrator</p>
                </div>
                <ul>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="data-buku.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="book-copy" class="mr-3"></i>Data Buku
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="data-anggota.php" class="flex items-center p-3 rounded-lg active-nav">
                            <i data-lucide="users" class="mr-3"></i>Data Anggota
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="transaksi.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="arrow-right-left" class="mr-3"></i>Transaksi
                        </a>
                    </li>
                     <li class="nav-item rounded-lg mb-2">
                        <a href="laporan.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="clipboard-list" class="mr-3"></i>Laporan
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
                    <span class="mr-4">Admin</span>
                    <a href="profil-admin.php">
                        <img src="https://placehold.co/40x40/FFFFFF/333333?text=A" alt="User Avatar" class="rounded-full w-10 h-10 cursor-pointer">
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-8 overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-300">Anggota</h2>
                        <p class="text-lg text-gray-400">Data Anggota</p>
                    </div>
                </div>
                <div class="bg-[#333333] p-6 rounded-xl shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-300">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="p-4">No</th>
                                    <th class="p-4">Nama Anggota</th>
                                    <th class="p-4">Kelas</th>
                                    <th class="p-4">Alamat</th>
                                    <th class="p-4">No Telepon</th>
                                    <th class="p-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $index => $user): ?>
                                     <tr class="border-b border-gray-700 hover:bg-gray-700">
                                        <td class="p-4"><?php echo $index + 1; ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($user['kelas']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($user['address']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($user['phone_number']); ?></td>
                                        <td class="p-4"><i data-lucide="edit" class="cursor-pointer text-gray-400 hover:text-white"></i></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($users)): ?>
                                     <tr class="border-b border-gray-700">
                                        <td colspan="6" class="p-4 text-center text-gray-400">No members found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
               </div>
            </div>
        </main>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
