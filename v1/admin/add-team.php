<?php
session_start();
include('../include/config.php'); // DB constants: DB_SERVER, DB_USER, DB_PASS, DB_NAME
error_reporting(0); // Dev: E_ALL; Prod: 0 and log errors

// --- Message Variables ---
$message = '';
$error = '';

// Check if user is logged in
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
} else {

    // --- CSRF Token Generation (Simple Implementation) ---
    // For better security, generate a token and include it in forms/links
    // if (!isset($_SESSION['csrf_token'])) {
    //     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    // }
    // $csrf_token = $_SESSION['csrf_token'];
    // --- NOTE: CSRF check is NOT fully implemented in action handling below for brevity ---
    // --- You would typically add a hidden field in forms and check $_POST['csrf_token']
    // --- Or add ?token=... to GET links and check $_GET['token'] against $_SESSION['csrf_token']

    // ========================================================================
    //  HANDLE ACTIONS (DELETE, TOGGLE STATUS) via GET Requests
    // ========================================================================
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {

        $action = $_GET['action'];
        $staff_id = isset($_GET['id']) ? intval($_GET['id']) : 0; // Sanitize ID (using old 'id' name for GET param for compatibility or could change to 'stid')

        // --- Verify CSRF Token (Example - Needs integration in links) ---
        // if (!isset($_GET['token']) || !hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        //     $error = "Invalid request (CSRF token mismatch).";
        //     // Optionally unset token and redirect
        // }
        // elseif ($staff_id > 0) { // Proceed only if ID is valid

            // Connect to DB for actions
            $conn_action = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
            if ($conn_action->connect_error) {
                $error = "Database Connection failed for action: " . $conn_action->connect_error;
                error_log("DB Action Connect Error: " . $conn_action->connect_error);
            } else {
                $conn_action->set_charset("utf8mb4");

                // --- Handle Delete Action ---
                if ($action === 'delete' && $staff_id > 0) {
                    // 1. Get photo path BEFORE deleting DB record
                    $photo_path = '';
                    // Table name changed to staff
                    $stmt_path = $conn_action->prepare("SELECT stimg FROM staff WHERE stid = ?");
                    if ($stmt_path) {
                        $stmt_path->bind_param("i", $staff_id);
                        $stmt_path->execute();
                        $stmt_path->bind_result($photo_path);
                        $stmt_path->fetch();
                        $stmt_path->close();
                    }

                    // 2. Delete DB record
                    // Table name changed to staff
                    $stmt_delete = $conn_action->prepare("DELETE FROM staff WHERE stid = ?");
                    if ($stmt_delete) {
                        $stmt_delete->bind_param("i", $staff_id);
                        if ($stmt_delete->execute()) {
                             // 3. Delete file AFTER successful DB deletion
                             if (!empty($photo_path) && file_exists($photo_path)) {
                                 @unlink($photo_path); // Use @ to suppress errors if file deletion fails, or add specific error handling
                             }
                             $redirect_param = "?deleted=1"; // Success
                         } else {
                             error_log("SQL Delete Error: " . $stmt_delete->error);
                             $redirect_param = "?delerror=" . urlencode("Database delete failed.");
                         }
                         $stmt_delete->close();
                     } else {
                         error_log("SQL Prepare Error (Delete): " . $conn_action->error);
                         $redirect_param = "?delerror=" . urlencode("Database prepare failed (delete).");
                     }
                     $conn_action->close();
                     header("Location: " . $_SERVER['PHP_SELF'] . $redirect_param);
                     exit();
                }

                // --- Handle Toggle Status Action ---
                elseif ($action === 'toggle_status' && $staff_id > 0) {
                    // Use NOT operator or CASE statement
                    // Table name changed to staff
                    $stmt_toggle = $conn_action->prepare("UPDATE staff SET status = NOT status WHERE stid = ?");
                    // OR $stmt_toggle = $conn_action->prepare("UPDATE staff SET status = 1 - status WHERE stid = ?"); // Assuming status is 0 or 1

                    if ($stmt_toggle) {
                        $stmt_toggle->bind_param("i", $staff_id);
                        if ($stmt_toggle->execute()) {
                            $redirect_param = "?status_changed=1"; // Success
                        } else {
                            error_log("SQL Update Status Error: " . $stmt_toggle->error);
                            $redirect_param = "?statuserror=" . urlencode("Status update failed.");
                        }
                        $stmt_toggle->close();
                    } else {
                        error_log("SQL Prepare Error (Toggle Status): " . $conn_action->error);
                        $redirect_param = "?statuserror=" . urlencode("Database prepare failed (status).");
                    }
                    $conn_action->close();
                    header("Location: " . $_SERVER['PHP_SELF'] . $redirect_param);
                    exit();
                }
                // --- Add other actions here if needed ---
                else {
                     // Close connection if action wasn't delete/toggle or ID was invalid
                     $conn_action->close();
                     // Optionally set an error for invalid action
                     $error = "Invalid action requested.";
                 }
             } // End DB connection check
         //} // End CSRF / ID validation check

     } // --- End of GET Action Handling ---


     // ========================================================================
     //  HANDLE MESSAGES FROM REDIRECTS (PRG Pattern)
     // ========================================================================
     if (isset($_GET['success']) && $_GET['success'] == '1') {
         $message = "New staff member added successfully!"; // Adjusted message
     }
     if (isset($_GET['updated']) && $_GET['updated'] == '1') { // For redirect from edit_staff.php (assuming you rename it)
         $message = "Staff member updated successfully!"; // Adjusted message
     }
      if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
         $message = "Staff member deleted successfully!"; // Adjusted message
     }
      if (isset($_GET['status_changed']) && $_GET['status_changed'] == '1') {
         $message = "Staff member status updated successfully!"; // Adjusted message
     }
     // Error Messages
     if (isset($_GET['delerror'])) $error = "Error deleting staff member: " . htmlspecialchars(urldecode($_GET['delerror'])); // Adjusted message
     if (isset($_GET['statuserror'])) $error = "Error updating status: " . htmlspecialchars(urldecode($_GET['statuserror']));
     // Add other error checks (e.g., ?updateerror=...)


     // --- File Upload Configuration & Directory Check (Keep as before) ---
     $upload_dir = "uploads/"; // Make sure this directory exists and is writable
     $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
     $max_file_size = 5 * 1024 * 1024; // 5 MB
     if (!is_dir($upload_dir)) { // Simplified check - assumes permissions are ok if dir exists
          if (!mkdir($upload_dir, 0755, true)) {
              // Set error ONLY if not already set by action handlers
              if (empty($error)) $error = "FATAL ERROR: Failed to create upload directory '$upload_dir'. Check permissions.";
              error_log("Failed to create upload directory: " . $upload_dir); // Log it
          }
     } elseif (!is_writable($upload_dir)) {
          if (empty($error)) $error = "Configuration Error: Upload directory '$upload_dir' is not writable.";
          error_log("Upload directory not writable: " . $upload_dir); // Log it
     }


    // ========================================================================
    //  HANDLE FORM SUBMISSION (ADD STAFF MEMBER - POST Request)
    // ========================================================================
    // Only process POST if $error is not already set by critical issues (like unwritable dir)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && empty($error)) {
        // --- POST handling logic remains largely the same as previous version ---
        // --- It will perform an INSERT ---
        // --- NOTE: Update logic should ideally be in edit_staff.php ---

        $sname = trim($_POST['sname'] ?? '');
        $spos = trim($_POST['spos'] ?? '');
        $stimg = $_FILES['stimg'] ?? null;

        // Basic Validation...
        if (empty($sname) || empty($spos)) { $error = "Name and Position are required."; }
        elseif ($stimg === null || $stimg['error'] === UPLOAD_ERR_NO_FILE) { $error = "Profile photo is required."; }
        elseif ($stimg['error'] !== UPLOAD_ERR_OK) { /* Handle upload errors... */ $error="Upload error code: ".$stimg['error']; }
        else {
            // File Validation (type, size)...
            $file_name = basename($stimg['name']);
            $file_tmp_name = $stimg['tmp_name'];
            $file_size = $stimg['size'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_types)) { $error = "Invalid file type."; }
            elseif ($file_size > $max_file_size) { $error = "File too large."; }
            else {
                // Generate unique filename & move file...
                $new_file_name = uniqid('staff_', true) . '.' . $file_extension; // Changed prefix
                $destination_path = rtrim($upload_dir, '/') . '/' . $new_file_name;

                if (move_uploaded_file($file_tmp_name, $destination_path)) {
                    // Database Interaction (INSERT)...
                    $conn_insert = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
                    if ($conn_insert->connect_error) {
                        $error = "Database Connection failed: " . $conn_insert->connect_error;
                        if (file_exists($destination_path)) unlink($destination_path);
                    } else {
                        $conn_insert->set_charset("utf8mb4");
                        // NOTE: status defaults to 1 in DB schema
                        // SQL query updated with new table name
                        $sql = "INSERT INTO staff (sname, spos, stimg) VALUES (?, ?, ?)";
                        $stmt = $conn_insert->prepare($sql);
                        if ($stmt === false) {
                            error_log("SQL Prepare Error (Insert): " . $conn_insert->error);
                            $error = "Database error (prepare insert).";
                            if (file_exists($destination_path)) unlink($destination_path);
                        } else {
                            $stmt->bind_param("sss", $sname, $spos, $destination_path);
                            if ($stmt->execute()) {
                                // SUCCESS: Redirect for PRG pattern (Add Staff Member)
                                $stmt->close();
                                $conn_insert->close();
                                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                                exit();
                            } else {
                                error_log("SQL Execute Error (Insert): " . $stmt->error);
                                $error = "Database error (execute insert).";
                                if (file_exists($destination_path)) unlink($destination_path);
                            }
                            $stmt->close();
                        }
                        $conn_insert->close();
                    }
                } else {
                    $error = "Failed to move uploaded file.";
                }
            }
        }
    } // --- End of POST Handling ---

    // ========================================================================
    //  FETCH EXISTING STAFF MEMBERS (for Display)
    // ========================================================================
    $staff_members = []; // Changed variable name
    $conn_select = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn_select->connect_error) {
        if (empty($error)) $error = "DB Connect Error (fetch): " . $conn_select->connect_error;
        error_log("DB Select Connect Error: " . $conn_select->connect_error);
    } else {
        $conn_select->set_charset("utf8mb4");
        // Select 'status' along with other fields - SQL query updated
        // Table name changed to staff
        $sql_select = "SELECT stid, sname, spos, stimg, status, sed FROM staff ORDER BY sed DESC"; // Updated column names
        $result = $conn_select->query($sql_select);
        if ($result === false) {
            if (empty($error)) $error = "Error fetching staff members: " . $conn_select->error; // Adjusted message
            error_log("SQL Select Error: " . $conn_select->error);
        } elseif ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $staff_members[] = $row; // Changed variable name
            }
            $result->free();
        }
        $conn_select->close();
    }
    // --- End of Fetching Staff Members ---

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin | Manage Staff Members</title> <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../plugins/switchery/switchery.min.css">
        <script src="assets/js/modernizr.min.js"></script>

        <style>
            /* Keep form section, message, users-section, users grid styles */
             .form-section { padding: 20px; background-color: #f9f9f9; border: 1px solid #eee; border-radius: 5px; margin-bottom: 30px; }
             .form-section h4 { margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
             .form-section .form-group { margin-bottom: 20px; }
             .form-section label { margin-bottom: 8px; font-weight: bold; }
             .form-section input[type="text"], .form-section input[type="file"], .form-section .form-control { max-width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
             .form-section small { display: block; margin-top: 5px; color: #777; }
             .alert-message { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; width: 100%; box-sizing: border-box; }
             .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
             .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }

             .users-section { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; }
             .users-section h4 { margin-bottom: 20px; }
             .users { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; padding: 0; margin: 0; }
             .no-members { grid-column: 1 / -1; text-align: center; color: #888; padding: 20px; } /* Adjusted class name */

             /* Card Styles (.anu) */
             .anu {
                 background-color: #fff;
                 border: 1px solid #ddd;
                 border-radius: 5px;
                 padding: 15px;
                 text-align: center;
                 box-shadow: 0 2px 4px rgba(0,0,0,0.08);
                 transition: all 0.2s ease-in-out;
                 display: flex; /* Use flexbox for better control */
                 flex-direction: column; /* Stack items vertically */
                 position: relative; /* For status badge positioning */
             }
             .anu:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
             .anu img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 10px auto; border: 3px solid #eee; }
             .anu h3 { font-size: 1.1em; margin: 5px 0; color: #333; }
             .anu .profession { font-size: 0.95em; font-weight: bold; color: #0056b3; margin-bottom: 8px; } /* Kept class name but refers to 'spos' */
             .anu .date { font-size: 0.8em; color: #888; margin-top: 5px; margin-bottom: 15px; } /* Space before buttons */

             /* Status Indicator */
             .status-badge {
                 position: absolute;
                 top: 10px;
                 right: 10px;
                 padding: 3px 8px;
                 border-radius: 10px;
                 font-size: 0.75em;
                 font-weight: bold;
                 color: white;
             }
             .status-badge.active { background-color: #28a745; } /* Green */
             .status-badge.inactive { background-color: #dc3545; } /* Red */

             /* Action Buttons Container */
             .actions {
                 margin-top: auto; /* Push actions to the bottom */
                 padding-top: 10px;
                 border-top: 1px solid #eee; /* Separator */
                 display: flex;
                 justify-content: space-around; /* Space out buttons */
                 gap: 5px; /* Gap between buttons */
             }
             .actions a, .actions button { /* Style links and buttons similarly */
                 padding: 5px 8px;
                 font-size: 0.8em;
                 border-radius: 3px;
                 text-decoration: none;
                 color: #fff;
                 border: none;
                 cursor: pointer;
                 transition: opacity 0.2s ease;
             }
             .actions a:hover, .actions button:hover { opacity: 0.85; }
             .btn-edit { background-color: #007bff; } /* Blue */
             .btn-activate { background-color: #28a745; } /* Green */
             .btn-deactivate { background-color: #ffc107; color: #333; } /* Yellow */
             .btn-delete { background-color: #dc3545; } /* Red */

         </style>
     </head>

     <body class="fixed-left">
         <div id="wrapper">
             <?php include('includes/topheader.php');?>
             <?php include('includes/leftsidebar.php');?>

             <div class="content-page">
                 <div class="content">
                     <div class="container">

                         <div class="row">
                             <div class="col-xs-12">
                                 <div class="page-title-box">
                                     <h4 class="page-title">Manage Staff Members</h4> <ol class="breadcrumb p-0 m-0"><li>Admin</li><li>Staff</li><li class="active">Manage</li></ol> <div class="clearfix"></div>
                                 </div>
                             </div>
                         </div>

                         <div class="row">
                             <div class="col-md-10 col-md-offset-1">

                                 <?php if ($message): ?><div class="alert-message alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                                 <?php if ($error): ?><div class="alert-message alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

                                 <div class="form-section">
                                     <h4>Add New Staff Member</h4> <form name="addstaff" method="post" enctype="multipart/form-data"> <div class="form-group m-b-20"><label for="sname">Name:</label><input type="text" class="form-control" id="sname" name="sname" placeholder="Enter full name" required value="<?php echo isset($_POST['sname']) && $error ? htmlspecialchars($_POST['sname']) : ''; ?>"></div>
                                         <div class="form-group m-b-20"><label for="spos">Position:</label><input type="text" class="form-control" id="spos" name="spos" placeholder="e.g., Teacher, Administrator" required value="<?php echo isset($_POST['spos']) && $error ? htmlspecialchars($_POST['spos']) : ''; ?>"></div> <div class="form-group m-b-20"><label for="stimg">Profile Photo:</label><input type="file" class="form-control-file" id="stimg" name="stimg" accept="image/png, image/jpeg, image/gif" required><small class="text-muted">Allowed: JPG, PNG, GIF. Max size: <?php echo ($max_file_size / 1024 / 1024); ?>MB.</small></div>
                                         <div class="form-group text-right m-b-0"><button class="btn btn-primary waves-effect waves-light" type="submit" name="submit">Add Staff Member</button><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-default waves-effect waves-light m-l-5">Reset Form</a></div> </form>
                                 </div>

                                 <div class="users-section">
                                     <h4>Current Staff Members</h4> <div class="users">
                                         <?php if (!empty($staff_members)): ?>
                                             <?php foreach ($staff_members as $staff_member): ?>
                                                 <div class="anu">
                                                     <span class="status-badge <?php echo $staff_member['status'] ? 'active' : 'inactive'; ?>">
                                                         <?php echo $staff_member['status'] ? 'Active' : 'Inactive'; ?>
                                                     </span>

                                                     <?php if (!empty($staff_member['stimg']) && file_exists($staff_member['stimg'])): ?>
                                                         <img src="<?php echo htmlspecialchars($staff_member['stimg']); ?>" alt="<?php echo htmlspecialchars($staff_member['sname']); ?>">
                                                     <?php else: ?>
                                                          <img src="assets/images/default-avatar.png" alt="Default Avatar"> {/* Adjust path if needed */}
                                                     <?php endif; ?>
                                                     <h3><?php echo htmlspecialchars($staff_member['sname']); ?></h3>
                                                     <h5 class="profession"><?php echo htmlspecialchars($staff_member['spos']); ?></h5> <?php
                                                     // Check if 'sed' is a valid date string before creating DateTime object
                                                     $created_date_str = $staff_member['sed'];
                                                     $created_date = DateTime::createFromFormat('Y-m-d H:i:s', $created_date_str);
                                                     $formatted_date = $created_date ? $created_date->format('M d, Y') : 'Invalid Date';
                                                     ?>
                                                     <h5 class="date">Added: <?php echo $formatted_date; ?></h5> <div class="actions">
                                                         <a href="edit_staff.php?id=<?php echo $staff_member['stid']; ?>" class="btn-edit" title="Edit Staff Member">Edit</a> <?php if ($staff_member['status']): ?>
                                                             <a href="?action=toggle_status&id=<?php echo $staff_member['stid']; /* &token=<?php echo $csrf_token; ?> */ ?>" class="btn-deactivate" title="Deactivate Staff Member">Deactivate</a> <?php else: ?>
                                                              <a href="?action=toggle_status&id=<?php echo $staff_member['stid']; /* &token=<?php echo $csrf_token; ?> */ ?>" class="btn-activate" title="Activate Staff Member">Activate</a> <?php endif; ?>

                                                          <a href="?action=delete&id=<?php echo $staff_member['stid']; /* &token=<?php echo $csrf_token; ?> */ ?>" class="btn-delete" title="Delete Staff Member" onclick="return confirm('Are you sure you want to delete this staff member? This cannot be undone.');">Delete</a> </div>
                                                  </div>
                                              <?php endforeach; ?>
                                          <?php else: ?>
                                              <p class="no-members">No staff members found.</p> <?php endif; ?>
                                      </div>
                                  </div>

                              </div> </div> </div> </div> <?php include('includes/footer.php');?>
          </div> </div> <script> var resizefunc = []; </script>
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