<?php
// Initialize the session
session_start();
require_once "../php/config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Fetch transaction history for the logged-in user
$history = [];
$user_id = $_SESSION["id"];

$sql = "SELECT b.title, b.author, t.borrow_date, t.due_date, t.return_date, t.fine_amount, t.status FROM transactions t JOIN books b ON t.book_id = b.id WHERE t.user_id = ? ORDER BY t.borrow_date DESC";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $history[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - Buku in</title>
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
                        <a href="history.php" class="flex items-center p-3 rounded-lg active-nav">
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
                            $header_fallback_url = "https://placehold.co/40x40/FFFFFF/333333?text=" . $fallback_text_initial;
                        ?>
                        <img src="../<?php echo $img_url; ?>" onerror="this.onerror=null; this.src='<?php echo $header_fallback_url; ?>'" alt="User Avatar" class="rounded-full w-10 h-10 cursor-pointer object-cover">
                    </a>
                </div>
            </header>

            <!-- Content Area -->
            <div class="flex-1 p-8 overflow-y-auto">
                <div>
                    <h2 class="text-3xl font-bold text-gray-300">History</h2>
                    <p class="text-lg text-gray-400 bg-[#4A4A4A] inline-block px-4 py-1 rounded-full mt-2">History Peminjaman</p>
                </div>
                <div class="bg-[#333333] p-6 rounded-xl shadow-lg mt-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-300 text-sm">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="p-3">No</th>
                                    <th class="p-3">Judul Buku</th>
                                    <th class="p-3">Penulis</th>
                                    <th class="p-3">Tanggal Pinjam</th>
                                    <th class="p-3">Tenggat Kembali</th>
                                    <th class="p-3">Tanggal Kembali</th>
                                    <th class="p-3">Denda</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $index => $item): ?>
                                <tr class="border-b border-gray-700 hover:bg-gray-700">
                                    <td class="p-3"><?php echo $index + 1; ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($item['author']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars(date('d/m/Y', strtotime($item['borrow_date']))); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars(date('d/m/Y', strtotime($item['due_date']))); ?></td>
                                    <td class="p-3"><?php echo $item['return_date'] ? htmlspecialchars(date('d/m/Y', strtotime($item['return_date']))) : '-'; ?></td>
                                    <td class="p-3"><?php echo 'Rp ' . number_format($item['fine_amount']); ?></td>
                                    <td class="p-3">
                                        <?php 
                                            $status = htmlspecialchars($item['status']);
                                            $statusClass = '';
                                            if ($status == 'Overdue') { $statusClass = 'bg-red-500 text-white'; } 
                                            elseif ($status == 'Returned') { $statusClass = 'bg-green-500 text-white'; } 
                                            else { $statusClass = 'bg-yellow-500 text-black'; }
                                        ?>
                                        <span class="<?php echo $statusClass; ?> text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full"><?php echo $status; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($history)): ?>
                                    <tr><td colspan="8" class="text-center p-4 text-gray-400">Your borrowing history is empty.</td></tr>
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
