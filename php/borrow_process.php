<?php
// Initialize the session
session_start();

// Include database configuration
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "You must be logged in to borrow a book.";
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["id"];
    $book_title = trim($_POST["book_title"]);
    $borrow_date = $_POST["borrow_date"];
    $due_date = $_POST["due_date"];

    // 1. Find the book and check its stock
    $book_id = null;
    $stock_available = 0;
    $sql_find_book = "SELECT id, stock_available FROM books WHERE title = ? LIMIT 1";

    if ($stmt = mysqli_prepare($link, $sql_find_book)) {
        mysqli_stmt_bind_param($stmt, "s", $book_title);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $book = mysqli_fetch_assoc($result);
                $book_id = $book['id'];
                $stock_available = $book['stock_available'];
            }
        }
        mysqli_stmt_close($stmt);
    }

    // 2. Check if book exists and is in stock
    if (is_null($book_id)) {
        die("Error: Book with that exact title not found. Please go back and try again.");
    }
    if ($stock_available <= 0) {
        die("Error: This book is currently out of stock.");
    }

    // Use a transaction to ensure data integrity
    mysqli_begin_transaction($link);

    try {
        // 3. Insert the new transaction
        $sql_insert_transaction = "INSERT INTO transactions (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'Borrowed')";
        if ($stmt_insert = mysqli_prepare($link, $sql_insert_transaction)) {
            mysqli_stmt_bind_param($stmt_insert, "iiss", $user_id, $book_id, $borrow_date, $due_date);
            mysqli_stmt_execute($stmt_insert);
            mysqli_stmt_close($stmt_insert);
        } else {
            throw new Exception("Error preparing transaction insert statement.");
        }

        // 4. Update the book's stock
        $sql_update_stock = "UPDATE books SET stock_available = stock_available - 1 WHERE id = ?";
        if ($stmt_update = mysqli_prepare($link, $sql_update_stock)) {
            mysqli_stmt_bind_param($stmt_update, "i", $book_id);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);
        } else {
            throw new Exception("Error preparing stock update statement.");
        }

        // If all queries were successful, commit the transaction
        mysqli_commit($link);

        // Redirect to history page to see the new transaction
        header("location: ../user/history.php"); // We will make this dynamic next
        exit;

    } catch (Exception $e) {
        // If any query failed, roll back the changes
        mysqli_rollback($link);
        die("An error occurred. The transaction has been rolled back. Error: " . $e->getMessage());
    }

} else {
    // If the page was accessed directly without POST data
    header("location: ../user/data-form.php");
    exit;
}

?>
