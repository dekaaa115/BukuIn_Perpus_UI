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

// Fetch all transaction data with user and book details
$transactions = [];
$sql = "SELECT 
            t.id, 
            b.title as book_title, 
            u.full_name as user_name, 
            t.borrow_date, 
            t.return_date,
            t.due_date
        FROM transactions t
        JOIN users u ON t.user_id = u.id
        JOIN books b ON t.book_id = b.id
        ORDER BY t.borrow_date DESC";

if ($result = mysqli_query($link, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
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
    <title>Data Transaksi - Admin Dashboard</title>
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
                        <a href="data-anggota.php" class="flex items-center p-3 rounded-lg">
                            <i data-lucide="users" class="mr-3"></i>Data Anggota
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="transaksi.php" class="flex items-center p-3 rounded-lg active-nav">
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
                        <h2 class="text-3xl font-bold text-gray-300">Transaksi</h2>
                        <p class="text-lg text-gray-400">Data Transaksi</p>
                    </div>
                </div>
               <div class="bg-[#333333] p-6 rounded-xl shadow-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-300">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="p-4">No</th>
                                    <th class="p-4">Judul Buku</th>
                                    <th class="p-4">Nama Peminjam</th>
                                    <th class="p-4">Tanggal Peminjaman</th>
                                    <th class="p-4">Tanggal Pengembalian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $index => $transaction): ?>
                                <tr class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-4"><?php echo $index + 1; ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars($transaction['user_name']); ?></td>
                                    <td class="p-4"><?php echo htmlspecialchars(date('d F Y', strtotime($transaction['borrow_date']))); ?></td>
                                    <td class="p-4"><?php echo $transaction['return_date'] ? htmlspecialchars(date('d F Y', strtotime($transaction['return_date']))) : 'Not Returned'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center p-4 text-gray-400">No transactions found.</td>
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
