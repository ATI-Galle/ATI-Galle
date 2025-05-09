<?php
include('include/header.php'); // Assuming your header.php sets up necessary things
?>

<style>
/* --- Basic Layout for Fixed Sidebar --- */
body {
    display: flex; /* Enable flexbox layout */
    margin-right: auto;
}

/* Assuming your sidebar has an ID or class like 'sidebar' */
/* Adjust selector and width as per your actual sidebar.php */

/* Adjust main content area to account for the fixed sidebar */
.page-container {
    margin-left: 300px; /* Adjust if your sidebar width is different */
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px); /* Adjust based on sidebar and right margin */
    min-width: 800px; /* Minimum width for content */
    height:100%;
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px; /* Adjusted from 0 to 10 */
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
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
.form-section .image-preview-container {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
/* Styling for individual image preview item in the form */
.form-section .image-preview-item {
    position: relative;
    display: inline-block; /* Allows items to sit side-by-side and wrap */
    margin: 5px; /* Spacing around items */
    border: 1px solid #eee;
    padding: 5px;
    background-color: #fff;
    border-radius: var(--border-radius);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.form-section .image-preview { /* This is the <img> tag itself */
    max-width: 100px;
    max-height: 100px;
    display: block; /* remove extra space below image */
    object-fit: cover;
}
.form-section .delete-existing-image-btn {
    position: absolute;
    top: -8px; /* Adjust to position nicely */
    right: -8px; /* Adjust to position nicely */
    background: var(--danger-color);
    color: white;
    border: 1px solid white;
    border-radius: 50%; /* Circular */
    width: 22px;
    height: 22px;
    font-size: 12px;
    line-height: 20px; /* Center the 'X' or symbol */
    text-align: center;
    cursor: pointer;
    padding: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.form-section .delete-existing-image-btn:hover {
    background-color: #c82333; /* Darker red on hover */
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

.event-list-section { margin-top: 30px; overflow-x: auto; }
.event-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.event-table th, .event-table td {
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
    text-align: left;
    vertical-align: middle;
}
.event-table th {
    background-color: #f2f5f8;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
}
.event-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.event-table tbody tr:hover { background-color: #e9ecef; }
.event-table .album-images-cell img {
    max-width: 60px; /* Smaller for table view */
    max-height: 60px;
    height: auto;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    margin: 2px;
    object-fit: cover;
}
.event-table .album-images-cell {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    min-width: 150px; /* Ensure some space for images */
}
.event-table .actions-cell { white-space: nowrap; min-width: 240px; }
.event-table .actions-cell form { display: inline-block; margin-right: 5px; margin-bottom: 5px; }
.event-table .actions-cell button,
.event-table .actions-cell .edit-btn {
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem;
    color: white;
    transition: background-color 0.2s ease;
}
.event-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.event-table .actions-cell .edit-btn:hover { background-color: #e0a800; }
.event-table .actions-cell button[name='action'][value='delete'] { background-color: var(--danger-color); }
.event-table .actions-cell button[name='action'][value='delete']:hover { background-color: #c82333; }
.event-table .actions-cell button[name='action'][value='activate'] { background-color: var(--success-color); }
.event-table .actions-cell button[name='action'][value='activate']:hover { background-color: #218838; }
.event-table .actions-cell button[name='action'][value='deactivate'] { background-color: var(--secondary-color); }
.event-table .actions-cell button[name='action'][value='deactivate']:hover { background-color: #5a6268; }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }
</style>

<?php include('include/sidebar.php'); // Assuming your sidebar.php ?>

<div class="page-container">
<?php
// --- Database Connection ---
include ('include/config.php'); // Your database connection file

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset("utf8mb4");
} else {
    error_log("Database connection (\$conn) not properly initialized in config.php");
    // exit("Database connection error."); // Optionally stop execution
}

$message = '';
$message_type = '';
$upload_base_dir = "uploads/albums/"; // Make sure this directory is writable by the server

// Function to recursively delete a directory
function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        return; // Not a directory, so nothing to do
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK); // GLOB_MARK adds a slash to directories
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file); // Recursively delete subdirectories
        } else {
            @unlink($file); // Delete files
        }
    }
    @rmdir($dirPath); // Delete the now-empty directory
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';
    $eid_param = $_POST['eid'] ?? null; // Renamed to avoid conflict with $eid variable inside loops/functions
    $etitle = $_POST['etitle'] ?? '';
    $etag = $_POST['etag'] ?? ''; // SEO Tags
    $etext = $_POST['etext'] ?? '';
    
    $client_provided_existing_album_id = $_POST['existing_album_id'] ?? null;
    $currentAlbumId = null; 

    if ($action === 'update' && !empty($client_provided_existing_album_id)) {
        $currentAlbumId = $client_provided_existing_album_id;
    }

    $new_images_uploaded = false; 

    try {
        $conn->begin_transaction(); // Start transaction for operations involving multiple queries

        if ($action === 'insert' || $action === 'update') {
            if (empty($eid_param) && $action === 'insert') {
                throw new Exception("Event ID cannot be empty for new event.");
            } elseif (empty($etitle)) {
                throw new Exception("Event Title cannot be empty.");
            }

            // 1. Handle Deletion of Existing Images (Only for Update action)
            if ($action === 'update' && !empty($currentAlbumId)) {
                $images_to_delete = $_POST['images_to_delete'] ?? [];
                if (!empty($images_to_delete)) {
                    $stmt_delete_single_img_record = $conn->prepare("DELETE FROM album_images WHERE AlbumId = ? AND image_path = ?");
                    foreach ($images_to_delete as $imgPathToDelete) {
                        // Sanitize or validate imgPathToDelete if necessary
                        $fullPathToDelete = $imgPathToDelete; 

                        $stmt_delete_single_img_record->bind_param("ss", $currentAlbumId, $fullPathToDelete);
                        if ($stmt_delete_single_img_record->execute()) {
                            if (file_exists($fullPathToDelete)) {
                                if (!@unlink($fullPathToDelete)) {
                                    error_log("Failed to delete physical image file: " . $fullPathToDelete);
                                    $message .= " Warning: Could not delete file " . basename($fullPathToDelete) . ".";
                                    $message_type = 'warning';
                                }
                            }
                        } else {
                            error_log("Failed to delete image record from DB: " . $fullPathToDelete . " for AlbumId: " . $currentAlbumId . " - " . $stmt_delete_single_img_record->error);
                        }
                    }
                    $stmt_delete_single_img_record->close();
                }
            }

            // 2. Handle Upload of New Images
            if (isset($_FILES['eimgs']) && !empty(array_filter($_FILES['eimgs']['name']))) {
                $new_images_uploaded = true;

                if (empty($currentAlbumId)) { 
                    $currentAlbumId = uniqid('album_');
                    $album_name_for_db = !empty($etitle) ? $etitle . " Album" : "Event Album (" . $currentAlbumId . ")";
                    $stmt_album = $conn->prepare("INSERT INTO album (AlbumId, album_name, created_at) VALUES (?, ?, NOW())");
                    $stmt_album->bind_param("ss", $currentAlbumId, $album_name_for_db);
                    if (!$stmt_album->execute()) {
                        throw new Exception("Error creating album record: " . $stmt_album->error);
                    }
                    $stmt_album->close();
                } elseif ($action === 'update' && !empty($currentAlbumId)) {
                    // Optionally update album_name if title changed
                    $album_name_for_db = !empty($etitle) ? $etitle . " Album" : "Event Album (" . $currentAlbumId . ")";
                    $stmt_update_album_name = $conn->prepare("UPDATE album SET album_name = ? WHERE AlbumId = ?");
                    $stmt_update_album_name->bind_param("ss", $album_name_for_db, $currentAlbumId);
                    $stmt_update_album_name->execute();
                    $stmt_update_album_name->close();
                }
                
                $album_specific_dir = $upload_base_dir . $currentAlbumId . "/";
                if (!is_dir($album_specific_dir)) {
                    if (!mkdir($album_specific_dir, 0777, true)) {
                        throw new Exception("Error: Failed to create album upload directory: " . $album_specific_dir);
                    }
                }

                $image_paths_for_db = [];
                foreach ($_FILES['eimgs']['name'] as $key => $name) {
                    if ($_FILES['eimgs']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['eimgs']['tmp_name'][$key];
                        $image_file_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($name));
                        $target_file_name = time() . "_" . uniqid() . "_" . $image_file_name;
                        $target_file = $album_specific_dir . $target_file_name;
                        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                        $check = getimagesize($tmp_name);
                        if($check === false) { throw new Exception("File '$name' is not a valid image."); }
                        if ($_FILES["eimgs"]["size"][$key] > 5000000) { throw new Exception("Sorry, image '$name' is too large (Max 5MB)."); }
                        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if(!in_array($imageFileType, $allowed_types)) { throw new Exception("Sorry, only JPG, JPEG, PNG, GIF, WEBP allowed for '$name'. Was: $imageFileType"); }

                        if (move_uploaded_file($tmp_name, $target_file)) {
                            $image_paths_for_db[] = $target_file; 
                        } else {
                            throw new Exception("Error uploading image '$name'. Check permissions for " . $album_specific_dir);
                        }
                    } elseif ($_FILES['eimgs']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                        throw new Exception("Image upload error for file '$name': Code " . $_FILES['eimgs']['error'][$key]);
                    }
                }

                if (!empty($image_paths_for_db)) {
                    $stmt_img_insert = $conn->prepare("INSERT INTO album_images (AlbumId, image_path) VALUES (?, ?)");
                    foreach ($image_paths_for_db as $path) {
                        $stmt_img_insert->bind_param("ss", $currentAlbumId, $path);
                        if(!$stmt_img_insert->execute()){
                            error_log("Error inserting image path $path: " . $stmt_img_insert->error);
                            // Decide if this should throw an exception or just be a warning
                        }
                    }
                    $stmt_img_insert->close();
                }
            } 

            // 3. Optional: Cleanup empty album (if all images removed and no new ones added during an update)
            if ($action === 'update' && !empty($client_provided_existing_album_id) && !$new_images_uploaded) {
                $stmt_count_remaining = $conn->prepare("SELECT COUNT(*) FROM album_images WHERE AlbumId = ?");
                $stmt_count_remaining->bind_param("s", $client_provided_existing_album_id);
                $stmt_count_remaining->execute();
                $stmt_count_remaining->bind_result($remaining_images_count);
                $stmt_count_remaining->fetch();
                $stmt_count_remaining->close();

                if ($remaining_images_count === 0) {
                    $album_dir_to_delete = $upload_base_dir . $client_provided_existing_album_id . "/";
                    deleteDir($album_dir_to_delete); 

                    // Delete from album table. If ON DELETE CASCADE is set up for album_images.AlbumId -> album.AlbumId,
                    // corresponding album_images records will be deleted automatically by the DB.
                    $stmt_del_alb = $conn->prepare("DELETE FROM album WHERE AlbumId = ?");
                    $stmt_del_alb->bind_param("s", $client_provided_existing_album_id);
                    $stmt_del_alb->execute();
                    $stmt_del_alb->close();
                    
                    // If ON DELETE CASCADE is NOT set up, you'd need this:
                    // $stmt_del_album_images = $conn->prepare("DELETE FROM album_images WHERE AlbumId = ?");
                    // $stmt_del_album_images->bind_param("s", $client_provided_existing_album_id);
                    // $stmt_del_album_images->execute();
                    // $stmt_del_album_images->close();
                    
                    if ($currentAlbumId === $client_provided_existing_album_id) {
                         $currentAlbumId = null; // Album is gone, so event should no longer link to it
                    }
                    $message .= ($message ? " " : "") . "Album (ID: " . htmlspecialchars($client_provided_existing_album_id) . ") was emptied and removed.";
                    if(empty($message_type) || $message_type == 'success') $message_type = 'success';
                }
            }
            
            // --- Database Operations for Event (Insert/Update) ---
            if ($action === 'insert') {
                $check_stmt = $conn->prepare("SELECT eid FROM event WHERE eid = ?");
                $check_stmt->bind_param("s", $eid_param);
                $check_stmt->execute();
                $check_stmt->store_result();

                if ($check_stmt->num_rows > 0) {
                    throw new Exception("Event ID '$eid_param' already exists. Please use a different ID.");
                } else {
                    $status = 1; 
                    $stmt = $conn->prepare("INSERT INTO event (eid, etitle, etag, etext, AlbumId, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    $stmt->bind_param("sssssi", $eid_param, $etitle, $etag, $etext, $currentAlbumId, $status);
                    if ($stmt->execute()) {
                        $message = "New event created successfully." . ($message ? " " . $message : "");
                        $message_type = ($message_type == 'warning' ? 'warning' : 'success');
                    } else {
                        throw new Exception("Error creating event: " . $stmt->error);
                    }
                    $stmt->close();
                }
                $check_stmt->close();
            } elseif ($action === 'update') {
                $stmt = $conn->prepare("UPDATE event SET etitle=?, etag=?, etext=?, AlbumId=?, updated_at=NOW() WHERE eid=?");
                $stmt->bind_param("sssss", $etitle, $etag, $etext, $currentAlbumId, $eid_param);
                if ($stmt->execute()) {
                    $message = "Event updated successfully." . ($message ? " " . $message : "");
                    $message_type = ($message_type == 'warning' ? 'warning' : 'success');
                } else {
                    throw new Exception("Error updating event: " . $stmt->error);
                }
                $stmt->close();
            }
            $conn->commit(); // Commit transaction if all successful

        } elseif ($action === 'delete') {
            if (!empty($eid_param)) {
                $albumIdToDelete = null;
                $stmt_get_album = $conn->prepare("SELECT AlbumId FROM event WHERE eid = ?");
                $stmt_get_album->bind_param("s", $eid_param);
                $stmt_get_album->execute();
                $stmt_get_album->bind_result($albumIdToDelete);
                $stmt_get_album->fetch();
                $stmt_get_album->close();

                // First, delete the event record.
                $stmt_delete_event = $conn->prepare("DELETE FROM event WHERE eid = ?");
                $stmt_delete_event->bind_param("s", $eid_param);
                
                if ($stmt_delete_event->execute()) {
                    if ($albumIdToDelete) {
                        // Album exists, proceed to delete its files and DB records.
                        $album_dir_to_delete = $upload_base_dir . $albumIdToDelete . "/";

                        // Fetch paths to delete physical files BEFORE deleting DB records for them
                        $img_paths_to_delete_sql = "SELECT image_path FROM album_images WHERE AlbumId = ?";
                        $stmt_paths = $conn->prepare($img_paths_to_delete_sql);
                        $stmt_paths->bind_param("s", $albumIdToDelete);
                        $stmt_paths->execute();
                        $result_paths = $stmt_paths->get_result();
                        while($row_path = $result_paths->fetch_assoc()){
                            if(!empty($row_path['image_path']) && file_exists($row_path['image_path'])) {
                                @unlink($row_path['image_path']);
                            }
                        }
                        $stmt_paths->close();
                        
                        // Recursively delete the album directory and its contents
                        deleteDir($album_dir_to_delete); 

                        // Now, delete the main album record from the 'album' table.
                        // If you set up `ON DELETE CASCADE` for the foreign key from `album_images.AlbumId`
                        // to `album.AlbumId`, the database will automatically delete all related
                        // records from the `album_images` table.
                        $stmt_delete_album = $conn->prepare("DELETE FROM album WHERE AlbumId = ?");
                        $stmt_delete_album->bind_param("s", $albumIdToDelete);
                        $stmt_delete_album->execute();
                        $stmt_delete_album->close();

                        // If `ON DELETE CASCADE` is NOT set on `album_images.AlbumId` -> `album.AlbumId`,
                        // you would need to manually delete from `album_images` first:
                        // $stmt_delete_imgs = $conn->prepare("DELETE FROM album_images WHERE AlbumId = ?");
                        // $stmt_delete_imgs->bind_param("s", $albumIdToDelete);
                        // $stmt_delete_imgs->execute();
                        // $stmt_delete_imgs->close();
                    }
                    $conn->commit(); // Commit transaction
                    $message = "Event and associated album/images deleted successfully.";
                    $message_type = 'success';
                } else {
                    $conn->rollback(); // Rollback on event deletion failure
                    throw new Exception("Error deleting event: " . $stmt_delete_event->error);
                }
                $stmt_delete_event->close();
            } else {
                throw new Exception("Event ID not provided for deletion.");
            }
        } elseif ($action === 'activate' || $action === 'deactivate') {
             if (!empty($eid_param)) {
                $new_status = ($action === 'activate') ? 1 : 0;
                $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
                $stmt = $conn->prepare("UPDATE event SET status=?, updated_at=NOW() WHERE eid=?");
                $stmt->bind_param("is", $new_status, $eid_param);
                if ($stmt->execute()) { 
                    $conn->commit(); // Commit transaction
                    $message = "Event " . $action_text . " successfully."; $message_type = 'success'; 
                }
                else { throw new Exception("Error " . $action_text . " event: " . $stmt->error); } // Will be caught, and rollback
                $stmt->close();
            } else { throw new Exception("Event ID not provided for status change.");} // Will be caught
        } else {
             // No action that requires transaction or specific handling, so commit if transaction was started (e.g. GET request)
             if ($conn->in_transaction) { // Check if in_transaction property exists (PHP 8+) or use $conn->get_transaction_status() for older
                $conn->commit();
             }
        }

    } catch (Exception $e) {
        if ($conn->in_transaction ?? false) { // Check if in_transaction property exists
             $conn->rollback(); 
        }
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
        error_log("Event Management Error: " . $e->getMessage() . "\nPOST data: " . print_r($_POST, true) . "\nFILES data: " . print_r($_FILES, true));
    }
}

// --- Fetch Existing Events ---
$events = [];
if (isset($conn)) {
    // Fetch events and their album images
    // Using GROUP_CONCAT to get all image paths for an album in one go
    $sql = "SELECT e.eid, e.etitle, e.etag, e.etext, e.AlbumId, e.status, e.created_at, e.updated_at,
            GROUP_CONCAT(DISTINCT ai.image_path SEPARATOR '||') as album_image_paths,
            a.album_name
            FROM event e
            LEFT JOIN album a ON e.AlbumId = a.AlbumId
            LEFT JOIN album_images ai ON a.AlbumId = ai.AlbumId 
            GROUP BY e.eid, a.AlbumId /* Include all non-aggregated columns from SELECT in GROUP BY */
            ORDER BY e.created_at DESC";
    $result = $conn->query($sql);
    if ($result) { 
        while($row = $result->fetch_assoc()) { 
            $events[] = $row; 
        } 
    }
    else { $message = "Error fetching events: " . $conn->error; $message_type = 'error'; }
} else { 
    if (empty($message)) { // Avoid overwriting POST handling messages
        $message = "DB connection not available to fetch events."; $message_type = 'error'; 
    }
}
?>

<h2>Event Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New Event</h3>
        <form id="eventForm" action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="insert">
            <input type="hidden" name="existing_album_id" id="existing_album_id" value="">
            
            <div id="images_to_delete_container"></div>

            <label for="eid">Event ID:</label>
            <input type="text" id="eid" name="eid" required>

            <label for="etitle">Event Title:</label>
            <input type="text" id="etitle" name="etitle" required>

            <label for="etag">SEO Tags (comma-separated):</label>
            <input type="text" id="etag" name="etag">

            <label for="etext">Event Description:</label>
            <textarea id="etext" name="etext" class="tinymce"></textarea>

            <label>Current Images (if any):</label>
            <div id="current_eimgs_preview_container" class="image-preview-container">
                </div>

            <label for="eimgs">Add/Replace Event Images (select multiple):</label>
            <input type="file" id="eimgs" name="eimgs[]" multiple accept="image/jpeg,image/png,image/gif,image/webp">
            
            <div class="button-group">
                <button type="submit" id="submitButton">Save Event</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="event-list-section">
        <h3>Existing Events</h3>
        <table class="event-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Tags</th>
                    <th>Description Preview</th>
                    <th>Images from Album: <em style="font-size:0.8em; color: #555;">(<?php echo htmlspecialchars($event['album_name'] ?? 'No Album'); ?>)</em></th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['eid']); ?></td>
                            <td><?php echo htmlspecialchars($event['etitle']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($event['etag'])); ?></td>
                            <td><?php $desc = strip_tags($event['etext']); echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : ''); ?></td>
                            <td class="album-images-cell">
                                <?php
                                if (!empty($event['album_image_paths'])) {
                                    $image_paths = explode('||', $event['album_image_paths']);
                                    $image_paths = array_unique(array_filter($image_paths)); // Ensure unique and non-empty paths
                                    foreach ($image_paths as $path) {
                                        if (file_exists($path)) { // Check if file still exists
                                            echo '<img src="' . htmlspecialchars($path) . '?t=' . filemtime($path) . '" alt="Event Image from album ' . htmlspecialchars($event['album_name'] ?? $event['AlbumId']) . '">';
                                        } else {
                                            // Optionally display a placeholder or message for missing files
                                            // echo '<img src="path/to/placeholder.png" alt="Image not found">';
                                        }
                                    }
                                } else {
                                    echo '<span>No Images</span>';
                                }
                                ?>
                            </td>
                            <td><span class="status-<?php echo $event['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $event['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($event['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($event['updated_at']))); ?></td>
                            <td class="actions-cell">
                                <button class="edit-btn"
                                        data-eid="<?php echo htmlspecialchars($event['eid']); ?>"
                                        data-etitle="<?php echo htmlspecialchars($event['etitle']); ?>"
                                        data-etag="<?php echo htmlspecialchars($event['etag']); ?>"
                                        data-etext="<?php echo htmlspecialchars($event['etext']); ?>"
                                        data-albumid="<?php echo htmlspecialchars($event['AlbumId'] ?? ''); ?>"
                                        data-albumimages="<?php echo htmlspecialchars($event['album_image_paths'] ?? ''); ?>"
                                        data-albumname="<?php echo htmlspecialchars($event['album_name'] ?? ''); ?>">
                                    Edit
                                </button>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="eid" value="<?php echo htmlspecialchars($event['eid']); ?>">
                                    <input type="hidden" name="action" value="<?php echo $event['status'] == 1 ? 'deactivate' : 'activate'; ?>">
                                    <button type="submit"><?php echo $event['status'] == 1 ? 'Deactivate' : 'Activate'; ?></button>
                                </form>
                                <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event and its album? This cannot be undone.');">
                                    <input type="hidden" name="eid" value="<?php echo htmlspecialchars($event['eid']); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> 

<script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> 



<script>
tinymce.init({
    selector: '.tinymce',
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    toolbar_mode: 'floating',
    height: 300,
    // Add any other configurations you need
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    const actionInput = document.getElementById('action');
    const formTitle = document.getElementById('formTitle');
    const submitButton = document.getElementById('submitButton');
    const cancelEditButton = document.getElementById('cancelEdit');
    const eidInput = document.getElementById('eid');
    const etitleInput = document.getElementById('etitle');
    const etagInput = document.getElementById('etag');
    const currentImagesPreviewContainer = document.getElementById('current_eimgs_preview_container');
    const imagesToDeleteContainer = document.getElementById('images_to_delete_container');
    const existingAlbumIdInput = document.getElementById('existing_album_id');
    const newImagesInput = document.getElementById('eimgs');


    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            formTitle.textContent = 'Edit Event';
            actionInput.value = 'update';
            submitButton.textContent = 'Update Event';
            cancelEditButton.style.display = 'inline-block';

            eidInput.value = this.dataset.eid;
            eidInput.readOnly = true; // Prevent editing ID during update
            etitleInput.value = this.dataset.etitle;
            etagInput.value = this.dataset.etag;
            tinymce.get('etext').setContent(this.dataset.etext);
            existingAlbumIdInput.value = this.dataset.albumid || '';

            currentImagesPreviewContainer.innerHTML = ''; // Clear previous previews
            imagesToDeleteContainer.innerHTML = ''; // Clear previous images marked for deletion

            const albumImages = this.dataset.albumimages ? this.dataset.albumimages.split('||') : [];
            albumImages.forEach(imgPath => {
                if (imgPath) {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'image-preview-item';
                    
                    const img = document.createElement('img');
                    img.src = imgPath + '?t=' + new Date().getTime(); // Cache bust
                    img.className = 'image-preview';
                    img.alt = 'Current Image';
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'delete-existing-image-btn';
                    deleteBtn.innerHTML = '&times;'; // 'X' symbol
                    deleteBtn.title = 'Mark to delete this image';

                    deleteBtn.onclick = function() {
                        // Add a hidden input to mark this image for deletion
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'images_to_delete[]';
                        hiddenInput.value = imgPath;
                        imagesToDeleteContainer.appendChild(hiddenInput);
                        
                        // Visually remove or strike-through the image preview
                        itemDiv.style.opacity = '0.5';
                        deleteBtn.disabled = true;
                        deleteBtn.textContent = 'RDY'; // Marked
                         // itemDiv.remove(); // Or just remove it
                    };
                    
                    itemDiv.appendChild(img);
                    itemDiv.appendChild(deleteBtn);
                    currentImagesPreviewContainer.appendChild(itemDiv);
                }
            });
            newImagesInput.value = ''; // Clear the file input
            window.scrollTo(0, 0); // Scroll to top of page
        });
    });

    cancelEditButton.addEventListener('click', function() {
        formTitle.textContent = 'Add New Event';
        actionInput.value = 'insert';
        submitButton.textContent = 'Save Event';
        this.style.display = 'none';
        eidInput.readOnly = false;
        form.reset();
        tinymce.get('etext').setContent('');
        currentImagesPreviewContainer.innerHTML = '';
        imagesToDeleteContainer.innerHTML = '';
        existingAlbumIdInput.value = '';
        newImagesInput.value = ''; // Clear the file input
    });
});
</script>
<?php
// include('include/footer.php'); // If you have a footer
?>