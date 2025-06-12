<?php
// Initialize the session
session_start();

// Include database configuration
require_once "../php/config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../login.php");
    exit;
}

// Check if book ID is provided in the URL
if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    die("Invalid request. Book ID not provided.");
}

$book_id = trim($_GET["id"]);
$book = [];

// Fetch the book's current data
$sql = "SELECT * FROM books WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $book = mysqli_fetch_assoc($result);
        } else {
            die("No book found with this ID.");
        }
    } else {
        die("Error fetching book data.");
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Admin Dashboard</title>
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
                        <h2 class="text-3xl font-bold text-gray-300">Edit Book</h2>
                        <p class="text-lg text-gray-400">Update the book's details below.</p>
                    </div>
                </div>
               <div class="bg-[#333333] p-8 rounded-xl shadow-lg">
                    <form action="../php/edit_book_process.php" method="POST" enctype="multipart/form-data">
                        <!-- Hidden input to store the book ID -->
                        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['id']); ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="title">Book Title</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="title" name="title" type="text" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="author">Author</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="author" name="author" type="text" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="genre">Genre</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="genre" name="genre" type="text" value="<?php echo htmlspecialchars($book['genre']); ?>">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="rating">Rating (1.0 - 5.0)</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="rating" name="rating" type="number" step="0.1" min="0" max="5" value="<?php echo htmlspecialchars($book['rating']); ?>">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="stock_available">Stock Available</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="stock_available" name="stock_available" type="number" min="0" value="<?php echo htmlspecialchars($book['stock_available']); ?>" required>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="stock_needed">Stock Needed</label>
                                <input class="w-full bg-[#4F4F4F] text-white rounded-lg py-3 px-4" id="stock_needed" name="stock_needed" type="number" min="0" value="<?php echo htmlspecialchars($book['stock_needed']); ?>" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 text-sm font-bold mb-2" for="cover_image">New Cover Image (Optional)</label>
                                <input class="w-full text-gray-300 text-sm bg-[#4F4F4F] rounded-lg cursor-pointer focus:outline-none" id="cover_image" name="cover_image" type="file" accept="image/png, image/jpeg">
                                <p class="text-xs text-gray-500 mt-1">Current: <?php echo htmlspecialchars($book['cover_image_url']); ?></p>
                            </div>
                        </div>
                        <div class="mt-8 text-right">
                             <button type="submit" class="bg-[#A78BFA] hover:bg-purple-600 text-white font-bold py-3 px-8 rounded-lg">Update Book</button>
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
