<?php
// Initialize the session
session_start();
require_once "../php/config.php";

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Get filter and search parameters from the URL
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre_filter = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$sort_order = isset($_GET['sort']) && $_GET['sort'] == 'latest' ? 'created_at DESC' : 'title ASC';

// Fetch all unique genres for the filter dropdown
$genres = [];
$genre_sql = "SELECT DISTINCT genre FROM books WHERE genre IS NOT NULL AND genre != '' ORDER BY genre ASC";
if ($genre_result = mysqli_query($link, $genre_sql)) {
    while ($row = mysqli_fetch_assoc($genre_result)) {
        $genres[] = $row['genre'];
    }
}

// Build the main SQL query for fetching books dynamically
$books = [];
$sql = "SELECT id, title, author, genre, cover_image_url, rating, created_at FROM books";
$where_clauses = [];
$params = [];
$types = "";

if (!empty($search_query)) {
    $where_clauses[] = "(title LIKE ? OR genre LIKE ?)";
    $search_param = "%" . $search_query . "%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= "ss";
}
if (!empty($genre_filter)) {
    $where_clauses[] = "genre = ?";
    $params[] = &$genre_filter;
    $types .= "s";
}
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY " . $sort_order;

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Buku in</title>
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
        <nav class="w-64 bg-[#333333] p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-col items-center mb-10">
                    <img src="../<?php echo htmlspecialchars($_SESSION['profile_image_url']); ?>" onerror="this.onerror=null; this.src='https://placehold.co/100x100/A78BFA/FFFFFF?text=<?php echo substr(htmlspecialchars($_SESSION['full_name']), 0, 1); ?>'" alt="User Profile" class="rounded-full w-24 h-24 mb-4 border-2 border-purple-400 object-cover">
                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($_SESSION['full_name']); ?></h3>
                    <p class="text-sm bg-blue-500 px-3 py-1 rounded-full mt-2">Pengguna</p>
                </div>
                <ul>
                    <li class="nav-item rounded-lg mb-2">
                        <a href="daftar-buku.php" class="flex items-center p-3 rounded-lg active-nav">
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

        <main class="flex-1 flex flex-col">
            <header class="bg-[#A78BFA] text-black p-4 flex justify-between items-center shadow-md">
                <div class="flex items-center">
                    <i data-lucide="library" class="mr-3"></i>
                    <h1 class="text-xl font-semibold">Buku in - Sistem Informasi Perpustakaan</h1>
                </div>
                <div class="flex items-center">
                    <span class="mr-4"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="profil-user.php">
                        <img src="../<?php echo htmlspecialchars($_SESSION['profile_image_url']); ?>" onerror="this.onerror=null; this.src='https://placehold.co/40x40/FFFFFF/333333?text=<?php echo substr(htmlspecialchars($_SESSION['full_name']), 0, 1); ?>'" alt="User Avatar" class="rounded-full w-10 h-10 cursor-pointer object-cover">
                    </a>
                </div>
            </header>

            <div class="flex-1 p-8 overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-300">Buku</h2>
                        <p class="text-lg text-gray-400">Daftar Buku</p>
                    </div>
                </div>
               <div class="bg-[#333333] p-6 rounded-xl shadow-lg">
                    <form method="GET" action="daftar-buku.php" class="flex justify-between items-center mb-6 flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <a href="daftar-buku.php?sort=latest" class="bg-gray-200 text-black font-bold py-2 px-4 rounded-lg hover:bg-gray-300">Latest</a>
                            <div class="dropdown inline-block relative">
                                <a href="daftar-buku.php" class="bg-gray-700 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600 flex items-center">
                                    <?php echo !empty($genre_filter) ? htmlspecialchars($genre_filter) : 'All Genre'; ?>
                                    <i data-lucide="chevron-down" class="ml-2 w-4 h-4"></i>
                                </a>
                                <ul class="dropdown-menu absolute hidden text-gray-300 pt-1 w-48 bg-gray-700 rounded-lg shadow-lg z-10">
                                    <li><a class="rounded-t hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="daftar-buku.php">All Genre</a></li>
                                    <?php foreach ($genres as $genre): ?>
                                    <li><a class="hover:bg-gray-600 py-2 px-4 block whitespace-no-wrap" href="daftar-buku.php?genre=<?php echo urlencode($genre); ?>"><?php echo htmlspecialchars($genre); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Search for Title, Genre..." value="<?php echo htmlspecialchars($search_query); ?>" class="bg-[#4F4F4F] w-64 text-white rounded-lg py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-purple-400">
                            <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </form>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-6 gap-y-8">
                        <?php if (!empty($books)): ?>
                            <?php foreach ($books as $book): ?>
                                <div class="text-center">
                                    <img src="../<?php echo htmlspecialchars($book['cover_image_url']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/150x220/333333/FFFFFF?text=No+Image';" alt="<?php echo htmlspecialchars($book['title']); ?>" class="rounded-lg mb-2 w-full h-auto object-cover shadow-lg" style="aspect-ratio: 150 / 220;">
                                    <h4 class="font-semibold text-white truncate"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    <p class="text-xs text-gray-400 truncate"><?php echo htmlspecialchars($book['genre']); ?></p>
                                    <div class="flex justify-center text-yellow-400 mt-1">
                                        <?php 
                                        $rating = floatval($book['rating']);
                                        for ($i = 1; $i <= 5; $i++): 
                                            echo ($i <= $rating) ? '<i data-lucide="star" class="w-4 h-4 fill-current"></i>' : '<i data-lucide="star" class="w-4 h-4"></i>';
                                        endfor; 
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="col-span-full text-center text-gray-400 py-10">No books found matching your criteria.</p>
                        <?php endif; ?>
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
