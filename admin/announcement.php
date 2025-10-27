<?php
// --- HEADER AND SIDEBAR INCLUDES ---
include('include/header.php');
include('include/sidebar.php');

// --- DATABASE CONNECTION ---
include('include/config.php');

// --- PHP LOGIC ---
$message = '';
$message_type = '';
$user_role = $_SESSION['role'] ?? 'user';
$is_super_admin = ($user_role === 'super_admin') || (isset($_SESSION['cid']) && $_SESSION['cid'] === 'SAdmin');

/**
 * Security function to check if the user can manage a specific news article.
 */
function canManageNews($conn, $announcement_id, $user_role, $user_cid) {
    // Super admins can always manage.
    if (($user_role === 'super_admin') || ($user_cid === 'SAdmin')) {
        return true;
    }
    // For this page, we assume sub-admins cannot manage announcements created by others.
    // This function can be expanded if sub-admins need more specific permissions.
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $announcement_id = $_POST['announcement_id'] ?? null;
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $target_audience = $_POST['target_audience'] ?? 'all';
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 0;
    $publish_datetime = !empty($_POST['publish_datetime']) ? date('Y-m-d H:i:s', strtotime($_POST['publish_datetime'])) : null;
    $expiry_datetime = !empty($_POST['expiry_datetime']) ? date('Y-m-d H:i:s', strtotime($_POST['expiry_datetime'])) : null;
    $current_image_path = $_POST['current_image_path_val'] ?? null;
    $delete_current_image = isset($_POST['delete_current_image']) && $_POST['delete_current_image'] == '1';
    $image_db_path = $current_image_path;

    try {
        $conn->begin_transaction();

        if ($action === 'insert' || $action === 'update') {
            if (empty($title)) { throw new Exception("Announcement Title cannot be empty."); }

            // Handle image deletion
            if ($delete_current_image && !empty($image_db_path) && file_exists($image_db_path)) {
                @unlink($image_db_path);
                $image_db_path = null;
            }

            // Handle new image upload
            if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
                if (!$delete_current_image && !empty($current_image_path) && file_exists($current_image_path)) {
                    @unlink($current_image_path);
                }
                $upload_dir = "uploads/announcement_images/";
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
                
                $image_name = time() . "_" . uniqid() . "_" . preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES['image_path']['name']));
                $target_file = $upload_dir . $image_name;
                
                if (!move_uploaded_file($_FILES['image_path']['tmp_name'], $target_file)) {
                    throw new Exception("Error uploading the new image.");
                }
                $image_db_path = $target_file;
            }

            if ($action === 'insert') {
                // *** AUTOMATIC ID GENERATION ***
                $announcement_id = 'ANN_' . strtoupper(uniqid());
                
                $status = $is_super_admin ? 0 : 2; // 0=Draft for admin, 2=Pending for sub-admin
                
                $stmt = $conn->prepare("INSERT INTO university_announcements (announcement_id, title, content, image_path, target_audience, priority, publish_datetime, expiry_datetime, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $stmt->bind_param("sssssissi", $announcement_id, $title, $content, $image_db_path, $target_audience, $priority, $publish_datetime, $expiry_datetime, $status);
                
                if ($stmt->execute()) {
                    $message = $is_super_admin ? "New announcement created as a draft." : "New announcement has been submitted for review.";
                    $message_type = 'success';
                } else { throw new Exception("Error creating announcement: " . $stmt->error); }

            } elseif ($action === 'update') {
                if ($is_super_admin) {
                    $stmt = $conn->prepare("UPDATE university_announcements SET title=?, content=?, image_path=?, target_audience=?, priority=?, publish_datetime=?, expiry_datetime=?, updated_at=NOW() WHERE announcement_id=?");
                    $stmt->bind_param("ssssisss", $title, $content, $image_db_path, $target_audience, $priority, $publish_datetime, $expiry_datetime, $announcement_id);
                } else {
                    $new_status = 2; // Updates from sub-admins go back to pending review
                    $stmt = $conn->prepare("UPDATE university_announcements SET title=?, content=?, image_path=?, target_audience=?, priority=?, publish_datetime=?, expiry_datetime=?, status=?, updated_at=NOW() WHERE announcement_id=?");
                    $stmt->bind_param("ssssissis", $title, $content, $image_db_path, $target_audience, $priority, $publish_datetime, $expiry_datetime, $new_status, $announcement_id);
                }
                if ($stmt->execute()) {
                    $message = $is_super_admin ? "Announcement updated successfully." : "Announcement changes submitted for review.";
                    $message_type = 'success';
                } else { throw new Exception("Error updating announcement: " . $stmt->error); }
            }

        } elseif (in_array($action, ['delete', 'activate', 'deactivate'])) {
            if (!$is_super_admin) { throw new Exception("You do not have permission for this action."); }
            
            if ($action === 'delete') {
                // First, get the image path to delete the file
                $stmt_get_img = $conn->prepare("SELECT image_path FROM university_announcements WHERE announcement_id = ?");
                $stmt_get_img->bind_param("s", $announcement_id);
                $stmt_get_img->execute();
                $imgPathToDelete = $stmt_get_img->get_result()->fetch_assoc()['image_path'] ?? null;
                $stmt_get_img->close();

                if ($imgPathToDelete && file_exists($imgPathToDelete)) { @unlink($imgPathToDelete); }

                $stmt = $conn->prepare("DELETE FROM university_announcements WHERE announcement_id = ?");
                $stmt->bind_param("s", $announcement_id);
                if ($stmt->execute()) { $message = "Announcement deleted."; $message_type = 'success'; }

            } else { // Activate or Deactivate
                $new_status = ($action === 'activate') ? 1 : 0;
                $action_text = ($new_status === 1) ? 'published' : 'deactivated';
                $stmt = $conn->prepare("UPDATE university_announcements SET status=?, updated_at=NOW() WHERE announcement_id=?");
                $stmt->bind_param("is", $new_status, $announcement_id);
                if ($stmt->execute()) { $message = "Announcement has been " . $action_text . "."; $message_type = 'success'; }
            }
        }
        $conn->commit();

    } catch (Exception $e) {
        if ($conn->in_transaction) { $conn->rollback(); }
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
    } finally {
        if (isset($stmt)) { $stmt->close(); }
    }
}

// --- Fetch data for the table ---
$announcements_list = [];
$sql = "SELECT * FROM university_announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result) {
    $announcements_list = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<style>
/* Your existing CSS styles... */
body { display: flex; }
.page-container { margin-left: 300px; padding: 25px; width: calc(100% - 325px); box-sizing: border-box; }
:root { --primary-color: #007bff; --secondary-color: #6c757d; --success-color: #28a745; --danger-color: #dc3545; --warning-color: #ffc107; --light-color: #f8f9fa; --dark-color: #343a40; --border-radius: 0.3rem; }
h2, h3 { color: var(--primary-color); margin-bottom: 1.5rem; text-align: center; font-weight: 600; }
h3 { color: var(--dark-color); margin-top: 2rem; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
.message-area { padding: 12px 18px; margin-bottom: 25px; border-radius: var(--border-radius); border: 1px solid transparent; }
.message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.form-section { margin-bottom: 30px; padding: 25px; background-color: var(--light-color); border-radius: var(--border-radius); border: 1px solid #ddd; }
.form-section label { display: block; margin-bottom: 8px; font-weight: 600; }
.form-section input, .form-section select, .form-section textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: var(--border-radius); box-sizing: border-box; }
.form-section button { background-color: var(--primary-color); color: white; padding: 12px 25px; border: none; border-radius: var(--border-radius); cursor: pointer; margin-right: 10px; }
.form-section button#cancelEditBtn { background-color: var(--secondary-color); }
.two-column-layout { display: flex; gap: 20px; }
.two-column-layout > div { flex: 1; }
.announcements-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.announcements-table th, .announcements-table td { border: 1px solid #e0e0e0; padding: 10px 12px; text-align: left; vertical-align: middle; font-size: 0.9rem; }
.announcements-table th { background-color: #f2f5f8; font-weight: 600; }
.announcements-table img { max-width: 70px; border-radius: var(--border-radius); }
.actions-cell { white-space: nowrap; }
.actions-cell form { display: inline-block; }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }
.status-pending { color: var(--warning-color); font-weight: bold; }
</style>

<div class="page-container">
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
            <input type="hidden" name="announcement_id" id="announcement_id_hidden">
            <input type="hidden" name="current_image_path_val" id="current_image_path_val">
            <input type="hidden" name="delete_current_image" id="delete_current_image" value="0">

            <label for="title_form">Announcement Title:</label>
            <input type="text" id="title_form" name="title" required>

            <label for="content_form">Announcement Content:</label>
            <textarea id="content_form" name="content"></textarea>

            <div class="two-column-layout" style="margin-top: 15px;">
                <div>
                    <label for="target_audience_form">Target Audience:</label>
                    <select id="target_audience_form" name="target_audience">
                        <option value="all">All</option> <option value="students">Students</option> <option value="faculty">Faculty</option> <option value="staff">Staff</option> <option value="public">Public</option>
                    </select>
                </div>
                <div>
                    <label for="priority_form">Priority:</label>
                    <select id="priority_form" name="priority">
                        <option value="0">Normal</option> <option value="1">High</option>
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
                <div id="current_image_preview_container"></div>
            </div>

            <label for="image_path_form" style="margin-top:15px;">Announcement Image (Upload new to replace):</label>
            <input type="file" id="image_path_form" name="image_path" accept="image/*">
            
            <div class="button-group" style="margin-top: 20px;">
                <button type="submit" id="submitBtn"><?php echo $is_super_admin ? 'Save Announcement' : 'Submit for Review'; ?></button>
                <button type="button" id="cancelEditBtn" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr>

    <div class="announcements-list-section">
        <h3>Existing Announcements</h3>
        <table class="announcements-table">
            <thead>
                <tr><th>ID</th><th>Title</th><th>Image</th><th>Target</th><th>Status</th><th>Updated</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($announcements_list)): ?>
                    <tr><td colspan="7" style="text-align: center;">No announcements found.</td></tr>
                <?php else: foreach ($announcements_list as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['announcement_id']); ?></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php if (!empty($item['image_path'])): ?><img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt=""><?php endif; ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($item['target_audience'])); ?></td>
                        <td>
                            <?php
                                $status_text = 'Inactive/Rejected'; $status_class = 'inactive';
                                if ($item['status'] == 1) { $status_text = 'Active'; $status_class = 'active'; }
                                elseif ($item['status'] == 2) { $status_text = 'Pending Review'; $status_class = 'pending'; }
                            ?>
                            <span class="status-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </td>
                        <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($item['updated_at']))); ?></td>
                        <td class="actions-cell">
                            <button class="edit-btn" data-item='<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>' type="button">Edit</button>
                            <?php if ($is_super_admin): ?>
                                <form action="" method="POST" onsubmit="return confirm('Delete this announcement?');">
                                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($item['announcement_id']); ?>">
                                    <button type="submit" name="action" value="delete">Delete</button>
                                </form>
                                <?php if ($item['status'] != 1): ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($item['announcement_id']); ?>">
                                        <button type="submit" name="action" value="activate"><?php echo ($item['status'] == 2) ? 'Approve' : 'Activate'; ?></button>
                                    </form>
                                <?php else: ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($item['announcement_id']); ?>">
                                        <button type="submit" name="action" value="deactivate">Deactivate</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#content_form',
        plugins: 'code lists link image media table wordcount fullscreen preview',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | table | code | fullscreen',
        height: 350,
    });

document.addEventListener('DOMContentLoaded', function() {
    const userRole = '<?php echo $user_role; ?>';
    const form = document.getElementById('announcementForm');
    const formTitle = document.getElementById('formTitle');
    const formActionInput = document.getElementById('formAction');
    const announcementIdHiddenInput = document.getElementById('announcement_id_hidden');
    const submitButton = document.getElementById('submitBtn');
    const cancelEditButton = document.getElementById('cancelEditBtn');
    const currentImageSection = document.getElementById('current-image-section');
    const currentImagePreviewContainer = document.getElementById('current_image_preview_container');
    const currentImagePathInput = document.getElementById('current_image_path_val');
    const deleteCurrentImageInput = document.getElementById('delete_current_image');

    function resetFormToDefaults() {
        form.reset();
        formTitle.textContent = 'Add New Announcement';
        formActionInput.value = 'insert';
        announcementIdHiddenInput.value = '';
        if (tinymce.get('content_form')) { tinymce.get('content_form').setContent(''); }
        currentImageSection.style.display = 'none';
        currentImagePreviewContainer.innerHTML = '';
        currentImagePathInput.value = '';
        deleteCurrentImageInput.value = '0';
        cancelEditButton.style.display = 'none';
        submitButton.textContent = (userRole === 'super_admin') ? 'Save Announcement' : 'Submit for Review';
        window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
    }

    cancelEditButton.addEventListener('click', resetFormToDefaults);

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const data = JSON.parse(this.dataset.item);

            formTitle.textContent = 'Edit Announcement: ' + data.title;
            formActionInput.value = 'update';
            announcementIdHiddenInput.value = data.announcement_id;

            document.getElementById('title_form').value = data.title;
            document.getElementById('target_audience_form').value = data.target_audience || 'all';
            document.getElementById('priority_form').value = data.priority || '0';
            document.getElementById('publish_datetime_form').value = data.publish_datetime ? data.publish_datetime.replace(' ', 'T') : '';
            document.getElementById('expiry_datetime_form').value = data.expiry_datetime ? data.expiry_datetime.replace(' ', 'T') : '';
            if (tinymce.get('content_form')) { tinymce.get('content_form').setContent(data.content || ''); }

            submitButton.textContent = (userRole === 'super_admin') ? 'Update Announcement' : 'Submit Changes for Review';
            cancelEditButton.style.display = 'inline-block';
            
            currentImagePreviewContainer.innerHTML = '';
            deleteCurrentImageInput.value = '0';

            if (data.image_path) {
                currentImageSection.style.display = 'block';
                currentImagePathInput.value = data.image_path;
                currentImagePreviewContainer.innerHTML = `<img src="${data.image_path}" alt="Current Image" style="max-width:150px; border-radius: 5px;"> <button type="button" id="removeImgBtn" style="background:red; color:white; border:0; padding:5px; cursor:pointer; border-radius:3px; margin-top:5px;">Remove</button>`;
                
                document.getElementById('removeImgBtn').addEventListener('click', () => {
                    deleteCurrentImageInput.value = '1';
                    currentImagePreviewContainer.innerHTML = '<p><em>Image will be removed upon saving.</em></p>';
                });
            } else {
                currentImageSection.style.display = 'none';
                currentImagePathInput.value = '';
            }
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
        });
    });
});
</script>

<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/0b4l260nbwgikhaerenongs5zgl39j7pja3yimxlbjkkfrs6/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>


</body>
</html>