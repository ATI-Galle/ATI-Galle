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

    .form-section input[type=text],
    .form-section input[type=file],
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

    /* --- Staff List Table Styling --- */
    .staff-list-section {
        margin-top: 30px;
        overflow-x: auto;
    }

    .staff-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
    }

    .staff-table th,
    .staff-table td {
        border: 1px solid #e0e0e0;
        padding: 12px 15px;
        text-align: left;
        vertical-align: middle;
    }

    .staff-table th {
        background-color: #f2f5f8;
        font-weight: 600;
        white-space: nowrap;
    }

    .staff-table tbody tr:nth-child(even) {
        background-color: var(--light-color);
    }

    .staff-table tbody tr:hover {
        background-color: #e9ecef;
    }

    .staff-table img {
        display: block;
        max-width: 80px;
        height: auto;
        border-radius: var(--border-radius);
    }

    .staff-table .actions-cell {
        white-space: nowrap;
        min-width: 240px;
    }

    .staff-table .actions-cell form {
        display: inline-block;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .staff-table .actions-cell button,
    .staff-table .actions-cell .edit-btn {
        padding: 6px 12px;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 0.85rem;
        color: white;
        transition: background-color 0.2s ease;
    }
    
    .staff-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
    .staff-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
    .staff-table .actions-cell button[value='delete'] { background-color: var(--danger-color); }
    .staff-table .actions-cell button[value='delete']:hover { background-color: #c82333; }
    .staff-table .actions-cell button[value='activate'] { background-color: var(--success-color); }
    .staff-table .actions-cell button[value='activate']:hover { background-color: #218838; }
    .staff-table .actions-cell button[value='deactivate'] { background-color: var(--secondary-color); }
    .staff-table .actions-cell button[value='deactivate']:hover { background-color: #5a6268; }

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
     * Security function to check if the current user can manage a specific staff member.
     * @param mysqli $conn The database connection.
     * @param int $staff_id The ID of the staff member to check.
     * @param string $user_role The role of the current user ('super_admin' or 'sub_admin').
     * @param string|null $user_cid The department ID of the current sub-admin.
     * @return bool True if permission is granted, false otherwise.
     */
    function canManageStaff($conn, $staff_id, $user_role, $user_cid) {
        if ($user_role === 'super_admin') {
            return true; // Super admin can do anything.
        }
        if ($user_role === 'sub_admin' && !empty($user_cid) && !empty($staff_id)) {
            $stmt = $conn->prepare("SELECT cid FROM staff WHERE stid = ?");
            $stmt->bind_param("i", $staff_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $staff = $result->fetch_assoc();
                $stmt->close();
                // Grant permission if the staff record exists and its department matches the sub-admin's.
                if ($staff && $staff['cid'] === $user_cid) {
                    return true;
                }
            }
        }
        return false; // Deny by default.
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
        $action = $_POST['action'] ?? '';
        $stid = $_POST['stid'] ?? null;
        $sname = $_POST['sname'] ?? '';
        $spos = $_POST['spos'] ?? '';
        $cid = $_POST['cid'] ?? null;
        $sed = $_POST['sed'] ?? '';
        $existing_stimg = $_POST['existing_stimg'] ?? '';
        $stimg = $existing_stimg;
        
        $permission_granted = true; // Assume permission is granted until checked

        // --- Handle POST Actions with Security Checks ---
        switch ($action) {
            case 'insert':
                // For sub-admins, force their own department ID. This is a security measure.
                if ($user_role === 'sub_admin') {
                    $cid = $user_cid;
                }
                break;
            case 'update':
            case 'delete':
            case 'activate':
            case 'deactivate':
                // For all modification actions, verify permission first.
                if (!canManageStaff($conn, $stid, $user_role, $user_cid)) {
                    $message = "Permission Denied: You are not authorized to modify this record.";
                    $message_type = 'error';
                    $permission_granted = false;
                }
                // For sub-admins updating a record, ensure they can't change the department.
                // This securely overrides any value that might be sent from the form.
                if ($action === 'update' && $user_role === 'sub_admin') {
                    $cid = $user_cid;
                }
                break;
        }

        if ($permission_granted) {
            // --- FIXED: Image Upload Handling ---
            $uploadOk = 1;
            if (isset($_FILES['stimg']) && $_FILES['stimg']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/staff/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                $image_name = time() . "_" . basename($_FILES["stimg"]["name"]);
                $target_file = $target_dir . $image_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Basic validation
                $check = getimagesize($_FILES["stimg"]["tmp_name"]);
                if ($check === false) {
                    $message = "File is not a valid image."; $message_type = 'error'; $uploadOk = 0;
                }
                if ($_FILES["stimg"]["size"] > 5000000) { // 5MB limit
                    $message = "Image file is too large."; $message_type = 'error'; $uploadOk = 0;
                }
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($imageFileType, $allowed_types)) {
                    $message = "Only JPG, JPEG, PNG, GIF, WEBP files are allowed."; $message_type = 'error'; $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["stimg"]["tmp_name"], $target_file)) {
                    $stimg = $target_file; // Set new image path
                    // Delete old image if updating
                    if ($action === 'update' && !empty($existing_stimg) && file_exists($existing_stimg)) {
                        @unlink($existing_stimg);
                    }
                } else if ($uploadOk) {
                    $message = "Error uploading image file."; $message_type = 'error'; $uploadOk = 0;
                }
            }
            
            if ($uploadOk) {
                 // --- Perform Database Action ---
                try {
                    switch ($action) {
                        case 'insert':
                            $stmt = $conn->prepare("INSERT INTO staff (sname, spos, cid, sed, stimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
                            $stmt->bind_param("sssss", $sname, $spos, $cid, $sed, $stimg);
                            if ($stmt->execute()) { $message = "New staff member added successfully."; $message_type = 'success'; }
                            else { $message = "Error adding staff: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'update':
                            $stmt = $conn->prepare("UPDATE staff SET sname=?, spos=?, cid=?, sed=?, stimg=?, updated_at=NOW() WHERE stid=?");
                            $stmt->bind_param("sssssi", $sname, $spos, $cid, $sed, $stimg, $stid);
                            if ($stmt->execute()) { $message = "Staff details updated successfully."; $message_type = 'success'; }
                            else { $message = "Error updating staff: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'delete':
                            $stmt = $conn->prepare("DELETE FROM staff WHERE stid = ?");
                            $stmt->bind_param("i", $stid);
                            if ($stmt->execute()) { $message = "Staff member deleted successfully."; $message_type = 'success'; }
                            else { $message = "Error deleting staff: " . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'activate':
                        case 'deactivate':
                            $new_status = ($action === 'activate') ? 1 : 0;
                            $stmt = $conn->prepare("UPDATE staff SET status=? WHERE stid=?");
                            $stmt->bind_param("ii", $new_status, $stid);
                            if ($stmt->execute()) { $message = "Status updated successfully."; $message_type = 'success'; }
                            else { $message = "Error updating status: " . $stmt->error; $message_type = 'error'; }
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

    // --- Fetch Data for Display ---
    $courses = [];
    if (isset($conn)) {
        $course_result = $conn->query("SELECT cid, cname FROM course WHERE status = 1 ORDER BY cname ASC");
        if ($course_result) $courses = $course_result->fetch_all(MYSQLI_ASSOC);
    }
    
    $staff_members = [];
    if (isset($conn)) {
        $sql = "SELECT s.stid, s.sname, s.spos, s.cid, c.cname, s.sed, s.stimg, s.status, s.created_at, s.updated_at 
                FROM staff s 
                LEFT JOIN course c ON s.cid = c.cid";
        $params = [];
        $types = '';

        if ($user_role === 'sub_admin') {
            $sql .= " WHERE s.cid = ?";
            $params[] = $user_cid;
            $types .= 's';
        }
        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $conn->prepare($sql);
        if($stmt){
            if (!empty($params)) $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) $staff_members = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $message = "Error fetching staff: " . $conn->error;
            $message_type = 'error';
        }
    }
    ?>

    <h2>Staff Management</h2>
    <?php if (!empty($message)) : ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- ADD/EDIT FORM -->
    <div class="form-section">
        <h3 id="formTitle">Add New Staff</h3>
        <form id="staffForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">
            <input type="hidden" name="stid" id="stid" value="">

            <label for="sname">Staff Name:</label>
            <input type="text" id="sname" name="sname" required>

            <label for="spos">Position:</label>
            <select id="spos" name="spos" required>
                <option value="">Select Profession</option>
                <option value="Dire">Director</option>
                <option value="HOD">Head of Department (HOD)</option>
                <option value="SLect">Senior Lecturer</option>
                <option value="Lect">Lecturer</option>
                <option value="Demo">Demonstrator</option>
                 <option value="Regi">Registrar</option>
                <option value="AReg">Asst. Registrar</option>
                <option value="Acct">Accountant</option>
                <option value="Lib">Librarian</option>
                <option value="Offi">Office Staff</option>
            </select>

            <label for="cid">Associated Department:</label>
            <select id="cid" name="cid" <?php if ($user_role === 'sub_admin') echo 'disabled'; ?>>
                <option value="">None</option>
                <?php foreach ($courses as $course) : ?>
                    <?php $selected = ($user_role === 'sub_admin' && $user_cid === $course['cid']) ? 'selected' : ''; ?>
                    <option value="<?php echo htmlspecialchars($course['cid']); ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($course['cid'] . ' - ' . $course['cname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="sed">Education/Details:</label>
            <textarea id="sed" name="sed"></textarea>

            <label for="stimg">Staff Image:</label>
            <input type="file" id="stimg" name="stimg" accept="image/*">
            <input type="hidden" name="existing_stimg" id="existing_stimg" value="">
            <img id="current_stimg_preview" src="#" alt="Image Preview" style="display: none;" class="image-preview">

            <div class="button-group">
                <button type="submit" id="submitButton">Save Staff</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr>

    <!-- STAFF LIST TABLE -->
    <div class="staff-list-section">
        <h3>Current Staff</h3>
        <table class="staff-table">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Position</th><th>Department</th><th>Image</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staff_members)) : ?>
                    <?php foreach ($staff_members as $staff) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['stid']); ?></td>
                            <td><?php echo htmlspecialchars($staff['sname']); ?></td>
                            <td><?php echo htmlspecialchars($staff['spos']); ?></td>
                            <td><?php echo htmlspecialchars($staff['cname'] ?? 'N/A'); ?></td>
                            <td><?php if (!empty($staff['stimg']) && file_exists($staff['stimg'])) : ?><img src="<?php echo htmlspecialchars($staff['stimg']); ?>" alt="Staff Image"><?php else : ?><span>No Image</span><?php endif; ?></td>
                            <td><span class="status-<?php echo $staff['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $staff['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td class="actions-cell">
                                <?php 
                                // Show actions only if user has permission
                                $can_manage_record = ($user_role === 'super_admin' || ($user_role === 'sub_admin' && $staff['cid'] === $user_cid));
                                if ($can_manage_record):
                                ?>
                                    <button class="edit-btn" data-stid="<?php echo htmlspecialchars($staff['stid']); ?>" data-sname="<?php echo htmlspecialchars($staff['sname']); ?>" data-spos="<?php echo htmlspecialchars($staff['spos']); ?>" data-cid="<?php echo htmlspecialchars($staff['cid'] ?? ''); ?>" data-sed="<?php echo htmlspecialchars($staff['sed']); ?>" data-stimg="<?php echo htmlspecialchars($staff['stimg']); ?>">Edit</button>
                                    <form action="" method="POST" onsubmit="return confirm('Delete this staff member?');" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>">
                                        <button type="submit" value="delete">Delete</button>
                                    </form>
                                    <?php if ($staff['status'] == 1) : ?>
                                        <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="deactivate"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>"><button type="submit" value="deactivate">Deactivate</button></form>
                                    <?php else : ?>
                                        <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="activate"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>"><button type="submit" value="activate">Activate</button></form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span>No actions permitted.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="7" style="text-align: center;">No staff members found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffForm = document.getElementById('staffForm');
    const actionInput = document.getElementById('action');
    const stidInput = document.getElementById('stid');
    const snameInput = document.getElementById('sname');
    const sposSelect = document.getElementById('spos');
    const cidSelect = document.getElementById('cid');
    const sedTextarea = document.getElementById('sed');
    const existingStimgInput = document.getElementById('existing_stimg');
    const currentStimgPreview = document.getElementById('current_stimg_preview');
    const stimgInput = document.getElementById('stimg');
    const formTitle = document.getElementById('formTitle');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelEdit');
    const userRole = "<?php echo $user_role; ?>";
    const userCid = "<?php echo $user_cid; ?>";

    function resetForm() {
        formTitle.textContent = 'Add New Staff';
        actionInput.value = 'insert';
        staffForm.reset();
        stidInput.value = '';
        existingStimgInput.value = '';
        currentStimgPreview.style.display = 'none';
        sedTextarea.value = '';
        stimgInput.value = ''; // Clear file input

        if (userRole === 'sub_admin') {
            cidSelect.value = userCid;
            cidSelect.disabled = true;
        } else {
            cidSelect.value = '';
            cidSelect.disabled = false;
        }

        submitButton.textContent = 'Save Staff';
        cancelButton.style.display = 'none';
    }

    // Initial state of the form for sub-admins
    if(userRole === 'sub_admin') {
        cidSelect.value = userCid;
        cidSelect.disabled = true;
    }

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            actionInput.value = 'update';
            formTitle.textContent = 'Edit Staff Details';
            submitButton.textContent = 'Update Staff';
            cancelButton.style.display = 'inline-block';

            stidInput.value = this.dataset.stid;
            snameInput.value = this.dataset.sname;
            sposSelect.value = this.dataset.spos;
            cidSelect.value = this.dataset.cid;
            sedTextarea.value = this.dataset.sed;
            existingStimgInput.value = this.dataset.stimg;
            
            if (this.dataset.stimg) {
                currentStimgPreview.src = this.dataset.stimg;
                currentStimgPreview.style.display = 'block';
            } else {
                currentStimgPreview.style.display = 'none';
            }
            stimgInput.value = ''; // Clear file input on edit

            staffForm.scrollIntoView({ behavior: 'smooth' });
        });
    });

    cancelButton.addEventListener('click', resetForm);
});
</script>

