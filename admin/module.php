<?php
// It's good practice to start sessions or include global settings here if not in header.php
// session_start();
error_reporting(E_ALL); // Recommended for development
ini_set('display_errors', 1); // Recommended for development

include('include/header.php'); // Assuming your header.php sets up necessary things
include('../include/config.php'); // DB constants: DB_SERVER, DB_USER, DB_PASS, DB_NAME

// --- Message Variables ---
$message = '';
$message_type = ''; // 'success', 'error', or 'warning'

// --- Data for Edit (if editing) ---
$edit_module_data = null;
$is_editing = false;

// --- Selected Course for Viewing Modules ---
$selected_cid_to_view = null;
$selected_course_name = ''; // For displaying "Modules for [Course Name]"

if (isset($_GET['view_course_cid']) && !empty(trim($_GET['view_course_cid']))) {
    $selected_cid_to_view = trim($_GET['view_course_cid']);

    // Fetch the course name for display
    $conn_get_cname = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (!$conn_get_cname->connect_error) {
        $conn_get_cname->set_charset("utf8mb4");
        $stmt_get_cname = $conn_get_cname->prepare("SELECT cname FROM course WHERE cid = ? LIMIT 1");
        if ($stmt_get_cname) {
            $stmt_get_cname->bind_param("s", $selected_cid_to_view);
            $stmt_get_cname->execute();
            $result_cname = $stmt_get_cname->get_result();
            if ($result_cname->num_rows > 0) {
                $selected_course_name = $result_cname->fetch_assoc()['cname'];
            }
            $stmt_get_cname->close();
        }
        $conn_get_cname->close();
    }
}


// ========================================================================
//  HANDLE INCOMING MESSAGES FROM REDIRECTS (PRG Pattern)
// ========================================================================
if (isset($_GET['success'])) {
    $message_type = 'success';
    $module_identifier = isset($_GET['module_code']) ? htmlspecialchars($_GET['module_code']) : 'the module';
    if ($_GET['success'] == '1') $message = "New module '" . $module_identifier . "' added successfully!";
    elseif ($_GET['success'] == '2') $message = "Module '" . $module_identifier . "' updated successfully!";
    elseif ($_GET['success'] == '3') $message = "Module '" . $module_identifier . "' deleted successfully!";
    elseif ($_GET['success'] == '4') $message = "Module '" . $module_identifier . "' activated successfully!";
    elseif ($_GET['success'] == '5') $message = "Module '" . $module_identifier . "' deactivated successfully!";
} elseif (isset($_GET['error'])) {
    $message_type = 'error';
    $message = htmlspecialchars(urldecode($_GET['error']));
} elseif (isset($_GET['warning'])) { // Added for warnings (e.g., no change on update)
    $message_type = 'warning';
    $message = htmlspecialchars(urldecode($_GET['warning']));
}


// ========================================================================
//  FETCH ALL COURSES (for the CID dropdown and course list)
// ========================================================================
$courses = [];
$conn_courses = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if ($conn_courses->connect_error) {
    if (empty($message_type)) {
        $message_type = 'error';
        $message = "Database Connection failed to fetch courses: " . $conn_courses->connect_error;
    }
    error_log("DB Connect Error (fetch courses): " . $conn_courses->connect_error);
} else {
    $conn_courses->set_charset("utf8mb4");
    $sql_courses = "SELECT cid, cname FROM course ORDER BY cname";
    $result_courses = $conn_courses->query($sql_courses);
    if ($result_courses === false) {
        if (empty($message_type)) {
            $message_type = 'error';
            $message = "Error fetching courses: " . $conn_courses->error;
        }
        error_log("SQL Select Error (fetch courses): " . $conn_courses->error);
    } elseif ($result_courses->num_rows > 0) {
        while ($row = $result_courses->fetch_assoc()) {
            $courses[] = $row;
        }
        $result_courses->free();
    }
    $conn_courses->close();
}


// ========================================================================
//  HANDLE EDIT REQUEST (GET Request with 'edit' parameter)
// ========================================================================
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit']) && empty($_POST) && empty($message_type)) {
    $module_code_to_edit = trim($_GET['edit']);
    if (!empty($module_code_to_edit)) {
        $conn_edit = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if ($conn_edit->connect_error) {
            $message_type = 'error';
            $message = "Database Connection failed for edit: " . $conn_edit->connect_error;
            error_log("DB Connect Error (fetch module for edit): " . $conn_edit->connect_error);
        } else {
            $conn_edit->set_charset("utf8mb4");
            $sql_edit = "SELECT * FROM modules WHERE module_code = ? LIMIT 1";
            $stmt_edit = $conn_edit->prepare($sql_edit);
            if ($stmt_edit === false) {
                error_log("SQL Prepare Error (fetch module for edit): " . $conn_edit->error);
                $message_type = 'error';
                $message = "Database error (prepare fetch for edit).";
            } else {
                $stmt_edit->bind_param("s", $module_code_to_edit);
                $stmt_edit->execute();
                $result_edit = $stmt_edit->get_result();
                if ($result_edit === false) {
                    error_log("SQL Execute Error (fetch module for edit): " . $stmt_edit->error);
                    $message_type = 'error';
                    $message = "Database error (execute fetch for edit).";
                } elseif ($result_edit->num_rows == 1) {
                    $edit_module_data = $result_edit->fetch_assoc();
                    $is_editing = true;
                } else {
                    $message_type = 'error';
                    $message = "Module '" . htmlspecialchars($module_code_to_edit) . "' not found for editing.";
                }
                $stmt_edit->close();
            }
            $conn_edit->close();
        }
    } else {
        $message_type = 'error';
        $message = "Invalid module code provided for editing.";
    }
}

// ========================================================================
//  HANDLE FORM SUBMISSION (ADD/UPDATE/DELETE/ACTIVATE/DEACTIVATE - POST Request)
// ========================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($message_type)) { // empty($message_type) check might be too restrictive if a minor DB error happened earlier but POST is still valid
    $action = $_POST['action'] ?? 'insert';
    $submitted_view_cid = $_POST['current_view_cid'] ?? null; // Get the course view context from form

    $conn_submit = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn_submit->connect_error) {
        $message_type = 'error';
        $message = "Database Connection failed for submission: " . $conn_submit->connect_error;
        error_log("DB Submit Connect Error: " . $conn_submit->connect_error);
    } else {
        $conn_submit->set_charset("utf8mb4");
        $redirect_base_url = $_SERVER['PHP_SELF'];
        $redirect_view_cid_param = '';

        // Determine the CID for redirecting to keep course context
        $cid_for_redirect_after_action = null;
        if (($action == 'insert' || $action == 'update') && isset($_POST['cid'])) {
            $cid_for_redirect_after_action = $_POST['cid'];
        } elseif ($submitted_view_cid) {
            $cid_for_redirect_after_action = $submitted_view_cid;
        }
        if ($cid_for_redirect_after_action) {
            $redirect_view_cid_param = "&view_course_cid=" . urlencode($cid_for_redirect_after_action);
        }


        if ($action == 'insert') {
            $module_code = trim($_POST['module_code'] ?? '');
            $module_title = trim($_POST['module_title'] ?? '');
            $module_type = trim($_POST['module_type'] ?? '');
            $credits = isset($_POST['credits']) ? intval($_POST['credits']) : 0;
            $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
            $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
            $semester = isset($_POST['semester']) ? intval($_POST['semester']) : 0;
            $cid = trim($_POST['cid'] ?? '');

            $validation_errors = [];
            if (empty($module_code)) $validation_errors[] = "Module Code is required.";
            // ... (rest of your validations) ...
            if (empty($module_title)) $validation_errors[] = "Module Title is required.";
            if (empty($module_type)) $validation_errors[] = "Module Type is required.";
            if ($credits <= 0) $validation_errors[] = "Credits must be a positive number.";
            if (!in_array($status, [0, 1])) $validation_errors[] = "Invalid status value.";
            if ($year <= 0 || $year > 4) $validation_errors[] = "Invalid year value.";
            if ($semester != 1 && $semester != 2) $validation_errors[] = "Semester must be 1 or 2.";
            if (empty($cid)) $validation_errors[] = "Course is required.";


            if (!empty($validation_errors)) {
                $message_type = 'error';
                $message = "Validation failed: " . implode(" ", $validation_errors);
            } else {
                $sql_submit = "INSERT INTO modules (module_code, module_title, module_type, credits, status, year, semester, cid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_submit = $conn_submit->prepare($sql_submit);
                if ($stmt_submit === false) {
                    error_log("SQL Prepare Error (Insert Module): " . $conn_submit->error);
                    $message_type = 'error'; $message = "Database error (prepare insert).";
                } else {
                    $stmt_submit->bind_param("sssiiiss", $module_code, $module_title, $module_type, $credits, $status, $year, $semester, $cid);
                    if ($stmt_submit->execute()) {
                        $stmt_submit->close(); $conn_submit->close();
                        header("Location: " . $redirect_base_url . "?success=1&module_code=" . urlencode($module_code) . $redirect_view_cid_param);
                        exit();
                    } else {
                        error_log("SQL Execute Error (Insert Module): " . $stmt_submit->error);
                        if ($conn_submit->errno == 1062) {
                            $message_type = 'error'; $message = "Module code '" . htmlspecialchars($module_code) . "' already exists.";
                        } else {
                            $message_type = 'error'; $message = "Database error (execute insert): " . $stmt_submit->error;
                        }
                    }
                    $stmt_submit->close();
                }
            }
        } elseif ($action == 'update') {
            $original_module_code = trim($_POST['original_module_code'] ?? '');
            $module_title = trim($_POST['module_title'] ?? '');
            $module_type = trim($_POST['module_type'] ?? '');
            $credits = isset($_POST['credits']) ? intval($_POST['credits']) : 0;
            // status is handled by activate/deactivate
            $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
            $semester = isset($_POST['semester']) ? intval($_POST['semester']) : 0;
            $cid = trim($_POST['cid'] ?? '');

            $validation_errors = [];
            if (empty($original_module_code)) $validation_errors[] = "Original module code is missing for update.";
            // ... (rest of your validations for update) ...
            if (empty($module_title)) $validation_errors[] = "Module Title is required.";
            if (empty($module_type)) $validation_errors[] = "Module Type is required.";
            if ($credits <= 0) $validation_errors[] = "Credits must be a positive number.";
            if ($year <= 0 || $year > 4) $validation_errors[] = "Invalid year value.";
            if ($semester != 1 && $semester != 2) $validation_errors[] = "Semester must be 1 or 2.";
            if (empty($cid)) $validation_errors[] = "Course is required.";

            if (!empty($validation_errors)) {
                $message_type = 'error';
                $message = "Validation failed: " . implode(" ", $validation_errors);
                // For update validation error, redirect back to edit form with error
                $redirect_url = $redirect_base_url . "?error=" . urlencode($message);
                if (!empty($original_module_code)) {
                    $redirect_url .= "&edit=" . urlencode($original_module_code);
                }
                $redirect_url .= $redirect_view_cid_param; // Keep course context
                $conn_submit->close();
                header("Location: " . $redirect_url);
                exit();
            } else {
                $sql_submit = "UPDATE modules SET module_title = ?, module_type = ?, credits = ?, year = ?, semester = ?, cid = ? WHERE module_code = ?";
                $stmt_submit = $conn_submit->prepare($sql_submit);
                if ($stmt_submit === false) {
                    error_log("SQL Prepare Error (Update Module): " . $conn_submit->error);
                    $message_type = 'error'; $message = "Database error (prepare update).";
                } else {
                    $stmt_submit->bind_param("ssiiiss", $module_title, $module_type, $credits, $year, $semester, $cid, $original_module_code);
                    if ($stmt_submit->execute()) {
                        if ($stmt_submit->affected_rows > 0) {
                            $stmt_submit->close(); $conn_submit->close();
                            header("Location: " . $redirect_base_url . "?success=2&module_code=" . urlencode($original_module_code) . $redirect_view_cid_param);
                            exit();
                        } else {
                            // No rows affected, but successful execution (data might be the same)
                            $stmt_submit->close(); $conn_submit->close();
                            $warning_message = "Module '" . htmlspecialchars($original_module_code) . "' updated, but no changes were made.";
                            header("Location: " . $redirect_base_url . "?warning=" . urlencode($warning_message) . "&module_code=" . urlencode($original_module_code) . $redirect_view_cid_param);
                            exit();
                        }
                    } else {
                        error_log("SQL Execute Error (Update Module): " . $stmt_submit->error);
                        $message_type = 'error'; $message = "Database error (execute update): " . $stmt_submit->error;
                    }
                    $stmt_submit->close();
                }
            }
        } elseif ($action == 'delete') {
            $module_code_to_delete = trim($_POST['module_code'] ?? '');
            if (empty($module_code_to_delete)) {
                $message_type = 'error'; $message = "Module code is missing for deletion.";
            } else {
                $sql_submit = "DELETE FROM modules WHERE module_code = ?";
                $stmt_submit = $conn_submit->prepare($sql_submit);
                if ($stmt_submit === false) { /* ... error handling ... */ $message_type = 'error'; $message = "DB error (prep del)."; }
                else {
                    $stmt_submit->bind_param("s", $module_code_to_delete);
                    if ($stmt_submit->execute()) {
                        if ($stmt_submit->affected_rows > 0) {
                            $stmt_submit->close(); $conn_submit->close();
                            header("Location: " . $redirect_base_url . "?success=3&module_code=" . urlencode($module_code_to_delete) . $redirect_view_cid_param);
                            exit();
                        } else { $message_type = 'error'; $message = "Module not found for deletion."; }
                    } else { /* ... error handling ... */ $message_type = 'error'; $message = "DB error (exec del)."; }
                    $stmt_submit->close();
                }
            }
        } elseif ($action == 'activate' || $action == 'deactivate') {
            $module_code_to_change_status = trim($_POST['module_code'] ?? '');
            $new_status = ($action == 'activate') ? 1 : 0;
            $action_name = ($action == 'activate') ? 'activate' : 'deactivate';

            if (empty($module_code_to_change_status)) { /* ... error handling ... */ $message_type = 'error'; $message = "Module code missing."; }
            else {
                $sql_submit = "UPDATE modules SET status = ? WHERE module_code = ?";
                $stmt_submit = $conn_submit->prepare($sql_submit);
                if ($stmt_submit === false) { /* ... error handling ... */ $message_type = 'error'; $message = "DB error (prep status)."; }
                else {
                    $stmt_submit->bind_param("is", $new_status, $module_code_to_change_status);
                    if ($stmt_submit->execute()) {
                        if ($stmt_submit->affected_rows > 0) {
                            $stmt_submit->close(); $conn_submit->close();
                            $success_code = ($action == 'activate') ? 4 : 5;
                            header("Location: " . $redirect_base_url . "?success=" . $success_code . "&module_code=" . urlencode($module_code_to_change_status) . $redirect_view_cid_param);
                            exit();
                        } else {
                             // Check if already in desired state
                            $stmt_check = $conn_submit->prepare("SELECT status FROM modules WHERE module_code = ?");
                            $stmt_check->bind_param("s", $module_code_to_change_status);
                            $stmt_check->execute();
                            $result_check = $stmt_check->get_result();
                            if ($result_check->num_rows > 0 && $result_check->fetch_assoc()['status'] == $new_status) {
                                $warn_msg = "Module already " . ($new_status == 1 ? 'active' : 'inactive') . ".";
                                $stmt_check->close(); $stmt_submit->close(); $conn_submit->close();
                                header("Location: " . $redirect_base_url . "?warning=" . urlencode($warn_msg) . "&module_code=" . urlencode($module_code_to_change_status) . $redirect_view_cid_param);
                                exit();
                            } else {
                                $message_type = 'error'; $message = "Module not found or status unchanged.";
                            }
                            if($stmt_check) $stmt_check->close();
                        }
                    } else { /* ... error handling ... */ $message_type = 'error'; $message = "DB error (exec status)."; }
                    $stmt_submit->close();
                }
            }
        }
        if ($conn_submit && $conn_submit->ping()) {
            $conn_submit->close();
        }
    }
} // --- End of POST Handling ---


// ========================================================================
//  FETCH EXISTING MODULES (for Display in table, IF a course is selected)
// ========================================================================
$modules = [];
if ($selected_cid_to_view) { // Only fetch if a course is selected
    $conn_select = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn_select->connect_error) {
        if (empty($message_type)) {
            $message_type = 'error';
            $message = "DB Connect Error (fetch modules): " . $conn_select->connect_error;
        }
        error_log("DB Select Connect Error (fetch modules): " . $conn_select->connect_error);
    } else {
        $conn_select->set_charset("utf8mb4");
        $sql_select = "SELECT m.*, c.cname AS course_name FROM modules m JOIN course c ON m.cid = c.cid WHERE m.cid = ? ORDER BY m.year, m.semester, m.module_code";
        $stmt_select_modules = $conn_select->prepare($sql_select);

        if ($stmt_select_modules) {
            $stmt_select_modules->bind_param("s", $selected_cid_to_view);
            $stmt_select_modules->execute();
            $result_modules = $stmt_select_modules->get_result();

            if ($result_modules === false) {
                if (empty($message_type)) {
                    $message_type = 'error';
                    $message = "Error fetching modules for course: " . $conn_select->error;
                }
                error_log("SQL Select Error (fetch modules for course): " . $conn_select->error);
            } elseif ($result_modules->num_rows > 0) {
                while ($row = $result_modules->fetch_assoc()) {
                    $modules[] = $row;
                }
                $result_modules->free();
            }
            $stmt_select_modules->close();
        } else {
             if (empty($message_type)) {
                $message_type = 'error';
                $message = "Error preparing to fetch modules: " . $conn_select->error;
            }
            error_log("SQL Prepare Error (fetch modules for course): " . $conn_select->error);
        }
        $conn_select->close();
    }
}
// --- End of Fetching Modules ---


// ========================================================================
//  DETERMINE DEFAULT FORM VALUES
// ========================================================================
// Base default values
$default_form_values = [
    'module_code' => '', 'module_title' => '', 'module_type' => 'GPA',
    'credits' => '', 'status' => '1', 'year' => '', 'semester' => '',
    'cid' => $selected_cid_to_view ?: '' // Pre-fill CID if a course is being viewed and not editing
];

// Start with defaults
$current_form_values = $default_form_values;

// If editing, $edit_module_data takes precedence
if ($is_editing && $edit_module_data) {
    foreach ($edit_module_data as $key => $value) {
        $current_form_values[$key] = $value; // Overwrite defaults with edit data
    }
}

// If it's a POST request (e.g., form submission with validation errors that didn't redirect),
// POST data should override anything else for fields that were submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($message_type == 'error' || $message_type == 'warning')) {
    // List of fields expected from the form
    $expected_post_fields = ['module_code', 'module_title', 'module_type', 'credits', 'status', 'year', 'semester', 'cid', 'original_module_code'];
    foreach ($expected_post_fields as $key) {
        if (isset($_POST[$key])) {
            $current_form_values[$key] = $_POST[$key];
        }
    }
}

// Assign to final form variables, ensuring htmlspecialchars for output in HTML attributes
$form_module_code = htmlspecialchars($current_form_values['module_code'] ?? '');
// For the hidden field original_module_code, it should be module_code if editing, or the POSTed value if exists
$form_original_module_code_val = '';
if ($is_editing) {
    $form_original_module_code_val = $edit_module_data['module_code'] ?? ''; // From initial edit load
}
if (isset($current_form_values['original_module_code'])) { // If POSTed (e.g. failed update)
    $form_original_module_code_val = $current_form_values['original_module_code'];
} elseif ($is_editing && isset($current_form_values['module_code'])) { // Fallback for editing mode if original_module_code wasn't explicitly set from POST
    $form_original_module_code_val = $current_form_values['module_code'];
}
$form_original_module_code = htmlspecialchars($form_original_module_code_val);


$form_module_title = htmlspecialchars($current_form_values['module_title'] ?? '');
$form_module_type = htmlspecialchars($current_form_values['module_type'] ?? 'GPA');
$form_credits = htmlspecialchars($current_form_values['credits'] ?? '');
$form_status = htmlspecialchars($current_form_values['status'] ?? '1');
$form_year = htmlspecialchars($current_form_values['year'] ?? '');
$form_semester = htmlspecialchars($current_form_values['semester'] ?? '');
$form_cid = htmlspecialchars($current_form_values['cid'] ?? '');

?>

<style>
/* Add or modify CSS as needed */
body { display: flex; margin-right: auto; }
.page-container { margin-left: 300px; margin-right:100px; padding: 20px; width: calc(100% - 400px); min-width: 800px; height:100%; box-sizing: border-box; max-width: none; margin-top: 10px; background: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border-top: 5px solid var(--primary-color); position: relative; flex-grow: 1; }
:root { --primary-color: #007bff; --secondary-color: #6c757d; --success-color: #28a745; --danger-color: #dc3545; --warning-color: #ffc107; --light-color: #f8f9fa; --dark-color: #343a40; --font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; --border-radius: 0.3rem; --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
h2, h3 { color: var(--primary-color); margin-bottom: 1.5rem; text-align: center; font-weight: 600; }
h3 { color: var(--dark-color); margin-top: 2rem; margin-bottom: 1rem; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
.message-area { padding: 12px 18px; margin-bottom: 25px; border-radius: var(--border-radius); border: 1px solid transparent; font-size: 0.95rem; text-align: center; }
.message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.message-area.warning { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
.form-section { margin-bottom: 30px; padding: 25px; background-color: var(--light-color); border-radius: var(--border-radius); border: 1px solid #ddd; }
.form-section label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-section input[type="text"], .form-section input[type="number"], .form-section input[type="file"], .form-section textarea, .form-section select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: var(--border-radius); box-sizing: border-box; font-size: 1rem; }
.form-section input:read-only, .form-section select:disabled { background-color: #e9ecef; cursor: not-allowed; }
.form-section .button-group { margin-top: 15px; }
.form-section button { background-color: var(--primary-color); color: white; padding: 12px 25px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 1rem; transition: background-color 0.3s ease; margin-right: 10px; }
.form-section button:hover { background-color: #0056b3; }
button#cancelEdit { background-color: var(--secondary-color); display: <?php echo $is_editing ? 'inline-block' : 'none'; ?>; }
button#cancelEdit:hover { background-color: #5a6268; }
.event-list-section { margin-top: 30px; overflow-x: auto; }
.event-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08); }
.event-table th, .event-table td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: left; vertical-align: middle; }
.event-table th { background-color: #f2f5f8; font-weight: 600; color: #333; white-space: nowrap; }
.event-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.event-table tbody tr:hover { background-color: #e9ecef; }
.event-table .actions-cell { white-space: nowrap; min-width: 220px; }
.event-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
.event-table .actions-cell button, .event-table .actions-cell .edit-btn { padding: 6px 12px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; transition: background-color 0.2s ease; text-decoration: none; display: inline-block; }
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
.required { color: red; }

/* Styles for Course List */
.course-list-container { margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border-radius: var(--border-radius); border: 1px solid #eee; }
.course-list-container h3 { margin-top: 0; margin-bottom: 10px; text-align: left; font-size: 1.2em; color: var(--dark-color); border-bottom: none;}
ul.course-list { list-style-type: none; padding: 0; margin: 0 0 10px 0; display: flex; flex-wrap: wrap; gap: 10px; }
ul.course-list li a { display: inline-block; padding: 8px 15px; background-color: #e9ecef; color: var(--primary-color); text-decoration: none; border-radius: var(--border-radius); border: 1px solid #ccc; transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; font-size: 0.9em; }
ul.course-list li a:hover { background-color: var(--primary-color); color: white; border-color: var(--primary-color); }
ul.course-list li a.active-course { background-color: var(--primary-color); color: white; font-weight: bold; border-color: #0056b3; }
.clear-selection-link { font-size: 0.9em; }
</style>

<div class="page-container">

    <h2>Module Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo $message; // Message is already appropriately escaped or static ?>
        </div>
    <?php endif; ?>

    <div class="course-list-container">
        <h3>Select a Course to View/Manage Modules:</h3>
        <ul class="course-list">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course_item): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?view_course_cid=<?php echo htmlspecialchars($course_item['cid']); ?>"
                           class="<?php echo ($selected_cid_to_view == $course_item['cid']) ? 'active-course' : ''; ?>">
                            <?php echo htmlspecialchars($course_item['cname']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No courses found. Please add courses first.</li>
            <?php endif; ?>
        </ul>
        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="clear-selection-link">Clear Course Selection / Add Module for Any Course</a>
    </div>
    
    <hr style="margin: 25px 0; border-top: 1px solid #ccc;">

    <div id="wrapper">
        <?php include('include/sidebar.php');?>

        <div class="content-page">
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2" style="width: 100%;">
                            <div class="form-section" style="width:1000px; margin: 0 auto;">
                                <h4 id="formTitle" style="text-align:left; border-bottom:none; margin-bottom: 20px;">
                                    <?php echo $is_editing ? 'Edit Module: ' . $form_module_code : 'Add New Module'; ?>
                                    <?php if ($is_editing && $selected_course_name && $edit_module_data && $edit_module_data['cid'] == $selected_cid_to_view): ?>
                                        <span style="font-size: 0.8em; color: #555;"> (for course: <?php echo htmlspecialchars($selected_course_name);?>)</span>
                                    <?php elseif (!$is_editing && $selected_course_name): ?>
                                         <span style="font-size: 0.8em; color: #555;"> (for course: <?php echo htmlspecialchars($selected_course_name);?>)</span>
                                    <?php endif; ?>
                                </h4>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo $selected_cid_to_view ? '?view_course_cid='.urlencode($selected_cid_to_view) : ''; ?>">
                                    <input type="hidden" name="action" value="<?php echo $is_editing ? 'update' : 'insert'; ?>">
                                    <?php if ($is_editing): ?>
                                        <input type="hidden" name="original_module_code" value="<?php echo $form_original_module_code; ?>">
                                    <?php endif; ?>
                                    <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">


                                    <label for="module_code">Module Code <span class="required">*</span></label>
                                    <input type="text" id="module_code" name="module_code" value="<?php echo $form_module_code; ?>" <?php if ($is_editing) echo 'readonly'; ?> required>

                                    <label for="module_title">Module Title <span class="required">*</span></label>
                                    <input type="text" id="module_title" name="module_title" value="<?php echo $form_module_title; ?>" required>

                                    <label for="module_type">Module Type <span class="required">*</span></label>
                                    <select id="module_type" name="module_type" required>
                                        <option value="GPA" <?php echo ($form_module_type == 'GPA') ? 'selected' : ''; ?>>GPA</option>
                                        <option value="NGPA" <?php echo ($form_module_type == 'NGPA') ? 'selected' : ''; ?>>NGPA</option>
                                        </select>

                                    <label for="credits">Credits <span class="required">*</span></label>
                                    <input type="number" id="credits" name="credits" value="<?php echo $form_credits; ?>" required min="0">

                                    <?php if (!$is_editing): // Only show status for new modules, for edit it's via activate/deactivate ?>
                                    <label for="status">Status <span class="required">*</span></label>
                                    <select id="status" name="status" required>
                                        <option value="1" <?php echo ($form_status == '1') ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo ($form_status == '0') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                    <?php elseif ($is_editing && isset($edit_module_data['status'])): ?>
                                        <label>Current Status</label>
                                        <input type="text" value="<?php echo ($edit_module_data['status'] == 1 ? 'Active' : 'Inactive'); ?>" readonly style="background-color: #e9ecef;">
                                    <?php endif; ?>


                                    <label for="year">Year <span class="required">*</span></label>
                                    <input type="number" id="year" name="year" value="<?php echo $form_year; ?>" required min="1" max="4">

                                    <label for="semester">Semester <span class="required">*</span></label>
                                    <select id="semester" name="semester" required>
                                        <option value="">-- Select Semester --</option>
                                        <option value="1" <?php echo ($form_semester == '1') ? 'selected' : ''; ?>>Semester 1</option>
                                        <option value="2" <?php echo ($form_semester == '2') ? 'selected' : ''; ?>>Semester 2</option>
                                    </select>

                                    <label for="cid">Course <span class="required">*</span></label>
                                    <select id="cid" name="cid" required>
                                        <option value="">-- Select Course --</option>
                                        <?php foreach ($courses as $course_opt): ?>
                                            <option value="<?php echo htmlspecialchars($course_opt['cid']); ?>" <?php echo ($form_cid == $course_opt['cid']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($course_opt['cname']); ?> (<?php echo htmlspecialchars($course_opt['cid']);?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div class="button-group">
                                        <button type="submit"><?php echo $is_editing ? 'Update Module' : 'Add Module'; ?></button>
                                        <?php if ($is_editing): ?>
                                            <button type="button" id="cancelEdit" onclick="window.location.href='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo $selected_cid_to_view ? '?view_course_cid='.urlencode($selected_cid_to_view) : ''; ?>'; return false;">Cancel Edit</button>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div> <?php if ($selected_cid_to_view): ?>
                                <h3 id="modulesTableTitle">Modules for <?php echo htmlspecialchars($selected_course_name ?: $selected_cid_to_view); ?></h3>
                                <?php if (!empty($modules)): ?>
                                    <div class="event-list-section">
                                        <table class="event-table">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Title</th>
                                                    <th>Type</th>
                                                    <th>Credits</th>
                                                    <th>Year</th>
                                                    <th>Semester</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($modules as $module): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                                                        <td><?php echo htmlspecialchars($module['module_title']); ?></td>
                                                        <td><?php echo htmlspecialchars($module['module_type']); ?></td>
                                                        <td><?php echo htmlspecialchars($module['credits']); ?></td>
                                                        <td><?php echo htmlspecialchars($module['year']); ?></td>
                                                        <td><?php echo htmlspecialchars($module['semester']); ?></td>
                                                        <td>
                                                            <span class="<?php echo ($module['status'] == 1) ? 'status-active' : 'status-inactive'; ?>">
                                                                <?php echo ($module['status'] == 1) ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td class="actions-cell">
                                                            <?php
                                                            $edit_link_params = '?edit=' . urlencode($module['module_code']);
                                                            if ($selected_cid_to_view) { // Keep current view context
                                                                $edit_link_params .= '&view_course_cid=' . urlencode($selected_cid_to_view);
                                                            } elseif ($module['cid']) { // Fallback to module's own course if no view context
                                                                $edit_link_params .= '&view_course_cid=' . urlencode($module['cid']);
                                                            }
                                                            ?>
                                                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . $edit_link_params); ?>" class="edit-btn">Edit</a>

                                                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return confirm('Are you sure you want to delete module \'<?php echo htmlspecialchars(addslashes($module['module_code'])); ?>\'?');" style="display:inline;">
                                                                <input type="hidden" name="module_code" value="<?php echo htmlspecialchars($module['module_code']); ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                                                                <button type="submit" name="delete_module">Delete</button>
                                                            </form>

                                                            <?php if ($module['status'] == 0): ?>
                                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:inline;">
                                                                    <input type="hidden" name="module_code" value="<?php echo htmlspecialchars($module['module_code']); ?>">
                                                                    <input type="hidden" name="action" value="activate">
                                                                    <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                                                                    <button type="submit" name="activate_module">Activate</button>
                                                                </form>
                                                            <?php else: ?>
                                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display:inline;">
                                                                    <input type="hidden" name="module_code" value="<?php echo htmlspecialchars($module['module_code']); ?>">
                                                                    <input type="hidden" name="action" value="deactivate">
                                                                    <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                                                                    <button type="submit" name="deactivate_module">Deactivate</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p>No modules found for '<?php echo htmlspecialchars($selected_course_name ?: $selected_cid_to_view); ?>'. You can add new modules using the form above.</p>
                                <?php endif; ?>
                            <?php elseif (empty($selected_cid_to_view) && $_SERVER["REQUEST_METHOD"] == "GET" && empty($_GET['edit'])): // Show only if no course selected and not editing ?>
                                <p style="text-align: center; margin-top: 20px; font-size: 1.1em; padding: 20px; background-color: #f0f8ff; border-radius: var(--border-radius);">
                                    Please select a course from the list above to view its modules, or use the form to add a new module to any course.
                                </p>
                            <?php endif; ?>

                        </div> </div> </div> </div> </div> </div> </div> <?php // include('include/footer.php'); // Assuming you have a footer ?>