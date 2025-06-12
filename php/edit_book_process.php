<?php
session_start();
require_once "config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Access Denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $book_id = intval($_POST['book_id']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $rating = !empty($_POST['rating']) ? floatval($_POST['rating']) : 0.0;
    $stock_available = intval($_POST['stock_available']);
    $stock_needed = intval($_POST['stock_needed']);
    
    // --- File Upload Handling ---
    // Check if a new file was uploaded
    if (isset($_FILES["cover_image"]) && $_FILES["cover_image"]["error"] == 0) {
        $target_dir = "../assets/images/";
        $safe_filename = preg_replace("/[^a-zA-Z0-9\s\.\-]/", "", basename($_FILES["cover_image"]["name"]));
        $target_file = $target_dir . $safe_filename;
        
        // Attempt to move the new file
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            $cover_image_path = "assets/images/" . $safe_filename;
            // Update the cover image path in the database
            $sql_update_image = "UPDATE books SET cover_image_url = ? WHERE id = ?";
            if ($stmt_img = mysqli_prepare($link, $sql_update_image)) {
                mysqli_stmt_bind_param($stmt_img, "si", $cover_image_path, $book_id);
                mysqli_stmt_execute($stmt_img);
                mysqli_stmt_close($stmt_img);
            }
        } else {
            // Handle upload error if necessary
            echo "Error uploading new cover image.";
        }
    }

    // --- Update other book details ---
    $sql = "UPDATE books SET title = ?, author = ?, genre = ?, rating = ?, stock_available = ?, stock_needed = ? WHERE id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssdiis", $title, $author, $genre, $rating, $stock_available, $stock_needed, $book_id);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the book list page on success
            header("location: ../admin/data-buku.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
