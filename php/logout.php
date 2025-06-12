<?php
// Initialize the session
session_start();

// Unset all of the session variables. This clears all the stored data.
$_SESSION = array();

// Destroy the session completely.
session_destroy();

// Redirect the user to the login page after the session is destroyed.
header("location: ../login.php");
exit;
?>