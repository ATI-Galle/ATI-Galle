<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect them to the welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php"); // Changed redirection to index.php as mentioned
    exit;
}

// Include config file
require_once "include/config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement to fetch user details including status
        $sql = "SELECT id, username, password, status FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password and status
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables, including the status
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $status);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Check if the user's status is 1 (active)
                        if ($status == 1) {
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, so start a new session
                                // session_start(); // Already started at the top

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;

                                // Redirect user to welcome page
                                header("location: index.php"); // Changed redirection to index.php
                                exit; // Added exit after header
                            } else {
                                // Password is not valid, display a generic error message
                                $login_err = "Invalid username or password.";
                            }
                        } else {
                            // User's status is not 1, display an error message
                            $login_err = "Your account is inactive. Please contact the administrator.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8f9fa; /* Light grey background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensure full viewport height */
            margin: 0; /* Remove default body margin */
            padding-top: 120px; /* Add padding to prevent overlap if logo is higher or larger */
            box-sizing: border-box;
        }

        .logo {
            position: absolute;
            top: 30px; /* Adjusted top position */
            left: 50%;
            transform: translateX(-50%); /* Horizontally center the logo */
            /* Removed bottom and margin:auto which were causing issues */
            z-index: 10; /* Ensure logo is above other elements if any overlap was intended (though not recommended here) */
        }

        .logo img {
            height: 70px; /* Set desired height */
            /* 'height: auto;' was redundant here as the next line overrides it */
            display: block; /* Helps with layout and margin behavior */
        }

        .wrapper {
            background-color: #fff; /* White background for the form */
            padding: 30px;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 400px; /* Adjusted width for better responsiveness */
            max-width: 90%; /* Ensure it doesn't get too wide on smaller screens */
            /* position: relative; /* ensure wrapper is in flow and can have z-index if needed, though not primary fix here */
            /* z-index: 1; /* Ensure wrapper is prioritized if stacking context issues arise */
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
            display: block;
            margin-top: 5px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
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
            width: 100%;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .mt-3 {
            margin-top: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d; /* Light grey text */
        }

        .text-primary {
            color: #007bff;
            text-decoration: none;
        }

        .text-primary:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="logo">
        <img src="../img/logo/logo2.png" alt="Your Logo">
    </div>

    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <div class="mt-3 text-center">
                <p class="text-muted">Don't have an account? <a href="signup.php" class="text-primary">Sign up now</a>.</p>
                <p class="text-muted"><a href="resetpw.php" class="text-primary">Forgot your password?</a></p>
            </div>
        </form>
    </div>
</body>
</html>