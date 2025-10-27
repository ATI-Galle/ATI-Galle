<?php

include('include/header.php');

// Get the logged-in user's role and department from the session.
$user_role = $_SESSION['role'] ?? 'guest';
$user_cid = $_SESSION['cid'] ?? null;

?>

<style>
    /* --- Basic Layout for Fixed Sidebar --- */
    body {
        display: flex; /* Enable flexbox layout */
        margin: 0; /* Reset default body margin */
    }
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
    h2, h3 {
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
    .message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
    .message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }

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
    .form-section select:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
    .form-section .image-preview {
        margin-top: 10px;
        max-width: 150px;
        border: 1px solid #ddd;
        padding: 5px;
        background: #fff;
        border-radius: var(--border-radius);
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
        margin-right: 10px;
        transition: background-color 0.3s ease;
    }
    .form-section button:hover {
        opacity: 0.9;
    }
    .form-section button#cancelEdit { background-color: var(--secondary-color); }

    /* --- Staff List Section - Container for search and cards --- */
    .staff-list-section {
        margin-top: 30px;
    }
    
    /* --- Search Bar Styling --- */
    .search-container {
        margin-bottom: 25px; /* Space below search bar */
    }
    #staffSearchInput {
        width: 100%;
        font-size: 1rem;
        padding: 12px 20px 12px 15px;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        box-sizing: border-box;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    /* --- Categorized Staff Sections --- */
    .staff-category-section {
        margin-bottom: 40px;
    }
    .staff-category-section h3 {
        text-align: left;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 8px;
        margin-bottom: 25px;
        display: block; /* Ensure full width border */
    }

    /* --- Staff Cards Grid --- */
    .staff-cards-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Responsive grid for cards */
        gap: 25px;
    }

    .staff-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .staff-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .staff-card img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid var(--primary-color);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .staff-card h4 {
        margin: 10px 0 5px 0;
        color: var(--dark-color);
        font-size: 1.3em;
        font-weight: 700;
    }
    .staff-card p {
        margin: 0;
        color: var(--secondary-color);
        font-size: 0.95em;
    }
    .staff-card .department {
        font-style: italic;
        color: #555;
        margin-top: 5px;
    }
    .staff-card .status {
        margin-top: 10px;
        font-weight: bold;
        padding: 4px 10px;
        border-radius: var(--border-radius);
    }
    .status-active { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }

    .staff-card .actions-card {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap; /* Allow buttons to wrap */
        justify-content: center;
        gap: 8px; /* Space between buttons */
    }
    .staff-card .actions-card button,
    .staff-card .actions-card .edit-btn {
        padding: 8px 15px;
        border: none;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 0.9rem;
        color: white;
        transition: background-color 0.3s ease, transform 0.2s ease;
        flex-grow: 1; /* Allow buttons to grow */
        min-width: 100px; /* Ensure buttons don't get too small */
    }
    .staff-card .actions-card button:hover,
    .staff-card .actions-card .edit-btn:hover {
        transform: translateY(-2px);
    }
    .staff-card .actions-card .edit-btn { background-color: var(--warning-color); color: #333; }
    .staff-card .actions-card .edit-btn:hover { background-color: #ffc82a; }
    .staff-card .actions-card button[value='delete'] { background-color: var(--danger-color); }
    .staff-card .actions-card button[value='delete']:hover { background-color: #e02d3d; }
    .staff-card .actions-card button[value='activate'] { background-color: var(--success-color); }
    .staff-card .actions-card button[value='activate']:hover { background-color: #218838; }
    .staff-card .actions-card button[value='deactivate'] { background-color: var(--secondary-color); }
    .staff-card .actions-card button[value='deactivate']:hover { background-color: #5a6268; }
    .staff-card .actions-card form {
        display: inline-block;
        margin: 0;
    }
    .staff-card .actions-card span {
        color: var(--secondary-color);
        font-style: italic;
        font-size: 0.85em;
    }

    /* --- Responsive Adjustments --- */
    @media (max-width: 992px) {
        .page-container {
            margin-left: 0;
            width: 100%;
            padding: 15px;
        }
    }
</style>

<?php include('include/sidebar.php'); ?>

<div class="page-container">
    <?php
    include('include/config.php');

    $message = '';
    $message_type = '';

    /**
     * Flexible security function to check permissions.
     */
    function canManageStaff($conn, $staff_id, $user_role, $user_cid) {
        if ($user_role === 'super_admin') {
            return true; // Super admin can always manage.
        }
        // A sub_admin can manage a record if their department matches the staff's department.
        if ($user_role === 'sub_admin' && !empty($user_cid) && !empty($staff_id)) {
            $stmt = $conn->prepare("SELECT cid FROM staff WHERE stid = ?");
            $stmt->bind_param("i", $staff_id);
            if ($stmt->execute()) {
                $staff = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                // Check if staff record was found and if its department matches the user's
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
        
        $permission_granted = false; // Deny by default

        // --- Reworked logic to check permissions before any action ---
        switch ($action) {
            case 'insert':
                // A sub_admin can add staff, but only to their own department.
                if ($user_role === 'super_admin' || ($user_role === 'sub_admin' && !empty($user_cid))) {
                    $permission_granted = true;
                    if ($user_role === 'sub_admin') {
                        $cid = $user_cid; // Force their own department ID for security.
                    }
                }
                break;
            case 'update':
            case 'delete':
            case 'activate':
            case 'deactivate':
                // For all modification actions, verify permission using our new function.
                if (canManageStaff($conn, $stid, $user_role, $user_cid)) {
                    $permission_granted = true;
                    // Securely override the department ID if a sub-admin is updating.
                    if ($action === 'update' && $user_role === 'sub_admin') {
                        $cid = $user_cid;
                    }
                }
                break;
        }

        if ($permission_granted) {
            // --- Start of Image Upload Logic ---
            $uploadOk = 1;
            // Check if a new image file was uploaded without errors
            if (isset($_FILES['stimg']) && $_FILES['stimg']['error'] === UPLOAD_ERR_OK) {
                $target_dir = 'uploads/staff/'; // IMPORTANT: Make sure this directory exists!
                
                // Ensure the target directory exists, if not, create it.
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                // Get file info and create a unique name to prevent overwriting
                $imageFileType = strtolower(pathinfo($_FILES['stimg']['name'], PATHINFO_EXTENSION));
                $unique_filename = uniqid('staff_', true) . '.' . $imageFileType;
                $target_file = $target_dir . $unique_filename;

                // Check if image file is an actual image
                $check = getimagesize($_FILES['stimg']['tmp_name']);
                if ($check === false) {
                    $message = 'File is not an image.';
                    $message_type = 'error';
                    $uploadOk = 0;
                }

                // Allow only certain file formats
                if ($uploadOk && !in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $message = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
                    $message_type = 'error';
                    $uploadOk = 0;
                }

                // Try to move the uploaded file
                if ($uploadOk) {
                    if (move_uploaded_file($_FILES['stimg']['tmp_name'], $target_file)) {
                        // If upload is successful, update the $stimg variable with the new path
                        $stimg = $target_file; 
                    } else {
                        $message = 'Sorry, there was an error uploading your file.';
                        $message_type = 'error';
                        $uploadOk = 0;
                    }
                }
            }
            // --- End of Image Upload Logic ---
            
            if ($uploadOk) {
                try {
                    switch ($action) {
                        case 'insert':
                            $stmt = $conn->prepare("INSERT INTO staff (sname, spos, cid, sed, stimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
                            $stmt->bind_param("sssss", $sname, $spos, $cid, $sed, $stimg);
                            if ($stmt->execute()) { $message = 'New staff member added successfully.'; $message_type = 'success'; }
                            else { $message = 'Error adding staff: ' . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'update':
                            $stmt = $conn->prepare("UPDATE staff SET sname=?, spos=?, cid=?, sed=?, stimg=?, updated_at=NOW() WHERE stid=?");
                            $stmt->bind_param("sssssi", $sname, $spos, $cid, $sed, $stimg, $stid);
                            if ($stmt->execute()) { $message = 'Staff details updated successfully.'; $message_type = 'success'; }
                            else { $message = 'Error updating staff: ' . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'delete':
                            // Add logic here to delete the image file if it exists
                            $stmt = $conn->prepare("DELETE FROM staff WHERE stid = ?");
                            $stmt->bind_param("i", $stid);
                            if ($stmt->execute()) { $message = 'Staff member deleted successfully.'; $message_type = 'success'; }
                            else { $message = 'Error deleting staff: ' . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                        case 'activate':
                        case 'deactivate':
                           $new_status = ($action === 'activate') ? 1 : 0;
                            $stmt = $conn->prepare("UPDATE staff SET status=? WHERE stid=?");
                            $stmt->bind_param("ii", $new_status, $stid);
                            if ($stmt->execute()) { $message = 'Status updated successfully.'; $message_type = 'success'; }
                            else { $message = 'Error updating status: ' . $stmt->error; $message_type = 'error'; }
                            $stmt->close();
                            break;
                    }
                } catch (mysqli_sql_exception $e) { $message = 'Database error: ' . $e->getMessage(); $message_type = 'error'; }
            }
        } else {
            $message = 'Permission Denied: You are not authorized to perform this action.';
            $message_type = 'error';
        }
    }

    // --- Fetch Data for Display ---
    $courses = [];
    if (isset($conn)) { $course_result = $conn->query("SELECT cid, cname FROM course WHERE status = 1 ORDER BY cname ASC"); if ($course_result) $courses = $course_result->fetch_all(MYSQLI_ASSOC); }
    
    $all_staff_members = []; // Renamed to avoid conflict with categorized array
    if (isset($conn)) {
        // Query to fetch staff now depends on the user's role.
        $sql = "SELECT s.stid, s.sname, s.spos, s.cid, c.cname, s.sed, s.stimg, s.status 
                FROM staff s 
                LEFT JOIN course c ON s.cid = c.cid";
        
        // If the user is a sub_admin, only fetch staff from their department.
        if ($user_role === 'sub_admin' && !empty($user_cid)) {
            $sql .= " WHERE s.cid = ?";
        }
        $sql .= " ORDER BY s.spos ASC, s.sname ASC"; // Order by position then name

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            if ($user_role === 'sub_admin' && !empty($user_cid)) {
                $stmt->bind_param('s', $user_cid);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) $all_staff_members = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }

    // Categorize staff members
    $categorized_staff = [
        'HOD' => [],
        'SLect' => [],
        'Lect' => [],
        'Demo' => [],
        'Others' => []
    ];

    foreach ($all_staff_members as $staff) {
        switch ($staff['spos']) {
            case 'HOD':
                $categorized_staff['HOD'][] = $staff;
                break;
            case 'SLect':
                $categorized_staff['SLect'][] = $staff;
                break;
            case 'Lect':
                $categorized_staff['Lect'][] = $staff;
                break;
            case 'Demo':
                $categorized_staff['Demo'][] = $staff;
                break;
            default:
                $categorized_staff['Others'][] = $staff;
                break;
        }
    }
    ?>

    <h2>Staff Management</h2>
    <?php if (!empty($message)) : ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($user_role === 'super_admin' || $user_role === 'sub_admin'): ?>
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
                    <option value="<?php echo htmlspecialchars($course['cid']); ?>">
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
    <?php endif; ?>

    <div class="staff-list-section">
        <div class="search-container">
            <input type="text" id="staffSearchInput" placeholder="Search for staff by name, position, or department...">
        </div>

        <?php 
        $position_titles = [
            'HOD' => 'Head of Department',
            'SLect' => 'Senior Lecturers',
            'Lect' => 'Lecturers',
            'Demo' => 'Demonstrators',
            'Others' => 'Other Staff'
        ];
        $all_categories_empty = true;
        foreach ($categorized_staff as $category => $staff_list) {
            if (!empty($staff_list)) {
                $all_categories_empty = false;
                break;
            }
        }
        ?>

        <?php if (!$all_categories_empty) : ?>
            <?php foreach ($categorized_staff as $category_key => $staff_list): ?>
                <?php if (!empty($staff_list)): ?>
                    <div class="staff-category-section" id="category-<?php echo strtolower($category_key); ?>">
                        <h3><?php echo htmlspecialchars($position_titles[$category_key]); ?></h3>
                        <div class="staff-cards-grid-container">
                            <?php foreach ($staff_list as $staff) : ?>
                                <div class="staff-card">
                                    <?php if (!empty($staff['stimg']) && file_exists($staff['stimg'])) : ?>
                                        <img src="<?php echo htmlspecialchars($staff['stimg']); ?>" alt="Staff Image">
                                    <?php else : ?>
                                        <img src="uploads/staff/def.jpg" alt="Default Staff Image"> <?php endif; ?>
                                    <h4><?php echo htmlspecialchars($staff['sname']); ?></h4>
                                    <p><?php echo htmlspecialchars($staff['spos']); ?></p>
                                    <p class="department"><?php echo htmlspecialchars($staff['cname'] ?? 'N/A'); ?></p>
                                    <span class="status status-<?php echo $staff['status'] == 1 ? 'active' : 'inactive'; ?>">
                                        <?php echo $staff['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <div class="actions-card">
                                        <?php 
                                        $can_manage_record = ($user_role === 'super_admin' || ($user_role === 'sub_admin' && $staff['cid'] === $user_cid));
                                        if ($can_manage_record):
                                        ?>
                                            <button class="edit-btn" 
                                                data-stid="<?php echo htmlspecialchars($staff['stid']); ?>" 
                                                data-sname="<?php echo htmlspecialchars($staff['sname']); ?>" 
                                                data-spos="<?php echo htmlspecialchars($staff['spos']); ?>" 
                                                data-cid="<?php echo htmlspecialchars($staff['cid'] ?? ''); ?>" 
                                                data-sed="<?php echo htmlspecialchars($staff['sed']); ?>" 
                                                data-stimg="<?php echo htmlspecialchars($staff['stimg']); ?>">Edit</button>
                                            
                                            <form action="" method="POST" onsubmit="return confirm('Delete this staff member?');" style="display:inline-block;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>">
                                                <button type="submit" value="delete">Delete</button>
                                            </form>
                                            
                                            <?php if ($staff['status'] == 1) : ?>
                                                <form action="" method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>">
                                                    <button type="submit" value="deactivate">Deactivate</button>
                                                </form>
                                            <?php else : ?>
                                                <form action="" method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="action" value="activate">
                                                    <input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>">
                                                    <button type="submit" value="activate">Activate</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span>Actions Restricted</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align: center; width: 100%;">No staff members found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pass PHP role and department to JavaScript to control the form
    const userRole = '<?php echo $user_role; ?>';
    const userCid = '<?php echo $user_cid; ?>';
    const staffForm = document.getElementById('staffForm');

    if (staffForm) { // Only run if the form exists for the current user
        const actionInput = document.getElementById('action');
        const stidInput = document.getElementById('stid');
        const snameInput = document.getElementById('sname');
        const sposSelect = document.getElementById('spos');
        const sedTextarea = document.getElementById('sed');
        const existingStimgInput = document.getElementById('existing_stimg');
        const currentStimgPreview = document.getElementById('current_stimg_preview');
        const cidSelect = document.getElementById('cid');
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');

        function resetForm() {
            formTitle.textContent = 'Add New Staff';
            actionInput.value = 'insert';
            staffForm.reset();
            stidInput.value = '';
            existingStimgInput.value = '';
            currentStimgPreview.style.display = 'none';
            sedTextarea.value = '';
            
            // For sub-admins, lock the department dropdown to their own department
            if (userRole === 'sub_admin') {
                cidSelect.value = userCid;
                cidSelect.disabled = true;
            } else {
                cidSelect.disabled = false;
            }
            submitButton.textContent = 'Save Staff';
            cancelButton.style.display = 'none';
        }

        // Set the initial state of the form when the page loads
        if (userRole === 'sub_admin') {
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
                
                if (this.dataset.stimg && this.dataset.stimg !== '') {
                    currentStimgPreview.src = this.dataset.stimg;
                    currentStimgPreview.style.display = 'block';
                } else {
                    currentStimgPreview.style.display = 'none';
                }
                
                staffForm.scrollIntoView({ behavior: 'smooth' });
            });
        });

        cancelButton.addEventListener('click', resetForm);
    }

    /*--- START: Live Search Functionality for Cards ---*/
    const searchInput = document.getElementById('staffSearchInput');
    const staffCategories = document.querySelectorAll('.staff-category-section'); // Get all category sections
    
    if (searchInput && staffCategories.length > 0) {
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            let totalVisibleCards = 0;

            staffCategories.forEach(categorySection => {
                let categoryHasVisibleCards = false;
                const categoryCards = categorySection.querySelectorAll('.staff-card');
                const categoryHeading = categorySection.querySelector('h3');

                categoryCards.forEach(card => {
                    // Get text from the name, position, and department within the card
                    const name = card.querySelector('h4')?.textContent || '';
                    const position = card.querySelector('p')?.textContent || '';
                    const department = card.querySelector('.department')?.textContent || '';
                    
                    const cardText = (name + position + department).toLowerCase();

                    if (cardText.indexOf(filter) > -1) {
                        card.style.display = '';
                        totalVisibleCards++;
                        categoryHasVisibleCards = true;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide category heading based on if it has any visible cards
                if (categoryHeading) {
                    if (categoryHasVisibleCards) {
                        categoryHeading.style.display = '';
                    } else {
                        categoryHeading.style.display = 'none';
                    }
                }
            });

            // Manage the "No results found" message for the entire list
            let noResultsMessage = document.getElementById('no-search-results-cards');
            if (totalVisibleCards === 0) {
                if (!noResultsMessage) {
                    const parentContainer = document.querySelector('.staff-list-section'); // Adjust if needed
                    noResultsMessage = document.createElement('p');
                    noResultsMessage.id = 'no-search-results-cards';
                    noResultsMessage.style.textAlign = 'center';
                    noResultsMessage.style.width = '100%';
                    noResultsMessage.textContent = 'No staff members found matching your search.';
                    parentContainer.appendChild(noResultsMessage);
                }
            } else {
                if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }
        });
    }
    /*--- END: Live Search Functionality for Cards ---*/
});
</script>