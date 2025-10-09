<?php
// It's good practice to start sessions or include global settings here if not in header.php
// session_start();
// error_reporting(E_ALL); // Recommended for development
// ini_set('display_errors', 1); // Recommended for development

include('include/header.php'); // Assuming your header.php sets up necessary things
?>

<style>
/* --- Basic Layout for Fixed Sidebar --- */
body {
    display: flex; /* Enable flexbox layout */
    margin-right: auto;
}

/* Assuming your sidebar has an ID or class like 'sidebar' */
/* Adjust selector and width as per your actual sidebar.php */

/* Adjust main content area to account for the fixed sidebar */
.page-container {
    margin-left: 300px; /* Adjust if your sidebar width is different */
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px); /* Adjust based on sidebar and right margin */
    min-width: 800px; /* Minimum width for content */
    height:100%;
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px; /* Adjusted from 0 to 10 */
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
}

:root {
    --primary-color: #007bff; /* Blue */
    --secondary-color: #6c757d; /* Gray */
    --success-color: #28a745; /* Green */
    --danger-color: #dc3545; /* Red */
    --warning-color: #ffc107; /* Yellow */
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --border-radius: 0.3rem;
    --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2, h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 600;
}
h3 {
    color: var(--dark-color);
    margin-top: 2rem;
    margin-bottom: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.message-area {
    padding: 12px 18px;
    margin-bottom: 25px;
    border-radius: var(--border-radius);
    border: 1px solid transparent;
    font-size: 0.95rem;
    text-align: center;
}
.message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.message-area.warning { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }


.form-section {
    margin-bottom: 30px;
    padding: 25px;
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
}
.form-section label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #555;
}
.form-section input[type="text"],
.form-section input[type="number"],
.form-section input[type="file"],
.form-section textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: var(--border-radius);
    box-sizing: border-box;
    font-size: 1rem;
}
.form-section input:read-only {
    background-color: #e9ecef;
    cursor: not-allowed;
}
/* Ensure TinyMCE editor respects border radius and standard border */
.tox-tinymce {
    border: 1px solid #ccc !important;
    border-radius: var(--border-radius) !important;
}
.form-section input[type="file"] {
    padding: 8px; /* Adjusted padding for file input */
    background-color: #fff; /* Ensure file input background is white */
}
.form-section .image-preview-container {
    margin-top: 10px;
    margin-bottom: 15px; /* Added margin for spacing */
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
/* Styling for individual image preview item in the form */
.form-section .image-preview-item {
    position: relative;
    display: inline-block; /* Allows items to sit side-by-side and wrap */
    margin: 5px; /* Spacing around items */
    border: 1px solid #eee;
    padding: 5px;
    background-color: #fff;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.form-section .image-preview-item img { /* Target img tag directly */
    max-width: 100px;
    max-height: 100px;
    display: block; /* remove extra space below image */
    object-fit: cover;
    border-radius: calc(var(--border-radius) - 2px); /* Slightly smaller radius than container */
}
.form-section .delete-existing-image-btn {
    position: absolute;
    top: -8px; /* Adjust to position nicely */
    right: -8px; /* Adjust to position nicely */
    background: var(--danger-color);
    color: white;
    border: 1px solid white;
    border-radius: 50%; /* Circular */
    width: 22px;
    height: 22px;
    font-size: 12px;
    line-height: 20px; /* Center the 'X' or symbol */
    text-align: center;
    cursor: pointer;
    padding: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.form-section .delete-existing-image-btn:hover {
    background-color: #c82333; /* Darker red on hover */
}
.form-section .delete-existing-image-btn:disabled {
    background-color: var(--secondary-color);
    cursor: not-allowed;
}
.form-section .image-preview-item.marked-for-deletion img {
    opacity: 0.5;
}


.form-section .button-group { margin-top: 15px; }
.form-section button {
    background-color: var(--primary-color);
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
    margin-right: 10px;
}
.form-section button:hover { background-color: #0056b3; }
.form-section button#cancelEdit { background-color: var(--secondary-color); }
.form-section button#cancelEdit:hover { background-color: #5a6268; }

.event-list-section { margin-top: 30px; overflow-x: auto; }
.event-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.event-table th, .event-table td {
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    text-align: left;
    vertical-align: middle;
}
.event-table th {
    background-color: #f2f5f8;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
}
.event-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.event-table tbody tr:hover { background-color: #e9ecef; }
.event-table .album-images-cell img {
    max-width: 60px; /* Smaller for table view */
    max-height: 60px;
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    margin: 2px;
    object-fit: cover;
}
.event-table .album-images-cell {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    min-width: 150px; /* Ensure some space for images */
}
.event-table .actions-cell { white-space: nowrap; min-width: 240px; }
.event-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
.event-table .actions-cell button,
.event-table .actions-cell .edit-btn { /* Applied common styles to edit-btn too */
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem;
    color: white;
    transition: background-color 0.2s ease;
    text-decoration: none; /* For edit button if it's an <a> styled as button */
    display: inline-block; /* For consistent spacing */
}
.event-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.event-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
.event-table .actions-cell button[name='action'][value='delete'] { background-color: var(--danger-color); }
.event-table .actions-cell button[name='action'][value='delete']:hover { background-color: #c82333; }
.event-table .actions-cell button[name='action'][value='activate'] { background-color: var(--success-color); }
.event-table .actions-cell button[name='action'][value='activate']:hover { background-color: #218838; }
.event-table .actions-cell button[name='action'][value='deactivate'] { background-color: var(--secondary-color); }
.event-table .actions-cell button[name='action'][value='deactivate']:hover { background-color: #5a6268; }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }
</style>

<?php include('include/sidebar.php'); // Assuming your sidebar.php ?>

<div class="page-container">

<h2>Event Management</h2>










<?php

include('include/config.php');

// Your database name - CHANGE THIS
$upload_dir_name = 'uploads_pdf';       // Name of the upload directory
$upload_path = __DIR__ . '/' . $upload_dir_name . '/'; // Absolute path to upload directory

// For development: Enable error display. Disable in production.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// -------------------- CREATE UPLOAD DIRECTORY IF NOT EXISTS --------------------
if (!is_dir($upload_path)) {
    if (!mkdir($upload_path, 0755, true)) { // 0755 permissions, recursive
        die("ERROR: Failed to create upload directory: " . htmlspecialchars($upload_path) . ". Please create it manually in the same directory as this script and ensure it's writable by the web server.");
    }
    // Create .htaccess in uploads directory for basic security on Apache
    $htaccess_content = "<Files *.php>\n    Deny from all\n</Files>\nOptions -Indexes\nRemoveHandler .php .phtml .php3\nRemoveType .php .phtml .php3\nphp_flag engine off\n<IfModule mod_mime.c>\n    AddType text/plain .php .phtml .php3 .pl .py .sh .cgi .js .exe .bat .com\n</IfModule>";
    @file_put_contents($upload_path . '.htaccess', $htaccess_content);
}

// -------------------- GLOBAL VARIABLES & MESSAGES --------------------
$success_message = '';
$error_message = '';
$edit_data = null; // For pre-filling update form

// Determine current action (from POST or GET)
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Retrieve session messages set by previous actions
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// -------------------- HELPER FUNCTION FOR REDIRECT --------------------

$current_script_url = 'block.php';

// -------------------- ACTION HANDLERS --------------------

// --- UPLOAD ACTION ---
if ($action === 'upload' && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_upload'])) {
    $exam_title = trim($_POST['exam_title']);
    $exam_session = trim($_POST['exam_session']);
    $exam_type = trim($_POST['exam_type']);
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    $upload_date = date('Y-m-d');
    $created_at = $updated_at = date('Y-m-d H:i:s');

    if (empty($exam_title) || empty($exam_session) || empty($exam_type)) {
        $_SESSION['error_message'] = "Error: All fields (Title, Session, Type) are required.";
        redirect($current_script_url);
    }

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['pdf_file']['tmp_name'];
        $file_name = $_FILES['pdf_file']['name'];
        $file_size = $_FILES['pdf_file']['size'];
        // $file_type = $_FILES['pdf_file']['type']; // Less reliable
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $safe_original_filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($file_name));
        $unique_file_name = uniqid('', true) . '_' . $safe_original_filename;
        $file_path_on_server = $upload_path . $unique_file_name;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp_name);
        finfo_close($finfo);

        if ($file_ext !== 'pdf' || $mime_type !== 'application/pdf') {
            $_SESSION['error_message'] = "Error: Only PDF files are allowed. (Checked ext: {$file_ext}, mime: {$mime_type})";
        } elseif ($file_size > 10 * 1024 * 1024) { // Max 10MB
            $_SESSION['error_message'] = "Error: File is too large. Maximum size is 10MB.";
        } else {
            if (move_uploaded_file($file_tmp_name, $file_path_on_server)) {
                $sql_insert = "INSERT INTO results (exam_title, exam_session, exam_type, upload_date, file_path, is_active, created_at, updated_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                if ($stmt_insert) {
                    $stmt_insert->bind_param("sssssiss", $exam_title, $exam_session, $exam_type, $upload_date, $unique_file_name, $is_active, $created_at, $updated_at);
                    if ($stmt_insert->execute()) {
                        $_SESSION['success_message'] = "PDF uploaded and record created successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error creating record: " . htmlspecialchars($stmt_insert->error);
                        unlink($file_path_on_server); // Delete uploaded file if DB insert fails
                    }
                    $stmt_insert->close();
                } else {
                    $_SESSION['error_message'] = "Error preparing insert statement: " . htmlspecialchars($conn->error);
                    unlink($file_path_on_server);
                }
            } else {
                $_SESSION['error_message'] = "Error: Failed to move uploaded file. Check directory permissions.";
            }
        }
    } elseif (isset($_FILES['pdf_file']['error']) && $_FILES['pdf_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $upload_errors = [ /* ... as defined in previous full example ... */ ];
        $_SESSION['error_message'] = "File upload error: " . ($upload_errors[$_FILES['pdf_file']['error']] ?? "Unknown upload error code: {$_FILES['pdf_file']['error']}");
    } else {
        $_SESSION['error_message'] = "Error: No file was uploaded or an unknown error occurred.";
    }
}

// --- UPDATE ACTION ---
if ($action === 'update' && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
    $result_id = isset($_POST['result_id']) ? (int)$_POST['result_id'] : 0;
    $exam_title = trim($_POST['exam_title']);
    $exam_session = trim($_POST['exam_session']);
    $exam_type = trim($_POST['exam_type']);
    $upload_date_str = trim($_POST['upload_date']);
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    $current_file_db_path = trim($_POST['current_file_path']); // Filename stored in DB
    $updated_at = date('Y-m-d H:i:s');
    $new_file_name_for_db = $current_file_db_path; // Assume current file path initially

    if (empty($exam_title) || empty($exam_session) || empty($exam_type) || empty($upload_date_str) || $result_id <= 0) {
        $_SESSION['error_message'] = "Error: All fields are required for update.";
        redirect($current_script_url . "?action=edit&id=" . $result_id); // Redirect back to edit form
    }

    $date_format = 'Y-m-d';
    $d = DateTime::createFromFormat($date_format, $upload_date_str);
    if (!$d || $d->format($date_format) !== $upload_date_str) {
        $_SESSION['error_message'] = "Error: Invalid upload date format. Please use YYYY-MM-DD.";
        redirect($current_script_url . "?action=edit&id=" . $result_id);
    }
    $upload_date = $upload_date_str;

    $temp_error_message = ''; // Temporary error for file handling within this block

    if (isset($_FILES['new_pdf_file']) && $_FILES['new_pdf_file']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['new_pdf_file']['tmp_name'];
        $file_name = $_FILES['new_pdf_file']['name'];
        $file_size = $_FILES['new_pdf_file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $safe_original_filename = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($file_name));
        $unique_new_file_name = uniqid('', true) . '_' . $safe_original_filename;
        $new_file_path_on_server = $upload_path . $unique_new_file_name;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp_name);
        finfo_close($finfo);

        if ($file_ext !== 'pdf' || $mime_type !== 'application/pdf') {
            $temp_error_message = "Error: New file must be a PDF. (Checked ext: {$file_ext}, mime: {$mime_type})";
        } elseif ($file_size > 10 * 1024 * 1024) { // Max 10MB
            $temp_error_message = "Error: New file is too large. Maximum size is 10MB.";
        } else {
            if (move_uploaded_file($file_tmp_name, $new_file_path_on_server)) {
                $new_file_name_for_db = $unique_new_file_name; // Update to new filename for DB
                // Delete old file if it exists and is different from the new one
                if (!empty($current_file_db_path) && file_exists($upload_path . $current_file_db_path) && $current_file_db_path !== $new_file_name_for_db) {
                    @unlink($upload_path . $current_file_db_path);
                }
            } else {
                $temp_error_message = "Error: Failed to move new uploaded file.";
            }
        }
    } elseif (isset($_FILES['new_pdf_file']['error']) && $_FILES['new_pdf_file']['error'] != UPLOAD_ERR_NO_FILE) {
        $temp_error_message = "Error uploading new file.";
    }

    if (!empty($temp_error_message)) {
        $_SESSION['error_message'] = $temp_error_message;
    } else {
        // Proceed with DB update
        $sql_update = "UPDATE results SET exam_title = ?, exam_session = ?, exam_type = ?,
                       upload_date = ?, file_path = ?, is_active = ?, updated_at = ?
                       WHERE result_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("sssssisi", $exam_title, $exam_session, $exam_type, $upload_date, $new_file_name_for_db, $is_active, $updated_at, $result_id);
            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = "Result updated successfully!";
            } else {
                $_SESSION['error_message'] = "Error updating record: " . htmlspecialchars($stmt_update->error);
                // If DB update failed AND a new file was uploaded, consider deleting the newly uploaded file
                if (isset($new_file_path_on_server) && file_exists($new_file_path_on_server) && $new_file_name_for_db !== $current_file_db_path) {
                    @unlink($new_file_path_on_server);
                }
            }
            $stmt_update->close();
        } else {
            $_SESSION['error_message'] = "Error preparing update statement: " . htmlspecialchars($conn->error);
        }
    }
    // Redirect to main page after update attempt, or back to edit form if significant error
    if (!empty($_SESSION['error_message']) && $action === 'update') {
         redirect($current_script_url . "?action=edit&id=" . $result_id);
    } else {
         redirect($current_script_url);
    }
}


// --- DELETE ACTION ---
if ($action === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $result_id_to_delete = (int)$_GET['id'];
    $file_to_delete_from_server = null;

    // First, get the file path from DB
    $stmt_get_path = $conn->prepare("SELECT file_path FROM results WHERE result_id = ?");
    if ($stmt_get_path) {
        $stmt_get_path->bind_param("i", $result_id_to_delete);
        $stmt_get_path->execute();
        $stmt_get_path->bind_result($db_file_path);
        if ($stmt_get_path->fetch()) {
            $file_to_delete_from_server = $upload_path . basename($db_file_path);
        }
        $stmt_get_path->close();
    } else {
        $_SESSION['error_message'] = "Error fetching file path for deletion: " . htmlspecialchars($conn->error);
        redirect($current_script_url);
    }

    // Then, delete the DB record
    $stmt_delete_db = $conn->prepare("DELETE FROM results WHERE result_id = ?");
    if ($stmt_delete_db) {
        $stmt_delete_db->bind_param("i", $result_id_to_delete);
        if ($stmt_delete_db->execute()) {
            if ($stmt_delete_db->affected_rows > 0) {
                // DB record deleted, now delete the file
                if ($file_to_delete_from_server && file_exists($file_to_delete_from_server)) {
                    if (unlink($file_to_delete_from_server)) {
                        $_SESSION['success_message'] = "Result and associated PDF file deleted successfully!";
                    } else {
                        $_SESSION['warning_message'] = "Result record deleted, but failed to delete the PDF file from the server. File: " . htmlspecialchars($file_to_delete_from_server); // Use warning for partial success
                    }
                } else {
                    $_SESSION['success_message'] = "Result record deleted. Associated file not found or already removed.";
                }
            } else {
                $_SESSION['warning_message'] = "Warning: No record found with ID " . $result_id_to_delete . " to delete, or no changes made.";
            }
        } else {
            $_SESSION['error_message'] = "Error deleting record: " . htmlspecialchars($stmt_delete_db->error);
        }
        $stmt_delete_db->close();
    } else {
        $_SESSION['error_message'] = "Error preparing delete statement: " . htmlspecialchars($conn->error);
    }
}

// --- PREPARE DATA FOR EDIT FORM ---
if ($action === 'edit' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $result_id_to_edit = (int)$_GET['id'];
    $sql_edit = "SELECT result_id, exam_title, exam_session, exam_type, upload_date, file_path, is_active FROM results WHERE result_id = ?";
    $stmt_edit_form = $conn->prepare($sql_edit);
    if ($stmt_edit_form) {
        $stmt_edit_form->bind_param("i", $result_id_to_edit);
        $stmt_edit_form->execute();
        $result_edit_query = $stmt_edit_form->get_result();
        if ($result_edit_query->num_rows === 1) {
            $edit_data = $result_edit_query->fetch_assoc();
        } else {
            $_SESSION['error_message'] = "Record with ID " . $result_id_to_edit . " not found for editing.";
            // Do not redirect here, let the main page load and show the error.
            // $edit_data will remain null, so upload form will show.
        }
        $stmt_edit_form->close();
    } else {
        $_SESSION['error_message'] = "Error preparing to fetch record for edit: " . htmlspecialchars($conn->error);
    }
}

// -------------------- FETCH EXISTING RESULTS FOR DISPLAY TABLE --------------------
$search_title_filter = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
$filter_session_val = isset($_GET['filter_session']) ? trim($_GET['filter_session']) : '';
$filter_type_val = isset($_GET['filter_type']) ? trim($_GET['filter_type']) : '';

$sql_select_all = "SELECT result_id, exam_title, exam_session, exam_type, upload_date, file_path, is_active FROM results WHERE 1=1";
$params_select = [];
$types_select = '';

if (!empty($search_title_filter)) {
    $sql_select_all .= " AND exam_title LIKE ?";
    $params_select[] = "%" . $search_title_filter . "%";
    $types_select .= 's';
}
if (!empty($filter_session_val)) {
    $sql_select_all .= " AND exam_session = ?";
    $params_select[] = $filter_session_val;
    $types_select .= 's';
}
if (!empty($filter_type_val)) {
    $sql_select_all .= " AND exam_type = ?";
    $params_select[] = $filter_type_val;
    $types_select .= 's';
}
$sql_select_all .= " ORDER BY created_at DESC";

$stmt_select_all_display = $conn->prepare($sql_select_all);
$all_results_display = null;
if ($stmt_select_all_display) {
    if (!empty($params_select)) {
        $stmt_select_all_display->bind_param($types_select, ...$params_select);
    }
    $stmt_select_all_display->execute();
    $all_results_display = $stmt_select_all_display->get_result();
} else {
    $error_message .= (empty($error_message) ? '' : '<br>') . "Error preparing statement to fetch results: " . htmlspecialchars($conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exam Results (PDFs)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1, h2 { color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .message { padding: 10px 15px; margin-bottom: 15px; border-radius: 4px; border: 1px solid transparent; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        form div { margin-bottom: 12px; }
        form label { display: block; margin-bottom: 5px; font-weight: bold; }
        form input[type="text"], form input[type="date"], form select, form input[type="file"] {
            width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;
        }
        form input[type="file"] { padding: 7px; }
        form button {
            background-color: #007bff; color: white; padding: 10px 15px; border: none;
            border-radius: 4px; cursor: pointer; font-size: 1em;
        }
        form button:hover { background-color: #0056b3; }
        form button.update-btn { background-color: #28a745; }
        form button.update-btn:hover { background-color: #1e7e34; }
        .cancel-update {
            display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white;
            text-decoration: none; border-radius: 4px; margin-left: 10px;
        }
        .cancel-update:hover { background-color: #545b62; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; }
        .actions a { margin-right: 8px; text-decoration: none; padding: 5px 8px; border-radius: 3px; color: white; }
        .actions .view { background-color: #17a2b8; }
        .actions .edit { background-color: #ffc107; color: #212529; }
        .actions .delete { background-color: #dc3545; }
        .current-file { font-style: italic; color: #555; margin-top: 5px; }
        .current-file a { color: #007bff; }
        .filter-form { margin-bottom: 20px; padding: 15px; background-color: #e9ecef; border-radius: 5px; display: flex; gap: 10px; align-items: center;}
        .filter-form input[type="text"] { width: auto; flex-grow: 1; }
        .filter-form button, .filter-form a.clear-filter { padding: 10px 15px; text-decoration: none; }
        .filter-form a.clear-filter { background-color: #6c757d; color:white; border-radius: 4px;}
        .filter-form a.clear-filter:hover { background-color: #5a6268;}
        hr { margin: 30px 0; border: 0; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Exam Results (PDFs)</h1>

        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
        <?php if ($error_message): /* Error message can contain HTML for multiple errors */ ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['warning_message'])): ?>
            <div class="message warning"><?php echo htmlspecialchars($_SESSION['warning_message']); unset($_SESSION['warning_message']); ?></div>
        <?php endif; ?>


        <?php if ($edit_data): ?>
            <h2>Update Result (ID: <?php echo htmlspecialchars($edit_data['result_id']); ?>)</h2>
            <form action="<?php echo $current_script_url; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="result_id" value="<?php echo htmlspecialchars($edit_data['result_id']); ?>">
                <input type="hidden" name="current_file_path" value="<?php echo htmlspecialchars($edit_data['file_path']); ?>">
                <div>
                    <label for="update_exam_title">Exam Title:</label>
                    <input type="text" id="update_exam_title" name="exam_title" value="<?php echo htmlspecialchars($edit_data['exam_title']); ?>" required>
                </div>
                <div>
                    <label for="update_exam_session">Exam Session (e.g., 2024-May):</label>
                    <input type="text" id="update_exam_session" name="exam_session" value="<?php echo htmlspecialchars($edit_data['exam_session']); ?>" required>
                </div>
                <div>
                    <label for="update_exam_type">Exam Type (e.g., Midterm, Final):</label>
                    <input type="text" id="update_exam_type" name="exam_type" value="<?php echo htmlspecialchars($edit_data['exam_type']); ?>" required>
                </div>
                <div>
                    <label for="update_upload_date">Upload Date (YYYY-MM-DD):</label>
                    <input type="date" id="update_upload_date" name="upload_date" value="<?php echo htmlspecialchars($edit_data['upload_date']); ?>" required>
                </div>
                <div>
                    <label for="update_is_active">Status:</label>
                    <select id="update_is_active" name="is_active">
                        <option value="1" <?php echo ($edit_data['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo ($edit_data['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="new_pdf_file">New PDF File (Optional - leave blank to keep current):</label>
                    <?php if (!empty($edit_data['file_path'])): ?>
                        <p class="current-file">Current:
                            <a href="<?php echo htmlspecialchars($upload_dir_name . '/' . basename($edit_data['file_path'])); ?>" target="_blank">
                                <?php echo htmlspecialchars(basename($edit_data['file_path'])); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <input type="file" id="new_pdf_file" name="new_pdf_file" accept=".pdf">
                </div>
                <button type="submit" name="submit_update" class="update-btn">Update Result</button>
                <a href="<?php echo $current_script_url; ?>" class="cancel-update">Cancel Update</a>
            </form>
        <?php else: ?>
            <h2>Upload New Result PDF</h2>
            <form action="<?php echo $current_script_url; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                <div>
                    <label for="exam_title">Exam Title:</label>
                    <input type="text" id="exam_title" name="exam_title" required value="<?php echo isset($_POST['exam_title']) ? htmlspecialchars($_POST['exam_title']) : ''; ?>">
                </div>
                <div>
                    <label for="exam_session">Exam Session (e.g., 2024-May):</label>
                    <input type="text" id="exam_session" name="exam_session" required value="<?php echo isset($_POST['exam_session']) ? htmlspecialchars($_POST['exam_session']) : ''; ?>">
                </div>
                <div>
                    <label for="exam_type">Exam Type (e.g., Midterm, Final):</label>
                    <input type="text" id="exam_type" name="exam_type" required value="<?php echo isset($_POST['exam_type']) ? htmlspecialchars($_POST['exam_type']) : ''; ?>">
                </div>
                <div>
                    <label for="pdf_file">PDF File:</label>
                    <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required>
                </div>
                <div>
                    <label for="is_active">Status:</label>
                    <select id="is_active" name="is_active">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="submit_upload">Upload PDF</button>
            </form>
        <?php endif; ?>

        <hr>

        <h2>Existing Results</h2>
        <form method="get" action="<?php echo $current_script_url; ?>" class="filter-form">
            <input type="text" name="search_title" placeholder="Search by Title..." value="<?php echo htmlspecialchars($search_title_filter); ?>">
            <input type="text" name="filter_session" placeholder="Filter by Session..." value="<?php echo htmlspecialchars($filter_session_val); ?>">
            <input type="text" name="filter_type" placeholder="Filter by Type..." value="<?php echo htmlspecialchars($filter_type_val); ?>">
            <button type="submit">Filter</button>
            <a href="<?php echo $current_script_url; ?>" class="clear-filter">Clear</a>
        </form>

        <?php if ($all_results_display && $all_results_display->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam Title</th>
                        <th>Session</th>
                        <th>Type</th>
                        <th>Upload Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $all_results_display->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['result_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_session']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_type']); ?></td>
                        <td><?php echo htmlspecialchars(date("M d, Y", strtotime($row['upload_date']))); ?></td>
                        <td><?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td class="actions">
                            <?php if (!empty($row['file_path'])): ?>
                            <a href="<?php echo htmlspecialchars($upload_dir_name . '/' . basename($row['file_path'])); ?>" target="_blank" class="view">View</a>
                            <?php endif; ?>
                            <a href="<?php echo $current_script_url; ?>?action=edit&id=<?php echo $row['result_id']; ?>" class="edit">Edit</a>
                            <a href="<?php echo $current_script_url; ?>?action=delete&id=<?php echo $row['result_id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this result and its PDF file? This action cannot be undone.');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No results found matching your criteria.</p>
        <?php endif; ?>

    </div>
</body>
</html>
<?php
// Close statement and connection
if (isset($stmt_select_all_display) && $stmt_select_all_display) $stmt_select_all_display->close();
$conn->close();
?>






    
</div> <script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'code lists link image media table wordcount fullscreen preview searchreplace help',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code | fullscreen preview | searchreplace | help',
        height: 300,
        menubar: 'file edit view insert format tools table help',
        // Removed image upload handler as it's handled by the main form file input
        // setup: function (editor) {
        //     editor.on('change', function () {
        //         tinymce.triggerSave();
        //     });
        // }
    });

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    const formTitle = document.getElementById('formTitle');
    const formActionInput = document.getElementById('formAction');
    const existingAlbumIdInput = document.getElementById('existing_album_id');
    const eidInput = document.getElementById('eid');
    const etitleInput = document.getElementById('etitle');
    const etagInput = document.getElementById('etag');
    // const etextTextarea = document.getElementById('etext'); // For TinyMCE, interact via its API
    const submitButton = document.getElementById('submitButton');
    const cancelEditButton = document.getElementById('cancelEdit');
    const currentImagesPreviewContainer = document.getElementById('current_eimgs_preview_container');
    const imagesToDeleteContainer = document.getElementById('images_to_delete_container');
    const newImagesInput = document.getElementById('eimgs');
    const currentImagesSection = document.getElementById('current-images-section');

    function resetFormToDefaults() {
        form.reset(); // Resets native form elements
        formTitle.textContent = 'Add New Event';
        formActionInput.value = 'insert';
        existingAlbumIdInput.value = '';
        eidInput.readOnly = false;
        eidInput.value = ''; 
        etitleInput.value = '';
        etagInput.value = '';
        if (tinymce.get('etext')) {
            tinymce.get('etext').setContent('');
        }
        currentImagesPreviewContainer.innerHTML = '';
        imagesToDeleteContainer.innerHTML = ''; // Clear hidden inputs for deletion
        newImagesInput.value = ''; // Clear file input
        currentImagesSection.style.display = 'none';
        cancelEditButton.style.display = 'none';
        submitButton.textContent = 'Save Event';
        window.scrollTo(0, form.offsetTop - 20); // Scroll to form
    }

    cancelEditButton.addEventListener('click', function() {
        resetFormToDefaults();
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const eventData = this.dataset;

            formTitle.textContent = 'Edit Event: ' + eventData.etitle;
            formActionInput.value = 'update';
            existingAlbumIdInput.value = eventData.albumid || '';
            
            eidInput.value = eventData.eid;
            eidInput.readOnly = true;

            etitleInput.value = eventData.etitle;
            etagInput.value = eventData.etag;

            if (tinymce.get('etext')) {
                tinymce.get('etext').setContent(eventData.etext || '');
            } else {
                // Fallback if TinyMCE hasn't initialized yet for some reason (should not happen with DOMContentLoaded)
                document.getElementById('etext').value = eventData.etext || '';
            }
            
            submitButton.textContent = 'Update Event';
            cancelEditButton.style.display = 'inline-block';
            currentImagesSection.style.display = 'block';

            // Clear previous previews and deletion markers
            currentImagesPreviewContainer.innerHTML = '';
            imagesToDeleteContainer.innerHTML = '';
            newImagesInput.value = ''; // Clear file input for new images

            if (eventData.images) {
                const imagePaths = eventData.images.split('||').filter(path => path.trim() !== '');
                if (imagePaths.length > 0) {
                    currentImagesSection.style.display = 'block';
                    imagePaths.forEach(path => {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'image-preview-item';

                        const img = document.createElement('img');
                        // Add a cache-busting query parameter to ensure fresh image is shown
                        img.src = path + '?t=' + new Date().getTime(); 
                        img.alt = 'Existing image';
                        img.className = 'image-preview'; // from your CSS

                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.className = 'delete-existing-image-btn';
                        deleteBtn.innerHTML = '&times;'; // 'X' symbol
                        deleteBtn.title = 'Mark for deletion';

                        deleteBtn.addEventListener('click', function() {
                            previewItem.classList.add('marked-for-deletion');
                            this.disabled = true; // Prevent multiple clicks
                            this.textContent = '✓'; // Indicate it's marked

                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'images_to_delete[]';
                            hiddenInput.value = path;
                            imagesToDeleteContainer.appendChild(hiddenInput);
                        });

                        previewItem.appendChild(img);
                        previewItem.appendChild(deleteBtn);
                        currentImagesPreviewContainer.appendChild(previewItem);
                    });
                } else {
                     currentImagesSection.style.display = 'none'; // Hide if no images
                }
            } else {
                 currentImagesSection.style.display = 'none'; // Hide if no images string
            }
            window.scrollTo(0, form.offsetTop - 20); // Scroll to form for better UX
        });
    });
});

</script>
<?php
// It's good practice to close the connection if it was opened.
// Assuming your footer.php might handle this or it's handled by PHP at script end.
// if (isset($conn) && $conn instanceof mysqli) {
// $conn->close();
// }
// include('include/footer.php'); // If you have a footer file
?>
</body>
</html>