<?php
// ACTION REQUIRED: This MUST be the very first line.
ob_start(); // Start output buffering to prevent "headers already sent" errors.
session_start();

// --- Role-Based Access Control (RBAC) ---
if (!isset($_SESSION['role']) || !isset($_SESSION['cid'])) {
    header('Location: login.php'); // Redirect to your login page
    exit();
}
$user_role = $_SESSION['role'];
$user_cid = $_SESSION['cid'];
$super_admin_roles = ['super_admin', 'SAadmin'];
$is_super_admin = in_array($user_role, $super_admin_roles);
// --- End RBAC ---

// Set error reporting for development. Change to 1 for production.
error_reporting(E_ALL);
ini_set('display_errors', 0);

include('include/header.php');
include('../include/config.php');

// --- Variable Initialization ---
$message = '';
$message_type = '';
$edit_module_data = null;
$is_editing = false;
$selected_cid_to_view = null;
$selected_course_name = '';

// --- Determine which course's modules to display ---
if ($is_super_admin) {
    if (isset($_GET['view_course_cid'])) {
        $selected_cid_to_view = trim($_GET['view_course_cid']);
    }
} else {
    $selected_cid_to_view = $user_cid;
}

// Fetch course name if a course is selected
if ($selected_cid_to_view) {
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT cname FROM course WHERE cid = ?");
        $stmt->bind_param("s", $selected_cid_to_view);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $selected_course_name = $row['cname'];
            }
        }
        $stmt->close();
        $conn->close();
    }
}

// --- Handle Messages from Redirects ---
if (isset($_GET['success'])) {
    $message_type = 'success';
    $message = htmlspecialchars(urldecode($_GET['success']));
} elseif (isset($_GET['error'])) {
    $message_type = 'error';
    $message = htmlspecialchars(urldecode($_GET['error']));
}

// --- Fetch Courses for Dropdowns ---
$courses = [];
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (!$conn->connect_error) {
    if ($is_super_admin) {
        $result = $conn->query("SELECT cid, cname FROM course ORDER BY cname");
    } else {
        $stmt = $conn->prepare("SELECT cid, cname FROM course WHERE cid = ?");
        $stmt->bind_param("s", $user_cid);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
    }
    if (isset($stmt)) $stmt->close();
    $conn->close();
}

// --- Handle 'Edit' Request (GET) with Permission Check ---
if (isset($_GET['edit'])) {
    $module_code_to_edit = trim($_GET['edit']);
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT * FROM modules WHERE module_code = ?");
        $stmt->bind_param("s", $module_code_to_edit);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Check if user has permission to edit this module
            if ($is_super_admin || $row['cid'] === $user_cid) {
                $edit_module_data = $row;
                $is_editing = true;
            } else {
                $message = "Permission Denied: You cannot edit a module that does not belong to your course.";
                $message_type = 'error';
            }
        } else {
            $error = "Module not found.";
            $message_type = 'error';
        }
        $stmt->close();
        $conn->close();
    }
}

// --- Handle Form Submissions (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset("utf8mb4");

    function can_modify_module($conn, $module_code, $current_user_cid, $is_super) {
        if ($is_super) return true;
        $stmt = $conn->prepare("SELECT cid FROM modules WHERE module_code = ?");
        $stmt->bind_param("s", $module_code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) { return $row['cid'] === $current_user_cid; }
        return false;
    }

    $cid_for_redirect = $_POST['current_view_cid'] ?? $user_cid;
    $redirect_url = $_SERVER['PHP_SELF'] . ($cid_for_redirect ? '?view_course_cid=' . urlencode($cid_for_redirect) : '');
    $error_msg = '';
    $success_msg = '';

    switch ($action) {
        case 'insert':
            $cid = $_POST['cid'];
            if (!$is_super_admin && $cid !== $user_cid) {
                $error_msg = "Permission Denied: You can only add modules to your own course.";
            } else {
                $stmt = $conn->prepare("INSERT INTO modules (module_code, module_title, module_type, credits, status, year, semester, cid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssiiiss", $_POST['module_code'], $_POST['module_title'], $_POST['module_type'], $_POST['credits'], $_POST['status'], $_POST['year'], $_POST['semester'], $cid);
                if ($stmt->execute()) {
                    $success_msg = "Module '" . htmlspecialchars($_POST['module_code']) . "' added successfully.";
                } else { $error_msg = "Error adding module: " . $stmt->error; }
                $stmt->close();
            }
            break;
        case 'update':
            $original_module_code = $_POST['original_module_code'];
            $cid = $_POST['cid'];
            if (!can_modify_module($conn, $original_module_code, $user_cid, $is_super_admin) || (!$is_super_admin && $cid !== $user_cid)) {
                $error_msg = "Permission Denied to update this module.";
            } else {
                $stmt = $conn->prepare("UPDATE modules SET module_title = ?, module_type = ?, credits = ?, year = ?, semester = ?, cid = ? WHERE module_code = ?");
                $stmt->bind_param("ssiiiss", $_POST['module_title'], $_POST['module_type'], $_POST['credits'], $_POST['year'], $_POST['semester'], $cid, $original_module_code);
                if ($stmt->execute()) {
                    $success_msg = "Module '" . htmlspecialchars($original_module_code) . "' updated successfully.";
                } else { $error_msg = "Error updating module: " . $stmt->error; }
                $stmt->close();
            }
            break;
        case 'delete':
        case 'activate':
        case 'deactivate':
            $module_code = $_POST['module_code'];
            if (!can_modify_module($conn, $module_code, $user_cid, $is_super_admin)) {
                $error_msg = "Permission Denied to modify this module.";
            } else {
                if ($action === 'delete') {
                    $stmt = $conn->prepare("DELETE FROM modules WHERE module_code = ?");
                    $stmt->bind_param("s", $module_code);
                    if($stmt->execute()) $success_msg = "Module '" . htmlspecialchars($module_code) . "' was deleted.";
                } else {
                    $new_status = ($action === 'activate') ? 1 : 0;
                    $stmt = $conn->prepare("UPDATE modules SET status = ? WHERE module_code = ?");
                    $stmt->bind_param("is", $new_status, $module_code);
                    if($stmt->execute()) $success_msg = "Module status was updated successfully.";
                }
                if(isset($stmt)) $stmt->close();
            }
            break;
    }
    $conn->close();
    
    if ($error_msg) {
        header("Location: " . $redirect_url . "&error=" . urlencode($error_msg));
    } else {
        header("Location: " . $redirect_url . "&success=" . urlencode($success_msg));
    }
    exit();
}

// --- Fetch Modules for Display Table ---
$modules = [];
if ($selected_cid_to_view) {
    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT * FROM modules WHERE cid = ? ORDER BY year, semester, module_code");
        $stmt->bind_param("s", $selected_cid_to_view);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $modules[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
}

// --- Determine Form Values for pre-filling ---
$form_values = [
    'module_code' => '', 'module_title' => '', 'module_type' => 'GPA', 'credits' => '',
    'status' => '1', 'year' => '', 'semester' => '',
    'cid' => $is_super_admin ? ($selected_cid_to_view ?? '') : $user_cid
];
if ($is_editing && $edit_module_data) {
    $form_values = array_merge($form_values, $edit_module_data);
}
?>

<style>
/* Modern UI Styles */
:root {
    --primary-color: #007bff; --success-color: #28a745; --danger-color: #dc3545;
    --warning-color: #ffc107; --secondary-color: #6c757d; --light-gray: #f8f9fa;
    --border-color: #dee2e6; --border-radius: 0.3rem;
}
.card {
    border: 1px solid var(--border-color); border-radius: var(--border-radius);
    margin-bottom: 1.5rem; background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.1);
}
.card-header {
    background-color: var(--light-gray); padding: 0.75rem 1.25rem;
    border-bottom: 1px solid var(--border-color); font-weight: 600;
}
.card-body { padding: 1.25rem; }
.form-group { margin-bottom: 1rem; }
.form-control {
    width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #ced4da;
    border-radius: var(--border-radius); transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.form-control:focus { border-color: var(--primary-color); outline: 0; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
.form-row { display: flex; flex-wrap: wrap; margin-right: -5px; margin-left: -5px; }
.form-row > .col { padding-right: 5px; padding-left: 5px; flex-basis: 0; flex-grow: 1; max-width: 100%; }
.btn {
    display: inline-block; font-weight: 400; text-align: center; vertical-align: middle;
    cursor: pointer; border: 1px solid transparent; padding: 0.375rem 0.75rem;
    font-size: 0.9rem; line-height: 1.5; border-radius: var(--border-radius); transition: all .15s ease-in-out;
}
.btn-primary { background-color: var(--primary-color); color: #fff; }
.btn-secondary { background-color: var(--secondary-color); color: #fff; }
.btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
.btn-warning { background-color: var(--warning-color); color: #212529; }
.btn-danger { background-color: var(--danger-color); color: #fff; }
.btn-success { background-color: var(--success-color); color: #fff; }
.btn-group .btn { margin-right: 5px; }
.alert { padding: 0.75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: var(--border-radius); }
.alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
.alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 0.75rem; vertical-align: top; border-top: 1px solid var(--border-color); }
.table thead th { vertical-align: bottom; border-bottom: 2px solid var(--border-color); background-color: var(--light-gray); }
.table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0,0,0,.05); }
.badge { display: inline-block; padding: .25em .4em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: .25rem; }
.badge-success { color: #fff; background-color: var(--success-color); }
.badge-secondary { color: #fff; background-color: var(--secondary-color); }
.actions-cell form { display: inline; }
.actions-cell .btn { margin: 0 2px; }
</style>

<div class="main-panel">
    <?php include 'include/sidebar.php'; ?>
    
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h3 class="page-title">Module Management</h3>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($is_super_admin && !empty($courses)): ?>
                <div class="card">
                    <div class="card-header">Select a Course to Manage</div>
                    <div class="card-body btn-group">
                        <?php foreach ($courses as $course): ?>
                            <a href="?view_course_cid=<?php echo htmlspecialchars($course['cid']); ?>" class="btn <?php echo ($selected_cid_to_view == $course['cid']) ? 'btn-primary' : 'btn-secondary'; ?>">
                                <?php echo htmlspecialchars($course['cname']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header"><?php echo $is_editing ? '✏️ Edit Module' : '➕ Add New Module'; ?></div>
                    <div class="card-body">
                        <?php if ($selected_course_name): ?>
                            <p>For Course: <strong><?php echo htmlspecialchars($selected_course_name); ?></strong></p>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="<?php echo $is_editing ? 'update' : 'insert'; ?>">
                            <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                            <?php if ($is_editing): ?>
                                <input type="hidden" name="original_module_code" value="<?php echo htmlspecialchars($form_values['module_code']); ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group col" style="flex: 1.5;">
                                    <label for="cid">Course *</label>
                                    <select id="cid" name="cid" class="form-control" required <?php if (!$is_super_admin) echo 'disabled'; ?>>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?php echo htmlspecialchars($course['cid']); ?>" <?php if ($form_values['cid'] == $course['cid']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($course['cname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (!$is_super_admin): ?>
                                        <input type="hidden" name="cid" value="<?php echo htmlspecialchars($user_cid); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="form-group col" style="flex: 1;">
                                    <label for="module_code">Module Code *</label>
                                    <input type="text" id="module_code" name="module_code" class="form-control" placeholder="e.g., HNDQS1101" value="<?php echo htmlspecialchars($form_values['module_code']); ?>" <?php if ($is_editing) echo 'readonly'; ?> required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="module_title">Module Title *</label>
                                <input type="text" id="module_title" name="module_title" class="form-control" placeholder="e.g., Introduction to Quantity Surveying" value="<?php echo htmlspecialchars($form_values['module_title']); ?>" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col">
                                    <label for="module_type">Module Type *</label>
                                    <select id="module_type" name="module_type" class="form-control" required>
                                        <option value="GPA" <?php if ($form_values['module_type'] == 'GPA') echo 'selected'; ?>>GPA</option>
                                        <option value="NGPA" <?php if ($form_values['module_type'] == 'NGPA') echo 'selected'; ?>>NGPA</option>
                                    </select>
                                </div>
                                <div class="form-group col">
                                    <label for="credits">Credits *</label>
                                    <input type="number" id="credits" name="credits" class="form-control" placeholder="e.g., 3" value="<?php echo htmlspecialchars($form_values['credits']); ?>" required min="0">
                                </div>
                                <div class="form-group col">
                                    <label for="year">Year *</label>
                                    <input type="number" id="year" name="year" class="form-control" placeholder="e.g., 1" value="<?php echo htmlspecialchars($form_values['year']); ?>" required min="1" max="4">
                                </div>
                                <div class="form-group col">
                                    <label for="semester">Semester *</label>
                                    <select id="semester" name="semester" class="form-control" required>
                                        <option value="1" <?php if ($form_values['semester'] == '1') echo 'selected'; ?>>Semester 1</option>
                                        <option value="2" <?php if ($form_values['semester'] == '2') echo 'selected'; ?>>Semester 2</option>
                                    </select>
                                </div>
                                <?php if (!$is_editing): ?>
                                <div class="form-group col">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option value="1" <?php if ($form_values['status'] == '1') echo 'selected'; ?>>Active</option>
                                        <option value="0" <?php if ($form_values['status'] == '0') echo 'selected'; ?>>Inactive</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary"><?php echo $is_editing ? 'Update Module' : 'Add Module'; ?></button>
                            <?php if ($is_editing): ?>
                                <a href="?view_course_cid=<?php echo urlencode($selected_cid_to_view);?>" class="btn btn-secondary">Cancel</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <?php if ($selected_cid_to_view): ?>
                <div class="card">
                    <div class="card-header">Modules List for <?php echo htmlspecialchars($selected_course_name); ?></div>
                    <div class="card-body">
                        <?php if (!empty($modules)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>Code</th><th>Title</th><th>Credits</th><th>Year/Sem</th><th>Status</th><th style="width: 220px;">Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                                        <td><?php echo htmlspecialchars($module['module_title']); ?></td>
                                        <td><?php echo htmlspecialchars($module['credits']); ?></td>
                                        <td><?php echo 'Y' . htmlspecialchars($module['year']) . ' / S' . htmlspecialchars($module['semester']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $module['status'] == 1 ? 'badge-success' : 'badge-secondary'; ?>">
                                                <?php echo $module['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <a href="?edit=<?php echo urlencode($module['module_code']); ?>&view_course_cid=<?php echo urlencode($selected_cid_to_view); ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this module?');" style="display:inline;">
                                                <input type="hidden" name="module_code" value="<?php echo htmlspecialchars($module['module_code']); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="module_code" value="<?php echo htmlspecialchars($module['module_code']); ?>">
                                                <input type="hidden" name="action" value="<?php echo $module['status'] == 1 ? 'deactivate' : 'activate'; ?>">
                                                <input type="hidden" name="current_view_cid" value="<?php echo htmlspecialchars($selected_cid_to_view ?? ''); ?>">
                                                <button type="submit" class="btn btn-sm <?php echo $module['status'] == 1 ? 'btn-secondary' : 'btn-success'; ?>">
                                                    <?php echo $module['status'] == 1 ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                            <p class="text-center">No modules found for this course. Use the form above to add one.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php
// Send all buffered output to the browser and turn off buffering
ob_end_flush();
?>