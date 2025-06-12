<?php
session_start();
require_once "config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Access Denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $rating = !empty($_POST['rating']) ? floatval($_POST['rating']) : 0.0;
    $stock_available = intval($_POST['stock_available']);
    $stock_needed = intval($_POST['stock_needed']);
    
    $cover_image_path = "assets/images/default_cover.png"; // Default image

    // --- File Upload Handling ---
    if (isset($_FILES["cover_image"]) && $_FILES["cover_image"]["error"] == 0) {
        $target_dir = "../assets/images/";
        $original_filename = basename($_FILES["cover_image"]["name"]);
        $safe_filename = preg_replace("/[^a-zA-Z0-9\s\.\-]/", "", $original_filename);
        $target_file = $target_dir . $safe_filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image
        $check = getimagesize($_FILES["cover_image"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                    $cover_image_path = "assets/images/" . $safe_filename;
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // --- Database Insertion ---
    $sql = "INSERT INTO books (title, author, genre, rating, stock_available, stock_needed, cover_image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssdiis", $title, $author, $genre, $rating, $stock_available, $stock_needed, $cover_image_path);

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
