<?php
// Include config file
require_once "include/config.php"; // Make sure this path is correct

// Define variables and initialize with empty values
$username = $password = $confirm_password = $pet_name = $childhood_nickname = "";
$username_err = $password_err = $confirm_password_err = $pet_name_err = $childhood_nickname_err = $profile_image_err = "";
$profile_image_path = NULL; // Will store the path to the uploaded image

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong with username check. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate Pet Name (optional, so no empty check unless required)
    if (!empty(trim($_POST["pet_name"]))) {
        $pet_name = trim($_POST["pet_name"]);
        // Add any specific validation for pet_name if needed
        if (strlen($pet_name) > 100) {
            $pet_name_err = "Pet name is too long (max 100 characters).";
        }
    } else {
        $pet_name = NULL; // Store as NULL if empty and optional
    }


    // Validate Childhood Nickname (optional)
    if (!empty(trim($_POST["childhood_nickname"]))) {
        $childhood_nickname = trim($_POST["childhood_nickname"]);
        // Add any specific validation for childhood_nickname if needed
        if (strlen($childhood_nickname) > 100) {
            $childhood_nickname_err = "Childhood nickname is too long (max 100 characters).";
        }
    } else {
        $childhood_nickname = NULL; // Store as NULL if empty and optional
    }


    // Handle Profile Image Upload
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
        $target_dir = "uploads/profile_images/";
        // Create a unique filename to prevent overwriting
        $image_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('user_', true) . '.' . $image_extension;
        $target_file = $target_dir . $unique_image_name;
        $uploadOk = 1;

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $profile_image_err = "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (e.g., 5MB limit)
        if ($_FILES["profile_image"]["size"] > 5000000) {
            $profile_image_err = "Sorry, your file is too large (max 5MB).";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_formats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($image_extension, $allowed_formats)) {
            $profile_image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            // $profile_image_err already set
        } else {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image_path = $target_file; // Store the path to be saved in DB
            } else {
                $profile_image_err = "Sorry, there was an error uploading your file.";
            }
        }
    } elseif (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] != UPLOAD_ERR_NO_FILE && $_FILES["profile_image"]["error"] != 0) {
        // Handle other upload errors
        $profile_image_err = "There was an error with the image upload (Error code: " . $_FILES["profile_image"]["error"] . ").";
    }
    // If no file is uploaded or an error occurred, $profile_image_path remains NULL or its previous value.

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($pet_name_err) && empty($childhood_nickname_err) && empty($profile_image_err)) {

        // Prepare an insert statement WITH the status column
        $sql = "INSERT INTO users (username, password, profile_image, pet_name_hint, childhood_nickname_hint, status) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters, including the status
            mysqli_stmt_bind_param($stmt, "sssssi", $param_username, $param_password, $param_profile_image, $param_pet_name, $param_childhood_nickname, $param_status);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_profile_image = $profile_image_path; // This can be NULL if no image uploaded
            $param_pet_name = $pet_name; // This can be NULL
            $param_childhood_nickname = $childhood_nickname; // This can be NULL
            $param_status = 0; // Set the status to 0 for new sign-ups

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
                exit; // It's good practice to exit after a header redirect
            } else {
                echo "Oops! Something went wrong while saving data. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Oops! Something went wrong with database preparation. Please try again later.";
        }
    }

    // Close connection if all checks are done (or move to the very end if errors occur before this)
    if ($conn) {
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
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

        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #4e555b;
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
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($username_err); ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value=""> <span class="invalid-feedback"><?php echo htmlspecialchars($password_err); ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value=""> <span class="invalid-feedback"><?php echo htmlspecialchars($confirm_password_err); ?></span>
            </div>

            <hr>
            <p class="text-muted">Optional: Hints for account recovery.</p>

            <div class="form-group">
                <label>Pet Name (Hint)</label>
                <input type="text" name="pet_name" class="form-control <?php echo (!empty($pet_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($pet_name); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($pet_name_err); ?></span>
            </div>
            <div class="form-group">
                <label>Childhood Nickname (Hint)</label>
                <input type="text" name="childhood_nickname" class="form-control <?php echo (!empty($childhood_nickname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($childhood_nickname); ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($childhood_nickname_err); ?></span>
            </div>

            <hr>

            <div class="form-group">
                <label>Profile Image (Optional)</label>
                <input type="file" name="profile_image" class="form-control-file <?php echo (!empty($profile_image_err)) ? 'is-invalid' : ''; ?>">
                <?php if(!empty($profile_image_err)): ?>
                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($profile_image_err); ?></div>
                <?php endif; ?>
                 <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 5MB.</small>
            </div>

            <div class="form-group mt-4">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
            <p>Forgot your password? <a href="resetpw.php">Reset here</a>.</p>
        </form>
    </div>
</body>
</html>