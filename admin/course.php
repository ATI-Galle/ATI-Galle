<?php include('include/header.php');?>

<style>
/* --- Basic Layout for Fixed Sidebar --- */
body {
    display: flex; /* Enable flexbox layout */
    margin-right: auto;

}

/* Assuming your sidebar has an ID or class like 'sidebar' */
/* Adjust selector and width as per your actual sidebar.php */


/* Adjust main content area to account for the fixed sidebar */
/* Assuming your main content wrapper is '.page-container' from previous code */
.page-container {
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

/* Include other CSS from the previous response here for form, table etc. */
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
.form-section input[type="number"], /* Added type number for ID */
.form-section input[type="file"],
.form-section textarea { /* Style the original textarea */
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: var(--border-radius);
    box-sizing: border-box; /* Include padding in width */
    font-size: 1rem;
}
/* Style readonly fields */
.form-section input:read-only {
    background-color: #e9ecef; /* Light gray background */
    cursor: not-allowed; /* Indicate non-editable */
}

/* TinyMCE styling might need adjustments inside its own config or specific CSS overrides if default doesn't fit */
.tox-tinymce {
    border: 1px solid #ccc !important; /* Add !important cautiously */
    border-radius: var(--border-radius) !important;
}


.form-section input[type="file"] {
    padding: 8px; /* Adjust padding for file input */
    background-color: #fff;
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
    background-color: #0056b3; /* Darker blue */
}
.form-section button#cancelEdit {
    background-color: var(--secondary-color);
}
.form-section button#cancelEdit:hover {
    background-color: #5a6268;
}

/* --- Course List Table Styling --- */
.course-list-section {
    margin-top: 30px;
    overflow-x: auto; /* Enable horizontal scrolling on small screens */
}
.course-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.course-table th, .course-table td {
    border: 1px solid #e0e0e0;
    padding: 12px 15px; /* More padding */
    text-align: left;
    vertical-align: middle; /* Align content vertically */
}
.course-table th {
    background-color: #f2f5f8; /* Slightly different header color */
    font-weight: 600;
    color: #333;
    white-space: nowrap; /* Prevent header text wrapping */
}
.course-table tbody tr:nth-child(even) {
    background-color: var(--light-color); /* Subtle striping */
}
 .course-table tbody tr:hover {
    background-color: #e9ecef; /* Hover effect */
}
.course-table img {
    display: block;
    max-width: 80px; /* Larger preview */
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
}
.course-table .actions-cell {
    white-space: nowrap; /* Prevent action buttons from wrapping */
     min-width: 240px; /* Ensure enough space for buttons */
}
.course-table .actions-cell form {
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px; /* Add space below buttons if they wrap */
}
.course-table .actions-cell button,
.course-table .actions-cell .edit-btn { /* Style edit button similarly */
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem; /* Smaller font for action buttons */
    color: white;
    transition: background-color 0.2s ease;
}
 .course-table .actions-cell .edit-btn {
    background-color: var(--warning-color);
    color: #333; /* Better contrast on yellow */
 }
 .course-table .actions-cell .edit-btn:hover {
    background-color: #e0a800;
 }
 .course-table .actions-cell button[value='delete'] {
    background-color: var(--danger-color);
 }
 .course-table .actions-cell button[value='delete']:hover {
    background-color: #c82333;
 }
 .course-table .actions-cell button[value='activate'] {
    background-color: var(--success-color);
 }
 .course-table .actions-cell button[value='activate']:hover {
    background-color: #218838;
 }
 .course-table .actions-cell button[value='deactivate'] {
    background-color: var(--secondary-color);
 }
 .course-table .actions-cell button[value='deactivate']:hover {
    background-color: #5a6268;
 }
 .status-active {
    color: var(--success-color);
    font-weight: bold;
 }
 .status-inactive {
    color: var(--secondary-color);
    font-weight: bold;
 }
</style>

<?php include('include/sidebar.php');?>

<div class="page-container"> <?php
// --- Database Connection ---
include ('include/config.php');

// Optional: Set character set
if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
} else {
    error_log("Database connection (\$conn) not properly initialized in config.php");
    // exit("Database connection error."); // Optionally stop execution
}


// --- PHP Logic ---
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $cid = $_POST['cid'] ?? null; // Course ID (now manually entered)
    $cname = $_POST['cname'] ?? '';
    $ctext = $_POST['ctext'] ?? '';
    $existing_cimg = $_POST['existing_cimg'] ?? '';
    $cimg = $existing_cimg;

    // --- Image Upload Handling (Keep previous logic) ---
    $uploadOk = 1;
    $new_image_uploaded = false;
    $target_dir = "uploads/courses/";
    $target_file = '';

    if (isset($_FILES['cimg']) && $_FILES['cimg']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                $message = "Error: Failed to create upload directory.";
                $message_type = 'error';
                $uploadOk = 0;
            }
        }
        if ($uploadOk) {
            $image_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES["cimg"]["name"]));
            $target_file = $target_dir . time() . "_" . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["cimg"]["tmp_name"]);
            if($check === false) { $message = "File is not a valid image."; $message_type = 'error'; $uploadOk = 0; }
            if ($_FILES["cimg"]["size"] > 5000000) { $message = "Sorry, image is too large (Max 5MB)."; $message_type = 'error'; $uploadOk = 0; }
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if(!in_array($imageFileType, $allowed_types)) { $message = "Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed."; $message_type = 'error'; $uploadOk = 0; }

            if ($uploadOk) {
                if (move_uploaded_file($_FILES["cimg"]["tmp_name"], $target_file)) {
                    $cimg = $target_file;
                    $new_image_uploaded = true;
                    if ($action === 'update' && !empty($existing_cimg) && $existing_cimg !== $cimg && file_exists($existing_cimg)) {
                        @unlink($existing_cimg);
                    }
                } else { $message = "Error uploading image."; $message_type = 'error'; $uploadOk = 0; }
            }
        }
        if (!$uploadOk) { $cimg = ($action === 'update') ? $existing_cimg : ''; }
    } elseif (isset($_FILES['cimg']) && $_FILES['cimg']['error'] !== UPLOAD_ERR_NO_FILE) {
        $message = "Image upload error: Code " . $_FILES['cimg']['error']; $message_type = 'error'; $uploadOk = 0;
        $cimg = ($action === 'update') ? $existing_cimg : '';
    }
    // --- End Image Handling ---


    // --- Perform Database Action ---
    if ($uploadOk || $action === 'delete' || $action === 'activate' || $action === 'deactivate' || ($action === 'update' && !$new_image_uploaded)) {
        try {
            switch ($action) {
                case 'insert':
                    // ** Check for duplicate CID before inserting ** [[NUMBER:1](https://stackoverflow.com/questions/27626054/mysql-php-check-for-duplicate-before-insert)], [[NUMBER:2](https://www.quora.com/How-can-I-check-and-prevent-duplicate-entries-in-a-database-using-PHP)]
                    if (!empty($cid) && !empty($cname)) {
                        $check_stmt = $conn->prepare("SELECT cid FROM course WHERE cid = ?");
                        $check_stmt->bind_param("s", $cid); // Assuming cid is entered as string/number
                        $check_stmt->execute();
                        $check_stmt->store_result();

                        if ($check_stmt->num_rows > 0) {
                            $message = "Error: Course ID '$cid' already exists. Please use a different ID.";
                            $message_type = 'error';
                        } else {
                            // No duplicate, proceed with insert
                            $status = 1; // Default status to active
                            $stmt = $conn->prepare("INSERT INTO course (cid, cname, ctext, cimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
                            // Bind parameters: s = string, i = integer
                            $stmt->bind_param("ssssi", $cid, $cname, $ctext, $cimg, $status); // Corrected bind type
                            if ($stmt->execute()) {
                                $message = "New course created successfully.";
                                $message_type = 'success';
                            } else {
                                $message = "Error creating course: " . $stmt->error;
                                $message_type = 'error';
                            }
                            $stmt->close();
                        }
                        $check_stmt->close();
                    } else {
                       $message = "Course ID and Course Name cannot be empty.";$message_type = 'error';
                    }
                    break;

                case 'update':
                    // Note: We generally don't allow changing the primary key (cid) easily.
                    // The cid field will be readonly in the form during update.
                    if (!empty($cid) && !empty($cname)) {
                        $stmt = $conn->prepare("UPDATE course SET cname=?, ctext=?, cimg=?, updated_at=NOW() WHERE cid=?");
                        // Bind parameters: s = string
                        $stmt->bind_param("ssss", $cname, $ctext, $cimg, $cid);
                        if ($stmt->execute()) {
                            $message = "Course updated successfully.";
                            $message_type = 'success';
                        } else {
                            $message = "Error updating course: " . $stmt->error;
                            $message_type = 'error';
                        }
                        $stmt->close();
                    } else {
                        $message = "Error: Course ID or Name missing for update.";
                        $message_type = 'error';
                    }
                    break;

                case 'delete':
                    if (!empty($cid)) {
                        // First, get the image path
                        $img_path = '';
                        $stmt_img = $conn->prepare("SELECT cimg FROM course WHERE cid = ?");
                        $stmt_img->bind_param("s", $cid);
                        if ($stmt_img->execute()) { $stmt_img->bind_result($img_path); $stmt_img->fetch(); }
                        $stmt_img->close();

                        // Prepare and execute deletion
                        $stmt = $conn->prepare("DELETE FROM course WHERE cid = ?");
                        $stmt->bind_param("s", $cid);
                        if ($stmt->execute()) {
                            $message = "Course deleted successfully.";
                            $message_type = 'success';
                            if (!empty($img_path) && file_exists($img_path)) { @unlink($img_path); }
                        } else { $message = "Error deleting course: " . $stmt->error; $message_type = 'error'; }
                        $stmt->close();
                    } else { $message = "Error: Course ID not provided for deletion."; $message_type = 'error'; }
                    break;

                case 'activate':
                case 'deactivate':
                    if (!empty($cid)) {
                        $new_status = ($action === 'activate') ? 1 : 0;
                        $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
                        $stmt = $conn->prepare("UPDATE course SET status=?, updated_at=NOW() WHERE cid=?");
                        $stmt->bind_param("is", $new_status, $cid);
                        if ($stmt->execute()) { $message = "Course " . $action_text . " successfully."; $message_type = 'success'; }
                        else { $message = "Error " . $action_text . " course: " . $stmt->error; $message_type = 'error'; }
                        $stmt->close();
                    } else { $message = "Error: Course ID not provided for status change."; $message_type = 'error'; }
                    break;
                default:
                    break;
            }
        } catch (mysqli_sql_exception $e) {
            $message = "Database error: " . $e->getMessage(); $message_type = 'error';
            error_log("Database error in course management: " . $e->getMessage());
        }
    }
}

// --- Fetch Existing Courses ---
$courses = [];
if (isset($conn)) {
    $sql = "SELECT cid, cname, ctext, cimg, status, created_at, updated_at FROM course ORDER BY created_at DESC";
    $result = $conn->query($sql);
    if ($result) { while($row = $result->fetch_assoc()) { $courses[] = $row; } }
    else { $message = "Error fetching courses: " . $conn->error; $message_type = 'error'; }
} else { $message = "DB connection not available to fetch courses."; $message_type = 'error'; }
?>

<h2>Course Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New Course</h3>
        <form id="courseForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">

            <label for="cid">Course ID:</label>
            <input type="text" id="cid" name="cid" required> <label for="cname">Course Name:</label>
            <input type="text" id="cname" name="cname" required>

            <label for="ctext">Course Description:</label>
            <textarea id="ctext" name="ctext" class="tinymce"></textarea>

            <label for="cimg">Course Image:</label>
            <input type="file" id="cimg" name="cimg" accept="image/jpeg, image/png, image/gif, image/webp">
            <input type="hidden" name="existing_cimg" id="existing_cimg" value="">
            <img id="current_cimg_preview" src="" alt="Current Image" style="display: none;" class="image-preview">

            <div class="button-group">
                <button type="submit" id="submitButton">Save Course</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="course-list-section">
        <h3>Existing Courses</h3>
        <table class="course-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description Preview</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['cid']); ?></td>
                            <td><?php echo htmlspecialchars($course['cname']); ?></td>
                            <td><?php $desc = strip_tags($course['ctext']); echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : ''); ?></td>
                            <td><?php if (!empty($course['cimg']) && file_exists($course['cimg'])): ?><img src="<?php echo htmlspecialchars($course['cimg']); ?>" alt="Course Image"><?php else: ?><span>No Image</span><?php endif; ?></td>
                            <td><span class="status-<?php echo $course['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $course['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($course['created_at'])); ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($course['updated_at'])); ?></td>
                            <td class="actions-cell">
                                <button class="edit-btn"
                                        data-cid="<?php echo htmlspecialchars($course['cid']); ?>"
                                        data-cname="<?php echo htmlspecialchars($course['cname']); ?>"
                                        data-ctext="<?php echo htmlspecialchars($course['ctext']); ?>"
                                        data-cimg="<?php echo htmlspecialchars($course['cimg']); ?>">Edit</button>
                                <form action="" method="POST" onsubmit="return confirm('Delete this course?');">
                                    <input type="hidden" name="action" value="delete"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit" value="delete">Delete</button></form>
                                <?php if ($course['status'] == 1): ?>
                                    <form action="" method="POST"><input type="hidden" name="action" value="deactivate"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit" value="deactivate">Deactivate</button></form>
                                <?php else: ?>
                                    <form action="" method="POST"><input type="hidden" name="action" value="activate"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit" value="activate">Activate</button></form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align: center; padding: 20px;">No courses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.tinymce',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
        height: 300
    });

    document.addEventListener('DOMContentLoaded', function() {
        // --- Get Form Elements ---
        const courseForm = document.getElementById('courseForm');
        const actionInput = document.getElementById('action');
        const cidInput = document.getElementById('cid'); // Get Course ID input
        const cnameInput = document.getElementById('cname');
        const ctextTextarea = document.getElementById('ctext');
        const cimgInput = document.getElementById('cimg');
        const existingCimgInput = document.getElementById('existing_cimg');
        const currentCimgPreview = document.getElementById('current_cimg_preview');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');

        // Edit Button Click Handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cid = this.getAttribute('data-cid'); // Get CID from data attribute
                const cname = this.getAttribute('data-cname');
                const ctext = this.getAttribute('data-ctext');
                const cimg = this.getAttribute('data-cimg');

                formTitle.textContent = 'Edit Course';
                actionInput.value = 'update';
                cidInput.value = cid; // Populate CID field
                cidInput.readOnly = true; // ** Make CID readonly during edit **
                cnameInput.value = cname;
                existingCimgInput.value = cimg;

                if (tinymce.get('ctext')) { tinymce.get('ctext').setContent(ctext || ''); }
                else { ctextTextarea.value = ctext; }

                if (cimg) { currentCimgPreview.src = cimg; currentCimgPreview.style.display = 'block'; }
                else { currentCimgPreview.src = ''; currentCimgPreview.style.display = 'none'; }

                submitButton.textContent = 'Update Course';
                cancelButton.style.display = 'inline-block';
                courseForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Cancel Edit Button Click Handler
        cancelButton.addEventListener('click', function() {
            resetForm();
        });

        // Function to reset the form
        function resetForm() {
            formTitle.textContent = 'Add New Course';
            actionInput.value = 'insert';
            cidInput.value = ''; // Clear CID field
            cidInput.readOnly = false; // ** Make CID editable again **
            cnameInput.value = '';
            cimgInput.value = '';
            existingCimgInput.value = '';
            currentCimgPreview.src = '';
            currentCimgPreview.style.display = 'none';

            if (tinymce.get('ctext')) { tinymce.get('ctext').setContent(''); }
            else { ctextTextarea.value = ''; }

            submitButton.textContent = 'Save Course';
            cancelButton.style.display = 'none';
        }

        // Image Preview Handler (no changes needed here)
        cimgInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { currentCimgPreview.src = e.target.result; currentCimgPreview.style.display = 'block'; }
                reader.readAsDataURL(file);
            } else {
                const existingImg = existingCimgInput.value;
                if (actionInput.value === 'update' && existingImg) { currentCimgPreview.src = existingImg; currentCimgPreview.style.display = 'block'; }
                else { currentCimgPreview.src = ''; currentCimgPreview.style.display = 'none'; }
            }
        });
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


<footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023. Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ms-1"></i></span>
  </div>
</footer>