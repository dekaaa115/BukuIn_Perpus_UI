<?php
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $target_dir = "../assets/uploads/"; // The directory where files will be stored
        $user_id = $_SESSION["id"];
        
        // Create a unique filename to prevent overwriting
        $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
        $safe_filename = "user_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $safe_filename;

        // --- Validation Checks ---
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check === false) {
            die("Error: File is not an image.");
        }

        // Check file size (e.g., 5MB maximum)
        if ($_FILES["profile_picture"]["size"] > 5000000) {
            die("Error: Sorry, your file is too large.");
        }

        // Allow certain file formats
        $allowed_types = array("jpg", "png", "jpeg", "gif");
        if (!in_array($file_extension, $allowed_types)) {
            die("Error: Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }
        
        // --- End Validation ---

        // Attempt to move the uploaded file to your target directory
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now update the database
            $profile_image_path = "assets/uploads/" . $safe_filename;

            $sql = "UPDATE users SET profile_image_url = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "si", $profile_image_path, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    // Update the session variable immediately
                    $_SESSION["profile_image_url"] = $profile_image_path;
                    
                    // Success, redirect back to the profile page
                    header("location: ../user/profil-user.php");
                    exit();
                } else {
                    echo "Error updating database.";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Error: No file uploaded or an error occurred during upload.";
    }
    mysqli_close($link);
}
?>
