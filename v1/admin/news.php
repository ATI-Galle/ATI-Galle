<?php

include('include/header.php');
?>

<style>
    /* --- Basic Layout for Fixed Sidebar --- */
    body {
        display: flex; /* Enable flexbox layout */
    }

    /* Adjust main content area to account for the fixed sidebar */
    .page-container {
        margin-left: 265px; /* Adjust based on your sidebar's width */
        padding: 25px;
        width: calc(100% - 265px); /* Full width minus sidebar */
        box-sizing: border-box;
        flex-grow: 1;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-top: 5px solid #007bff;
        position: relative;
    }

    /* --- General Styles --- */
    :root {
        --primary-color: #007bff;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-radius: 0.3rem;
    }

    h2,
    h3 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: 600;
    }

    h3 {
        color: var(--dark-color);
        margin-top: 2rem;
        text-align: left;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }

    /* --- Messages --- */
    .message-area {
        padding: 12px 18px;
        margin-bottom: 25px;
        border-radius: var(--border-radius);
        border: 1px solid transparent;
        font-size: 0.95rem;
        text-align: center;
    }

    .message-area.success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .message-area.error {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    /* --- Form Styling --- */
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
    .form-section input[type="file"],
    .form-section textarea,
    .form-section select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: var(--border-radius);
        box-sizing: border-box;
        font-size: 1rem;
    }

    .form-section input:read-only,
    .form-section select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .form-section .image-preview {
        margin-top: 10px;
        max-width: 150px;
        max-height: 100px;
        border: 1px solid #ddd;
        padding: 5px;
        background: #fff;
        border-radius: var(--border-radius);
    }
    .form-section .button-group {
        margin-top: 15px;
    }
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
    .form-section button:hover {
        background-color: #0056b3;
    }
    .form-section button#cancelEdit {
        background-color: var(--secondary-color);
    }
    .form-section button#cancelEdit:hover {
        background-color: #5a6268;
    }

    /* --- News Table Styling --- */
    .news-list-section { margin-top: 30px; overflow-x: auto; }
    .news-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 1px 5px rgba(0,0,0,0.08); }
    .news-table th, .news-table td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: left; vertical-align: middle; }
    .news-table th { background-color: #f2f5f8; font-weight: 600; white-space: nowrap; }
    .news-table tbody tr:nth-child(even) { background-color: var(--light-color); }
    .news-table tbody tr:hover { background-color: #e9ecef; }
    .news-table img { display: block; max-width: 80px; height: auto; border-radius: var(--border-radius); }
    .news-table .actions-cell { white-space: nowrap; min-width: 240px; }
    .news-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
    .news-table .actions-cell button, .news-table .actions-cell .edit-btn { padding: 6px 12px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; transition: background-color 0.2s ease; }
    .news-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
    .news-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
    .news-table .actions-cell button[value='delete'] { background-color: var(--danger-color); }
    .news-table .actions-cell button[value='delete']:hover { background-color: #c82333; }
    .news-table .actions-cell button[value='activate'] { background-color: var(--success-color); }
    .news-table .actions-cell button[value='activate']:hover { background-color: #218838; }
    .news-table .actions-cell button[value='deactivate'] { background-color: var(--secondary-color); }
    .news-table .actions-cell button[value='deactivate']:hover { background-color: #5a6268; }
    .status-active { color: var(--success-color); font-weight: bold; }
    .status-inactive { color: var(--secondary-color); font-weight: bold; }

</style>

<?php include('include/sidebar.php'); ?>

<div class="page-container">
    <?php
    // --- Database Connection ---
    include('include/config.php');

    // --- PHP LOGIC ---
    $message = '';
    $message_type = '';

    /**
     * Security function to check if the user can manage a specific news article.
     * @param mysqli $conn The database connection.
     * @param int $news_id The ID of the news article to check.
     * @param string $user_role The role of the current user.
     * @param string|null $user_cid The department ID of the current sub-admin.
     * @return bool True if permission is granted, false otherwise.
     */
    function canManageNews($conn, $news_id, $user_role, $user_cid) {
        if ($user_role === 'super_admin') {
            return true; // Super admin can manage all news.
        }
        if ($user_role === 'sub_admin' && !empty($user_cid) && !empty($news_id)) {
            $stmt = $conn->prepare("SELECT cid FROM news WHERE nid = ?");
            $stmt->bind_param("i", $news_id); // nid is INT
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $news_item = $result->fetch_assoc();
                $stmt->close();
                // Grant permission if the news article's department matches the sub-admin's.
                return ($news_item && $news_item['cid'] === $user_cid);
            }
        }
        return false; // Deny by default.
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

        // --- Handle POST Actions with Security Checks ---
        if ($action === 'insert') {
            if ($user_role === 'sub_admin') {
                $cid = $user_cid; // Force sub-admin's department on creation.
            }
        } elseif (in_array($action, ['update', 'delete', 'activate', 'deactivate'])) {
            if (!canManageNews($conn, $nid, $user_role, $user_cid)) {
                $message = "Permission Denied: You cannot manage this news article.";
                $message_type = 'error';
                $permission_granted = false;
            }
            if ($action === 'update' && $user_role === 'sub_admin') {
                $cid = $user_cid; // Ensure sub-admin cannot change the department on update.
            }
        }
        
        if ($permission_granted) {
            // --- FIXED: Full Image Upload Logic ---
            $uploadOk = 1;
            if (isset($_FILES['nimg']) && $_FILES['nimg']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/news_images/";
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0755, true)) {
                        $message = "Failed to create upload directory.";
                        $message_type = 'error';
                        $uploadOk = 0;
                    }
                }
                
                if ($uploadOk) {
                    $image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES["nimg"]["name"]));
                    $target_file = $target_dir . $image_name;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $check = getimagesize($_FILES["nimg"]["tmp_name"]);
                    if ($check === false) {
                        $message = "File is not a valid image."; $message_type = 'error'; $uploadOk = 0;
                    }
                    if ($_FILES["nimg"]["size"] > 5000000) { // 5MB limit
                        $message = "Image file is too large (Max 5MB)."; $message_type = 'error'; $uploadOk = 0;
                    }
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($imageFileType, $allowed_types)) {
                        $message = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed."; $message_type = 'error'; $uploadOk = 0;
                    }

                    if ($uploadOk && move_uploaded_file($_FILES["nimg"]["tmp_name"], $target_file)) {
                        $nimg = $target_file; 
                        if ($action === 'update' && !empty($existing_nimg) && file_exists($existing_nimg)) {
                            @unlink($existing_nimg);
                        }
                    } else if ($uploadOk) {
                        $message = "Error uploading image file."; $message_type = 'error'; $uploadOk = 0;
                    }
                }
            } elseif (isset($_FILES['nimg']) && $_FILES['nimg']['error'] !== UPLOAD_ERR_NO_FILE) {
                $message = "Image upload error with code: " . $_FILES['nimg']['error']; $message_type = 'error'; $uploadOk = 0;
            }
            
            if ($uploadOk) {
                try {
                    switch ($action) {
                        case 'insert':
                            $stmt = $conn->prepare("INSERT INTO news (ntitle, ntag, cid, ntext, nimg, status, count, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, 0, NOW(), NOW())");
                            $stmt->bind_param("sssss", $ntitle, $ntag, $cid, $ntext, $nimg);
                            if ($stmt->execute()) { $message = "News created."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'update':
                            $stmt = $conn->prepare("UPDATE news SET ntitle=?, ntag=?, cid=?, ntext=?, nimg=?, updated_at=NOW() WHERE nid=?");
                            $stmt->bind_param("sssssi", $ntitle, $ntag, $cid, $ntext, $nimg, $nid);
                            if ($stmt->execute()) { $message = "News updated."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'delete':
                            $stmt = $conn->prepare("DELETE FROM news WHERE nid = ?");
                            $stmt->bind_param("i", $nid);
                            if ($stmt->execute()) { $message = "News deleted."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'activate':
                        case 'deactivate':
                            $new_status = ($action === 'activate') ? 1 : 0;
                            $stmt = $conn->prepare("UPDATE news SET status=? WHERE nid=?");
                            $stmt->bind_param("ii", $new_status, $nid);
                            if ($stmt->execute()) { $message = "Status updated."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                    }
                } catch (mysqli_sql_exception $e) {
                    $message = "Database error: " . $e->getMessage();
                    $message_type = 'error';
                }
            }
        }
    }

    // --- Fetch Data for Display (Filtered by Role) ---
    $courses = []; // For the dropdown
    $news_articles = [];
    if (isset($conn)) {
        // Fetch all courses for the dropdown (for super_admin)
        $course_result = $conn->query("SELECT cid, cname FROM course WHERE status = 1 ORDER BY cname ASC");
        if ($course_result) $courses = $course_result->fetch_all(MYSQLI_ASSOC);

        // Fetch news articles based on role
        $sql = "SELECT n.nid, n.ntitle, n.ntag, n.cid, c.cname, n.ntext, n.nimg, n.status, n.count, n.created_at, n.updated_at 
                FROM news n
                LEFT JOIN course c ON n.cid = c.cid";
        $params = [];
        $types = '';

        if ($user_role === 'sub_admin') {
            $sql .= " WHERE n.cid = ? OR n.cid IS NULL"; // Sub-admins can see their own news AND general news
            $params[] = $user_cid;
            $types .= 's';
        }
        $sql .= " ORDER BY n.created_at DESC";

        $stmt = $conn->prepare($sql);
        if($stmt){
            if (!empty($params)) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) $news_articles = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }
    ?>

    <h2>News Management</h2>
    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- ADD/EDIT FORM -->
    <div class="form-section">
        <h3 id="formTitle">Add New News Article</h3>
        <form id="newsForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">
            <input type="hidden" name="nid" id="nid" value="">

            <label for="ntitle">News Title:</label>
            <input type="text" id="ntitle" name="ntitle" required>
            
            <label for="cid">Department (Optional):</label>
            <select id="cid" name="cid" <?php if ($user_role === 'sub_admin') echo 'disabled'; ?>>
                <option value="">General News</option>
                <?php foreach ($courses as $course) : ?>
                    <?php $selected = ($user_role === 'sub_admin' && $user_cid === $course['cid']) ? 'selected' : ''; ?>
                    <option value="<?php echo htmlspecialchars($course['cid']); ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($course['cname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="ntag">SEO Tags (comma-separated):</label>
            <input type="text" id="ntag" name="ntag">

            <label for="ntext">News Content:</label>
            <textarea id="ntext" name="ntext"></textarea>

            <label for="nimg">News Image:</label>
            <input type="file" id="nimg" name="nimg" accept="image/*">
            <input type="hidden" name="existing_nimg" id="existing_nimg" value="">
            <img id="current_nimg_preview" src="" alt="Current Image" style="display: none;" class="image-preview">

            <div class="button-group">
                <button type="submit" id="submitButton">Save News</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr>

    <!-- NEWS LIST TABLE -->
    <div class="news-list-section">
        <h3>Existing News Articles</h3>
        <table class="news-table">
            <thead>
                <tr>
                    <th>ID</th><th>Title</th><th>Department</th><th>Image</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($news_articles)): ?>
                    <?php foreach ($news_articles as $news): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($news['nid']); ?></td>
                            <td><?php echo htmlspecialchars($news['ntitle']); ?></td>
                            <td><?php echo htmlspecialchars($news['cname'] ?? 'General'); ?></td>
                            <td><?php if (!empty($news['nimg']) && file_exists($news['nimg'])): ?><img src="<?php echo htmlspecialchars($news['nimg']); ?>"><?php endif; ?></td>
                            <td><span class="status-<?php echo $news['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $news['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td class="actions-cell">
                                <?php if (canManageNews($conn, $news['nid'], $user_role, $user_cid)): ?>
                                    <button class="edit-btn" data-nid="<?php echo htmlspecialchars($news['nid']); ?>" data-ntitle="<?php echo htmlspecialchars($news['ntitle']); ?>" data-ntag="<?php echo htmlspecialchars($news['ntag']); ?>" data-cid="<?php echo htmlspecialchars($news['cid']); ?>" data-ntext="<?php echo htmlspecialchars($news['ntext']); ?>" data-nimg="<?php echo htmlspecialchars($news['nimg']); ?>">Edit</button>
                                    <form action="" method="POST" onsubmit="return confirm('Delete this news?');" style="display:inline-block;"><input type="hidden" name="nid" value="<?php echo htmlspecialchars($news['nid']); ?>"><button type="submit" name="action" value="delete">Delete</button></form>
                                    <?php if ($news['status'] == 1): ?>
                                        <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="nid" value="<?php echo htmlspecialchars($news['nid']); ?>"><button type="submit" name="action" value="deactivate">Deactivate</button></form>
                                    <?php else: ?>
                                        <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="nid" value="<?php echo htmlspecialchars($news['nid']); ?>"><button type="submit" name="action" value="activate">Activate</button></form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span>No actions permitted.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">No news articles found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea#ntext',
        plugins: 'lists link image charmap preview anchor',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
        height: 250
    });

    document.addEventListener('DOMContentLoaded', function() {
        const newsForm = document.getElementById('newsForm');
        const actionInput = document.getElementById('action');
        const nidInput = document.getElementById('nid');
        const ntitleInput = document.getElementById('ntitle');
        const ntagInput = document.getElementById('ntag');
        const cidSelect = document.getElementById('cid');
        const ntextTextarea = document.getElementById('ntext');
        const existingNimgInput = document.getElementById('existing_nimg');
        const currentNimgPreview = document.getElementById('current_nimg_preview');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');
        const userRole = "<?php echo $user_role; ?>";
        const userCid = "<?php echo $user_cid; ?>";

        function resetForm() {
            newsForm.reset();
            actionInput.value = 'insert';
            formTitle.textContent = 'Add New News Article';
            submitButton.textContent = 'Save News';
            cancelButton.style.display = 'none';
            currentNimgPreview.style.display = 'none';
            tinymce.get('ntext').setContent('');
            
            if (userRole === 'sub_admin') {
                cidSelect.value = userCid;
                cidSelect.disabled = true;
            } else {
                cidSelect.disabled = false;
            }
        }
        
        // Initial state for sub-admin
        if (userRole === 'sub_admin') {
            cidSelect.value = userCid;
            cidSelect.disabled = true;
        }

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                formTitle.textContent = 'Edit News Article';
                actionInput.value = 'update';
                submitButton.textContent = 'Update News';
                cancelButton.style.display = 'inline-block';
                
                nidInput.value = this.dataset.nid;
                ntitleInput.value = this.dataset.ntitle;
                ntagInput.value = this.dataset.ntag;
                cidSelect.value = this.dataset.cid;
                tinymce.get('ntext').setContent(this.dataset.ntext || '');
                existingNimgInput.value = this.dataset.nimg;

                if (this.dataset.nimg) {
                    currentNimgPreview.src = this.dataset.nimg;
                    currentNimgPreview.style.display = 'block';
                } else {
                    currentNimgPreview.style.display = 'none';
                }

                newsForm.scrollIntoView({ behavior: 'smooth' });
            });
        });

        cancelButton.addEventListener('click', resetForm);
    });
</script>


<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: 'textarea',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until May 20, 2025:
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
</script>