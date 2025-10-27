<?php
// MOVED: All PHP logic now runs before any HTML output.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('include/config.php');

// Your database name - CHANGE THIS
$upload_dir_name = 'uploads_pdf';
$upload_path = __DIR__ . '/' . $upload_dir_name . '/';

ini_set('display_errors', 0); // Set to 1 for development, 0 for production
error_reporting(E_ALL);

// SUPER ADMIN: Check for super admin role.
$is_super_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin');

// MODIFICATION: Authorization Check for regular users and super admins.
$is_authorized = $is_super_admin || (isset($_SESSION['cid']) && !empty($_SESSION['cid']));

$user_subject_codes = [];
if ($is_authorized && !$is_super_admin) {
    // Prepare subject codes ONLY for regular users.
    $user_subject_codes = array_map('trim', explode(',', $_SESSION['cid']));
    $placeholders = implode(',', array_fill(0, count($user_subject_codes), '?'));
    $types = str_repeat('s', count($user_subject_codes));
}

// Only proceed with the rest of the logic if the user is authorized
if ($is_authorized) {
    // Helper function to check if a user can manage a specific subject code
    function canManageSubject($subject_code, $allowed_codes, $is_super_admin) {
        if ($is_super_admin) return true;
        return in_array($subject_code, $allowed_codes);
    }

    // Helper function to check if a user can manage a specific record ID
    function canManageRecord($conn, $record_id, $allowed_codes, $placeholders, $types, $is_super_admin) {
        if ($is_super_admin) return true;
        if (empty($allowed_codes)) return false; // Prevent SQL error if user has no codes
        $stmt = $conn->prepare("SELECT subject_code FROM results WHERE result_id = ? AND subject_code IN ($placeholders)");
        if (!$stmt) return false;
        
        $params = array_merge([$record_id], $allowed_codes);
        $param_types = 'i' . $types;

        $stmt->bind_param($param_types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        return $result->num_rows > 0;
    }

    if (!is_dir($upload_path)) {
        if (!mkdir($upload_path, 0755, true)) {
            die("ERROR: Failed to create upload directory.");
        }
    }

    $success_message = $_SESSION['success_message'] ?? '';
    $error_message = $_SESSION['error_message'] ?? '';
    unset($_SESSION['success_message'], $_SESSION['error_message']);
    
    $edit_data = null;
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $current_script_url = 'exam-result.php';

    // --- ACTION HANDLERS ---
    
    // --- UPLOAD ACTION ---
    if ($action === 'upload' && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_upload'])) {
        $exam_title = trim($_POST['exam_title']);
        $exam_session = trim($_POST['exam_session']);
        $exam_type = trim($_POST['exam_type']);
        $subject_code = trim($_POST['subject_code']);
        
        if (!canManageSubject($subject_code, $user_subject_codes, $is_super_admin)) {
            $_SESSION['error_message'] = "Authorization Error: You cannot add records for this subject.";
        } elseif (empty($exam_title) || empty($subject_code)) {
            $_SESSION['error_message'] = "Error: Exam Title and Subject Code are required.";
        } elseif (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
            // FIX: Restored the complete file handling and database insertion logic.
            $file_tmp_name = $_FILES['pdf_file']['tmp_name'];
            $file_name = basename($_FILES['pdf_file']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($file_ext !== 'pdf') {
                $_SESSION['error_message'] = "Error: Only PDF files are allowed.";
            } else {
                $unique_file_name = uniqid('', true) . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", $file_name);
                $file_path_on_server = $upload_path . $unique_file_name;

                if (move_uploaded_file($file_tmp_name, $file_path_on_server)) {
                    $created_at = date('Y-m-d H:i:s');
                    $sql_insert = "INSERT INTO results (exam_title, exam_session, exam_type, subject_code, upload_date, file_path, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, CURDATE(), ?, 1, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("sssssss", $exam_title, $exam_session, $exam_type, $subject_code, $unique_file_name, $created_at, $created_at);
                    if ($stmt_insert->execute()) {
                        $_SESSION['success_message'] = "Record created successfully!";
                    } else {
                        $_SESSION['error_message'] = "Error creating record: " . $stmt_insert->error;
                        @unlink($file_path_on_server); // Clean up failed upload
                    }
                    $stmt_insert->close();
                } else {
                    $_SESSION['error_message'] = "Error: Failed to move uploaded file.";
                }
            }
        } else {
            $_SESSION['error_message'] = "Error: A PDF file is required for upload.";
        }
        header("Location: $current_script_url");
        exit();
    }

    // --- UPDATE ACTION ---
    if ($action === 'update' && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
        $result_id = (int)($_POST['result_id'] ?? 0);
        $subject_code = trim($_POST['subject_code']);

        if (!canManageRecord($conn, $result_id, $user_subject_codes, $placeholders, $types, $is_super_admin)) {
            $_SESSION['error_message'] = "Authorization Error: You cannot modify this record.";
        } elseif (!canManageSubject($subject_code, $user_subject_codes, $is_super_admin)) {
            $_SESSION['error_message'] = "Authorization Error: You cannot assign this subject code.";
        } else {
            // FIX: Restored the complete database update logic.
            $exam_title = trim($_POST['exam_title']);
            $exam_session = trim($_POST['exam_session']);
            $exam_type = trim($_POST['exam_type']);
            $current_file_db_path = trim($_POST['current_file_path']);
            $updated_at = date('Y-m-d H:i:s');
            $new_file_name_for_db = $current_file_db_path; // Assume no new file is uploaded initially

            // Handle optional new file upload
            if (isset($_FILES['new_pdf_file']) && $_FILES['new_pdf_file']['error'] == UPLOAD_ERR_OK) {
                $file_tmp_name = $_FILES['new_pdf_file']['tmp_name'];
                $file_name = basename($_FILES['new_pdf_file']['name']);
                $new_file_name_for_db = uniqid('', true) . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "_", $file_name);
                
                if (move_uploaded_file($file_tmp_name, $upload_path . $new_file_name_for_db)) {
                    // New file moved successfully, delete old one if it exists
                    if (!empty($current_file_db_path) && file_exists($upload_path . $current_file_db_path)) {
                        @unlink($upload_path . $current_file_db_path);
                    }
                } else {
                    $_SESSION['error_message'] = "Error moving new file. Update failed.";
                    $new_file_name_for_db = $current_file_db_path; // Revert to old path on failure
                }
            }
            
            // Proceed with DB update if no file move error
            if (!isset($_SESSION['error_message'])) {
                $sql_update = "UPDATE results SET exam_title=?, exam_session=?, exam_type=?, subject_code=?, file_path=?, updated_at=? WHERE result_id=?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssssssi", $exam_title, $exam_session, $exam_type, $subject_code, $new_file_name_for_db, $updated_at, $result_id);
                if ($stmt_update->execute()) {
                    $_SESSION['success_message'] = "Result updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Error updating record: " . $stmt_update->error;
                }
                $stmt_update->close();
            }
        }
        header("Location: $current_script_url");
        exit();
    }

    // --- (DELETE ACTION and other logic remains the same) ---
    if ($action === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $result_id_to_delete = (int)$_GET['id'];
        if (!canManageRecord($conn, $result_id_to_delete, $user_subject_codes, $placeholders, $types, $is_super_admin)) {
             $_SESSION['error_message'] = "Authorization Error: You cannot delete this record.";
        } else {
            $stmt_path = $conn->prepare("SELECT file_path FROM results WHERE result_id = ?");
            $stmt_path->bind_param('i', $result_id_to_delete);
            $stmt_path->execute();
            $db_file_path = $stmt_path->get_result()->fetch_assoc()['file_path'] ?? null;
            $stmt_path->close();
            
            $stmt_delete = $conn->prepare("DELETE FROM results WHERE result_id = ?");
            $stmt_delete->bind_param("i", $result_id_to_delete);
            if ($stmt_delete->execute()) {
                if ($db_file_path && file_exists($upload_path . $db_file_path)) {
                    @unlink($upload_path . $db_file_path);
                }
                $_SESSION['success_message'] = "Result deleted successfully!";
            }
            $stmt_delete->close();
        }
        header("Location: $current_script_url");
        exit();
    }

    if ($action === 'edit' && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $result_id_to_edit = (int)$_GET['id'];
        if (canManageRecord($conn, $result_id_to_edit, $user_subject_codes, $placeholders, $types, $is_super_admin)) {
            $sql_edit = "SELECT * FROM results WHERE result_id = ?";
            $stmt_edit = $conn->prepare($sql_edit);
            $stmt_edit->bind_param("i", $result_id_to_edit);
            $stmt_edit->execute();
            $edit_data = $stmt_edit->get_result()->fetch_assoc();
            $stmt_edit->close();
        } else {
            $_SESSION['error_message'] = "Record not found or you do not have permission to edit it.";
            // Redirect to prevent trying to show a form with no data
            header("Location: $current_script_url");
            exit();
        }
    }

    $form_subject_codes = [];
    if ($is_super_admin) {
        $result = $conn->query("SELECT DISTINCT subject_code FROM results WHERE subject_code IS NOT NULL AND subject_code != '' ORDER BY subject_code ASC");
        while ($row = $result->fetch_assoc()) {
            $form_subject_codes[] = $row['subject_code'];
        }
    } else {
        $form_subject_codes = $user_subject_codes;
    }

    $search_title_filter = isset($_GET['search_title']) ? trim($_GET['search_title']) : '';
    $sql_select_all = "SELECT result_id, exam_title, exam_session, exam_type, subject_code, file_path, is_active FROM results";
    $params_select = [];
    $types_select = '';
    $where_clauses = [];

    if (!$is_super_admin) {
        if (!empty($user_subject_codes)) {
            $where_clauses[] = "subject_code IN ($placeholders)";
            $params_select = array_merge($params_select, $user_subject_codes);
            $types_select .= $types;
        } else {
            // If a non-admin has no subjects, force the query to return nothing
            $where_clauses[] = "1=0"; 
        }
    }

    if (!empty($search_title_filter)) {
        $where_clauses[] = "exam_title LIKE ?";
        $params_select[] = "%" . $search_title_filter . "%";
        $types_select .= 's';
    }
    
    if (!empty($where_clauses)) {
        $sql_select_all .= " WHERE " . implode(" AND ", $where_clauses);
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
        $error_message .= " Error fetching results: " . htmlspecialchars($conn->error);
    }
}
?>
<?php
// SECTION 2: HTML OUTPUT
include('include/header.php'); 
?>
<style>
/* (Your CSS remains the same) */
body { display: flex; margin-right: auto; }
.page-container { margin-left: 300px; margin-right:100px; padding: 20px; width: calc(100% - 400px); min-width: 800px; margin-top: 10px; background: #fff; border-radius: 0.3rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-top: 5px solid #007bff; }
h2 { color: #007bff; margin-bottom: 1.5rem; text-align: center; font-weight: 600; }
.message { padding: 12px 18px; margin-bottom: 25px; border-radius: 0.3rem; text-align: center; }
.message.success { background-color: #d4edda; color: #155724; }
.message.error { background-color: #f8d7da; color: #721c24; }
.form-section { margin-bottom: 30px; padding: 25px; background-color: #f8f9fa; border-radius: 0.3rem; border: 1px solid #ddd; }
.form-section label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-section input[type="text"], .form-section select, .form-section input[type="file"] { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 0.3rem; }
.form-section button { background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 0.3rem; cursor: pointer; }
#cancelEdit { background-color: #6c757d; color: white; text-decoration: none; padding: 12px 25px; border-radius: 0.3rem; display: inline-block; }
.event-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.event-table th, .event-table td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: left; }
.event-table th { background-color: #f2f5f8; font-weight: 600; }
.actions-cell a { padding: 6px 12px; border-radius: 0.3rem; color: white; text-decoration: none; margin-right: 5px; display: inline-block; }
.actions-cell .edit-btn { background-color: #ffc107; color: #333; }
</style>

<?php include('include/sidebar.php'); ?>

<div class="page-container">

    <?php if (!$is_authorized): ?>
        <div class="message error">ACCESS DENIED: You are not authorized to view this page.</div>
    <?php else: ?>

        <?php if ($success_message): ?><div class="message success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

        <?php if ($edit_data): ?>
            <h2>Update Result</h2>
            <form action="<?php echo $current_script_url; ?>" method="post" enctype="multipart/form-data" class="form-section">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="result_id" value="<?php echo htmlspecialchars($edit_data['result_id']); ?>">
                <input type="hidden" name="current_file_path" value="<?php echo htmlspecialchars($edit_data['file_path']); ?>">
                
                <label>Exam Title:</label>
                <input type="text" name="exam_title" value="<?php echo htmlspecialchars($edit_data['exam_title']); ?>" required>
                
                <label>Subject Code:</label>
                <select name="subject_code" required>
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($form_subject_codes as $code): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>" <?php echo ($edit_data['subject_code'] == $code) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(strtoupper($code)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <label>Exam Session:</label>
                <input type="text" name="exam_session" value="<?php echo htmlspecialchars($edit_data['exam_session']); ?>" required>
                <label>Exam Type:</label>
                <input type="text" name="exam_type" value="<?php echo htmlspecialchars($edit_data['exam_type']); ?>" required>
                
                <label>Replace PDF (Optional):</label>
                <input type="file" name="new_pdf_file" accept=".pdf">

                <button type="submit" name="submit_update">Update Result</button>
                <a href="<?php echo $current_script_url; ?>" id="cancelEdit">Cancel</a>
            </form>
        
        <?php else: ?>
            <h2>Upload New Result PDF</h2>
            <form action="<?php echo $current_script_url; ?>" method="post" enctype="multipart/form-data" class="form-section">
                <input type="hidden" name="action" value="upload">
                <label>Exam Title:</label>
                <input type="text" name="exam_title" required>
                
                <label>Subject Code:</label>
                <select name="subject_code" required>
                    <option value="">-- Select Subject --</option>
                    <?php foreach ($form_subject_codes as $code): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>"><?php echo htmlspecialchars(strtoupper($code)); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Exam Session:</label>
                <input type="text" name="exam_session" required>
                <label>Exam Type:</label>
                <input type="text" name="exam_type" required>
                <label>PDF File:</label>
                <input type="file" name="pdf_file" accept=".pdf" required>

                <button type="submit" name="submit_upload">Upload PDF</button>
            </form>
        <?php endif; ?>

        <hr>
        <h2>Existing Results</h2>
        <?php if ($all_results_display && $all_results_display->num_rows > 0): ?>
            <table class="event-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Exam Title</th>
                        <th>Subject</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $all_results_display->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['result_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                        <td><strong><?php echo htmlspecialchars(strtoupper($row['subject_code'])); ?></strong></td>
                        <td class="actions-cell">
                            <a href="view_pdf.php?id=<?php echo htmlspecialchars($row['result_id']); ?>" target="_blank" class="edit-btn" style="background-color: #17a2b8;">View</a>
                            <a href="<?php echo $current_script_url; ?>?action=edit&id=<?php echo $row['result_id']; ?>" class="edit-btn">Edit</a>
                            <a href="<?php echo $current_script_url; ?>?action=delete&id=<?php echo $row['result_id']; ?>" class="edit-btn" style="background-color: #dc3545;" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>

    <?php endif; // End of $is_authorized HTML block ?>

</div>
</body>
</html>
<?php
if (isset($stmt_select_all_display) && $stmt_select_all_display) $stmt_select_all_display->close();
if (isset($conn) && $conn) $conn->close();
?>