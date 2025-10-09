<?php
// session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include('include/header.php');
?>

<style>
/* --- Basic Layout for Fixed Sidebar --- */
body {
    display: flex; /* Enable flexbox layout */
    margin-right: auto;
}

.page-container {
    margin-left: 300px; /* Adjust if your sidebar width is different */
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px); /* Adjust based on sidebar and right margin */
    min-width: 800px; /* Minimum width for content */
    /* height:100%; Removed to allow content to define height */
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px;
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
    margin-bottom: 30px; /* Ensure space for content */
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
.tox-tinymce {
    border: 1px solid #ccc !important;
    border-radius: var(--border-radius) !important;
}
.form-section input[type="file"] {
    padding: 8px;
    background-color: #fff;
}

/* Styles for single image preview */
.form-section .image-preview-container {
    margin-top: 10px;
    margin-bottom: 15px;
    display: flex; /* Changed for single item potentially */
    flex-direction: column; /* Stack image and button */
    align-items: flex-start; /* Align items to the start */
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
    max-width: 150px; /* Increased size for single prominent image */
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
    margin-top: 5px; /* Space it from the image if needed */
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
.form-section button#cancelEdit { background-color: var(--secondary-color); }
.form-section button#cancelEdit:hover { background-color: #5a6268; }

.news-list-section { margin-top: 30px; overflow-x: auto; }
.news-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.news-table th, .news-table td {
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    text-align: left;
    vertical-align: middle;
}
.news-table th {
    background-color: #f2f5f8;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
}
.news-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.news-table tbody tr:hover { background-color: #e9ecef; }
.news-table .news-image-cell img {
    max-width: 80px; 
    max-height: 80px;
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    object-fit: cover;
}
.news-table .actions-cell { white-space: nowrap; min-width: 240px; }
.news-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
.news-table .actions-cell button,
.news-table .actions-cell .edit-btn {
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem;
    color: white;
    transition: background-color 0.2s ease;
    text-decoration: none;
    display: inline-block;
}
.news-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.news-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
.news-table .actions-cell button[name='action'][value='delete'] { background-color: var(--danger-color); }
.news-table .actions-cell button[name='action'][value='delete']:hover { background-color: #c82333; }
.news-table .actions-cell button[name='action'][value='activate'] { background-color: var(--success-color); }
.news-table .actions-cell button[name='action'][value='activate']:hover { background-color: #218838; }
.news-table .actions-cell button[name='action'][value='deactivate'] { background-color: var(--secondary-color); }
.news-table .actions-cell button[name='action'][value='deactivate']:hover { background-color: #5a6268; }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }
</style>

<?php include('include/sidebar.php'); ?>

<div class="page-container">
<?php
include ('include/config.php'); 

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
} else {
    echo "<div class='message-area error'>Database connection error. Please check config.php.</div>";
    // exit; // Stop further script execution if no DB connection
}

$message = '';
$message_type = ''; 
$upload_news_image_dir = "uploads/news_images/"; // Directory for single news images

if (!is_dir($upload_news_image_dir)) {
    mkdir($upload_news_image_dir, 0777, true); // Create if not exists
}

// --- Form Submission Handling ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $nid_param = $_POST['nid'] ?? null;
    $ntitle = $_POST['ntitle'] ?? '';
    $ntag = $_POST['ntag'] ?? '';
    $ntext = $_POST['ntext'] ?? '';
    // count is not taken from POST for insert/update in admin, it's updated user-side
    $delete_current_nimg = isset($_POST['delete_current_nimg']) && $_POST['delete_current_nimg'] == '1';
    $current_nimg_path = $_POST['current_nimg_path'] ?? null; // Path of the existing image

    $nimg_db_path = $current_nimg_path; // Start with current image path

    try {
        $conn->begin_transaction();

        // Handle Image Upload/Deletion for Insert/Update
        if ($action === 'insert' || $action === 'update') {
            if (empty($ntitle)) {
                throw new Exception("News Title cannot be empty.");
            }
            if ($action === 'insert' && empty($nid_param)) {
                throw new Exception("News ID cannot be empty for a new article.");
            }

            // If "delete current image" is checked
            if ($delete_current_nimg && !empty($nimg_db_path)) {
                if (file_exists($nimg_db_path)) {
                    @unlink($nimg_db_path);
                }
                $nimg_db_path = null; // Remove from DB
            }

            // Handle new image upload
            if (isset($_FILES['nimg']) && $_FILES['nimg']['error'] === UPLOAD_ERR_OK) {
                // If there was an old image (and it wasn't just marked for deletion), delete it
                if (!$delete_current_nimg && !empty($current_nimg_path) && file_exists($current_nimg_path)) {
                    @unlink($current_nimg_path);
                }
                
                $tmp_name = $_FILES['nimg']['tmp_name'];
                $image_file_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES['nimg']['name']));
                $target_file_name = time() . "_" . uniqid() . "_" . $image_file_name;
                $target_file_path = $upload_news_image_dir . $target_file_name;
                $imageFileType = strtolower(pathinfo($target_file_path, PATHINFO_EXTENSION));

                $check = getimagesize($tmp_name);
                if($check === false) { throw new Exception("File is not a valid image."); }
                if ($_FILES["nimg"]["size"] > 5000000) { // 5MB limit
                    throw new Exception("Sorry, image is too large (Max 5MB).");
                }
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if(!in_array($imageFileType, $allowed_types)) {
                    throw new Exception("Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed. Type was: '$imageFileType'");
                }

                if (move_uploaded_file($tmp_name, $target_file_path)) {
                    $nimg_db_path = $target_file_path; // Path to store in DB
                } else {
                    throw new Exception("Error uploading image. Check permissions for " . $upload_news_image_dir);
                }
            } elseif (isset($_FILES['nimg']) && $_FILES['nimg']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['nimg']['error'] !== UPLOAD_ERR_OK) {
                 throw new Exception("Image upload error: Code " . $_FILES['nimg']['error']);
            }


            // --- Database Operations for News (Insert/Update) ---
            if ($action === 'insert') {
                $check_stmt = $conn->prepare("SELECT nid FROM news WHERE nid = ?");
                $check_stmt->bind_param("s", $nid_param);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    throw new Exception("News ID '$nid_param' already exists. Please use a different ID.");
                } else {
                    $status = 1; // Default status for new news is Active
                    $count = 0;  // Default view count
                    $stmt = $conn->prepare("INSERT INTO news (nid, ntitle, ntag, ntext, nimg, count, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->bind_param("sssssis", $nid_param, $ntitle, $ntag, $ntext, $nimg_db_path, $count, $status);
                    if ($stmt->execute()) {
                        $message = "New news article created successfully.";
                        $message_type = 'success';
                    } else {
                        throw new Exception("Error creating news article: " . $stmt->error);
                    }
                    $stmt->close();
                }
                $check_stmt->close();
            } elseif ($action === 'update') {
                // For update, we don't modify 'count' from admin side
                $stmt = $conn->prepare("UPDATE news SET ntitle=?, ntag=?, ntext=?, nimg=?, updated_at=NOW() WHERE nid=?");
                $stmt->bind_param("sssss", $ntitle, $ntag, $ntext, $nimg_db_path, $nid_param);
                if ($stmt->execute()) {
                    $message = "News article updated successfully.";
                    $message_type = 'success';
                } else {
                    throw new Exception("Error updating news article: " . $stmt->error);
                }
                $stmt->close();
            }
        }
        // --- DELETE News ---
        elseif ($action === 'delete') {
            if (!empty($nid_param)) {
                // Get the image path to delete the file
                $stmt_get_img = $conn->prepare("SELECT nimg FROM news WHERE nid = ?");
                $stmt_get_img->bind_param("s", $nid_param);
                $stmt_get_img->execute();
                $stmt_get_img->bind_result($imgPathToDelete);
                $stmt_get_img->fetch();
                $stmt_get_img->close();

                if (!empty($imgPathToDelete) && file_exists($imgPathToDelete)) {
                    @unlink($imgPathToDelete);
                }

                $stmt_delete_news = $conn->prepare("DELETE FROM news WHERE nid = ?");
                $stmt_delete_news->bind_param("s", $nid_param);
                if ($stmt_delete_news->execute()) {
                    $message = "News article deleted successfully.";
                    $message_type = 'success';
                } else {
                    throw new Exception("Error deleting news: " . $stmt_delete_news->error);
                }
                $stmt_delete_news->close();
            } else {
                throw new Exception("News ID not provided for deletion.");
            }
        } 
        // --- ACTIVATE/DEACTIVATE News ---
        elseif ($action === 'activate' || $action === 'deactivate') {
             if (!empty($nid_param)) {
                $new_status = ($action === 'activate') ? 1 : 0;
                $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
                $stmt = $conn->prepare("UPDATE news SET status=?, updated_at=NOW() WHERE nid=?");
                $stmt->bind_param("is", $new_status, $nid_param);
                if ($stmt->execute()) {
                    $message = "News article " . $action_text . " successfully."; 
                    $message_type = 'success';
                } else { 
                    throw new Exception("Error " . $action_text . "ing news: " . $stmt->error);
                }
                $stmt->close();
            } else { 
                throw new Exception("News ID not provided for status change.");
            }
        }
        $conn->commit();
    } catch (Exception $e) {
        if (isset($conn) && $conn->ping() && $conn->in_transaction) {
            $conn->rollback(); 
        }
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
        error_log("News Management Error: " . $e->getMessage() . "\nPOST data: " . print_r($_POST, true) . "\nFILES data: " . print_r($_FILES, true));
    }
}

// --- Fetch Existing News Articles for Display ---
$news_articles = [];
if (isset($conn)) {
    $sql = "SELECT nid, ntitle, ntag, ntext, nimg, count, status, created_at, updated_at 
            FROM news 
            ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $news_articles[] = $row;
        }
    } else {
        if(empty($message)) {
            $message = "Error fetching news articles: " . $conn->error;
            $message_type = 'error';
        }
    }
} else {
    if (empty($message)) {
        $message = "Database connection not available. Cannot fetch or manage news articles.";
        $message_type = 'error';
    }
}
?>

<h2>News Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New News Article</h3>
        <form id="newsForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="insert">
            <input type="hidden" name="current_nimg_path" id="current_nimg_path" value=""> <input type="hidden" name="delete_current_nimg" id="delete_current_nimg" value="0"> 
            <input type="hidden" id="nid" name="nid"  value="sw">

            <label for="ntitle">News Title:</label>
            <input type="text" id="ntitle" name="ntitle" required>

            <label for="ntag">SEO Tags (comma-separated):</label>
            <input type="text" id="ntag" name="ntag">

            <label for="ntext">News Content:</label>
            <textarea id="ntext" name="ntext" class="tinymce"></textarea>

            <div id="current-image-section" style="display:none;">
                <label>Current Image:</label>
                <div id="current_nimg_preview_container" class="image-preview-container">
                    </div>
            </div>

            <label for="nimg">News Image (Upload new to replace):</label>
            <input type="file" id="nimg" name="nimg" accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Max file size: 5MB. Allowed types: JPG, JPEG, PNG, GIF, WEBP.</small>
            
            <div class="button-group" style="margin-top: 20px;">
                <button type="submit" id="submitButton">Save News Article</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="news-list-section">
        <h3>Existing News Articles</h3>
        <table class="news-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Tags</th>
                    <th>Preview</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($news_articles)): ?>
                    <?php foreach ($news_articles as $news_item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($news_item['nid']); ?></td>
                            <td><?php echo htmlspecialchars($news_item['ntitle']); ?></td>
                            <td class="news-image-cell">
                                <?php if (!empty($news_item['nimg']) && file_exists($news_item['nimg'])): ?>
                                    <img src="<?php echo htmlspecialchars($news_item['nimg']) . '?t=' . @filemtime($news_item['nimg']); ?>" alt="<?php echo htmlspecialchars($news_item['ntitle']); ?>">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars($news_item['ntag'])); ?></td>
                            <td>
                                <?php 
                                $desc = strip_tags($news_item['ntext']); 
                                echo htmlspecialchars(mb_substr($desc, 0, 70)) . (mb_strlen($desc) > 70 ? '...' : ''); 
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($news_item['count']); ?></td>
                            <td><span class="status-<?php echo $news_item['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $news_item['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($news_item['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($news_item['updated_at']))); ?></td>
                            <td class="actions-cell">
                                <button class="edit-btn"
                                    data-nid="<?php echo htmlspecialchars($news_item['nid']); ?>"
                                    data-ntitle="<?php echo htmlspecialchars($news_item['ntitle']); ?>"
                                    data-ntag="<?php echo htmlspecialchars($news_item['ntag']); ?>"
                                    data-ntext="<?php echo htmlspecialchars($news_item['ntext']); ?>"
                                    data-nimg="<?php echo htmlspecialchars($news_item['nimg'] ?? ''); ?>"
                                    data-count="<?php echo htmlspecialchars($news_item['count']); ?>" 
                                    type="button">Edit</button>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="nid" value="<?php echo htmlspecialchars($news_item['nid']); ?>">
                                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this news article? This cannot be undone.');">Delete</button>
                                </form>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" style="display: inline-block;">
                                    <input type="hidden" name="nid" value="<?php echo htmlspecialchars($news_item['nid']); ?>">
                                    <?php if ($news_item['status'] == 1): ?>
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
                        <td colspan="10" style="text-align: center;">No news articles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div> <script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'code lists link image media table wordcount fullscreen preview searchreplace help',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code | fullscreen preview | searchreplace | help',
        height: 300,
        menubar: 'file edit view insert format tools table help',
    });

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsForm');
    const formTitle = document.getElementById('formTitle');
    const formActionInput = document.getElementById('formAction');
    const nidInput = document.getElementById('nid');
    const ntitleInput = document.getElementById('ntitle');
    const ntagInput = document.getElementById('ntag');
    const submitButton = document.getElementById('submitButton');
    const cancelEditButton = document.getElementById('cancelEdit');
    
    const currentImageSection = document.getElementById('current-image-section');
    const currentNimgPreviewContainer = document.getElementById('current_nimg_preview_container');
    const currentNimgPathInput = document.getElementById('current_nimg_path');
    const deleteCurrentNimgInput = document.getElementById('delete_current_nimg');
    const newNimgInput = document.getElementById('nimg');

    function resetFormToDefaults() {
        form.reset(); 
        formTitle.textContent = 'Add New News Article';
        formActionInput.value = 'insert';
        nidInput.readOnly = false;
        nidInput.value = ''; 
        ntitleInput.value = '';
        ntagInput.value = '';
        if (tinymce.get('ntext')) {
            tinymce.get('ntext').setContent('');
        }
        
        currentNimgPreviewContainer.innerHTML = '';
        currentImageSection.style.display = 'none';
        currentNimgPathInput.value = '';
        deleteCurrentNimgInput.value = '0';
        newNimgInput.value = ''; 

        cancelEditButton.style.display = 'none';
        submitButton.textContent = 'Save News Article';
        window.scrollTo(0, form.offsetTop - 20);
    }

    cancelEditButton.addEventListener('click', function() {
        resetFormToDefaults();
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const newsData = this.dataset;

            formTitle.textContent = 'Edit News Article: ' + newsData.ntitle;
            formActionInput.value = 'update';
            
            nidInput.value = newsData.nid;
            nidInput.readOnly = true;

            ntitleInput.value = newsData.ntitle;
            ntagInput.value = newsData.ntag;

            if (tinymce.get('ntext')) {
                tinymce.get('ntext').setContent(newsData.ntext || '');
            } else {
                document.getElementById('ntext').value = newsData.ntext || '';
            }
            
            submitButton.textContent = 'Update News Article';
            cancelEditButton.style.display = 'inline-block';

            // Image handling
            currentNimgPreviewContainer.innerHTML = ''; // Clear previous
            deleteCurrentNimgInput.value = '0'; // Reset deletion flag
            newNimgInput.value = ''; // Clear file input for new images

            if (newsData.nimg && newsData.nimg.trim() !== '') {
                currentImageSection.style.display = 'block';
                currentNimgPathInput.value = newsData.nimg;

                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';

                const img = document.createElement('img');
                img.src = newsData.nimg + '?t=' + new Date().getTime(); 
                img.alt = 'Current news image';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-current-image-btn';
                removeBtn.textContent = 'Remove Current Image';

                removeBtn.addEventListener('click', function() {
                    currentNimgPreviewContainer.innerHTML = '<p><em>Image will be removed upon saving.</em></p>';
                    deleteCurrentNimgInput.value = '1'; // Signal backend to delete
                    // currentNimgPathInput.value = ''; // Path will be cleared by backend if new image not uploaded
                });

                previewItem.appendChild(img);
                currentNimgPreviewContainer.appendChild(previewItem);
                currentNimgPreviewContainer.appendChild(removeBtn);

            } else {
                currentImageSection.style.display = 'none';
                currentNimgPathInput.value = '';
            }
            
            window.scrollTo(0, form.offsetTop - 20);
        });
    });
});

</script>
<?php
// if (isset($conn) && $conn instanceof mysqli) {
// $conn->close();
// }
// include('include/footer.php'); 
?>
</body>
</html>