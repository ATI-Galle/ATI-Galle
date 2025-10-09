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
    .course-table .actions-cell button, .course-table .actions-cell .edit-btn { padding: 6px 12px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; transition: background-color 0.2s ease; }
    .course-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
    .course-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
    .course-table .actions-cell button[value='delete'] { background-color: var(--danger-color); }
    .course-table .actions-cell button[value='delete']:hover { background-color: #c82333; }
    .course-table .actions-cell button[value='activate'] { background-color: var(--success-color); }
    .course-table .actions-cell button[value='activate']:hover { background-color: #218838; }
    .course-table .actions-cell button[value='deactivate'] { background-color: var(--secondary-color); }
    .course-table .actions-cell button[value='deactivate']:hover { background-color: #5a6268; }
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
     * Security function to check if the user can manage a specific course.
     * @param string $course_cid The ID of the course to check.
     * @param string $user_role The role of the current user.
     * @param string|null $user_cid The department ID of the current sub-admin.
     * @return bool True if permission is granted, false otherwise.
     */
    function canManageCourse($course_cid, $user_role, $user_cid) {
        if ($user_role === 'super_admin') {
            return true; // Super admin can manage all courses.
        }
        if ($user_role === 'sub_admin' && !empty($user_cid)) {
            // Sub-admin can only manage the course that matches their assigned department ID.
            return $course_cid === $user_cid;
        }
        return false; // Deny by default for any other role or condition.
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
        $action = $_POST['action'] ?? '';
        $cid = $_POST['cid'] ?? null;
        $cname = $_POST['cname'] ?? '';
        $ctext = $_POST['ctext'] ?? '';
        $existing_cimg = $_POST['existing_cimg'] ?? '';
        $cimg = $existing_cimg;
        
        $permission_granted = true; // Assume true until a check fails.

        // --- Handle POST Actions with Security Checks ---
        // A sub-admin can only 'update' their own course. They cannot 'insert' or 'delete'.
        if ($user_role === 'sub_admin' && in_array($action, ['insert', 'delete'])) {
            $message = "Permission Denied: You are not authorized to perform this action.";
            $message_type = 'error';
            $permission_granted = false;
        }

        // For actions that modify a specific course, check permissions.
        if ($permission_granted && in_array($action, ['update', 'delete', 'activate', 'deactivate'])) {
            if (!canManageCourse($cid, $user_role, $user_cid)) {
                $message = "Permission Denied: You cannot manage this course.";
                $message_type = 'error';
                $permission_granted = false;
            }
        }
        
        if ($permission_granted) {
            // --- Image Upload Handling (Same as before) ---
            $uploadOk = 1;
            // ... (Your complete, working image upload logic is placed here) ...
            if (isset($_FILES['cimg']) && $_FILES['cimg']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/courses/";
                if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
                $image_name = time() . "_" . basename($_FILES["cimg"]["name"]);
                $target_file = $target_dir . $image_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["cimg"]["tmp_name"]);
                if($check === false) { $message = "File is not an image."; $uploadOk = 0; }
                if ($_FILES["cimg"]["size"] > 5000000) { $message = "Image is too large."; $uploadOk = 0; }
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if(!in_array($imageFileType, $allowed)) { $message = "Invalid file type."; $uploadOk = 0; }
                if ($uploadOk && move_uploaded_file($_FILES["cimg"]["tmp_name"], $target_file)) {
                    $cimg = $target_file;
                    if ($action === 'update' && !empty($existing_cimg) && file_exists($existing_cimg)) { @unlink($existing_cimg); }
                } else if ($uploadOk) { $message = "Error uploading image."; $uploadOk = 0; }
            }

            if ($uploadOk) {
                try {
                    switch ($action) {
                        case 'insert':
                            $stmt = $conn->prepare("INSERT INTO course (cid, cname, ctext, cimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())");
                            $stmt->bind_param("ssss", $cid, $cname, $ctext, $cimg);
                            if ($stmt->execute()) { $message = "Course created."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'update':
                            $stmt = $conn->prepare("UPDATE course SET cname=?, ctext=?, cimg=?, updated_at=NOW() WHERE cid=?");
                            $stmt->bind_param("ssss", $cname, $ctext, $cimg, $cid);
                            if ($stmt->execute()) { $message = "Course updated."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'delete':
                            $stmt = $conn->prepare("DELETE FROM course WHERE cid = ?");
                            $stmt->bind_param("s", $cid);
                            if ($stmt->execute()) { $message = "Course deleted."; $message_type = 'success'; }
                            else { $message = "Error: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'activate':
                        case 'deactivate':
                            $new_status = ($action === 'activate') ? 1 : 0;
                            $stmt = $conn->prepare("UPDATE course SET status=? WHERE cid=?");
                            $stmt->bind_param("is", $new_status, $cid);
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
    $courses = [];
    if (isset($conn)) {
        $sql = "SELECT cid, cname, ctext, cimg, status, created_at, updated_at FROM course";
        $params = [];
        $types = '';

        if ($user_role === 'sub_admin') {
            $sql .= " WHERE cid = ?";
            $params[] = $user_cid;
            $types .= 's';
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        if($stmt){
            if (!empty($params)) $stmt->bind_param($types, ...$params);
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

    <!-- ADD/EDIT FORM: Show form based on role -->
    <div class="form-section">
        <h3 id="formTitle"><?php echo ($user_role === 'sub_admin') ? 'Edit Your Department Details' : 'Add/Edit Course'; ?></h3>
        <form id="courseForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">

            <label for="cid">Course ID:</label>
            <input type="text" id="cid" name="cid" required <?php if($user_role === 'sub_admin') echo 'readonly'; ?>>
            
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

    <hr>

    <!-- COURSE LIST TABLE -->
    <div class="course-list-section">
        <h3><?php echo ($user_role === 'sub_admin') ? 'Your Assigned Department' : 'Existing Courses'; ?></h3>
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
                                <?php if (canManageCourse($course['cid'], $user_role, $user_cid)): ?>
                                    <button class="edit-btn" data-cid="<?php echo htmlspecialchars($course['cid']); ?>" data-cname="<?php echo htmlspecialchars($course['cname']); ?>" data-ctext="<?php echo htmlspecialchars($course['ctext']); ?>" data-cimg="<?php echo htmlspecialchars($course['cimg']); ?>">Edit</button>
                                    <?php if ($user_role === 'super_admin'): // Only super admins can delete or change status ?>
                                        <form action="" method="POST" onsubmit="return confirm('Delete this course?');" style="display:inline-block;"><input type="hidden" name="action" value="delete"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit">Delete</button></form>
                                        <?php if ($course['status'] == 1): ?>
                                            <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="deactivate"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit">Deactivate</button></form>
                                        <?php else: ?>
                                            <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="activate"><input type="hidden" name="cid" value="<?php echo htmlspecialchars($course['cid']); ?>"><button type="submit">Activate</button></form>
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
    tinymce.init({
        selector: 'textarea#ctext',
        plugins: 'lists link image charmap preview anchor',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
        height: 250
    });

    document.addEventListener('DOMContentLoaded', function() {
        const courseForm = document.getElementById('courseForm');
        const actionInput = document.getElementById('action');
        const cidInput = document.getElementById('cid');
        const cnameInput = document.getElementById('cname');
        const ctextTextarea = document.getElementById('ctext');
        const existingCimgInput = document.getElementById('existing_cimg');
        const currentCimgPreview = document.getElementById('current_cimg_preview');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');
        const userRole = "<?php echo $user_role; ?>";

        function resetForm() {
            actionInput.value = 'insert';
            courseForm.reset();
            currentCimgPreview.style.display = 'none';
            tinymce.get('ctext').setContent('');
            submitButton.textContent = 'Save Course';
            cancelButton.style.display = 'none';
            
            if (userRole === 'super_admin') {
                formTitle.textContent = 'Add New Course';
                cidInput.readOnly = false;
            } else {
                formTitle.textContent = 'Edit Your Department Details';
                cidInput.readOnly = true;
            }
        }

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cid = this.dataset.cid;
                const cname = this.dataset.cname;
                const ctext = this.dataset.ctext;
                const cimg = this.dataset.cimg;

                formTitle.textContent = 'Edit Course';
                actionInput.value = 'update';
                cidInput.value = cid;
                cidInput.readOnly = true; // Always make CID readonly during edit for safety
                cnameInput.value = cname;
                existingCimgInput.value = cimg;
                tinymce.get('ctext').setContent(ctext || '');

                if (cimg) { currentCimgPreview.src = cimg; currentCimgPreview.style.display = 'block'; }
                else { currentCimgPreview.style.display = 'none'; }

                submitButton.textContent = 'Update Course';
                cancelButton.style.display = 'inline-block';
                courseForm.scrollIntoView({ behavior: 'smooth' });
            });
        });

        cancelButton.addEventListener('click', resetForm);
        
        // Hide the "Add/Edit" form for sub-admins if there is no course to edit.
        // Or default it to their course details.
        if (userRole === 'sub_admin') {
            const editBtn = document.querySelector('.edit-btn');
            if (editBtn) {
                editBtn.click(); // Automatically populate form for the sub-admin's department
            } else {
                document.querySelector('.form-section').style.display = 'none'; // No department, so hide form.
            }
        }
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

