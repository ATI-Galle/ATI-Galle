<?php include('include/header.php');?>

<style>
/* --- Basic Layout for Fixed Sidebar --- */
body {
    display: flex; /* Enable flexbox layout */
}

/* Assuming your sidebar has an ID or class like 'sidebar' */
/* Adjust selector and width as per your actual sidebar.php */
.sidebar { /* Or #sidebar */
    width: 250px; /* Example width */
    height: 100vh; /* Full viewport height */
    position: fixed; /* Fixed position */
    top: 0; /* Adjusted to 0 if header is part of page-container or handled by body flex */
    left: 0;
    background-color: #f8f9fa; /* Example background */
    border-right: 1px solid #dee2e6; /* Example border */
    overflow-y: auto; /* Allow sidebar scrolling if content exceeds height */
    z-index: 1000; /* Ensure sidebar stays on top */
    padding: 15px; /* Example padding */
    box-sizing: border-box;
}

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
    --font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif;
    --border-radius: 0.3rem;
    --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

body {
    /* font-family is already set above */
    font-family: var(--font-family);
    line-height: 1.6;
    margin: 0; /* Reset margin */
    background-color: #eef2f7; /* Lighter background */
    color: var(--dark-color);
    /* padding: 15px; Remove padding as flexbox handles layout */
    display: flex; /* Already set */
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
.form-section input[type=text],
.form-section input[type=number], /* Added type number for ID */
.form-section input[type=file],
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
.form-section input[readonly] { /* Corrected selector for readonly attribute */
    background-color: #e9ecef; /* Light gray background */
    cursor: not-allowed; /* Indicate non-editable */
}

/* TinyMCE styling might need adjustments inside its own config or specific CSS overrides if default doesn't fit */
.tox-tinymce {
      border: 1px solid #ccc !important; /* Add !important cautiously */
      border-radius: var(--border-radius) !important;
}


.form-section input[type=file] {
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
.form-section button:hover { /* General hover for primary button */
    background-color: #0056b3; /* Darker blue */
}
.form-section button#cancelEdit {
    background-color: var(--secondary-color);
}
.form-section button#cancelEdit:hover { /* Specific hover for cancel button */
    background-color: #5a6268;
}

/* --- Staff List Table Styling --- */
.staff-list-section { /* Changed from course-list-section */
    margin-top: 30px;
    overflow-x: auto; /* Enable horizontal scrolling on small screens */
}
.staff-table { /* Changed from course-table */
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.staff-table th, .staff-table td { /* Changed from course-table */
    border: 1px solid #e0e0e0;
    padding: 12px 15px; /* More padding */
    text-align: left;
    vertical-align: middle; /* Align content vertically */
}
.staff-table th { /* Changed from course-table */
    background-color: #f2f5f8; /* Slightly different header color */
    font-weight: 600;
    color: #333;
    white-space: nowrap; /* Prevent header text wrapping */
}
.staff-table tbody tr:nth-child(even) { /* Changed from course-table */
    background-color: var(--light-color); /* Subtle striping */
}
.staff-table tbody tr:hover {  /* Changed from course-table */
    background-color: #e9ecef; /* Hover effect */
}
.staff-table img { /* Changed from course-table */
    display: block;
    max-width: 80px; /* Larger preview */
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
}
.staff-table .actions-cell { /* Changed from course-table */
    white-space: nowrap; /* Prevent action buttons from wrapping */
      min-width: 240px; /* Ensure enough space for buttons */
}
.staff-table .actions-cell form { /* Changed from course-table */
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px; /* Add space below buttons if they wrap */
}
.staff-table .actions-cell button, /* Changed from course-table */
.staff-table .actions-cell .edit-btn { /* Style edit button similarly */ /* Changed from course-table */
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem; /* Smaller font for action buttons */
    color: white;
    transition: background-color 0.2s ease;
}
.staff-table .actions-cell .edit-btn { /* Changed from course-table */
    background-color: var(--warning-color);
    color: #333; /* Better contrast on yellow */
}
.staff-table .actions-cell .edit-btn:hover { /* Corrected hover selector for edit button */ /* Changed from course-table */
    background-color: #e0a800;
}
.staff-table .actions-cell button[value='delete'] { /* Changed from course-table */
    background-color: var(--danger-color);
}
.staff-table .actions-cell button[value='delete']:hover { /* Changed from course-table */
    background-color: #c82333;
}
.staff-table .actions-cell button[value='activate'] { /* Changed from course-table */
    background-color: var(--success-color);
}
.staff-table .actions-cell button[value='activate']:hover { /* Changed from course-table */
    background-color: #218838;
}
.staff-table .actions-cell button[value='deactivate'] { /* Changed from course-table */
    background-color: var(--secondary-color);
}
.staff-table .actions-cell button[value='deactivate']:hover { /* Changed from course-table */
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

<div class="page-container">
<?php
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
    $stid = $_POST['stid'] ?? null; // Staff ID
    $sname = $_POST['sname'] ?? ''; // Staff Name
    $spos = $_POST['spos'] ?? '';   // Staff Position
    $sed = $_POST['sed'] ?? '';     // Staff Education/Details
    $existing_stimg = $_POST['existing_stimg'] ?? ''; // Existing Staff Image
    $stimg = $existing_stimg; // Staff Image

    // --- Image Upload Handling ---
    $uploadOk = 1;
    $new_image_uploaded = false;
    $target_dir = "uploads/staff/"; // Changed directory
    $target_file = '';

    if (isset($_FILES['stimg']) && $_FILES['stimg']['error'] === UPLOAD_ERR_OK) { // Check 'stimg'
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) { // Consider 0755 for security
                $message = "Error: Failed to create upload directory.";
                $message_type = 'error';
                $uploadOk = 0;
            }
        }
        if ($uploadOk) {
            $image_name = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', basename($_FILES["stimg"]["name"]));
            $target_file = $target_dir . time() . "_" . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["stimg"]["tmp_name"]);
            if($check === false) { $message = "File is not a valid image."; $message_type = 'error'; $uploadOk = 0; }
            if ($_FILES["stimg"]["size"] > 5000000) { $message = "Sorry, image is too large (Max 5MB)."; $message_type = 'error'; $uploadOk = 0; }
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if(!in_array($imageFileType, $allowed_types)) { $message = "Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed."; $message_type = 'error'; $uploadOk = 0; }

            if ($uploadOk) {
                if (move_uploaded_file($_FILES["stimg"]["tmp_name"], $target_file)) {
                    $stimg = $target_file;
                    $new_image_uploaded = true;
                    if ($action === 'update' && !empty($existing_stimg) && $existing_stimg !== $stimg && file_exists($existing_stimg)) {
                        @unlink($existing_stimg);
                    }
                } else { $message = "Error uploading image."; $message_type = 'error'; $uploadOk = 0; }
            }
        }
        if (!$uploadOk) { $stimg = ($action === 'update') ? $existing_stimg : ''; }
    } elseif (isset($_FILES['stimg']) && $_FILES['stimg']['error'] !== UPLOAD_ERR_NO_FILE) {
        $message = "Image upload error: Code " . $_FILES['stimg']['error']; $message_type = 'error'; $uploadOk = 0;
        $stimg = ($action === 'update') ? $existing_stimg : '';
    }
    // --- End Image Handling ---

    // --- Perform Database Action ---
    if ($uploadOk || $action === 'delete' || $action === 'activate' || $action === 'deactivate' || ($action === 'update' && !$new_image_uploaded)) {
        try {
            switch ($action) {
                case 'insert':
                    if (!empty($stid) && !empty($sname) && !empty($spos)) { // Added spos check
                        $check_stmt = $conn->prepare("SELECT stid FROM staff WHERE stid = ?"); // Changed table and column
                        $check_stmt->bind_param("s", $stid);
                        $check_stmt->execute();
                        $check_stmt->store_result();

                        if ($check_stmt->num_rows > 0) {
                            $message = "Error: Staff ID '$stid' already exists. Please use a different ID.";
                            $message_type = 'error';
                        } else {
                            $status = 1; // Default status to active
                            // Added spos to INSERT query and bind_param
                            $stmt = $conn->prepare("INSERT INTO staff (stid, sname, spos, sed, stimg, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                            $stmt->bind_param("sssssi", $stid, $sname, $spos, $sed, $stimg, $status); // Added 's' for spos
                            if ($stmt->execute()) {
                                $message = "New staff member added successfully.";
                                $message_type = 'success';
                            } else {
                                $message = "Error adding staff member: " . $stmt->error;
                                $message_type = 'error';
                            }
                            $stmt->close();
                        }
                        $check_stmt->close();
                    } else {
                        $message = "Staff ID, Name, and Position cannot be empty."; // Updated message
                        $message_type = 'error';
                    }
                    break;

                case 'update':
                    if (!empty($stid) && !empty($sname) && !empty($spos)) { // Added spos check
                        // Added spos to UPDATE query and bind_param
                        $stmt = $conn->prepare("UPDATE staff SET sname=?, spos=?, sed=?, stimg=?, updated_at=NOW() WHERE stid=?");
                        $stmt->bind_param("sssss", $sname, $spos, $sed, $stimg, $stid); // Added 's' for spos
                        if ($stmt->execute()) {
                            $message = "Staff details updated successfully.";
                            $message_type = 'success';
                        } else {
                            $message = "Error updating staff details: " . $stmt->error;
                            $message_type = 'error';
                        }
                        $stmt->close();
                    } else {
                        $message = "Error: Staff ID, Name or Position missing for update."; // Updated message
                        $message_type = 'error';
                    }
                    break;

                case 'delete':
                    if (!empty($stid)) {
                        $stimg_path = ''; // Changed variable name
                        $stmt_img = $conn->prepare("SELECT stimg FROM staff WHERE stid = ?"); // Changed table, column
                        $stmt_img->bind_param("s", $stid);
                        if ($stmt_img->execute()) { $stmt_img->bind_result($stimg_path); $stmt_img->fetch(); }
                        $stmt_img->close();

                        $stmt = $conn->prepare("DELETE FROM staff WHERE stid = ?"); // Changed table, column
                        $stmt->bind_param("s", $stid);
                        if ($stmt->execute()) {
                            $message = "Staff member deleted successfully.";
                            $message_type = 'success';
                            if (!empty($stimg_path) && file_exists($stimg_path)) { @unlink($stimg_path); }
                        } else { $message = "Error deleting staff member: " . $stmt->error; $message_type = 'error'; }
                        $stmt->close();
                    } else { $message = "Error: Staff ID not provided for deletion."; $message_type = 'error'; }
                    break;

                case 'activate':
                case 'deactivate':
                    if (!empty($stid)) {
                        $new_status = ($action === 'activate') ? 1 : 0;
                        $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
                        $stmt = $conn->prepare("UPDATE staff SET status=?, updated_at=NOW() WHERE stid=?"); // Changed table, column
                        $stmt->bind_param("is", $new_status, $stid);
                        if ($stmt->execute()) { $message = "Staff member " . $action_text . " successfully."; $message_type = 'success'; }
                        else { $message = "Error " . $action_text . " staff member: " . $stmt->error; $message_type = 'error'; }
                        $stmt->close();
                    } else { $message = "Error: Staff ID not provided for status change."; $message_type = 'error'; }
                    break;
                default:
                    break;
            }
        } catch (mysqli_sql_exception $e) {
            $message = "Database error: " . $e->getMessage(); $message_type = 'error';
            error_log("Database error in staff management: " . $e->getMessage()); // Updated log message
        }
    }
}

// --- Fetch Existing Staff ---
$staff_members = []; // Changed variable name
if (isset($conn)) {
    // Added spos to SELECT query
    $sql = "SELECT stid, sname, spos, sed, stimg, status, created_at, updated_at FROM staff ORDER BY created_at DESC"; // Changed table and columns
    $result = $conn->query($sql);
    if ($result) { while($row = $result->fetch_assoc()) { $staff_members[] = $row; } } // Changed variable name
    else { $message = "Error fetching staff: " . $conn->error; $message_type = 'error'; } // Updated message
} else { $message = "DB connection not available to fetch staff."; $message_type = 'error'; } // Updated message
?>

<h2>Staff Management</h2> <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New Staff</h3> <form id="staffForm" action="" method="POST" enctype="multipart/form-data"> <input type="hidden" name="action" id="action" value="insert">

            <label for="stid">Staff ID:</label> <input type="text" id="stid" name="stid" required>

            <label for="sname">Staff Name:</label> <input type="text" id="sname" name="sname" required>

            <label for="spos">Position:</label> <input type="text" id="spos" name="spos" required>

            <label for="sed">Education/Details:</label> <textarea id="sed" name="sed"></textarea> <label for="stimg">Staff Image:</label> <input type="file" id="stimg" name="stimg" accept="image/jpeg, image/png, image/gif, image/webp"> <input type="hidden" name="existing_stimg" id="existing_stimg" value=""> <img id="current_stimg_preview" src="#" alt="Current Staff Image" style="display: none;" class="image-preview"> <div class="button-group">
                <button type="submit" id="submitButton">Save Staff</button> <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="staff-list-section"> <h3>Current Staff</h3> <table class="staff-table"> <thead>
                <tr>
                    <th>Staff ID</th> <th>Name</th>
                    <th>Position</th> <th>Education/Details Preview</th> <th>Image</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staff_members)): ?>
                    <?php foreach ($staff_members as $staff): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['stid']); ?></td>
                            <td><?php echo htmlspecialchars($staff['spos']); ?></td> 
                            <td><?php $desc = strip_tags($staff['sed']); echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : ''); ?></td> 
                            <td><?php if (!empty($staff['stimg']) && file_exists($staff['stimg'])): ?><img src="<?php echo htmlspecialchars($staff['stimg']); ?>" alt="Staff Image"><?php else: ?><span>No Image</span><?php endif; ?></td> 
                            <td><span class="status-<?php echo $staff['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $staff['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo date("Y-m-d" , strtotime($staff['created_at'])); ?></td>
                            <td><?php echo date("Y-m-d" , strtotime($staff['updated_at'])); ?></td>
                            <td class="actions-cell">
                                <button class="edit-btn"
                                        data-stid="<?php echo htmlspecialchars($staff['stid']); ?>" 
                                        data-sname="<?php echo htmlspecialchars($staff['sname']); ?>"
                                        data-spos="<?php echo htmlspecialchars($staff['spos']); ?>" 
                                        data-sed="<?php echo htmlspecialchars($staff['sed']); ?>" 
                                        data-stimg="<?php echo htmlspecialchars($staff['stimg']); ?>">Edit</button> 
                                <form action="" method="POST" onsubmit="return confirm('Delete this staff member?');" style="display:inline-block;">
                                    <input type="hidden" name="action" value="delete"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>"><button type="submit" value="delete">Delete</button></form> 
                                <?php if ($staff['status'] == 1): ?>
                                    <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="deactivate"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>"><button type="submit" value="deactivate">Deactivate</button></form> 
                                <?php else: ?>
                                    <form action="" method="POST" style="display:inline-block;"><input type="hidden" name="action" value="activate"><input type="hidden" name="stid" value="<?php echo htmlspecialchars($staff['stid']); ?>"><button type="submit" value="activate">Activate</button></form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" style="text-align: center; padding: 20px;">No staff members found.</td></tr> {/* Changed colspan to 9 and message */}
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <footer class="footer">
  <div class="d-sm-flex justify-content-center justify-content-sm-between">
    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023. Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rig . keep the design like this. </span>
  </div>
</footer>

</div> <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Get Form Elements ---
        const staffForm = document.getElementById('staffForm'); // Changed ID
        const actionInput = document.getElementById('action');
        const stidInput = document.getElementById('stid'); // Changed ID
        const snameInput = document.getElementById('sname'); // Changed ID
        const sposInput = document.getElementById('spos');   // New ID
        const sedTextarea = document.getElementById('sed'); // Changed ID
        const stimgInput = document.getElementById('stimg'); // Changed ID
        const existingStimgInput = document.getElementById('existing_stimg'); // Changed ID
        const currentStimgPreview = document.getElementById('current_stimg_preview'); // Changed ID
        const formTitle = document.getElementById('formTitle');
        const submitButton = document.getElementById('submitButton');
        const cancelButton = document.getElementById('cancelEdit');

        // Edit Button Click Handler
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const stid = this.getAttribute('data-stid'); // Changed data attribute
                const sname = this.getAttribute('data-sname'); // Changed data attribute
                const spos = this.getAttribute('data-spos');   // New data attribute
                const sed = this.getAttribute('data-sed');     // Changed data attribute
                const stimg = this.getAttribute('data-stimg'); // Changed data attribute

                formTitle.textContent = 'Edit Staff Details'; // Changed text
                actionInput.value = 'update';
                stidInput.value = stid;
                stidInput.readOnly = true;
                snameInput.value = sname;
                sposInput.value = spos; // Populate spos field
                existingStimgInput.value = stimg;

                if (typeof tinymce !== 'undefined' && tinymce.get('sed')) { tinymce.get('sed').setContent(sed || ''); } // Check for 'sed'
                else { sedTextarea.value = sed; }

                if (stimg && stimg !== '') { currentStimgPreview.src = stimg; currentStimgPreview.style.display = 'block'; }
                else { currentStimgPreview.src = '#'; currentStimgPreview.style.display = 'none'; }

                submitButton.textContent = 'Update Staff'; // Changed text
                cancelButton.style.display = 'inline-block';
                staffForm.scrollIntoView({ behavior: 'smooth', block: 'start' }); // scroll staffForm
            });
        });

        // Cancel Edit Button Click Handler
        cancelButton.addEventListener('click', function() {
            resetForm();
        });

        // Function to reset the form
        function resetForm() {
            formTitle.textContent = 'Add New Staff'; // Changed text
            actionInput.value = 'insert';
            stidInput.value = '';
            stidInput.readOnly = false;
            snameInput.value = '';
            sposInput.value = ''; // Clear spos field
            stimgInput.value = ''; // Clear the file input
            existingStimgInput.value = '';
            currentStimgPreview.src = '#';
            currentStimgPreview.style.display = 'none';

            if (typeof tinymce !== 'undefined' && tinymce.get('sed')) { tinymce.get('sed').setContent(''); } // Check for 'sed'
            else { sedTextarea.value = ''; }

            submitButton.textContent = 'Save Staff'; // Changed text
            cancelButton.style.display = 'none';
        }

        // Image Preview Handler
        stimgInput.addEventListener('change', function(event) { // Listen to stimgInput
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) { currentStimgPreview.src = e.target.result; currentStimgPreview.style.display = 'block'; }
                reader.readAsDataURL(file);
            } else {
                const existingImg = existingStimgInput.value;
                if (actionInput.value === 'update' && existingImg && existingImg !== '') { currentStimgPreview.src = existingImg; currentStimgPreview.style.display = 'block'; }
                else { currentStimgPreview.src = '#'; currentStimgPreview.style.display = 'none'; }
            }
        });
    });
</script>

