<?php
// Initialize the session
session_start();

// Include database configuration
require_once "config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Access Denied.");
}

// Check if book ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Prepare a delete statement
    $sql = "DELETE FROM books WHERE id = ?";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Records deleted successfully. Redirect to landing page.
            header("location: ../admin/data-buku.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else {
    // If ID parameter is missing, redirect to error page or back to the list
    die("Invalid request. No ID provided.");
}
?>
