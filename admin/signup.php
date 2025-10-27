<?php
// Include config file
require_once "include/config.php"; // Make sure this path is correct

// Fetch departments (courses) for the dropdown
$departments = [];
$sql_departments = "SELECT cid, cname FROM course ORDER BY cname ASC";
if ($result = mysqli_query($conn, $sql_departments)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $departments[] = $row;
    }
    mysqli_free_result($result);
}

// Define variables and initialize with empty values
$username = $password = $confirm_password = $pet_name = $childhood_nickname = $role = $cid = "";
$username_err = $password_err = $confirm_password_err = $pet_name_err = $childhood_nickname_err = $profile_image_err = $role_err = $cid_err = "";
$profile_image_path = NULL;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username (existing logic is fine)
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

    // Validate password (existing logic is fine)
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password (existing logic is fine)
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate Role and Department
    if (empty($_POST["role"])) {
        $role_err = "Please select a role for the user.";
    } else {
        $role = $_POST["role"];
        if ($role === 'sub_admin') {
            if (empty($_POST["cid"])) {
                $cid_err = "Please select a department for the sub-admin.";
            } else {
                $cid = $_POST["cid"];
            }
        } elseif ($role === 'super_admin') {
            // *** MODIFICATION HERE ***
            // Set cid to 'SAdmin' for super admins.
            $cid = 'SAdmin';
        } else {
            $role_err = "Invalid role selected.";
        }
    }


    // Validate hints (existing logic is fine)
    $pet_name = !empty(trim($_POST["pet_name"])) ? trim($_POST["pet_name"]) : NULL;
    $childhood_nickname = !empty(trim($_POST["childhood_nickname"])) ? trim($_POST["childhood_nickname"]) : NULL;

    // Handle Profile Image Upload (existing logic is fine)
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
        $target_dir = "uploads/profile_images/";
        $image_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $unique_image_name = uniqid('user_', true) . '.' . $image_extension;
        $target_file = $target_dir . $unique_image_name;
        $uploadOk = 1;

        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check === false) {
            $profile_image_err = "File is not an image.";
            $uploadOk = 0;
        }

        if ($_FILES["profile_image"]["size"] > 5000000) {
            $profile_image_err = "Sorry, your file is too large (max 5MB).";
            $uploadOk = 0;
        }

        $allowed_formats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($image_extension, $allowed_formats)) {
            $profile_image_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image_path = $target_file;
            } else {
                $profile_image_err = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($role_err) && empty($cid_err) && empty($profile_image_err)) {

        // Prepare an insert statement with role, cid, and status
        $sql = "INSERT INTO users (username, password, profile_image, pet_name_hint, childhood_nickname_hint, role, cid, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement
            mysqli_stmt_bind_param($stmt, "sssssssi", $param_username, $param_password, $param_profile_image, $param_pet_name, $param_childhood_nickname, $param_role, $param_cid, $param_status);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_profile_image = $profile_image_path;
            $param_pet_name = $pet_name;
            $param_childhood_nickname = $childhood_nickname;
            $param_role = $role;
            $param_cid = $cid;   // This will now be 'SAdmin' for super admins
            $param_status = 0;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
                exit;
            } else {
                echo "Oops! Something went wrong while saving data. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Oops! Something went wrong with database preparation. Please try again later.";
        }
    }
} // End of POST processing block

// Close connection at the very end
if(isset($conn)) {
    mysqli_close($conn);
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
            font-family: sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
            box-sizing: border-box;
        }

        .wrapper {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-width: 95%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Style for the department dropdown initially hidden */
        #department-group {
            display: none;
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
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($password_err); ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo htmlspecialchars($confirm_password_err); ?></span>
            </div>

            <hr>
            
            <div class="form-group">
                <label for="role">User Role</label>
                <select name="role" id="role" class="form-control <?php echo (!empty($role_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a Role...</option>
                    <option value="super_admin" <?php echo ($role === 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                    <option value="sub_admin" <?php echo ($role === 'sub_admin') ? 'selected' : ''; ?>>Sub Admin</option>
                </select>
                <span class="invalid-feedback"><?php echo htmlspecialchars($role_err); ?></span>
            </div>

            <div class="form-group" id="department-group">
                <label for="cid">Department</label>
                <select name="cid" id="cid" class="form-control <?php echo (!empty($cid_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a Department...</option>
                    <?php foreach ($departments as $dept) : ?>
                        <option value="<?php echo htmlspecialchars($dept['cid']); ?>" <?php echo ($cid === $dept['cid']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['cname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback"><?php echo htmlspecialchars($cid_err); ?></span>
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
                <?php if (!empty($profile_image_err)) : ?>
                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($profile_image_err); ?></div>
                <?php endif; ?>
                <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 5MB.</small>
            </div>

            <div class="form-group mt-4">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const departmentGroup = document.getElementById('department-group');

            function toggleDepartmentField() {
                if (roleSelect.value === 'sub_admin') {
                    departmentGroup.style.display = 'block';
                } else {
                    departmentGroup.style.display = 'none';
                }
            }

            // Check on page load (in case of form resubmission with errors)
            toggleDepartmentField();

            // Check when the role selection changes
            roleSelect.addEventListener('change', toggleDepartmentField);
        });
    </script>

</body>
</html>