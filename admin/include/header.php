<?php
// --- SESSION AND ROLE MANAGEMENT ---
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "include/config.php";

// Initialize variables for update messages
$update_message = '';
$update_message_type = ''; // Will be 'success' or 'danger' for Bootstrap alerts

// ===================================================================
// NEW: PROFILE UPDATE LOGIC (MOVED FROM update-profile.php)
// This block will only execute when the profile editing form is submitted.
// ===================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $user_id = $_SESSION["id"];
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validate username
    if (empty($username)) {
        $errors[] = "Username cannot be empty.";
    }

    // Validate password change
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
    }

    // File Upload Handling
    $profile_image_sql_part = "";
    $profile_image_path_for_db = null;

    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
        $target_dir = "uploads/profile_images/";
        if (!is_dir($target_dir)) { // Create directory if it doesn't exist
            mkdir($target_dir, 0755, true);
        }

        $image_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed_formats = ["jpg", "jpeg", "png", "gif"];

        if ($_FILES["profile_image"]["size"] > 2000000) { // 2MB limit
            $errors[] = "File is too large (max 2MB).";
        } elseif (!in_array($image_extension, $allowed_formats)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            $unique_image_name = uniqid('user_' . $user_id . '_', true) . '.' . $image_extension;
            $target_file = $target_dir . $unique_image_name;
            
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $profile_image_path_for_db = $target_file;
                $profile_image_sql_part = ", profile_image = ?";
            } else {
                $errors[] = "There was an error uploading your file.";
            }
        }
    }

    // If there are no validation errors, proceed with database update
    if (empty($errors)) {
        $params = [];
        $types = "";

        $sql = "UPDATE users SET username = ?";
        $params[] = $username;
        $types .= "s";

        if (!empty($new_password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }

        if ($profile_image_path_for_db) {
            $sql .= $profile_image_sql_part;
            $params[] = $profile_image_path_for_db;
            $types .= "s";
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);

            if (mysqli_stmt_execute($stmt)) {
                $update_message = "Profile updated successfully!";
                $update_message_type = 'success';
            } else {
                $update_message = "Database error: Could not update profile.";
                $update_message_type = 'danger';
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        // If there were errors, prepare them for display
        $update_message = implode("<br>", $errors);
        $update_message_type = 'danger';
    }
}

// ===================================================================
// DATA FETCHING FOR PAGE DISPLAY
// This runs after the update logic, so it will show the latest data.
// ===================================================================
$profile_image_path = 'assets/images/faces/face28.jpg'; // Default image
$current_username = ''; // Default username

$sql_fetch = "SELECT username, profile_image FROM users WHERE id = ?";
if($stmt_fetch = mysqli_prepare($conn, $sql_fetch)){
    mysqli_stmt_bind_param($stmt_fetch, "i", $param_id);
    $param_id = $_SESSION["id"];

    if(mysqli_stmt_execute($stmt_fetch)){
        $result = mysqli_stmt_get_result($stmt_fetch);
        if($row = mysqli_fetch_assoc($result)){
            $current_username = $row["username"];
            if(!empty($row["profile_image"])){
                $profile_image_path = $row["profile_image"];
            }
        }
    }
    mysqli_stmt_close($stmt_fetch);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ATI GALLE | ADMIN</title>
    <link rel="stylesheet" href="assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <style>
      .profile-image-preview {
        width: 120px; height: 120px; object-fit: cover; border-radius: 50%;
        border: 3px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: block; margin: 0 auto 15px;
      }
    </style>
  </head>
  <body>

    <?php if (!empty($update_message)): ?>
      <div style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 250px;">
        <div class="alert alert-<?php echo $update_message_type; ?> alert-dismissible fade show" role="alert">
          <?php echo $update_message; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      </div>
    <?php endif; ?>

    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo me-5" href="index.php"><img src="../img/logo/logo2.png" class="me-2" alt="logo" /></a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src=" ../img/logo/logo2.png" alt="logo" /></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
              <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="profile" />
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileEditModal">
                <i class="ti-settings text-primary"></i> Settings
              </a>
              <a class="dropdown-item" href="include/logout.php">
                <i class="ti-power-off text-primary"></i> Logout
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    
    <div class="modal fade" id="profileEditModal" tabindex="-1" aria-labelledby="profileEditModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="profileEditModalLabel">Edit Your Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
              <input type="hidden" name="action" value="update_profile">

              <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="Profile Preview" id="imagePreview" class="profile-image-preview">
              
              <div class="mb-3">
                <label for="profile_image" class="form-label">Change Profile Picture</label>
                <input class="form-control" type="file" id="profile_image" name="profile_image" accept="image/png, image/jpeg, image/gif">
              </div>

             <div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($current_username); ?>" required readonly>
</div>

              <hr>
              <p class="text-muted">Leave password fields blank to keep current password.</p>

              <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
              </div>

              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" autocomplete="new-password">
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
          </form> </div>
      </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/template.js"></script>

    <script>
      // Live preview for profile image upload
      document.getElementById('profile_image').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
          document.getElementById('imagePreview').src = URL.createObjectURL(file);
        }
      };
    </script>
  </body>
</html>
<?php
// Close the database connection at the very end
mysqli_close($conn);
?>