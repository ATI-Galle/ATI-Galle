<?php
session_start();
include('../include/config.php'); // DB constants: DB_SERVER, DB_USER, DB_PASS, DB_NAME
error_reporting(0); // Dev: E_ALL; Prod: 0 and log errors

// --- Message Variables ---
$message = '';
$error = '';

// Default form mode (add)
$edit_mode = false;
$user_data_for_form = [
    'id' => '',
    'username' => '',
    'profile_image' => ''
];
{
    $loggedInUsername = $_SESSION['username'] ?? ''; // IMPORTANT: Assumes 'username' is stored in session
    $loggedInUserId = 0; // Will be fetched if username exists

    // Fetch logged-in user's ID
    if (!empty($loggedInUsername)) {
        $conn_tmp = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if (!$conn_tmp->connect_error) {
            $conn_tmp->set_charset("utf8mb4");
            $stmt_tmp = $conn_tmp->prepare("SELECT id FROM users WHERE username = ?");
            if ($stmt_tmp) {
                $stmt_tmp->bind_param("s", $loggedInUsername);
                $stmt_tmp->execute();
                $stmt_tmp->bind_result($fetched_id);
                if ($stmt_tmp->fetch()) {
                    $loggedInUserId = $fetched_id;
                }
                $stmt_tmp->close();
            }
            $conn_tmp->close();
        }
    }
    
    // --- AUTHORIZATION CHECK ---
    // Check for admin privileges based on role or cid from the session
    $userRole = $_SESSION['role'] ?? '';
    $userCid = $_SESSION['cid'] ?? '';
    $isAdmin = ($userRole === 'super_admin' || $userCid === 'SAdmin');


    // ========================================================================
    //  HANDLE GET ACTIONS (DELETE, TOGGLE STATUS, ENTER EDIT MODE)
    // ========================================================================
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
        $action = $_GET['action'];
        $user_id_get = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($user_id_get > 0) {
            if ($action === 'edit') {
                if (!$isAdmin) {
                    $error = "Authorization Error: You are not permitted to edit users.";
                } else {
                    $conn_edit_fetch = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                    if ($conn_edit_fetch->connect_error) {
                        $error = "Database Connection failed: " . $conn_edit_fetch->connect_error;
                    } else {
                        $conn_edit_fetch->set_charset("utf8mb4");
                        // Select only fields needed for the edit form by Super Admin
                        $stmt_fetch = $conn_edit_fetch->prepare("SELECT id, username, profile_image FROM users WHERE id = ?");
                        if ($stmt_fetch) {
                            $stmt_fetch->bind_param("i", $user_id_get);
                            $stmt_fetch->execute();
                            $result_fetch = $stmt_fetch->get_result();
                            if ($user_to_edit = $result_fetch->fetch_assoc()) {
                                $edit_mode = true;
                                $user_data_for_form = $user_to_edit;
                                $user_data_for_form['password'] = ''; // For the new password field
                            } else { $error = "User not found for editing."; }
                            $stmt_fetch->close();
                        } else { $error = "Database prepare failed (fetch for edit)."; }
                        $conn_edit_fetch->close();
                    }
                }
            } elseif ($action === 'delete' || $action === 'toggle_status') {
                if (!$isAdmin) {
                    $error = "Authorization Error: You do not have permission for this action.";
                } else {
                    // Admin cannot delete self for 'delete' action
                    if ($action === 'delete' && $loggedInUserId === $user_id_get && $isAdmin) {
                         $error = "Admin account cannot be deleted from here.";
                    } else {
                        $conn_action = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                        if ($conn_action->connect_error) {
                            $error = "Database Connection failed: " . $conn_action->connect_error;
                        } else {
                            $conn_action->set_charset("utf8mb4");
                            if ($action === 'delete') {
                                $photo_path = '';
                                $stmt_path = $conn_action->prepare("SELECT profile_image FROM users WHERE id = ?");
                                if ($stmt_path) { /* ... fetch path ... */ $stmt_path->bind_param("i", $user_id_get); $stmt_path->execute(); $stmt_path->bind_result($photo_path); $stmt_path->fetch(); $stmt_path->close(); }

                                $stmt_delete = $conn_action->prepare("DELETE FROM users WHERE id = ?");
                                if ($stmt_delete) {
                                    $stmt_delete->bind_param("i", $user_id_get);
                                    if ($stmt_delete->execute()) {
                                        if (!empty($photo_path) && file_exists($photo_path)) { @unlink($photo_path); }
                                        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1"); exit();
                                    } else { $error = "Database delete failed."; error_log("SQL Delete Error: ".$stmt_delete->error); }
                                    $stmt_delete->close();
                                } else { $error = "Database prepare failed (delete)."; }
                            } elseif ($action === 'toggle_status') {
                                $stmt_toggle = $conn_action->prepare("UPDATE users SET status = NOT status WHERE id = ?");
                                if ($stmt_toggle) {
                                    $stmt_toggle->bind_param("i", $user_id_get);
                                    if ($stmt_toggle->execute()) {
                                        header("Location: " . $_SERVER['PHP_SELF'] . "?status_changed=1"); exit();
                                    } else { $error = "Status update failed."; error_log("SQL Toggle Error: ".$stmt_toggle->error); }
                                    $stmt_toggle->close();
                                } else { $error = "Database prepare failed (toggle)."; }
                            }
                            $conn_action->close();
                        }
                    }
                } // end $isAdmin check for delete/toggle
                 if ($error) { // If an error occurred in action, redirect with error message
                    $param_key = ($action === 'delete') ? 'delerror' : (($action === 'toggle_status') ? 'statuserror' : 'autherror');
                    header("Location: " . $_SERVER['PHP_SELF'] . "?".$param_key."=" . urlencode($error));
                    exit();
                }
            } // end delete or toggle_status block
        } else if ($action !== 'edit') { // user_id_get was 0, and action was not 'edit'
             $error = "Invalid user ID or action specified.";
        }
    }


    // --- MESSAGES FROM REDIRECTS ---
    if (isset($_GET['success'])) $message = "New user added successfully!";
    if (isset($_GET['updated'])) $message = "User updated successfully!";
    if (isset($_GET['deleted'])) $message = "User deleted successfully!";
    if (isset($_GET['status_changed'])) $message = "User status updated successfully!";
    if (isset($_GET['delerror'])) $error = "Error deleting user: " . htmlspecialchars(urldecode($_GET['delerror']));
    if (isset($_GET['statuserror'])) $error = "Error updating status: " . htmlspecialchars(urldecode($_GET['statuserror']));
    if (isset($_GET['autherror'])) $error = "Authorization Error: " . htmlspecialchars(urldecode($_GET['autherror']));


    // --- File Upload Configuration & Directory Check --- (Same as before)
    $upload_dir = "uploads/";
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 5 * 1024 * 1024; // 5 MB
    if (!is_dir($upload_dir)) { /* ... mkdir logic ... */ if (!@mkdir($upload_dir, 0755, true)) { if (empty($error) && !is_dir($upload_dir)) $error = "FATAL ERROR: Failed to create upload directory '$upload_dir'. Check permissions."; if (!is_dir($upload_dir)) error_log("Failed to create upload directory: " . $upload_dir); }
    } elseif (!is_writable($upload_dir)) { if (empty($error)) $error = "Configuration Error: Upload directory '$upload_dir' is not writable."; error_log("Upload directory not writable: " . $upload_dir); }


    // ========================================================================
    //  HANDLE FORM SUBMISSION (POST Request) - ADD or UPDATE
    // ========================================================================
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)) {
        $username_post = trim($_POST['username'] ?? '');
        $password_post = $_POST['password'] ?? ''; // For add, or new password for edit
        $profile_image_file_post = $_FILES['profile_image'] ?? null;

        // --- UPDATE USER (Admin Only) ---
        if (isset($_POST['submit_update']) && isset($_POST['edit_user_id'])) {
            if (!$isAdmin) {
                $error = "Authorization Error: You are not permitted to update users.";
            } else {
                $user_id_to_update = intval($_POST['edit_user_id']);
                if (empty($username_post)) {
                    $error = "Username is required.";
                } else {
                    $conn_update = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                    if ($conn_update->connect_error) { $error = "DB Connection failed: " . $conn_update->connect_error; }
                    else {
                        $conn_update->set_charset("utf8mb4");
                        $current_image_path = $_POST['current_profile_image'] ?? '';
                        $destination_path_update = $current_image_path;
                        $new_image_uploaded = false;

                        // Handle new image upload (optional)
                        if ($profile_image_file_post && $profile_image_file_post['error'] !== UPLOAD_ERR_NO_FILE) {
                            // ... (file validation and move logic - same as before)
                            if ($profile_image_file_post['error'] === UPLOAD_ERR_OK) {
                                $file_name = basename($profile_image_file_post['name']); $file_tmp_name = $profile_image_file_post['tmp_name']; $file_size = $profile_image_file_post['size']; $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                if (!in_array($file_extension, $allowed_types)) { $error = "Invalid new file type."; }
                                elseif ($file_size > $max_file_size) { $error = "New file too large."; }
                                else { $new_file_name = uniqid('user_', true) . '.' . $file_extension; $destination_path_update = rtrim($upload_dir, '/') . '/' . $new_file_name; if (move_uploaded_file($file_tmp_name, $destination_path_update)) { $new_image_uploaded = true; } else { $error = "Failed to move new uploaded file."; $destination_path_update = $current_image_path; }}
                            } else { $error = "Upload error for new image: code " . $profile_image_file_post['error']; }
                        }

                        if (empty($error)) {
                            $sql_update_parts = ["username = ?"];
                            $sql_update_params = [$username_post];
                            $sql_update_types = "s";

                            // Handle password update (optional)
                            if (!empty($password_post)) {
                                $hashed_password = password_hash($password_post, PASSWORD_DEFAULT);
                                $sql_update_parts[] = "password = ?";
                                $sql_update_params[] = $hashed_password;
                                $sql_update_types .= "s";
                            }
                            if ($new_image_uploaded) {
                                $sql_update_parts[] = "profile_image = ?";
                                $sql_update_params[] = $destination_path_update;
                                $sql_update_types .= "s";
                            }
                            $sql_update_params[] = $user_id_to_update; $sql_update_types .= "i";

                            $sql_update_query = "UPDATE users SET " . implode(", ", $sql_update_parts) . " WHERE id = ?";
                            $stmt_update = $conn_update->prepare($sql_update_query);

                            if ($stmt_update) {
                                $stmt_update->bind_param($sql_update_types, ...$sql_update_params);
                                if ($stmt_update->execute()) {
                                    if ($new_image_uploaded && !empty($current_image_path) && $current_image_path !== $destination_path_update && file_exists($current_image_path)) {
                                        @unlink($current_image_path);
                                    }
                                    $stmt_update->close(); $conn_update->close();
                                    header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1"); exit();
                                } else { $error = "DB error (execute update). Username conflict?"; error_log("SQL Update Exec Error: ".$stmt_update->error); if ($new_image_uploaded && file_exists($destination_path_update)) unlink($destination_path_update); }
                                if($stmt_update) $stmt_update->close();
                            } else { $error = "DB error (prepare update)."; error_log("SQL Update Prepare Error: ".$conn_update->error); if ($new_image_uploaded && file_exists($destination_path_update)) unlink($destination_path_update); }
                        }
                        // If error during update, repopulate form for Admin
                        if (!empty($error) && $isAdmin) {
                            $edit_mode = true;
                            $user_data_for_form['id'] = $user_id_to_update;
                            $user_data_for_form['username'] = $username_post;
                            $user_data_for_form['profile_image'] = $new_image_uploaded ? $destination_path_update : $current_image_path;
                        }
                        $conn_update->close();
                    }
                }
            }
        }
        // --- ADD NEW USER (Admin Only) ---
        elseif (isset($_POST['submit_add'])) {
            if (!$isAdmin) {
                $error = "Authorization Error: You are not permitted to add users.";
            } elseif (empty($username_post) || empty($password_post)) {
                $error = "Username and Password are required for new user.";
            } elseif ($profile_image_file_post === null || $profile_image_file_post['error'] === UPLOAD_ERR_NO_FILE) {
                $error = "Profile photo is required for new user.";
            } elseif ($profile_image_file_post['error'] !== UPLOAD_ERR_OK) {
                $error = "Upload error code: " . $profile_image_file_post['error'];
            } else {
                // ... (file validation: type, size - same as before)
                $file_name = basename($profile_image_file_post['name']); $file_tmp_name = $profile_image_file_post['tmp_name']; $file_size = $profile_image_file_post['size']; $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                if (!in_array($file_extension, $allowed_types)) { $error = "Invalid file type."; }
                elseif ($file_size > $max_file_size) { $error = "File too large."; }
                else {
                    $new_file_name = uniqid('user_', true) . '.' . $file_extension;
                    $destination_path_add = rtrim($upload_dir, '/') . '/' . $new_file_name;

                    if (move_uploaded_file($file_tmp_name, $destination_path_add)) {
                        $conn_insert = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                        if ($conn_insert->connect_error) { $error = "DB Connection failed: " . $conn_insert->connect_error; if (file_exists($destination_path_add)) unlink($destination_path_add); }
                        else {
                            $conn_insert->set_charset("utf8mb4");
                            $hashed_password_add = password_hash($password_post, PASSWORD_DEFAULT);
                            $sql = "INSERT INTO users (username, password, profile_image) VALUES (?, ?, ?)";
                            $stmt = $conn_insert->prepare($sql);
                            if ($stmt === false) { $error = "DB error (prepare insert)."; error_log("SQL Insert Prepare Error: ".$conn_insert->error); if (file_exists($destination_path_add)) unlink($destination_path_add); }
                            else {
                                $stmt->bind_param("sss", $username_post, $hashed_password_add, $destination_path_add);
                                if ($stmt->execute()) {
                                    $stmt->close(); $conn_insert->close();
                                    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1"); exit();
                                } else { $error = "DB error (execute insert). Username exists?"; error_log("SQL Insert Exec Error: ".$stmt->error); if (file_exists($destination_path_add)) unlink($destination_path_add); }
                                $stmt->close();
                            }
                            $conn_insert->close();
                        }
                    } else { $error = "Failed to move uploaded file."; }
                }
            }
            // If error in add by Admin, repopulate form
            if (!empty($error) && $isAdmin) {
                $user_data_for_form['username'] = $username_post;
            }
        }
    } // --- End of POST Handling ---

    // ========================================================================
    //  FETCH EXISTING USERS (for Display) - Select only needed fields
    // ========================================================================
    $users_list = [];
    $conn_select_list = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn_select_list->connect_error) { /* ... error handling ... */ }
    else {
        $conn_select_list->set_charset("utf8mb4");
        // Select only username, profile_image, status, id for display and actions
        $sql_select_list = "SELECT id, username, profile_image, status FROM users ORDER BY username ASC";
        $result_list = $conn_select_list->query($sql_select_list);
        if ($result_list) {
            while ($row_list = $result_list->fetch_assoc()) { $users_list[] = $row_list; }
            $result_list->free();
        } else { /* ... error handling ... */ }
        $conn_select_list->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Users</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="../plugins/switchery/switchery.min.css">
    <script src="assets/js/modernizr.min.js"></script>
    <style>
        .form-section { padding: 20px; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 5px; margin-bottom: 30px; }
        .form-section h4 { margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .form-section .form-group { margin-bottom: 20px; }
        .form-section label { margin-bottom: 8px; font-weight: bold; }
        .form-section input[type="text"], .form-section input[type="password"], .form-section input[type="file"], .form-section .form-control { max-width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-section small { display: block; margin-top: 5px; color: #777; }
        .alert-message { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; width: 100%; box-sizing: border-box; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .users-section { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
        .users-section h4 { margin-bottom: 20px; }
        .users { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; padding: 0; margin: 0; }
        .no-users { grid-column: 1 / -1; text-align: center; color: #888; padding: 20px; }
        .anu { background-color: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.08); transition: all 0.2s ease-in-out; display: flex; flex-direction: column; position: relative; min-height: 250px; }
        .anu:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .anu img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin: 0 auto 10px auto; border: 3px solid #eee; }
        .anu h3 { font-size: 1em; margin: 10px 0; color: #333; word-wrap: break-word; }
        .status-badge { position: absolute; top: 10px; right: 10px; padding: 3px 8px; border-radius: 10px; font-size: 0.75em; font-weight: bold; color: white; }
        .status-badge.active { background-color: #28a745; }
        .status-badge.inactive { background-color: #dc3545; }
        .actions { margin-top: auto; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-around; gap: 5px; flex-wrap: wrap; }
        .actions a, .actions button { padding: 5px 8px; font-size: 0.8em; border-radius: 3px; text-decoration: none; color: #fff; border: none; cursor: pointer; transition: opacity 0.2s ease; margin-bottom: 5px; }
        .actions a:hover, .actions button:hover { opacity: 0.85; }
        .btn-edit { background-color: #007bff; }
        .btn-activate { background-color: #28a745; }
        .btn-deactivate { background-color: #ffc107; color: #333; }
        .btn-delete { background-color: #dc3545; }
        .current-image-preview img { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; border: 2px solid #eee; }
    </style>
</head>
<body class="fixed-left">
    <div id="wrapper">
        <?php include('include/header.php');?>
        <?php include('include/sidebar.php');?>
        
        <div class="content-page" style="margin-left:300px;">
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Manage Users</h4>
                                <ol class="breadcrumb p-0 m-0"><li>Admin</li><li>Users</li><li class="active">Manage</li></ol>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <?php if ($message): ?><div class="alert-message alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                            <?php if ($error): ?><div class="alert-message alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                            <?php if ($isAdmin): // Only Admins see the Add/Edit form ?>
                            <div class="form-section">
                                <h4><?php echo $edit_mode ? 'Edit User Details' : 'Add New User'; ?></h4>
                                <form name="userform" method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <?php if ($edit_mode): ?>
                                        <input type="hidden" name="edit_user_id" value="<?php echo htmlspecialchars($user_data_for_form['id']); ?>">
                                        <input type="hidden" name="current_profile_image" value="<?php echo htmlspecialchars($user_data_for_form['profile_image']); ?>">
                                    <?php endif; ?>

                                    <div class="form-group m-b-20">
                                        <label for="username">Username:</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required value="<?php echo htmlspecialchars($user_data_for_form['username']); ?>">
                                    </div>
                                    
                                    <div class="form-group m-b-20">
                                        <label for="password"><?php echo $edit_mode ? 'New Password (leave blank to keep current):' : 'Password:'; ?></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" <?php echo !$edit_mode ? 'required' : ''; ?>>
                                    </div>
                                    
                                    <?php if ($edit_mode && !empty($user_data_for_form['profile_image']) && file_exists($user_data_for_form['profile_image'])): ?>
                                    <div class="form-group m-b-10 current-image-preview">
                                        <label>Current Profile Photo:</label><br>
                                        <img src="<?php echo htmlspecialchars($user_data_for_form['profile_image']); ?>" alt="Current Profile Image">
                                    </div>
                                    <?php endif; ?>

                                    <div class="form-group m-b-20">
                                        <label for="profile_image"><?php echo $edit_mode ? 'Change Profile Photo (Optional):' : 'Profile Photo:'; ?></label>
                                        <input type="file" class="form-control-file" id="profile_image" name="profile_image" accept="image/png, image/jpeg, image/gif" <?php echo !$edit_mode ? 'required' : ''; ?>>
                                        <small class="text-muted">Allowed: JPG, PNG, GIF. Max size: <?php echo ($max_file_size / 1024 / 1024); ?>MB.</small>
                                    </div>

                                    <div class="form-group text-right m-b-0">
                                        <?php if ($edit_mode): ?>
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="submit_update">Update User</button>
                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default waves-effect waves-light m-l-5">Cancel Edit</a>
                                        <?php else: ?>
                                            <button class="btn btn-primary waves-effect waves-light" type="submit" name="submit_add">Add User</button>
                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default waves-effect waves-light m-l-5">Reset Form</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                            <?php endif; // End $isAdmin check for form display ?>

                            <div class="users-section">
                                <h4>Current Users</h4>
                                <div class="users">
                                    <?php if (!empty($users_list)): ?>
                                        <?php foreach ($users_list as $user_item): ?>
                                            <div class="anu">
                                                <span class="status-badge <?php echo $user_item['status'] ? 'active' : 'inactive'; ?>">
                                                    <?php echo $user_item['status'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                                <?php if (!empty($user_item['profile_image']) && file_exists($user_item['profile_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($user_item['profile_image']); ?>" alt="<?php echo htmlspecialchars($user_item['username']); ?>">
                                                <?php else: ?>
                                                    <img src="assets/images/default-avatar.png" alt="Default Avatar">
                                                <?php endif; ?>
                                                <h3><?php echo htmlspecialchars($user_item['username']); ?></h3>
                                                
                                                <?php if ($isAdmin): // Action buttons only for Admin ?>
                                                <div class="actions">
                                                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=edit&id=<?php echo $user_item['id']; ?>" class="btn-edit" title="Edit User">Edit</a>
                                                    
                                                    <?php if ($user_item['status']): ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=toggle_status&id=<?php echo $user_item['id']; ?>" class="btn-deactivate" title="Deactivate User" onclick="return confirm('Deactivate this user?');">Deactivate</a>
                                                    <?php else: ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=toggle_status&id=<?php echo $user_item['id']; ?>" class="btn-activate" title="Activate User" onclick="return confirm('Activate this user?');">Activate</a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($loggedInUserId != $user_item['id']): // Any logged-in admin cannot delete themselves ?>
                                                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&id=<?php echo $user_item['id']; ?>" class="btn-delete" title="Delete User" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.');">Delete</a>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; // End $isAdmin check for action buttons ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="no-users">No users found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include('includes/footer.php');?>
        </div>
    </div>
    <script> var resizefunc = []; </script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/detect.js"></script>
    <script src="assets/js/fastclick.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="../plugins/switchery/switchery.min.js"></script>
    <script src="assets/js/jquery.core.js"></script>
    <script src="assets/js/jquery.app.js"></script>
</body>
</html>
<?php } // End the main else block (session check) ?>