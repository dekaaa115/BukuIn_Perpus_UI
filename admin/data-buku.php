<?php
// Initialize the session
session_start();
require_once "../php/config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// --- Get filter and search parameters from the URL ---
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$stock_filter = isset($_GET['filter']) ? trim($_GET['filter']) : '';


// --- Build the main SQL query for fetching books dynamically ---
$books = [];
$sql = "SELECT id, title, stock_available, stock_needed FROM books";
$where_clauses = [];
$params = [];
$types = "";

// Add search condition
if (!empty($search_query)) {
    $where_clauses[] = "title LIKE ?";
    $search_param = "%" . $search_query . "%";
    $params[] = &$search_param;
    $types .= "s";
}

// Add stock filter condition
if (!empty($stock_filter)) {
    switch ($stock_filter) {
        case 'in_stock':
            $where_clauses[] = "stock_available > 0";
            break;
        case 'out_of_stock':
            $where_clauses[] = "stock_available = 0";
            break;
        case 'needs_restock':
            $where_clauses[] = "stock_available < stock_needed";
            break;
    }
}

if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY id ASC";

// Prepare and execute the statement
if ($stmt = mysqli_prepare($link, $sql)) {
    if (!empty($types)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
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
    <title>Data Buku - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dropdown:hover .dropdown-menu { display: block; }
    </style>
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
                        <a href="data-buku.php" class="flex items-center p-3 rounded-lg active-nav">
                            <i data-lucide="book-copy" class="mr-3"></i>Data Buku
                        </a>
                    </li>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="data-anggota.php" class="flex items-center p-3 rounded-lg">
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
                        <h2 class="text-3xl font-bold text-gray-300">Buku</h2>
                        <p class="text-lg text-gray-400">Data Buku</p>
                    </div>
                </div>
               <div class="bg-[#333333] p-6 rounded-xl shadow-lg">
                    <form method="GET" action="data-buku.php" class="flex justify-between items-center mb-4 flex-wrap gap-4">
                         <div class="flex gap-2">
                            <a href="add-book.php" class="bg-gray-200 text-black font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Add New Book</a>
                            <!-- Stock Filter Dropdown -->
                            <div class="dropdown inline-block relative">
                                <button type="button" class="bg-gray-700 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600 flex items-center">
                                    Filter by Stock <i data-lucide="chevron-down" class="ml-2 w-4 h-4"></i>
                                </button>
                                <ul class="dropdown-menu absolute hidden text-gray-300 pt-1 w-48 bg-gray-700 rounded-lg shadow-lg z-10">
                                    <li><a class="rounded-t hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="data-buku.php">All Books</a></li>
                                    <li><a class="hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="data-buku.php?filter=in_stock">In Stock</a></li>
                                    <li><a class="hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="data-buku.php?filter=out_of_stock">Out of Stock</a></li>
                                    <li><a class="rounded-b hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="data-buku.php?filter=needs_restock">Needs Restock</a></li>
                                </ul>
                            </div>
                         </div>
                         <div class="relative">
                             <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search for book titles..." class="bg-[#4F4F4F] text-white rounded-lg py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-400">
                             <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i data-lucide="search" class="w-5 h-5"></i>
                             </button>
                         </div>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-300">
                            <thead>
                                <tr class="border-b border-gray-600">
                                    <th class="p-4">No</th>
                                    <th class="p-4">Nama Buku</th>
                                    <th class="p-4">Stok Tersedia</th>
                                    <th class="p-4">Stok Kebutuhan</th>
                                    <th class="p-4">Kekurangan</th>
                                    <th class="p-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $index => $book): ?>
                                    <tr class="border-b border-gray-700 hover:bg-gray-700">
                                        <td class="p-4"><?php echo $index + 1; ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($book['title']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($book['stock_available']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($book['stock_needed']); ?></td>
                                        <td class="p-4"><?php echo htmlspecialchars($book['stock_needed'] - $book['stock_available']); ?></td>
                                        <td class="p-4 flex items-center gap-4">
                                            <a href="edit-book.php?id=<?php echo $book['id']; ?>">
                                                <i data-lucide="edit" class="cursor-pointer text-gray-400 hover:text-white"></i>
                                            </a>
                                            <a href="../php/delete_book_process.php?id=<?php echo $book['id']; ?>" onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.');">
                                                <i data-lucide="trash-2" class="cursor-pointer text-red-500 hover:text-red-400"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($books)): ?>
                                    <tr class="border-b border-gray-700">
                                        <td colspan="6" class="p-4 text-center text-gray-400">No books found matching your criteria.</td>
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
