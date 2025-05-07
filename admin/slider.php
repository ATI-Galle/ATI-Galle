<?php include("include/header.php");?>
<?php include("include/sidebar.php");?>

<?php
include ("include/config.php");

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
                    $stmt = $conn->prepare("INSERT INTO slider (stitle, stext, simg, created_at, updated_at, status) VALUES (?, ?, ?, NOW(), NOW(), ?)");
                    $stmt->bind_param("sssi", $stitle, $stext, $simg, $status);
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
                    $stmt = $conn->prepare("UPDATE slider SET stitle=?, stext=?, simg=?, updated_at=NOW() WHERE sid=?");
                    $stmt->bind_param("sssi", $stitle, $stext, $simg, $sid);
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
                $stmt = $conn->prepare("UPDATE slider SET status=?, updated_at=NOW() WHERE sid=?");
                $stmt->bind_param("ii", $new_status, $sid); // sid is integer
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
            --font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif;
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
        .page-container { /* Changed from .container to match first example's structure */
            margin-left: 300px;
    margin-right:100px; /* Same as sidebar width */
    padding: 20px;
    width: 1000px;
    height:100%; /* Full width minus sidebar width */
    box-sizing: border-box;
    /* --- Add previous .page-container styles here --- */
    max-width: none; /* Override previous max-width if needed */
    margin-top: 10; /* Remove top margin if body flex handles alignment */
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative; /* Needed if there were absolute elements inside */
    flex-grow: 1; /* Allow main content to grow */

        }
        h2 { /* General h2 style from first example */
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }
        h3 { /* General h3 style from first example */
            color: var(--dark-color);
            margin-top: 2rem;
            margin-bottom: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            font-weight: 600;
        }
        .message-area { /* Message style from first example */
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
        .form-section { /* Form container style from first example */
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
        .form-section input[type=text],
        .form-section input[type=file],
        .form-section textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            box-sizing: border-box;
            font-size: 1rem;
        }
        .form-section input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .tox-tinymce {
            border: 1px solid #ccc !important;
            border-radius: var(--border-radius) !important;
        }
        .form-section .image-preview {
            margin-top: 10px;
            max-width: 150px; /* Consistent preview size */
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

        /* Table Styling from first example - adapted */
        .data-list-section { 
            margin-top: 30px;
            overflow-x: auto;
        }
        .styled-data-table { /* New class for the slider table */
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 1px 5px rgba(0,0,0,0.08);
        }
        .styled-data-table th, .styled-data-table td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
            vertical-align: middle;
        }
        .styled-data-table th {
            background-color: #f2f5f8;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }
        .styled-data-table tbody tr:nth-child(even) {
            background-color: var(--light-color);
        }
        .styled-data-table tbody tr:hover {
            background-color: #e9ecef;
        }
        .styled-data-table img.thumbnail { /* Class for image in table */
            display: block;
            max-width: 80px; 
            height: auto;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
        }
        .styled-data-table .actions-cell {
            white-space: nowrap;
            min-width: 240px; 
        }
        .styled-data-table .actions-cell form {
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .styled-data-table .actions-cell button,
        .styled-data-table .actions-cell .edit-btn {
            padding: 6px 12px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.85rem;
            color: white;
            transition: background-color 0.2s ease;
        }
        .styled-data-table .actions-cell .edit-btn {
            background-color: var(--warning-color);
            color: #333;
        }
        .styled-data-table .actions-cell .edit-btn:hover {
            background-color: #e0a800;
        }
        .styled-data-table .actions-cell button[value='delete'], /* More specific for delete button if it's a submit */
        .styled-data-table .actions-cell form button[type="submit"] { /* General submit buttons in actions */
            background-color: var(--danger-color);
        }
        .styled-data-table .actions-cell button[value='delete']:hover,
        .styled-data-table .actions-cell form button[type="submit"]:hover {
             background-color: #c82333;
        }

        .styled-data-table .actions-cell button[value='activate'],
        .styled-data-table .actions-cell form button[type="submit"][data-action-type='activate'] { /* For specific activate button */
            background-color: var(--success-color);
        }
        .styled-data-table .actions-cell button[value='activate']:hover,
        .styled-data-table .actions-cell form button[type="submit"][data-action-type='activate']:hover {
            background-color: #218838;
        }
        .styled-data-table .actions-cell button[value='deactivate'],
        .styled-data-table .actions-cell form button[type="submit"][data-action-type='deactivate'] { /* For specific deactivate button */
            background-color: var(--secondary-color);
        }
        .styled-data-table .actions-cell button[value='deactivate']:hover,
        .styled-data-table .actions-cell form button[type="submit"][data-action-type='deactivate']:hover {
            background-color: #5a6268;
        }
        .status-active {
            color: var(--success-color);
            font-weight: bold;
        }
        .status-inactive {
            color: var(--secondary-color); /* Was --danger-color, changed to secondary for inactive */
            font-weight: bold;
        }
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
            <table class="styled-data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Text Preview</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT sid, stitle, stext, simg, status, created_at, updated_at FROM slider ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["sid"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["stitle"]) . "</td>";
                            echo "<td>" . htmlspecialchars(mb_substr(strip_tags($row["stext"]), 0, 50)) . (mb_strlen(strip_tags($row["stext"])) > 50 ? '...' : '') . "</td>";
                            echo "<td>";
                            if (!empty($row["simg"]) && file_exists($row["simg"])) {
                                echo "<img src='" . htmlspecialchars($row["simg"]) . "' alt='Slider Image' class='thumbnail'>";
                            } else {
                                echo "<span>No Image</span>";
                            }
                            echo "</td>";
                            echo "<td><span class='status-" . ($row["status"] == 1 ? "active" : "inactive") . "'>" . ($row["status"] == 1 ? "Active" : "Inactive") . "</span></td>";
                            echo "<td>" . date("Y-m-d", strtotime($row["created_at"])) . "</td>";
                            echo "<td>" . date("Y-m-d", strtotime($row["updated_at"])) . "</td>";
                            echo "<td class='actions-cell'>";
                            
                            echo "<button class='edit-btn' 
                                    data-sid='" . $row["sid"] . "' 
                                    data-stitle='" . htmlspecialchars($row["stitle"]) . "' 
                                    data-stext='" . htmlspecialchars($row["stext"]) . "' 
                                    data-simg='" . htmlspecialchars($row["simg"]) . "'>Edit</button>";

                            echo "<form action='' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this slider?\");'>";
                            echo "<input type='hidden' name='action' value='delete'>";
                            echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                            echo "<button type='submit' value='delete'>Delete</button>";
                            echo "</form>";

                            if ($row["status"] == 1) {
                                echo "<form action='' method='POST'>";
                                echo "<input type='hidden' name='action' value='deactivate'>";
                                echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                                echo "<button type='submit' value='deactivate' data-action-type='deactivate'>Deactivate</button>";
                                echo "</form>";
                            } else {
                                echo "<form action='' method='POST'>";
                                echo "<input type='hidden' name='action' value='activate'>";
                                echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                                echo "<button type='submit' value='activate' data-action-type='activate'>Activate</button>";
                                echo "</form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' style='text-align: center; padding: 20px;'>No sliders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#stext', // Target the specific textarea by ID
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange formatpainter pageembed a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown importword exportword exportpdf',
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderForm = document.getElementById('sliderForm');
        const actionInput = document.getElementById('action');
        const sidInput = document.getElementById('sid'); // Slider ID input
        const stitleInput = document.getElementById('stitle');
        const stextTextarea = document.getElementById('stext'); // The textarea for TinyMCE
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
                const stextData = this.getAttribute('data-stext'); // Renamed to avoid conflict with stextTextarea
                const simg = this.getAttribute('data-simg');

                formTitle.textContent = 'Edit Slider';
                actionInput.value = 'update';
                sidInput.value = sid; 
                stitleInput.value = stitle;
                existingSimgInput.value = simg;

                // Set content for TinyMCE
                if (typeof tinymce !== 'undefined' && tinymce.get('stext')) {
                    tinymce.get('stext').setContent(stextData || '');
                } else {
                    stextTextarea.value = stextData; // Fallback if TinyMCE not ready
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

            // Clear content for TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('stext')) {
                tinymce.get('stext').setContent('');
            } else {
                stextTextarea.value = ''; // Fallback
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
                // If no new file is selected during an update, show existing image
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

</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>