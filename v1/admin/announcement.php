<?php
// session_start(); // Uncomment if you use sessions for admin authentication
// error_reporting(E_ALL); // Recommended for development
// ini_set('display_errors', 1); // Recommended for development

include('include/header.php'); // Ensure this path is correct
?>

<style>
/* --- Basic Layout for Fixed Sidebar (using your provided styles) --- */
body {
    display: flex; 
    margin-right: auto;
}

.page-container {
    margin-left: 300px; 
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px); 
    min-width: 800px; 
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px;
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
    margin-bottom: 30px; 
}

:root {
    --primary-color: #007bff; 
    --secondary-color: #6c757d; 
    --success-color: #28a745; 
    --danger-color: #dc3545; 
    --warning-color: #ffc107; 
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
.form-section input[type="datetime-local"],
.form-section input[type="file"],
.form-section select,
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
.tox-tinymce {
    border: 1px solid #ccc !important;
    border-radius: var(--border-radius) !important;
}
.form-section input[type="file"] {
    padding: 8px;
    background-color: #fff;
}

.form-section .image-preview-container {
    margin-top: 10px;
    margin-bottom: 15px;
    display: flex; 
    flex-direction: column; 
    align-items: flex-start; 
    gap: 10px;
}
.form-section .image-preview-item {
    position: relative;
    display: inline-block;
    border: 1px solid #eee;
    padding: 5px;
    background-color: #fff;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.form-section .image-preview-item img {
    max-width: 150px; 
    max-height: 150px;
    display: block;
    object-fit: cover;
    border-radius: calc(var(--border-radius) - 2px);
}
.form-section .remove-current-image-btn {
    background: var(--danger-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    padding: 5px 10px;
    font-size: 0.8rem;
    cursor: pointer;
    margin-top: 5px; 
}
.form-section .remove-current-image-btn:hover {
    background-color: #c82333;
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
.form-section button#cancelEditBtn { background-color: var(--secondary-color); }
.form-section button#cancelEditBtn:hover { background-color: #5a6268; }

.announcements-list-section { margin-top: 30px; overflow-x: auto; }
.announcements-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.announcements-table th, .announcements-table td {
    border: 1px solid #e0e0e0;
    padding: 10px 12px; /* Adjusted padding */
    text-align: left;
    vertical-align: middle;
    font-size: 0.9rem; /* Slightly smaller font for more info */
}
.announcements-table th {
    background-color: #f2f5f8;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
}
.announcements-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.announcements-table tbody tr:hover { background-color: #e9ecef; }
.announcements-table .announcement-image-cell img {
    max-width: 70px; 
    max-height: 70px;
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    object-fit: cover;
}
.announcements-table .actions-cell { white-space: nowrap; min-width: 250px; } /* Adjusted width */
.announcements-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
.announcements-table .actions-cell button,
.announcements-table .actions-cell .edit-btn {
    padding: 6px 10px; /* Adjusted padding */
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.8rem; /* Adjusted font size */
    color: white;
    transition: background-color 0.2s ease;
    text-decoration: none;
    display: inline-block;
}
.announcements-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.announcements-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
.announcements-table .actions-cell button[name='action'][value='delete'] { background-color: var(--danger-color); }
.announcements-table .actions-cell button[name='action'][value='delete']:hover { background-color: #c82333; }
.announcements-table .actions-cell button[name='action'][value='activate'] { background-color: var(--success-color); }
.announcements-table .actions-cell button[name='action'][value='activate']:hover { background-color: #218838; }
.announcements-table .actions-cell button[name='action'][value='deactivate'] { background-color: var(--secondary-color); }
.announcements-table .actions-cell button[name='action'][value='deactivate']:hover { background-color: #5a6268; }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }

.two-column-layout { display: flex; gap: 20px; }
.two-column-layout > div { flex: 1; }
</style>

<?php include('include/sidebar.php'); // Ensure this path is correct ?>

<div class="page-container">
<?php
include ('include/config.php'); // Ensure this path is correct and $conn is established

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
} else {
    echo "<div class='message-area error'>Database connection error. Please check config.php.</div>";
    // exit; // Consider exiting if no DB connection
}

$message = '';
$message_type = ''; 
$upload_announcements_image_dir = "uploads/announcement_images/"; // Directory for announcement images

if (!is_dir($upload_announcements_image_dir)) {
    mkdir($upload_announcements_image_dir, 0777, true);
}

// --- Form Submission Handling ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $announcement_id_param = $_POST['announcement_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $target_audience = $_POST['target_audience'] ?? 'all';
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 0;
    $publish_datetime_str = $_POST['publish_datetime'] ?? null;
    $expiry_datetime_str = $_POST['expiry_datetime'] ?? null;
    
    // Handle datetime conversion carefully
    $publish_datetime = !empty($publish_datetime_str) ? date('Y-m-d H:i:s', strtotime($publish_datetime_str)) : null;
    $expiry_datetime = !empty($expiry_datetime_str) ? date('Y-m-d H:i:s', strtotime($expiry_datetime_str)) : null;

    $delete_current_image = isset($_POST['delete_current_image']) && $_POST['delete_current_image'] == '1';
    $current_image_path_val = $_POST['current_image_path_val'] ?? null;

    $image_db_path = $current_image_path_val;

    try {
        $conn->begin_transaction();

        if ($action === 'insert' || $action === 'update') {
            if (empty($title)) {
                throw new Exception("Announcement Title cannot be empty.");
            }
            if ($action === 'insert' && empty($announcement_id_param)) {
                throw new Exception("Announcement ID cannot be empty for a new announcement.");
            }

            if ($delete_current_image && !empty($image_db_path)) {
                if (file_exists($image_db_path)) {
                    @unlink($image_db_path);
                }
                $image_db_path = null;
            }

            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                if (!$delete_current_image && !empty($current_image_path_val) && file_exists($current_image_path_val)) {
                    @unlink($current_image_path_val);
                }
                
                $tmp_name = $_FILES['image_path']['tmp_name'];
                $image_file_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES['image_path']['name']));
                $target_file_name = time() . "_" . uniqid() . "_" . $image_file_name;
                $target_file_path = $upload_announcements_image_dir . $target_file_name;
                $imageFileType = strtolower(pathinfo($target_file_path, PATHINFO_EXTENSION));

                $check = getimagesize($tmp_name);
                if($check === false) { throw new Exception("File is not a valid image."); }
                if ($_FILES["image_path"]["size"] > 5000000) { // 5MB limit
                    throw new Exception("Sorry, image is too large (Max 5MB).");
                }
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if(!in_array($imageFileType, $allowed_types)) {
                    throw new Exception("Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed. Type was: '$imageFileType'");
                }

                if (move_uploaded_file($tmp_name, $target_file_path)) {
                    $image_db_path = $target_file_path;
                } else {
                    throw new Exception("Error uploading image. Check permissions for " . $upload_announcements_image_dir);
                }
            } elseif (isset($_FILES['image_path']) && $_FILES['image_path']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['image_path']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Image upload error: Code " . $_FILES['image_path']['error']);
            }

            if ($action === 'insert') {
                $check_stmt = $conn->prepare("SELECT announcement_id FROM university_announcements WHERE announcement_id = ?");
                $check_stmt->bind_param("s", $announcement_id_param);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    throw new Exception("Announcement ID '$announcement_id_param' already exists. Please use a different ID.");
                } else {
                    $status = 0; // Default status for new announcement is Draft/Inactive
                    $view_count = 0;
                    $stmt = $conn->prepare("INSERT INTO university_announcements (announcement_id, title, content, image_path, target_audience, priority, publish_datetime, expiry_datetime, status, view_count, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->bind_param("sssssisssi", $announcement_id_param, $title, $content, $image_db_path, $target_audience, $priority, $publish_datetime, $expiry_datetime, $status, $view_count);
                    if ($stmt->execute()) {
                        $message = "New announcement created successfully.";
                        $message_type = 'success';
                    } else {
                        throw new Exception("Error creating announcement: " . $stmt->error);
                    }
                    $stmt->close();
                }
                $check_stmt->close();
            } elseif ($action === 'update') {
                $stmt = $conn->prepare("UPDATE university_announcements SET title=?, content=?, image_path=?, target_audience=?, priority=?, publish_datetime=?, expiry_datetime=?, updated_at=NOW() WHERE announcement_id=?");
                $stmt->bind_param("ssssisss", $title, $content, $image_db_path, $target_audience, $priority, $publish_datetime, $expiry_datetime, $announcement_id_param);
                if ($stmt->execute()) {
                    $message = "Announcement updated successfully.";
                    $message_type = 'success';
                } else {
                    throw new Exception("Error updating announcement: " . $stmt->error);
                }
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            if (!empty($announcement_id_param)) {
                $stmt_get_img = $conn->prepare("SELECT image_path FROM university_announcements WHERE announcement_id = ?");
                $stmt_get_img->bind_param("s", $announcement_id_param);
                $stmt_get_img->execute();
                $stmt_get_img->bind_result($imgPathToDelete);
                $stmt_get_img->fetch();
                $stmt_get_img->close();

                if (!empty($imgPathToDelete) && file_exists($imgPathToDelete)) {
                    @unlink($imgPathToDelete);
                }

                $stmt_delete_announcement = $conn->prepare("DELETE FROM university_announcements WHERE announcement_id = ?");
                $stmt_delete_announcement->bind_param("s", $announcement_id_param);
                if ($stmt_delete_announcement->execute()) {
                    $message = "Announcement deleted successfully.";
                    $message_type = 'success';
                } else {
                    throw new Exception("Error deleting announcement: " . $stmt_delete_announcement->error);
                }
                $stmt_delete_announcement->close();
            } else {
                throw new Exception("Announcement ID not provided for deletion.");
            }
        } elseif ($action === 'activate' || $action === 'deactivate') {
            if (!empty($announcement_id_param)) {
                $new_status = ($action === 'activate') ? 1 : 0;
                $action_text = ($action === 'activate') ? 'activated (published)' : 'deactivated (set to draft)';
                $stmt = $conn->prepare("UPDATE university_announcements SET status=?, updated_at=NOW() WHERE announcement_id=?");
                $stmt->bind_param("is", $new_status, $announcement_id_param);
                if ($stmt->execute()) {
                    $message = "Announcement " . $action_text . " successfully."; 
                    $message_type = 'success';
                } else { 
                    throw new Exception("Error " . $action_text . "ing announcement: " . $stmt->error);
                }
                $stmt->close();
            } else { 
                throw new Exception("Announcement ID not provided for status change.");
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        if (isset($conn) && $conn->ping() && $conn->in_transaction) { // Check if $conn is an object and in transaction
            $conn->rollback(); 
        }
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
        error_log("Announcement Management Error: " . $e->getMessage() . "\nPOST data: " . print_r($_POST, true) . "\nFILES data: " . print_r($_FILES, true));
    }
}

// --- Fetch Existing Announcements for Display ---
$announcements_list = [];
if (isset($conn) && $conn instanceof mysqli) { // Check if $conn is a valid mysqli object
    $sql = "SELECT announcement_id, title, content, image_path, target_audience, priority, publish_datetime, expiry_datetime, status, view_count, created_at, updated_at 
            FROM university_announcements 
            ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $announcements_list[] = $row;
        }
    } else {
        if(empty($message)) {
            $message = "Error fetching announcements: " . $conn->error;
            $message_type = 'error';
        }
    }
} else {
    if (empty($message)) {
        $message = "Database connection not available. Cannot fetch or manage announcements.";
        $message_type = 'error';
    }
}
?>

<h2>University Announcements Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New Announcement</h3>
        <form id="announcementForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="insert">
            <input type="hidden" name="current_image_path_val" id="current_image_path_val" value="">
            <input type="hidden" name="delete_current_image" id="delete_current_image" value="0"> 

            <label for="announcement_id_form">Announcement ID (Unique Code, e.g., ANNC001):</label>
            <input type="text" id="announcement_id_form" name="announcement_id" required>

            <label for="title_form">Announcement Title:</label>
            <input type="text" id="title_form" name="title" required>

            <label for="content_form">Announcement Content:</label>
            <textarea id="content_form" name="content" class="tinymce"></textarea>

            <div class="two-column-layout" style="margin-top: 15px;">
                <div>
                    <label for="target_audience_form">Target Audience:</label>
                    <select id="target_audience_form" name="target_audience">
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="faculty">Faculty</option>
                        <option value="staff">Staff</option>
                        <option value="public">Public</option>
                    </select>
                </div>
                <div>
                    <label for="priority_form">Priority:</label>
                    <select id="priority_form" name="priority">
                        <option value="0">Normal</option>
                        <option value="1">High</option>
                    </select>
                </div>
            </div>
            
            <div class="two-column-layout">
                <div>
                    <label for="publish_datetime_form">Publish Date & Time:</label>
                    <input type="datetime-local" id="publish_datetime_form" name="publish_datetime">
                </div>
                <div>
                    <label for="expiry_datetime_form">Expiry Date & Time (Optional):</label>
                    <input type="datetime-local" id="expiry_datetime_form" name="expiry_datetime">
                </div>
            </div>

            <div id="current-image-section" style="display:none; margin-top:15px;">
                <label>Current Image:</label>
                <div id="current_image_preview_container" class="image-preview-container"></div>
            </div>

            <label for="image_path_form" style="margin-top:15px;">Announcement Image (Upload new to replace):</label>
            <input type="file" id="image_path_form" name="image_path" accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Max file size: 5MB. Allowed types: JPG, JPEG, PNG, GIF, WEBP.</small>
            
            <div class="button-group" style="margin-top: 20px;">
                <button type="submit" id="submitBtn">Save Announcement</button>
                <button type="button" id="cancelEditBtn" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="announcements-list-section">
        <h3>Existing Announcements</h3>
        <table class="announcements-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Target</th>
                    <th>Priority</th>
                    <th>Publish On</th>
                    <th>Expires On</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($announcements_list)): ?>
                    <?php foreach ($announcements_list as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['announcement_id']); ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td class="announcement-image-cell">
                                <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_path']) . '?t=' . @filemtime($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(ucfirst($item['target_audience'])); ?></td>
                            <td><?php echo $item['priority'] == 1 ? 'High' : 'Normal'; ?></td>
                            <td><?php echo $item['publish_datetime'] ? htmlspecialchars(date("Y-m-d H:i", strtotime($item['publish_datetime']))) : 'N/A'; ?></td>
                            <td><?php echo $item['expiry_datetime'] ? htmlspecialchars(date("Y-m-d H:i", strtotime($item['expiry_datetime']))) : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($item['view_count']); ?></td>
                            <td><span class="status-<?php echo $item['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $item['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($item['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($item['updated_at']))); ?></td>
                            <td class="actions-cell">
                                <button class="edit-btn"
                                    data-id="<?php echo htmlspecialchars($item['announcement_id']); ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-content="<?php echo htmlspecialchars($item['content']); ?>"
                                    data-image_path="<?php echo htmlspecialchars($item['image_path'] ?? ''); ?>"
                                    data-target_audience="<?php echo htmlspecialchars($item['target_audience']); ?>"
                                    data-priority="<?php echo htmlspecialchars($item['priority']); ?>"
                                    data-publish_datetime="<?php echo $item['publish_datetime'] ? htmlspecialchars(date("Y-m-d\TH:i", strtotime($item['publish_datetime']))) : ''; ?>"
                                    data-expiry_datetime="<?php echo $item['expiry_datetime'] ? htmlspecialchars(date("Y-m-d\TH:i", strtotime($item['expiry_datetime']))) : ''; ?>"
                                    type="button">Edit</button>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($item['announcement_id']); ?>">
                                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this announcement? This cannot be undone.');">Delete</button>
                                </form>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($item['announcement_id']); ?>">
                                    <?php if ($item['status'] == 1): ?>
                                        <button type="submit" name="action" value="deactivate">Deactivate</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="activate">Activate</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align: center;">No announcements found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> 
<script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> <script>
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'code lists link image media table wordcount fullscreen preview searchreplace help autoresize',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code | fullscreen preview | searchreplace | help',
        height: 350,
        menubar: 'file edit view insert format tools table help',
        autoresize_bottom_margin: 20,
        image_advtab: true,
        // Example image upload handler (you'll need a backend script for this)
        // images_upload_url: 'your_image_upload_handler.php', 
        // images_upload_base_path: '/uploads/announcement_content_images/', // Base path for content images
        // automatic_uploads: true,
        // file_picker_types: 'image media',
        // file_picker_callback: function(cb, value, meta) { /* ... */ } 
    });

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('announcementForm');
    const formTitle = document.getElementById('formTitle');
    const formActionInput = document.getElementById('formAction');
    
    const announcementIdInput = document.getElementById('announcement_id_form');
    const titleInput = document.getElementById('title_form');
    const targetAudienceInput = document.getElementById('target_audience_form');
    const priorityInput = document.getElementById('priority_form');
    const publishDatetimeInput = document.getElementById('publish_datetime_form');
    const expiryDatetimeInput = document.getElementById('expiry_datetime_form');
    const contentEditor = tinymce.get('content_form'); // Get editor instance

    const submitButton = document.getElementById('submitBtn');
    const cancelEditButton = document.getElementById('cancelEditBtn');
    
    const currentImageSection = document.getElementById('current-image-section');
    const currentImagePreviewContainer = document.getElementById('current_image_preview_container');
    const currentImagePathInput = document.getElementById('current_image_path_val');
    const deleteCurrentImageInput = document.getElementById('delete_current_image');
    const newImageInput = document.getElementById('image_path_form');

    function resetFormToDefaults() {
        form.reset(); 
        formTitle.textContent = 'Add New Announcement';
        formActionInput.value = 'insert';
        
        announcementIdInput.value = '';
        announcementIdInput.readOnly = false;
        titleInput.value = '';
        targetAudienceInput.value = 'all';
        priorityInput.value = '0';
        publishDatetimeInput.value = '';
        expiryDatetimeInput.value = '';

        if (tinymce.get('content_form')) {
            tinymce.get('content_form').setContent('');
        }
        
        currentImagePreviewContainer.innerHTML = '';
        currentImageSection.style.display = 'none';
        currentImagePathInput.value = '';
        deleteCurrentImageInput.value = '0';
        newImageInput.value = ''; 

        cancelEditButton.style.display = 'none';
        submitButton.textContent = 'Save Announcement';
        window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
    }

    cancelEditButton.addEventListener('click', function() {
        resetFormToDefaults();
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            formTitle.textContent = 'Edit Announcement: ' + data.title;
            formActionInput.value = 'update';
            
            announcementIdInput.value = data.id;
            announcementIdInput.readOnly = true;

            titleInput.value = data.title;
            targetAudienceInput.value = data.target_audience || 'all';
            priorityInput.value = data.priority || '0';
            publishDatetimeInput.value = data.publish_datetime || '';
            expiryDatetimeInput.value = data.expiry_datetime || '';
            
            if (tinymce.get('content_form')) {
                tinymce.get('content_form').setContent(data.content || '');
            } else {
                // Fallback if TinyMCE isn't ready, though unlikely
                document.getElementById('content_form').value = data.content || '';
            }
            
            submitButton.textContent = 'Update Announcement';
            cancelEditButton.style.display = 'inline-block';

            currentImagePreviewContainer.innerHTML = ''; 
            deleteCurrentImageInput.value = '0'; 
            newImageInput.value = ''; 

            if (data.image_path && data.image_path.trim() !== '') {
                currentImageSection.style.display = 'block';
                currentImagePathInput.value = data.image_path;

                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';

                const img = document.createElement('img');
                img.src = data.image_path + '?t=' + new Date().getTime(); 
                img.alt = 'Current announcement image';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-current-image-btn';
                removeBtn.textContent = 'Remove Current Image';

                removeBtn.addEventListener('click', function() {
                    currentImagePreviewContainer.innerHTML = '<p><em>Image will be removed upon saving.</em></p>';
                    deleteCurrentImageInput.value = '1'; 
                });

                previewItem.appendChild(img);
                currentImagePreviewContainer.appendChild(previewItem);
                currentImagePreviewContainer.appendChild(removeBtn);
            } else {
                currentImageSection.style.display = 'none';
                currentImagePathInput.value = '';
            }
            
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
        });
    });
});

</script>
<?php
// Close connection if it was opened and is valid
// if (isset($conn) && $conn instanceof mysqli) {
// $conn->close();
// }
// include('include/footer.php'); // Ensure this path is correct
?>
</body>
</html>