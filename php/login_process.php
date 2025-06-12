<?php
// Start a new session or resume the existing one
session_start();

// Include the database configuration file
require_once "config.php";

$login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        $login_err = "Please enter both email and password.";
    } else {
        // Prepare a SELECT statement
        $sql = "SELECT id, full_name, email, password, role, profile_image_url FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // ** NEW: Bind profile_image_url as well **
                    mysqli_stmt_bind_result($stmt, $id, $full_name, $db_email, $hashed_password, $role, $profile_image_url);
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $db_email;
                            $_SESSION["full_name"] = $full_name;
                            $_SESSION["role"] = $role;
                            // ** NEW: Store profile image URL in session **
                            $_SESSION["profile_image_url"] = $profile_image_url;

                            // Redirect user based on their role
                            if ($role == 'admin') {
                                header("location: ../admin/data-buku.php");
                                exit;
                            } else {
                                header("location: ../user/daftar-buku.php");
                                exit;
                            }
                        } else {
                            $login_err = "Invalid email or password.";
                        }
                    }
                } else {
                    $login_err = "Invalid email or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    if (!empty($login_err)) {
        $_SESSION['login_error'] = $login_err;
        header("location: ../login.php");
        exit;
    }
    mysqli_close($link);
}
?>
