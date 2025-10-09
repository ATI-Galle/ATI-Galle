<?php
// Initialize the session
session_start();

// Include config file
require_once "include/config.php"; // Make sure this path is correct

// Define variables and initialize with empty values
$username = $pet_name_hint = $childhood_nickname_hint = $new_password = $confirm_password = "";
$username_err = $pet_name_hint_err = $childhood_nickname_hint_err = $new_password_err = $confirm_password_err = $reset_err = $reset_success = "";

// Define lockout parameters
$login_attempts_key = 'reset_attempts_' . $_SERVER['REMOTE_ADDR'];
$lockout_duration = 43200; // 12 hours in seconds
$max_attempts = 5;

// Function to check if lockout period has expired
function isLockoutExpired($last_attempt_time, $duration) {
    return (time() - $last_attempt_time) > $duration;
}

// Check if user is locked out
if (isset($_SESSION[$login_attempts_key]) && $_SESSION[$login_attempts_key]['count'] >= $max_attempts && !isLockoutExpired($_SESSION[$login_attempts_key]['last_attempt'], $lockout_duration)) {
    $reset_err = "Too many failed attempts. Please try again after 12 hours.";
} else {
    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if username is empty
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter your username.";
        } else {
            $username = trim($_POST["username"]);
        }

        // Check if pet name hint is empty
        if (empty(trim($_POST["pet_name_hint"]))) {
            $pet_name_hint_err = "Please enter your pet's name hint.";
        } else {
            $pet_name_hint = trim($_POST["pet_name_hint"]);
        }

        // Check if childhood nickname hint is empty
        if (empty(trim($_POST["childhood_nickname_hint"]))) {
            $childhood_nickname_hint_err = "Please enter your childhood nickname hint.";
        } else {
            $childhood_nickname_hint = trim($_POST["childhood_nickname_hint"]);
        }

        // Check if new password is empty
        if (empty(trim($_POST["new_password"]))) {
            $new_password_err = "Please enter the new password.";
        } elseif (strlen(trim($_POST["new_password"])) < 6) { // Example: Minimum 6 characters
            $new_password_err = "Password must have at least 6 characters.";
        } else {
            $new_password = trim($_POST["new_password"]);
        }

        // Check if confirm password is empty
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm the new password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($new_password_err) && ($new_password != $confirm_password)) {
                $confirm_password_err = "Passwords did not match.";
            }
        }

        // Validate credentials before updating the database
        if (empty($username_err) && empty($pet_name_hint_err) && empty($childhood_nickname_hint_err) && empty($new_password_err) && empty($confirm_password_err)) {
            // Prepare a select statement to check if username and hints match
            $sql_check_user = "SELECT id FROM users WHERE username = ? AND pet_name_hint = ? AND childhood_nickname_hint = ?";
            $stmt_check_user = mysqli_prepare($conn, $sql_check_user);

            if ($stmt_check_user) {
                mysqli_stmt_bind_param($stmt_check_user, "sss", $param_username, $param_pet_name_hint, $param_childhood_nickname_hint);
                $param_username = $username;
                $param_pet_name_hint = $pet_name_hint;
                $param_childhood_nickname_hint = $childhood_nickname_hint;

                if (mysqli_stmt_execute($stmt_check_user)) {
                    mysqli_stmt_store_result($stmt_check_user);

                    if (mysqli_stmt_num_rows($stmt_check_user) == 1) {
                        // Hints are correct, proceed to update password
                        $sql_update_pass = "UPDATE users SET password = ? WHERE username = ?";
                        $stmt_update_pass = mysqli_prepare($conn, $sql_update_pass);

                        if ($stmt_update_pass) {
                            mysqli_stmt_bind_param($stmt_update_pass, "ss", $param_new_password, $param_username);
                            $param_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $param_username = $username;

                            if (mysqli_stmt_execute($stmt_update_pass)) {
                                $reset_success = "Your password has been reset successfully. You can now <a href='login.php'>login</a>.";
                                // Reset attempt count on successful reset
                                unset($_SESSION[$login_attempts_key]);
                            } else {
                                $reset_err = "Oops! Something went wrong while updating your password. Please try again later.";
                            }
                            mysqli_stmt_close($stmt_update_pass);
                        } else {
                            $reset_err = "Oops! Something went wrong preparing the password update. Please try again later.";
                        }
                    } else {
                        // Incorrect hints, record the failed attempt
                        if (!isset($_SESSION[$login_attempts_key])) {
                            $_SESSION[$login_attempts_key]['count'] = 1;
                            $_SESSION[$login_attempts_key]['last_attempt'] = time();
                        } else {
                            $_SESSION[$login_attempts_key]['count']++;
                            $_SESSION[$login_attempts_key]['last_attempt'] = time();
                        }

                        if ($_SESSION[$login_attempts_key]['count'] >= $max_attempts) {
                            $reset_err = "Too many incorrect attempts. Please try again after 12 hours.";
                        } else {
                            $reset_err = "Incorrect username or hints. Please try again.";
                        }
                    }
                } else {
                    $reset_err = "Oops! Something went wrong checking your details. Please try again later.";
                }
                mysqli_stmt_close($stmt_check_user);
            } else {
                $reset_err = "Oops! Something went wrong preparing the user check. Please try again later.";
            }
        }

        // Close connection
        if ($conn) {
            mysqli_close($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif; /* Changed from '14px sans-serif' for consistency */
            background-color: #f8f9fa; /* Light grey background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensure full viewport height */
            margin: 0; /* Remove default body margin */
            /* Adding some padding in case content gets too close to edges, adjust as needed */
            padding: 20px 0;
            box-sizing: border-box;
        }

        .wrapper {
            background-color: #fff; /* White background for the form */
            padding: 30px;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 400px; /* Adjusted width for better responsiveness */
            max-width: 90%; /* Ensure it doesn't get too wide on smaller screens */
            /* margin: auto; and margin-top: 50px; are handled by flexbox on body */
        }

        h2 {
            color: #343a40; /* Dark grey heading */
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #495057; /* Grey label text */
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da; /* Light grey border */
            border-radius: 4px;
            box-sizing: border-box; /* Ensure padding doesn't affect width */
        }

        .form-control.is-invalid {
            border-color: #dc3545; /* Red border for invalid input */
        }

        .invalid-feedback {
            color: #dc3545;
            display: block; /* Ensures it takes space */
            margin-top: 5px;
        }

        .alert-danger { /* Styles for error messages */
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .alert-success { /* Styles for success messages */
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff; /* Blue primary button */
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%; /* Make button full width */
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-bottom: 10px; /* Add some space if there are multiple buttons or links below */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .btn-link { /* Bootstrap class, ensure it's styled as desired or override */
            color: #007bff;
            padding-left: 0; /* Adjust if needed */
        }
        .btn-link:hover {
            color: #0056b3;
        }

        .mt-3 { /* Bootstrap utility class */
            margin-top: 1rem !important; /* 1rem is typically 16px, 15px was used before */
        }

        .text-center { /* Bootstrap utility class */
            text-align: center !important;
        }

        .text-muted { /* Style for less prominent text */
            color: #6c757d;
        }

        /* Specific styling for links within paragraph text if needed */
        p a {
            color: #007bff;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please enter your username and the answers to your security hints to reset your password.</p>

        <?php
        if(!empty($reset_err)){
            echo '<div class="alert alert-danger">' . htmlspecialchars($reset_err) . '</div>';
        }
        if(!empty($reset_success)){
            echo '<div class="alert alert-success">' . $reset_success . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($username_err); ?></span>
            </div>
            <div class="form-group">
                <label>What was your pet's name?</label>
                <input type="text" name="pet_name_hint" class="form-control <?php echo (!empty($pet_name_hint_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($pet_name_hint); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($pet_name_hint_err); ?></span>
            </div>
            <div class="form-group">
                <label>What was your childhood nickname?</label>
                <input type="text" name="childhood_nickname_hint" class="form-control <?php echo (!empty($childhood_nickname_hint_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($childhood_nickname_hint); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($childhood_nickname_hint_err); ?></span>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($new_password_err); ?></span>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($confirm_password_err); ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Reset Password" <?php if (isset($_SESSION[$login_attempts_key]) && $_SESSION[$login_attempts_key]['count'] >= $max_attempts && !isLockoutExpired($_SESSION[$login_attempts_key]['last_attempt'], $lockout_duration)) echo 'disabled'; ?>>
                <a class="btn btn-link ml-2" href="login.php">Cancel</a>
            </div>
            <p>Remembered your password? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>