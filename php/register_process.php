<?php
// Include database configuration
require_once "config.php";

// Define variables and initialize with empty values
$email = $name = $password = "";
$email_err = $name_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement to check if email already exists
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Check input errors before inserting in database
    if (empty($email_err) && empty($name_err) && empty($password_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')";
         
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_email, $param_password);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            // Creates a password hash
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page upon successful registration
                header("location: ../login.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);

    // If there were errors, display them
    if(!empty($email_err) || !empty($name_err) || !empty($password_err)){
        echo $email_err . "<br>";
        echo $name_err . "<br>";
        echo $password_err . "<br>";
    }
}
?>
