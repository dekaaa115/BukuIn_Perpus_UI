<?php
// Initialize the session
session_start();
require_once "config.php";

// Admin check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    die("Access Denied.");
}

// Check if transaction ID is provided
if (!isset($_GET["id"]) || empty(trim($_GET["id"]))) {
    die("Invalid request. Transaction ID not provided.");
}

$transaction_id = intval($_GET["id"]);
$fine_per_day = 2000; // Set the fine amount per day (e.g., Rp. 2000)

// Use a transaction to ensure all updates succeed or fail together
mysqli_begin_transaction($link);

try {
    // 1. Get transaction details (including due_date and book_id)
    $sql_get_trans = "SELECT book_id, due_date, status FROM transactions WHERE id = ?";
    $book_id = null;
    $due_date = null;
    $current_status = null;

    if ($stmt_get = mysqli_prepare($link, $sql_get_trans)) {
        mysqli_stmt_bind_param($stmt_get, "i", $transaction_id);
        mysqli_stmt_execute($stmt_get);
        $result = mysqli_stmt_get_result($stmt_get);
        $transaction = mysqli_fetch_assoc($result);

        if (!$transaction || $transaction['status'] == 'Returned') {
            // If transaction doesn't exist or is already returned, do nothing.
            throw new Exception("Transaction not found or already returned.");
        }
        $book_id = $transaction['book_id'];
        $due_date = new DateTime($transaction['due_date']);
    } else {
        throw new Exception("Error fetching transaction details.");
    }
    mysqli_stmt_close($stmt_get);

    // 2. Calculate fine if overdue
    $return_date = new DateTime(); // Today's date
    $fine_amount = 0;
    $new_status = 'Returned';

    if ($return_date > $due_date) {
        $interval = $return_date->diff($due_date);
        $days_overdue = $interval->days;
        $fine_amount = $days_overdue * $fine_per_day;
        // Even if returned, if it was late, we can still consider the original status as Overdue for records
    }
    
    // 3. Update the transaction record with return date, new status, and fine
    $sql_update_trans = "UPDATE transactions SET return_date = ?, status = ?, fine_amount = ? WHERE id = ?";
    if ($stmt_update_trans = mysqli_prepare($link, $sql_update_trans)) {
        $return_date_str = $return_date->format('Y-m-d');
        mysqli_stmt_bind_param($stmt_update_trans, "ssdi", $return_date_str, $new_status, $fine_amount, $transaction_id);
        mysqli_stmt_execute($stmt_update_trans);
    } else {
        throw new Exception("Error updating transaction record.");
    }
    mysqli_stmt_close($stmt_update_trans);

    // 4. Update the book's stock (increase by 1)
    $sql_update_stock = "UPDATE books SET stock_available = stock_available + 1 WHERE id = ?";
    if ($stmt_update_stock = mysqli_prepare($link, $sql_update_stock)) {
        mysqli_stmt_bind_param($stmt_update_stock, "i", $book_id);
        mysqli_stmt_execute($stmt_update_stock);
    } else {
        throw new Exception("Error updating book stock.");
    }
    mysqli_stmt_close($stmt_update_stock);

    // If all queries were successful, commit the transaction
    mysqli_commit($link);

    // Redirect back to the reports page
    header("location: ../admin/laporan.php");
    exit();

} catch (Exception $e) {
    // If any query failed, roll back the changes
    mysqli_rollback($link);
    die("An error occurred: " . $e->getMessage());
}
?>
