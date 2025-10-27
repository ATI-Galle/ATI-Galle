<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect them to the welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Include config file
require_once "include/config.php";

// ---- ADDED: Helper functions for logging ----

/**
 * Gets the approximate location from an IP address using a free API.
 * NOTE: For high-volume production use, consider a paid, more robust service.
 * @param string $ip The IP address.
 * @return string The location details or a default message.
 */
function get_location_from_ip($ip) {
    // This function requires the cURL extension to be enabled in your PHP installation.
    $url = "http://ip-api.com/json/{$ip}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        if ($data && $data['status'] == 'success') {
            return $data['city'] . ', ' . $data['regionName'] . ', ' . $data['country'];
        }
    }
    return 'Location not found';
}

/**
 * Inserts a login attempt record into the database.
 * @param mysqli $conn The database connection object.
 * @param string $username The username that was used for the attempt.
 * @param string $status The result of the login attempt ('success' or 'failed').
 */
function log_login_attempt($conn, $username, $status) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    $location = get_location_from_ip($ip_address);

    $sql = "INSERT INTO login_logs (user_cid, ip_address, location, user_agent, status) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $username, $ip_address, $location, $user_agent, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// ---- END: Helper functions for logging ----


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
        // Prepare a select statement to fetch user details
        $sql = "SELECT id, username, password, status, role, cid FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                // Check if username exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $status, $role, $cid);
                    if (mysqli_stmt_fetch($stmt)) {
                        // Check if the user's account is active
                        if ($status == 1) {
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, so start a new session
                                // session_start(); // Already started at the top

                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["role"] = $role;
                                $_SESSION["cid"] = $cid;

                                // ---- ADDED: Log the successful login attempt ----
                                log_login_attempt($conn, $username, 'success');

                                // Redirect user to the welcome page
                                header("location: index.php");
                                exit;
                            } else {
                                // Password is not valid
                                $login_err = "Invalid username or password.";
                                // ---- ADDED: Log the failed login attempt ----
                                log_login_attempt($conn, $username, 'failed');
                            }
                        } else {
                            // User's account is inactive
                            $login_err = "Your account is inactive. Please contact the administrator.";
                             // ---- ADDED: Log the failed login attempt ----
                            log_login_attempt($conn, $username, 'failed');
                        }
                    }
                } else {
                    // Username doesn't exist
                    $login_err = "Invalid username or password.";
                     // ---- ADDED: Log the failed login attempt (user not found) ----
                    log_login_attempt($conn, $username, 'failed');
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
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding-top: 120px;
            box-sizing: border-box;
        }

        .logo {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .logo img {
            height: 70px;
            display: block;
        }

        .wrapper {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
        }

        h2 {
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #495057;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
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
            background-color: #007bff;
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
            background-color: #0056b3;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
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
            echo '<div class="alert alert-danger">' . htmlspecialchars($login_err) . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($username_err); ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($password_err); ?></span>
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
