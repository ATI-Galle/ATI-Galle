<?php
// It's crucial that your session is started. This is typically done in a central file like 'header.php'.
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

    /* --- Course Table Styling --- */
    .course-list-section { margin-top: 30px; overflow-x: auto; }
    .course-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 1px 5px rgba(0,0,0,0.08); }
    .course-table th, .course-table td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: left; vertical-align: middle; }
    .course-table th { background-color: #f2f5f8; font-weight: 600; white-space: nowrap; }
    .course-table tbody tr:nth-child(even) { background-color: var(--light-color); }
    .course-table tbody tr:hover { background-color: #e9ecef; }
    .course-table img { display: block; max-width: 80px; height: auto; border-radius: var(--border-radius); }
    .course-table .actions-cell { white-space: nowrap; min-width: 240px; }
    .course-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
    .course-table .actions-cell button { padding: 6px 12px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; transition: background-color 0.2s ease; }
    .course-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
    .course-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
    .course-table .actions-cell button[name='delete_action'] { background-color: var(--danger-color); }
    .course-table .actions-cell button[name='delete_action']:hover { background-color: #c82333; }
    .course-table .actions-cell button[name='activate_action'] { background-color: var(--success-color); }
    .course-table .actions-cell button[name='activate_action']:hover { background-color: #218838; }
    .course-table .actions-cell button[name='deactivate_action'] { background-color: var(--secondary-color); }
    .course-table .actions-cell button[name='deactivate_action']:hover { background-color: #5a6268; }
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

    // Directly use session variables for clarity and robustness.
    $user_role = $_SESSION['role'] ?? 'guest';
    $user_cid = $_SESSION['cid'] ?? null;

    // Create a single variable to check for full management permissions.
    $is_manager = ($user_role === 'super_admin' || $user_cid === 'SAdmin');

    /**
     * Security function to check if the user can manage a specific course.
     * Managers can manage everything. Sub-admins can only manage their own course.
     */
    function canManageCourse($course_cid, $is_manager_flag, $user_cid_from_session) {
        if ($is_manager_flag) {
            return true; // Managers have universal access.
        }
        // Sub-admin can only manage the course matching their assigned CID.
        return $course_cid === $user_cid_from_session;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
        $action = $_POST['action'] ?? '';
        $cid = $_POST['cid'] ?? null; // The CID of the course being acted upon
        $cname = $_POST['cname'] ?? '';
        $ctext = $_POST['ctext'] ?? '';
        $existing_cimg = $_POST['existing_cimg'] ?? '';
        $cimg = $existing_cimg;
        
        $permission_granted = false;

        // Reworked permission logic to be clearer.
        // Step 1: Check if the action requires a manager.
        $is_manager_only_action = in_array($action, ['insert', 'delete', 'activate', 'deactivate']);

        if ($is_manager_only_action && !$is_manager) {
            $message = "Permission Denied: You are not authorized to perform this action.";
            $message_type = 'error';
        } 
        // Step 2: For any action on an existing record, check if the user is allowed to manage that specific course.
        elseif (!in_array($action, ['insert']) && !canManageCourse($cid, $is_manager, $user_cid)) {
             $message = "Permission Denied: You cannot manage this specific course.";
             $message_type = 'error';
        } else {
            // If we reach here, the user has permission to perform the requested action.
            $permission_granted = true;
        }
        
        if ($permission_granted) {
            // --- Image Upload Handling (no changes needed here) ---
            $uploadOk = 1;
            if (isset($_FILES['cimg']) && $_FILES['cimg']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/course_images/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $imageFileType = strtolower(pathinfo($_FILES["cimg"]["name"], PATHINFO_EXTENSION));
                $new_filename = $target_dir . uniqid('course_', true) . '.' . $imageFileType;
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["cimg"]["tmp_name"]);
                if($check === false) {
                    $message = "File is not an image.";
                    $message_type = 'error';
                    $uploadOk = 0;
                }
                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $message_type = 'error';
                    $uploadOk = 0;
                }
                
                if ($uploadOk && move_uploaded_file($_FILES["cimg"]["tmp_name"], $new_filename)) {
                    $cimg = $new_filename; // Update cimg to new path
                } else {
                    $uploadOk = 0;
                    if(empty($message)) {
                        $message = "Sorry, there was an error uploading your file.";
                        $message_type = 'error';
                    }
                }
            }

            if ($uploadOk) {
                try {
                    // ==========================================================
                    // FIXED CODE BLOCK STARTS HERE
                    // ==========================================================
                    switch ($action) {
                        case 'insert':
                            $stmt = $conn->prepare("INSERT INTO course (cid, cname, ctext, cimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
                            $stmt->bind_param("ssss", $cid, $cname, $ctext, $cimg);
                            if ($stmt->execute()) { $message = "Course created successfully."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        
                        case 'update':
                            $stmt = $conn->prepare("UPDATE course SET cname=?, ctext=?, cimg=?, updated_at=NOW() WHERE cid=?");
                            $stmt->bind_param("ssss", $cname, $ctext, $cimg, $cid);
                            if ($stmt->execute()) { $message = "Course updated successfully."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;

                        case 'delete':
                            $stmt = $conn->prepare("DELETE FROM course WHERE cid=?");
                            $stmt->bind_param("s", $cid);
                            if ($stmt->execute()) { $message = "Course deleted successfully."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;

                        case 'activate':
                            $stmt = $conn->prepare("UPDATE course SET status=1, updated_at=NOW() WHERE cid=?");
                            $stmt->bind_param("s", $cid);
                            if ($stmt->execute()) { $message = "Course activated successfully."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;

                        case 'deactivate':
                            $stmt = $conn->prepare("UPDATE course SET status=0, updated_at=NOW() WHERE cid=?");
                            $stmt->bind_param("s", $cid);
                            if ($stmt->execute()) { $message = "Course deactivated successfully."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                    }
                    // ==========================================================
                    // FIXED CODE BLOCK ENDS HERE
                    // ==========================================================
                } catch (mysqli_sql_exception $e) {
                    $message = "Database error: " . $e->getMessage();
                    $message_type = 'error';
                }
            }
        }
    }

    // --- Fetch Data for Display (Filtered by Role) ---
    $courses = [];
    if (isset($conn)) {
        // Simplified the query logic. Managers see all, others see their specific one.
        if ($is_manager) {
            $sql = "SELECT * FROM course ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
        } else {
            // Regular sub-admins only see their own course
            $sql = "SELECT * FROM course WHERE cid = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $user_cid);
        }

        if($stmt){
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) $courses = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $message = "Error fetching courses: " . $conn->error;
            $message_type = 'error';
        }
    }
    ?>

    <h2>Course Management</h2>
    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($is_manager || !empty($user_cid)): ?>
    <div class="form-section">
        <h3 id="formTitle"><?php echo ($is_manager) ? 'Add/Edit Course' : 'Edit Your Department Details'; ?></h3>
        <form id="courseForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">

            <label for="cid">Course ID:</label>
            <input type="text" id="cid" name="cid" required <?php if(!$is_manager && !empty($courses) && count($courses) > 0) echo 'readonly'; ?>>
            
            <label for="cname">Course Name:</label>
            <input type="text" id="cname" name="cname" required>

            <label for="ctext">Course Description:</label>
            <textarea id="ctext" name="ctext"></textarea>

            <label for="cimg">Course Image:</label>
            <input type="file" id="cimg" name="cimg" accept="image/*">
            <input type="hidden" name="existing_cimg" id="existing_cimg" value="">
            <img id="current_cimg_preview" src="" alt="Current Image" style="display: none;" class="image-preview">

            <div class="button-group">
                <button type="submit" id="submitButton">Save Course</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <hr>

    <div class="course-list-section">
        <h3><?php echo ($is_manager) ? 'Existing Courses' : 'Your Assigned Department'; ?></h3>
        <table class="course-table">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Description</th><th>Image</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['cid']); ?></td>
                            <td><?php echo htmlspecialchars($course['cname']); ?></td>
                            <td><?php $desc = strip_tags($course['ctext']); echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : ''); ?></td>
                            <td><?php if (!empty($course['cimg']) && file_exists($course['cimg'])): ?><img src="<?php echo htmlspecialchars($course['cimg']); ?>" alt="Course Image"><?php endif; ?></td>
                            <td><span class="status-<?php echo $course['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $course['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td class="actions-cell">
                                <?php if (canManageCourse($course['cid'], $is_manager, $user_cid)): ?>
                                    
                                    <button type="button" class="edit-btn" 
                                        data-cid="<?php echo htmlspecialchars($course['cid']); ?>" 
                                        data-cname="<?php echo htmlspecialchars($course['cname']); ?>" 
                                        data-ctext="<?php echo htmlspecialchars($course['ctext']); ?>" 
                                        data-cimg="<?php echo htmlspecialchars($course['cimg']); ?>">Edit</button>
                                    
                                    <?php if ($is_manager): ?>
                                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>">
                                            <button type="submit" name="delete_action">Delete</button>
                                        </form>
                                        
                                        <?php if ($course['status'] == 1): ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="action" value="deactivate">
                                                <input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>">
                                                <button type="submit" name="deactivate_action">Deactivate</button>
                                            </form>
                                        <?php else: ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="action" value="activate">
                                                <input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>">
                                                <button type="submit" name="activate_action">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span>No actions permitted.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">No courses found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // Initialize TinyMCE
    tinymce.init({
        selector: 'textarea#ctext',
        plugins: 'lists link image media table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
                  alignleft aligncenter alignright alignjustify | \
                  bullist numlist outdent indent | removeformat | help'
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('courseForm');
        const actionInput = document.getElementById('action');
        const cidInput = document.getElementById('cid');
        const cnameInput = document.getElementById('cname');
        const ctextTextarea = document.getElementById('ctext');
        const cimgInput = document.getElementById('cimg');
        const existingCimgInput = document.getElementById('existing_cimg');
        const imgPreview = document.getElementById('current_cimg_preview');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelEditButton = document.getElementById('cancelEdit');
        
        // Use event delegation for edit buttons
        document.querySelector('.course-table').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('edit-btn')) {
                const button = e.target;
                
                // Populate form with data attributes
                actionInput.value = 'update';
                cidInput.value = button.dataset.cid;
                cnameInput.value = button.dataset.cname;
                
                // Set content for TinyMCE
                tinymce.get('ctext').setContent(button.dataset.ctext);

                existingCimgInput.value = button.dataset.cimg;

                // Show image preview if image exists
                if (button.dataset.cimg) {
                    imgPreview.src = button.dataset.cimg;
                    imgPreview.style.display = 'block';
                } else {
                    imgPreview.style.display = 'none';
                }

                // Update UI for editing
                formTitle.textContent = 'Edit Course Details';
                submitButton.textContent = 'Update Course';
                cancelEditButton.style.display = 'inline-block';
                cidInput.setAttribute('readonly', true); // Make CID readonly during edit

                // Scroll to form
                form.scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Cancel Edit button functionality
        cancelEditButton.addEventListener('click', function() {
            resetForm();
        });

        function resetForm() {
            form.reset();
            actionInput.value = 'insert';
            existingCimgInput.value = '';
            imgPreview.style.display = 'none';
            imgPreview.src = '';
            
            // Reset TinyMCE
            tinymce.get('ctext').setContent('');
            
            formTitle.textContent = '<?php echo ($is_manager) ? 'Add New Course' : 'Edit Your Department Details'; ?>';
            submitButton.textContent = 'Save Course';
            cancelEditButton.style.display = 'none';
            
            // Only make CID editable if user is a manager
            <?php if ($is_manager): ?>
                cidInput.removeAttribute('readonly');
            <?php endif; ?>
        }
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
