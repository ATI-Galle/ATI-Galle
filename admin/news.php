<?php
// --- HEADER AND SIDEBAR INCLUDES ---
include('include/header.php');
include('include/sidebar.php');

// --- DATABASE CONNECTION ---
include('include/config.php');

// --- PHP LOGIC ---
$message = '';
$message_type = '';
$is_super_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') || (isset($_SESSION['cid']) && $_SESSION['cid'] === 'SAdmin');

function canManageNews($conn, $news_id, $user_role, $user_cid) {
    if (($user_role === 'super_admin') || ($user_cid === 'SAdmin')) { return true; }
    if ($user_role === 'sub_admin' && !empty($user_cid) && !empty($news_id)) {
        $stmt = $conn->prepare("SELECT cid FROM news WHERE nid = ?");
        $stmt->bind_param("i", $news_id);
        if ($stmt->execute()) {
            $news_item = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return ($news_item && $news_item['cid'] === $user_cid);
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $nid = $_POST['nid'] ?? null;
    $ntitle = $_POST['ntitle'] ?? '';
    $ntag = $_POST['ntag'] ?? '';
    $cid = $_POST['cid'] ?? null;
    $ntext = $_POST['ntext'] ?? '';
    $existing_nimg = $_POST['existing_nimg'] ?? '';
    $nimg = $existing_nimg;
    
    $permission_granted = true;

    if (in_array($action, ['update', 'delete', 'activate', 'deactivate', 'approve', 'reject'])) {
        if (!canManageNews($conn, $nid, $_SESSION['role'], $_SESSION['cid'])) {
            $message = "Permission Denied: You cannot manage this news article.";
            $message_type = 'error';
            $permission_granted = false;
        }
    }
    
    if ($permission_granted) {
        // Image Upload Logic... (No changes here)
        $uploadOk = 1;
        if (isset($_FILES['nimg']) && $_FILES['nimg']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/news_images/";
            if (!is_dir($target_dir)) { @mkdir($target_dir, 0755, true); }
            $image_name = time() . "_" . preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', basename($_FILES["nimg"]["name"]));
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (@getimagesize($_FILES["nimg"]["tmp_name"]) === false) { $message = "File is not a valid image."; $uploadOk = 0; }
            if ($_FILES["nimg"]["size"] > 5000000) { $message = "Image file is too large (Max 5MB)."; $uploadOk = 0; }
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { $message = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed."; $uploadOk = 0; }

            if ($uploadOk && move_uploaded_file($_FILES["nimg"]["tmp_name"], $target_file)) {
                $nimg = $target_file;
                if ($action === 'update' && !empty($existing_nimg) && file_exists($existing_nimg)) { @unlink($existing_nimg); }
            } elseif ($uploadOk) {
                $message = "Error uploading image file."; $uploadOk = 0;
            }
        }
        
        if ($uploadOk) {
            try {
                switch ($action) {
                    case 'insert':
                        $status = $is_super_admin ? 1 : 2;
                        $current_cid = $is_super_admin ? $cid : $_SESSION['cid'];
                        // MODIFIED: Removed created_by field
                        $stmt = $conn->prepare("INSERT INTO news (ntitle, ntag, cid, ntext, nimg, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                        $stmt->bind_param("sssssi", $ntitle, $ntag, $current_cid, $ntext, $nimg, $status);
                        if ($stmt->execute()) {
                            $message = $is_super_admin ? "News created successfully." : "News submitted for approval.";
                            $message_type = 'success';
                        }
                        $stmt->close();
                        break;
                    case 'update':
                        // Update logic remains the same
                        $status_sql_part = $is_super_admin ? "" : ", status = 2";
                        $current_cid = $is_super_admin ? $cid : $_SESSION['cid'];
                        $stmt = $conn->prepare("UPDATE news SET ntitle=?, ntag=?, cid=?, ntext=?, nimg=?, updated_at=NOW() $status_sql_part WHERE nid=?");
                        $stmt->bind_param("sssssi", $ntitle, $ntag, $current_cid, $ntext, $nimg, $nid);
                        if ($stmt->execute()) {
                            $message = $is_super_admin ? "News updated successfully." : "News re-submitted for approval.";
                            $message_type = 'success';
                        }
                        $stmt->close();
                        break;
                    
                    // Super Admin actions
                    case 'delete':
                    case 'activate':
                    case 'deactivate':
                    case 'approve':
                    case 'reject':
                         if (!$is_super_admin) {
                            $message = "Permission Denied."; $message_type = 'error'; break;
                        }
                        if ($action === 'delete') {
                            $stmt = $conn->prepare("DELETE FROM news WHERE nid = ?");
                            $stmt->bind_param("i", $nid);
                        } else {
                            $new_status = 1; // Default to active
                            if ($action === 'deactivate' || $action === 'reject') $new_status = 0;
                            if ($action === 'activate' || $action === 'approve') $new_status = 1;

                            $stmt = $conn->prepare("UPDATE news SET status=? WHERE nid=?");
                            $stmt->bind_param("ii", $new_status, $nid);
                        }
                        if ($stmt->execute()) { $message = "Action completed successfully."; $message_type = 'success'; }
                        $stmt->close();
                        break;
                }
            } catch (mysqli_sql_exception $e) {
                $message = "Database error: " . $e->getMessage(); $message_type = 'error';
            }
        }
    }
}

// --- Fetch Data for Display ---
$courses = $conn->query("SELECT cid, cname FROM course WHERE status = 1 ORDER BY cname ASC")->fetch_all(MYSQLI_ASSOC);
$sql = "SELECT n.*, c.cname FROM news n LEFT JOIN course c ON n.cid = c.cid";
if (!$is_super_admin) {
    $sql .= " WHERE n.cid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $_SESSION['cid']);
} else {
    $sql .= " ORDER BY n.created_at DESC";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$news_articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
    /* --- Root Variables and Basic Styles --- */
    :root { --primary-color: #007bff; --secondary-color: #6c757d; --success-color: #28a745; --danger-color: #dc3545; --warning-color: #ffc107; --light-color: #f8f9fa; --dark-color: #343a40; --border-radius: 0.4rem; --box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    body { display: flex; background-color: #f4f6f9; }
    .page-container { margin-left: 265px; padding: 25px; width: calc(100% - 265px); }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    h2 { color: var(--dark-color); margin: 0; }
    .message-area { padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--border-radius); border: 1px solid transparent; text-align: center; }
    .message-area.success { background-color: #d4edda; color: #155724; }
    .message-area.error { background-color: #f8d7da; color: #721c24; }
    .add-news-btn { background-color: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 1rem; font-weight: 500; }

    /* --- Card View Styles --- */
    .news-card-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
    .news-card { background-color: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); display: flex; flex-direction: column; overflow: hidden; }
    .card-img-top { width: 100%; height: 180px; object-fit: cover; background-color: #e9ecef; }
    .card-body { padding: 20px; flex-grow: 1; }
    .card-title { font-size: 1.2rem; font-weight: 600; margin: 0 0 10px 0; }
    .card-department { font-size: 0.9rem; color: var(--secondary-color); margin-bottom: 15px; }
    .card-footer { background-color: var(--light-color); padding: 15px 20px; border-top: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; }
    .status-badge { padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; font-weight: bold; color: white; }
    .status-active { background-color: var(--success-color); }
    .status-pending { background-color: var(--warning-color); color: #333; }
    .status-inactive { background-color: var(--secondary-color); }
    
    /* --- Action Button Styles --- */
    .action-buttons form { display: inline-block; margin-left: 5px; }
    .action-buttons button { padding: 6px 12px; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; border: none; }
    .edit-btn { background-color: var(--warning-color); color: #333; }
    .delete-btn { background-color: var(--danger-color); }
    .activate-btn { background-color: var(--success-color); }
    .deactivate-btn { background-color: var(--secondary-color); }

    /* --- Modal Styles --- */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-content { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; padding-bottom: 15px; margin-bottom: 20px; }
    .modal-title { font-size: 1.5rem; color: var(--dark-color); margin: 0; }
    .close-modal { font-size: 2rem; cursor: pointer; background: none; border: none; }
    .modal-form label { display: block; margin-bottom: 8px; font-weight: 600; }
    .modal-form input, .modal-form textarea, .modal-form select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: var(--border-radius); }
    .modal-footer { text-align: right; margin-top: 20px; border-top: 1px solid #e9ecef; padding-top: 20px; }
    .modal-footer button { padding: 12px 25px; border-radius: var(--border-radius); cursor: pointer; border: none; }
    #modalSubmitButton { background-color: var(--primary-color); color: white; }
    #modalCancelButton { background-color: var(--secondary-color); color: white; margin-left: 10px; }
</style>

<div class="page-container">
    <div class="page-header">
        <h2>News Management</h2>
        <button id="addNewsBtn" class="add-news-btn">+ Add New Article</button>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="news-card-container">
        <?php foreach ($news_articles as $news): ?>
            <div class="news-card">
                <img src="<?php echo !empty($news['nimg']) ? htmlspecialchars($news['nimg']) : 'path/to/default-image.jpg'; ?>" alt="News Image" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($news['ntitle']); ?></h5>
                    <p class="card-department"><?php echo htmlspecialchars($news['cname'] ?? 'General'); ?></p>
                </div>
                <div class="card-footer">
                    <?php
                        $status_class = 'inactive'; $status_text = 'Rejected/Inactive';
                        if ($news['status'] == 1) { $status_class = 'active'; $status_text = 'Active'; }
                        if ($news['status'] == 2) { $status_class = 'pending'; $status_text = 'Pending'; }
                    ?>
                    <span class="status-badge status-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    
                    <div class="action-buttons">
                        <button class="edit-btn" data-news='<?php echo htmlspecialchars(json_encode($news), ENT_QUOTES, 'UTF-8'); ?>'>Edit</button>
                        
                        <?php if ($is_super_admin): ?>
                            <?php if ($news['status'] == 1): // If Active, show Deactivate button ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="nid" value="<?php echo $news['nid']; ?>">
                                    <button type="submit" name="action" value="deactivate" class="deactivate-btn">Deactivate</button>
                                </form>
                            <?php else: // If Inactive, Rejected, or Pending, show Activate/Approve button ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="nid" value="<?php echo $news['nid']; ?>">
                                    <button type="submit" name="action" value="activate" class="activate-btn">Activate</button>
                                </form>
                            <?php endif; ?>
                            
                            <form action="" method="POST" onsubmit="return confirm('Permanently delete this news?');">
                                <input type="hidden" name="nid" value="<?php echo $news['nid']; ?>">
                                <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="newsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle" class="modal-title">Add New Article</h3>
            <button id="closeModalBtn" class="close-modal">&times;</button>
        </div>
        <form id="newsForm" class="modal-form" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="nid" id="nid">
            <input type="hidden" name="existing_nimg" id="existing_nimg">

            <label for="ntitle">News Title:</label>
            <input type="text" id="ntitle" name="ntitle" required>
            
            <label for="cid">Department:</label>
            <select id="cid" name="cid" <?php if (!$is_super_admin) echo 'disabled'; ?>>
                <option value="">General News</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo htmlspecialchars($course['cid']); ?>"><?php echo htmlspecialchars($course['cname']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="ntag">SEO Tags:</label>
            <input type="text" id="ntag" name="ntag">

            <label for="ntext">News Content:</label>
            <textarea id="ntext" name="ntext" rows="6"></textarea>

            <label for="nimg">News Image:</label>
            <input type="file" id="nimg" name="nimg" accept="image/*">
            <img id="current_nimg_preview" src="" alt="Current Image" style="display: none; max-width: 150px; margin-top: 10px;">

            <div class="modal-footer">
                <button type="button" id="modalCancelButton">Cancel</button>
                <button type="submit" id="modalSubmitButton">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// Javascript for modal functionality remains the same
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({ selector: 'textarea#ntext', height: 250, plugins: 'lists link image charmap', toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image' });

    const modal = document.getElementById('newsModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('newsForm');
    const submitButton = document.getElementById('modalSubmitButton');
    const isSuperAdmin = <?php echo json_encode($is_super_admin); ?>;

    const actionInput = form.querySelector('#action');
    const nidInput = form.querySelector('#nid');
    const ntitleInput = form.querySelector('#ntitle');
    const ntagInput = form.querySelector('#ntag');
    const cidSelect = form.querySelector('#cid');
    const existingNimgInput = form.querySelector('#existing_nimg');
    const previewImg = form.querySelector('#current_nimg_preview');

    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    
    document.getElementById('addNewsBtn').addEventListener('click', () => {
        form.reset();
        actionInput.value = 'insert';
        modalTitle.textContent = 'Add New Article';
        submitButton.textContent = isSuperAdmin ? 'Save News' : 'Submit for Approval';
        previewImg.style.display = 'none';
        tinymce.get('ntext').setContent('');
        if (!isSuperAdmin) { cidSelect.value = "<?php echo $_SESSION['cid'] ?? ''; ?>"; }
        openModal();
    });

    document.querySelector('.news-card-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn')) {
            const newsData = JSON.parse(e.target.dataset.news);
            actionInput.value = 'update';
            nidInput.value = newsData.nid;
            ntitleInput.value = newsData.ntitle;
            ntagInput.value = newsData.ntag;
            cidSelect.value = newsData.cid;
            existingNimgInput.value = newsData.nimg;
            tinymce.get('ntext').setContent(newsData.ntext || '');
            
            if (newsData.nimg) {
                previewImg.src = newsData.nimg;
                previewImg.style.display = 'block';
            } else {
                previewImg.style.display = 'none';
            }

            modalTitle.textContent = 'Edit News Article';
            submitButton.textContent = isSuperAdmin ? 'Update News' : 'Re-Submit for Approval';
            openModal();
        }
    });

    document.getElementById('closeModalBtn').addEventListener('click', closeModal);
    document.getElementById('modalCancelButton').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) { closeModal(); } });
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
