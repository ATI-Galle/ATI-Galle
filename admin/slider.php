<?php

// -- MODIFIED: Start session to get user info --
session_start();
error_reporting(0);

include('include/header.php');
include('include/sidebar.php');
?>

<?php
include ('include/config.php');

// -- MODIFIED: Assume a username is stored in the session after login --
// Replace 'admin' with your actual default or guest user if needed.
$current_user = $_SESSION['username'] ?? 'admin';

// Optional: Set character set
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
} else {
    error_log("Database connection (\$conn) not properly initialized in config.php");
    // exit("Database connection error."); // Optionally stop execution
}

// --- PHP Logic for Handling Actions (Insert, Update, Delete, Activate/Deactivate) ---
$message = '';
$message_type = ''; // To control message style (success/error)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'insert':
            $stitle = $_POST['stitle'] ?? '';
            $stext = $_POST['stext'] ?? '';
            $simg = '';
            $status = 1;
            $uploadOk = 1; // Assume upload will be okay initially

            if (isset($_FILES['simg']) && $_FILES['simg']['error'] == 0) {
                $target_dir = "uploads/sliders/"; // Specific directory for sliders
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0777, true)) {
                        $message = "Error: Failed to create upload directory.";
                        $message_type = 'error';
                        $uploadOk = 0;
                    }
                }
                
                if ($uploadOk) {
                    $image_name = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', basename($_FILES["simg"]["name"]));
                    $target_file = $target_dir . time() . "_" . $image_name;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                    $check = getimagesize($_FILES["simg"]["tmp_name"]);
                    if($check === false) {
                        $message = "File is not a valid image."; $message_type = 'error'; $uploadOk = 0;
                    }
                    if ($_FILES["simg"]["size"] > 5000000) { // 5MB
                        $message = "Sorry, image is too large (Max 5MB)."; $message_type = 'error'; $uploadOk = 0;
                    }
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if(!in_array($imageFileType, $allowed_types)) {
                        $message = "Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed."; $message_type = 'error'; $uploadOk = 0;
                    }

                    if ($uploadOk) {
                        if (move_uploaded_file($_FILES["simg"]["tmp_name"], $target_file)) {
                            $simg = $target_file;
                        } else {
                            $message = "Error uploading image."; $message_type = 'error'; $uploadOk = 0;
                        }
                    }
                }
            } elseif (isset($_FILES['simg']) && $_FILES['simg']['error'] !== UPLOAD_ERR_NO_FILE) {
                $message = "Image upload error: Code " . $_FILES['simg']['error']; $message_type = 'error'; $uploadOk = 0;
            }


            if ($uploadOk) { // Proceed only if upload was okay or no new image was attempted
                 if (!empty($stitle)) { // Basic validation
                    // -- MODIFIED: Added updated_by to the INSERT query --
                    $stmt = $conn->prepare("INSERT INTO slider (stitle, stext, simg, created_at, updated_at, status, updated_by) VALUES (?, ?, ?, NOW(), NOW(), ?, ?)");
                    $stmt->bind_param("sssis", $stitle, $stext, $simg, $status, $current_user); // 's' for the string $current_user
                    if ($stmt->execute()) {
                        $message = "New slider created successfully."; $message_type = 'success';
                    } else {
                        $message = "Error creating slider: " . $stmt->error; $message_type = 'error';
                    }
                    $stmt->close();
                 } else {
                     $message = "Slider title cannot be empty."; $message_type = 'error';
                 }
            }
            break;

        case 'update':
            $sid = $_POST['sid'] ?? '';
            $stitle = $_POST['stitle'] ?? '';
            $stext = $_POST['stext'] ?? '';
            $existing_simg = $_POST['existing_simg'] ?? '';
            $simg = $existing_simg;
            $uploadOk = 1;
            $new_image_uploaded = false;

            if (isset($_FILES['simg']) && $_FILES['simg']['error'] == 0) {
                $target_dir = "uploads/sliders/";
                if (!is_dir($target_dir)) {
                    if (!mkdir($target_dir, 0777, true)) {
                        $message = "Error: Failed to create upload directory.";
                        $message_type = 'error';
                        $uploadOk = 0;
                    }
                }

                if($uploadOk){
                    $image_name = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', basename($_FILES["simg"]["name"]));
                    $target_file = $target_dir . time() . "_" . $image_name;
                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                    
                    $check = getimagesize($_FILES["simg"]["tmp_name"]);
                    if($check === false) { $message = "New file is not an image."; $message_type = 'error'; $uploadOk = 0; }
                    if ($_FILES["simg"]["size"] > 5000000) { $message = "Sorry, new image is too large."; $message_type = 'error'; $uploadOk = 0; }
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if(!in_array($imageFileType, $allowed_types)) { $message = "Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed for new image."; $message_type = 'error'; $uploadOk = 0; }

                    if ($uploadOk) {
                        if (move_uploaded_file($_FILES["simg"]["tmp_name"], $target_file)) {
                            $simg = $target_file;
                            $new_image_uploaded = true;
                            if (!empty($existing_simg) && $existing_simg !== $simg && file_exists($existing_simg)) {
                                @unlink($existing_simg);
                            }
                        } else { $message = "Error uploading new image."; $message_type = 'error'; $uploadOk = 0; }
                    }
                }
            } elseif (isset($_FILES['simg']) && $_FILES['simg']['error'] !== UPLOAD_ERR_NO_FILE) {
                $message = "Image upload error: Code " . $_FILES['simg']['error']; $message_type = 'error'; $uploadOk = 0;
            }
            
            if ($uploadOk) {
                if (!empty($sid) && !empty($stitle)) {
                    // -- MODIFIED: Added updated_by to the UPDATE query --
                    $stmt = $conn->prepare("UPDATE slider SET stitle=?, stext=?, simg=?, updated_at=NOW(), updated_by=? WHERE sid=?");
                    $stmt->bind_param("ssssi", $stitle, $stext, $simg, $current_user, $sid); // 's' for the string $current_user
                    if ($stmt->execute()) {
                        $message = "Slider updated successfully."; $message_type = 'success';
                    } else {
                        $message = "Error updating slider: " . $stmt->error; $message_type = 'error';
                    }
                    $stmt->close();
                } else {
                    $message = "Error: Slider ID or Title missing for update."; $message_type = 'error';
                }
            }
            break;

        case 'delete':
            // ... (no changes needed for delete, activate, or deactivate logic) ...
            $sid = $_POST['sid'] ?? '';
            if (!empty($sid)) {
                $image_path_to_delete = '';
                $stmt_img = $conn->prepare("SELECT simg FROM slider WHERE sid = ?");
                $stmt_img->bind_param("i", $sid);
                if ($stmt_img->execute()) { $stmt_img->bind_result($image_path_to_delete); $stmt_img->fetch(); }
                $stmt_img->close();

                $stmt = $conn->prepare("DELETE FROM slider WHERE sid = ?");
                $stmt->bind_param("i", $sid);
                if ($stmt->execute()) {
                    $message = "Slider deleted successfully."; $message_type = 'success';
                    if (!empty($image_path_to_delete) && file_exists($image_path_to_delete)) {
                        @unlink($image_path_to_delete);
                    }
                } else {
                    $message = "Error deleting slider: " . $stmt->error; $message_type = 'error';
                }
                $stmt->close();
            } else {
                $message = "Error: Slider ID not provided for deletion."; $message_type = 'error';
            }
            break;

        case 'activate':
        case 'deactivate':
            $sid = $_POST['sid'] ?? '';
            if (!empty($sid)) {
                $new_status = ($action === 'activate') ? 1 : 0;
                $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
                // -- MODIFIED: Also update the 'updated_by' field on status change --
                $stmt = $conn->prepare("UPDATE slider SET status=?, updated_at=NOW(), updated_by=? WHERE sid=?");
                $stmt->bind_param("isi", $new_status, $current_user, $sid);
                if ($stmt->execute()) {
                    $message = "Slider " . $action_text . " successfully."; $message_type = 'success';
                } else {
                    $message = "Error " . $action_text . " slider: " . $stmt->error; $message_type = 'error';
                }
                $stmt->close();
            } else {
                $message = "Error: Slider ID not provided for status change."; $message_type = 'error';
            }
            break;
        default:
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management</title>
    <style>
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
        body {
            font-family: var(--font-family);
            line-height: 1.6;
            margin-top: 50;
            background-color: #eef2f7;
            color: var(--dark-color);
            display: flex; 
        }
        .page-container {
            margin-left: 300px;
            margin-right:100px;
            padding: 20px;
            width: 1000px;
            height:100%;
            box-sizing: border-box;
            max-width: none;
            margin-top: 10;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border-top: 5px solid var(--primary-color);
            position: relative;
            flex-grow: 1;
        }
        h2 {
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
            font-weight: 600;
        }
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
        .form-section textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            box-sizing: border-box;
            font-size: 1rem;
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

        /* --- NEW CARD VIEW STYLES --- */
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .slider-card {
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 5px rgba(0,0,0,0.08);
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .slider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .card-image-wrapper {
            width: 100%;
            height: 180px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
        }
        .card-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-content {
            padding: 15px;
            flex-grow: 1;
        }
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 10px 0;
            color: var(--dark-color);
        }
        .card-text-preview {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
            height: 40px; /* Approx 2 lines */
            overflow: hidden;
        }
        .card-meta {
            font-size: 0.8rem;
            color: var(--secondary-color);
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .card-meta span {
            display: block;
        }
        .card-actions {
            background-color: var(--light-color);
            padding: 10px 15px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .card-actions form {
            margin: 0;
        }
        .card-actions button, .card-actions .edit-btn {
            padding: 6px 12px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.85rem;
            color: white;
            border: none;
            transition: background-color 0.2s ease;
        }
        .card-actions .edit-btn { background-color: var(--warning-color); color: #333; }
        .card-actions .edit-btn:hover { background-color: #e0a800; }
        .card-actions button[value='delete'] { background-color: var(--danger-color); }
        .card-actions button[value='delete']:hover { background-color: #c82333; }
        .card-actions button[value='activate'] { background-color: var(--success-color); }
        .card-actions button[value='activate']:hover { background-color: #218838; }
        .card-actions button[value='deactivate'] { background-color: var(--secondary-color); }
        .card-actions button[value='deactivate']:hover { background-color: #5a6268; }

        .card-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
            z-index: 2;
        }
        .status-active { background-color: var(--success-color); }
        .status-inactive { background-color: var(--secondary-color); }
    </style>
</head>
<body>
    <div class="page-container">
        <h2>Slider Management</h2>

        <?php if (!empty($message)): ?>
            <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-section">
            <h3 id="formTitle">Add New Slider</h3>
            <form id="sliderForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="insert">
                <input type="hidden" name="sid" id="sid" value="">

                <label for="stitle">Title:</label>
                <input type="text" id="stitle" name="stitle" required>

                <label for="stext">Text:</label>
                <textarea id="stext" name="stext"></textarea>

                <label for="simg">Image:</label>
                <input type="file" id="simg" name="simg" accept="image/*">
                <input type="hidden" name="existing_simg" id="existing_simg" value="">
                <img id="current_simg_preview" src="#" alt="Current Slider Image" style="display: none;" class="image-preview">

                <div class="button-group">
                    <button type="submit" id="submitButton">Save Slider</button>
                    <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
                </div>
            </form>
        </div>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

        <div class="data-list-section">
            <h3>Existing Sliders</h3>
            <div class="card-container">
                <?php
                $sql = "SELECT sid, stitle, stext, simg, status, created_at, updated_at, updated_by FROM slider ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                        <div class="slider-card">
                            <div class="card-status status-<?php echo ($row['status'] == 1 ? 'active' : 'inactive'); ?>">
                                <?php echo ($row['status'] == 1 ? 'Active' : 'Inactive'); ?>
                            </div>
                            
                            <div class="card-image-wrapper">
                                <?php if (!empty($row['simg']) && file_exists($row['simg'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['simg']); ?>" alt="Slider Image">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </div>

                            <div class="card-content">
                                <h4 class="card-title"><?php echo htmlspecialchars($row['stitle']); ?></h4>
                                <p class="card-text-preview">
                                    <?php echo htmlspecialchars(mb_substr(strip_tags($row['stext']), 0, 100)) . (mb_strlen(strip_tags($row['stext'])) > 100 ? '...' : ''); ?>
                                </p>
                                <div class="card-meta">
                                    <span><strong>Updated By:</strong> <?php echo htmlspecialchars($row['updated_by']); ?></span>
                                    <span><strong>Updated On:</strong> <?php echo date("Y-m-d", strtotime($row['updated_at'])); ?></span>
                                </div>
                            </div>

                            <div class="card-actions">
                                <button class='edit-btn' 
                                        data-sid='<?php echo $row['sid']; ?>' 
                                        data-stitle='<?php echo htmlspecialchars($row['stitle']); ?>' 
                                        data-stext='<?php echo htmlspecialchars($row['stext']); ?>' 
                                        data-simg='<?php echo htmlspecialchars($row['simg']); ?>'>Edit</button>

                                <form action='' method='POST' onsubmit='return confirm("Are you sure you want to delete this slider?");'>
                                    <input type='hidden' name='action' value='delete'>
                                    <input type='hidden' name='sid' value='<?php echo $row['sid']; ?>'>
                                    <button type='submit' value='delete'>Delete</button>
                                </form>

                                <?php if ($row['status'] == 1): ?>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='action' value='deactivate'>
                                        <input type='hidden' name='sid' value='<?php echo $row['sid']; ?>'>
                                        <button type='submit' value='deactivate'>Deactivate</button>
                                    </form>
                                <?php else: ?>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='action' value='activate'>
                                        <input type='hidden' name='sid' value='<?php echo $row['sid']; ?>'>
                                        <button type='submit' value='activate'>Activate</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='text-align: center; padding: 20px; width: 100%;'>No sliders found.</p>";
                }
                ?>
            </div>
        </div>
    </div>

<script src="https://cdn.tiny.cloud/1/0b4l260nbwgikhaerenongs5zgl39j7pja3yimxlbjkkfrs6/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  tinymce.init({
    selector: '#stext',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange formatpainter pageembed a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown importword exportword exportpdf',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderForm = document.getElementById('sliderForm');
        const actionInput = document.getElementById('action');
        const sidInput = document.getElementById('sid');
        const stitleInput = document.getElementById('stitle');
        const stextTextarea = document.getElementById('stext');
        const simgInput = document.getElementById('simg');
        const existingSimgInput = document.getElementById('existing_simg');
        const currentSimgPreview = document.getElementById('current_simg_preview');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sid = this.getAttribute('data-sid');
                const stitle = this.getAttribute('data-stitle');
                const stextData = this.getAttribute('data-stext');
                const simg = this.getAttribute('data-simg');

                formTitle.textContent = 'Edit Slider';
                actionInput.value = 'update';
                sidInput.value = sid; 
                stitleInput.value = stitle;
                existingSimgInput.value = simg;

                if (typeof tinymce !== 'undefined' && tinymce.get('stext')) {
                    tinymce.get('stext').setContent(stextData || '');
                } else {
                    stextTextarea.value = stextData;
                }

                if (simg && simg !== '') {
                    currentSimgPreview.src = simg;
                    currentSimgPreview.style.display = 'block';
                } else {
                    currentSimgPreview.src = '#';
                    currentSimgPreview.style.display = 'none';
                }

                submitButton.textContent = 'Update Slider';
                cancelButton.style.display = 'inline-block';
                sliderForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        cancelButton.addEventListener('click', function() {
            resetForm();
        });

        function resetForm() {
            formTitle.textContent = 'Add New Slider';
            actionInput.value = 'insert';
            sidInput.value = ''; 
            stitleInput.value = '';
            simgInput.value = ''; 
            existingSimgInput.value = '';
            currentSimgPreview.src = '#';
            currentSimgPreview.style.display = 'none';

            if (typeof tinymce !== 'undefined' && tinymce.get('stext')) {
                tinymce.get('stext').setContent('');
            } else {
                stextTextarea.value = '';
            }

            submitButton.textContent = 'Save Slider';
            cancelButton.style.display = 'none';
        }

        simgInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentSimgPreview.src = e.target.result;
                    currentSimgPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                const existingImg = existingSimgInput.value;
                if (actionInput.value === 'update' && existingImg && existingImg !== '') {
                    currentSimgPreview.src = existingImg;
                    currentSimgPreview.style.display = 'block';
                } else {
                    currentSimgPreview.src = '#';
                    currentSimgPreview.style.display = 'none';
                }
            }
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
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>