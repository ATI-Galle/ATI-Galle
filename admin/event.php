<?php

// --- Start Session & Get Admin CID ---

ini_set('display_errors', 0);
error_reporting(E_ALL);
include('include/header.php');

// Check if the admin is logged in.
if (!isset($_SESSION['cid']) || empty($_SESSION['cid'])) {
    die("<div style='font-family: sans-serif; padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
        <strong>Access Denied.</strong> You must be logged in to manage events. Please log in first.
    </div>");
}

// Store the logged-in admin's CID
$current_admin_cid = $_SESSION['cid'];

// --- MODIFICATION 1: Create a flag for Super Admin ---
// This boolean will make it easy to check for super admin privileges throughout the script.
$is_super_admin = ($current_admin_cid === 'SAdmin');
?>

<style>
/* --- STYLES ARE UNCHANGED, REMAINS THE SAME AS YOUR ORIGINAL CODE --- */
body { display: flex; margin-right: auto; }
.page-container { margin-left: 300px; margin-right:100px; padding: 20px; width: calc(100% - 400px); min-width: 800px; height:100%; box-sizing: border-box; max-width: none; margin-top: 10px; background: #fff; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border-top: 5px solid var(--primary-color); position: relative; flex-grow: 1; }
:root { --primary-color:rgb(0, 195, 255); --secondary-color: #6c757d; --success-color: #28a745; --danger-color: #dc3545; --warning-color: #ffc107; --light-color: #f8f9fa; --dark-color: #343a40; --font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, sans-serif; --border-radius: 0.3rem; --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
h2, h3 { color: var(--primary-color); margin-bottom: 1.5rem; text-align: center; font-weight: 600; }
h3 { color: var(--dark-color); margin-top: 2rem; margin-bottom: 1rem; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
.message-area { padding: 12px 18px; margin-bottom: 25px; border-radius: var(--border-radius); border: 1px solid transparent; font-size: 0.95rem; text-align: center; }
.message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.message-area.warning { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
.form-section { margin-bottom: 30px; padding: 25px; background-color: var(--light-color); border-radius: var(--border-radius); border: 1px solid #ddd; }
.form-section label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-section input[type=text], .form-section input[type=number], .form-section input[type=file], .form-section textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: var(--border-radius); box-sizing: border-box; font-size: 1rem; }
.form-section input[type=text]:disabled { background-color: #e9ecef; cursor: not-allowed; }
.tox-tinymce { border: 1px solid #ccc !important; border-radius: var(--border-radius) !important; }
.form-section input[type=file] { padding: 8px; background-color: #fff; }
.form-section .image-preview-container { margin-top: 10px; margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px; }
.form-section .image-preview-item { position: relative; display: inline-block; margin: 5px; border: 1px solid #eee; padding: 5px; background-color: #fff; border-radius: var(--border-radius); box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.form-section .image-preview-item img { max-width: 100px; max-height: 100px; display: block; object-fit: cover; border-radius: calc(var(--border-radius) - 2px); }
.form-section .delete-existing-image-btn { position: absolute; top: -8px; right: -8px; background: var(--danger-color); color: white; border: 1px solid white; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; line-height: 20px; text-align: center; cursor: pointer; padding: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
.form-section .delete-existing-image-btn:hover { background-color: #c82333; }
.form-section .delete-existing-image-btn:disabled { background-color: var(--secondary-color); cursor: not-allowed; }
.form-section .image-preview-item.marked-for-deletion img { opacity: 0.5; }
.form-section .button-group { margin-top: 15px; }
.form-section button { background-color: var(--primary-color); color: white; padding: 12px 25px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 1rem; transition: background-color 0.3s ease; margin-right: 10px; }
.form-section button:hover { background-color: #0056b3; }
.form-section button#cancelEdit { background-color: var(--secondary-color); }
.form-section button#cancelEdit:hover { background-color: #5a6268; }

/* New Card View Styles */
.event-card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.event-card {
    background-color: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border: 1px solid #e0e0e0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.event-card-header {
    background-color: var(--primary-color);
    color: white;
    padding: 15px;
    font-size: 1.2rem;
    font-weight: 600;
    word-break: break-word;
}

.event-card-body {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.event-card-body p {
    margin-bottom: 10px;
    line-height: 1.5;
}

.event-card-body strong {
    color: var(--dark-color);
}

.event-card-images {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
    min-height: 70px; /* Ensure consistent height for image area */
    align-items: center;
}

.event-card-images img {
    max-width: 60px;
    max-height: 60px;
    object-fit: cover;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
}

.event-card-status {
    margin-top: 10px;
    padding: 5px 10px;
    border-radius: var(--border-radius);
    display: inline-block;
    font-size: 0.9rem;
    font-weight: bold;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.event-card-footer {
    padding: 15px;
    background-color: var(--light-color);
    border-top: 1px solid #eee;
    display: flex;
    flex-wrap: wrap; /* Allow buttons to wrap */
    gap: 8px;
    justify-content: flex-end; /* Align buttons to the right */
}

.event-card-footer .btn {
    padding: 8px 15px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.9rem;
    color: white;
    transition: background-color 0.2s ease;
    text-decoration: none;
    display: inline-block;
}

.event-card-footer .btn-edit { background-color: var(--warning-color); color: #333; }
.event-card-footer .btn-edit:hover { background-color: #e0a800; }
.event-card-footer .btn-delete { background-color: var(--danger-color); }
.event-card-footer .btn-delete:hover { background-color: #c82333; }
.event-card-footer .btn-activate { background-color: var(--success-color); }
.event-card-footer .btn-activate:hover { background-color: #218838; }
.event-card-footer .btn-deactivate { background-color: var(--secondary-color); }
.event-card-footer .btn-deactivate:hover { background-color: #5a6268; }

.no-events-message {
    text-align: center;
    padding: 30px;
    font-size: 1.1rem;
    color: var(--secondary-color);
    background-color: var(--light-color);
    border-radius: var(--border-radius);
    margin-top: 20px;
    box-shadow: var(--box-shadow);
}

</style>

<?php include('include/sidebar.php'); ?>

<div class=page-container>
<?php
// --- Database Connection ---
include ('include/config.php');

if (isset($conn) && $conn instanceof mysqli) {
    $conn->set_charset(utf8mb4);
} else {
    echo "<div class='message-area error'>Database connection error. Please check config.php.</div>";
}

$message = '';
$message_type = '';
$upload_base_dir = 'uploads/albums/';

// --- FORM SUBMISSION HANDLING ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    // Basic form data
    $action = $_POST['action'] ?? '';
    $eid_param = $_POST['eid'] ?? null;
    $etitle = $_POST['etitle'] ?? '';
    $etag = $_POST['etag'] ?? '';
    $etext = $_POST['etext'] ?? '';
    
    // Data for image updates
    $existing_album_id = $_POST['existing_album_id'] ?? null;
    $images_to_delete = $_POST['images_to_delete'] ?? [];

    try {
        $conn->begin_transaction();

        // --- Handle Image Uploads & Album Creation (Logic is fine, no changes needed) ---
        $currentAlbumId = $existing_album_id;
        if (isset($_FILES['eimgs']) && !empty(array_filter($_FILES['eimgs']['name']))) {
            if (empty($currentAlbumId)) {
                $currentAlbumId = 'album_' . uniqid();
                $album_name = !empty($etitle) ? $etitle . ' Album' : 'Untitled Album';
                $stmt_album = $conn->prepare("INSERT INTO albums (AlbumId, album_name, created_at) VALUES (?, ?, NOW())");
                if (!$stmt_album) throw new Exception("Prepare failed (album insert): " . $conn->error);
                $stmt_album->bind_param("ss", $currentAlbumId, $album_name);
                if (!$stmt_album->execute()) throw new Exception("Failed to create album record: " . $stmt_album->error);
                $stmt_album->close();
            }
            $album_specific_dir = $upload_base_dir . $currentAlbumId . '/';
            if (!is_dir($album_specific_dir)) {
                if (!mkdir($album_specific_dir, 0777, true)) {
                    throw new Exception("Error: Failed to create album directory. Check server permissions for '{$upload_base_dir}'.");
                }
            }
            $stmt_img_insert = $conn->prepare("INSERT INTO album_images (AlbumId, image_path) VALUES (?, ?)");
            if (!$stmt_img_insert) throw new Exception("Prepare failed (image insert): " . $conn->error);
            foreach ($_FILES['eimgs']['name'] as $key => $name) {
                if ($_FILES['eimgs']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['eimgs']['tmp_name'][$key];
                    $image_file_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($name));
                    $target_file_name = time() . '_' . uniqid() . '_' . $image_file_name;
                    $target_file_path = $album_specific_dir . $target_file_name;
                    if (move_uploaded_file($tmp_name, $target_file_path)) {
                        $stmt_img_insert->bind_param("ss", $currentAlbumId, $target_file_path);
                        if (!$stmt_img_insert->execute()) {
                            error_log("Failed to insert image path {$target_file_path}: " . $stmt_img_insert->error);
                        }
                    } else {
                        throw new Exception("Error uploading image '{$name}'. Check permissions for the target directory.");
                    }
                }
            }
            $stmt_img_insert->close();
        }

        // --- Handle Deletion of Marked Images (Logic is fine, no changes needed) ---
        if ($action === 'update' && !empty($images_to_delete) && is_array($images_to_delete)) {
            $stmt_img_delete = $conn->prepare("DELETE FROM album_images WHERE image_path = ? AND AlbumId = ?");
            if (!$stmt_img_delete) throw new Exception("Prepare failed (image delete): " . $conn->error);
            foreach ($images_to_delete as $path_to_delete) {
                if (file_exists($path_to_delete)) {
                    @unlink($path_to_delete);
                }
                $stmt_img_delete->bind_param("ss", $path_to_delete, $currentAlbumId);
                $stmt_img_delete->execute();
            }
            $stmt_img_delete->close();
        }

        // --- Main Event Database Operations (Insert/Update) ---
        if ($action === 'insert') {
            if (empty($etitle)) throw new Exception("Event Title is required for a new event.");
            $check_stmt = $conn->prepare("SELECT eid FROM events WHERE eid = ?");
            $check_stmt->bind_param("s", $eid_param);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $check_stmt->close();
                throw new Exception("Event ID '{$eid_param}' already exists. Please use a different ID.");
            }
            $check_stmt->close();
            $status = 1;
            $stmt = $conn->prepare("INSERT INTO events (eid, etitle, etag, etext, AlbumId, status, created_by_cid, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            if (!$stmt) throw new Exception("Prepare failed (event insert): " . $conn->error);
            $stmt->bind_param("sssssis", $eid_param, $etitle, $etag, $etext, $currentAlbumId, $status, $current_admin_cid);
            if ($stmt->execute()) {
                $message = "New event created successfully.";
                $message_type = 'success';
            } else {
                throw new Exception("Error creating event: " . $stmt->error);
            }
            $stmt->close();

        } elseif ($action === 'update') {
            if (empty($etitle) || empty($eid_param)) throw new Exception("Event ID and Title cannot be empty for an update.");
            
            // --- MODIFICATION 2: Allow SAdmin to update ANY event ---
            // If super admin, the WHERE clause only checks for 'eid'.
            // If not, it also checks for 'created_by_cid'.
            if ($is_super_admin) {
                $stmt = $conn->prepare("UPDATE events SET etitle=?, etag=?, etext=?, AlbumId=?, updated_at=NOW() WHERE eid=?");
                if (!$stmt) throw new Exception("Prepare failed (SA event update): " . $conn->error);
                $stmt->bind_param("sssss", $etitle, $etag, $etext, $currentAlbumId, $eid_param);
            } else {
                $stmt = $conn->prepare("UPDATE events SET etitle=?, etag=?, etext=?, AlbumId=?, updated_at=NOW() WHERE eid=? AND created_by_cid=?");
                if (!$stmt) throw new Exception("Prepare failed (event update): " . $conn->error);
                $stmt->bind_param("ssssss", $etitle, $etag, $etext, $currentAlbumId, $eid_param, $current_admin_cid);
            }

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0 || !empty($_FILES['eimgs']['name'][0]) || !empty($images_to_delete)) {
                    $message = "Event updated successfully.";
                    $message_type = 'success';
                } else {
                    $message = "No changes were made. You may not have permission or the data was the same.";
                    $message_type = 'warning';
                }
            } else {
                throw new Exception("Error updating event: " . $stmt->error);
            }
            $stmt->close();
        }

        // --- Other Actions: Delete, Activate, Deactivate ---
        elseif ($action === 'delete') {
            if (empty($eid_param)) throw new Exception("Event ID not provided for deletion.");
            
            // --- MODIFICATION 3: Allow SAdmin to delete ANY event ---
            if ($is_super_admin) {
                $stmt_get_album = $conn->prepare("SELECT AlbumId FROM events WHERE eid = ?");
                $stmt_get_album->bind_param("s", $eid_param);
            } else {
                $stmt_get_album = $conn->prepare("SELECT AlbumId FROM events WHERE eid = ? AND created_by_cid = ?");
                $stmt_get_album->bind_param("ss", $eid_param, $current_admin_cid);
            }
            $stmt_get_album->execute();
            $stmt_get_album->bind_result($albumIdToDelete);
            $stmt_get_album->fetch();
            $stmt_get_album->close();

            if ($is_super_admin) {
                $stmt_delete_event = $conn->prepare("DELETE FROM events WHERE eid = ?");
                $stmt_delete_event->bind_param("s", $eid_param);
            } else {
                $stmt_delete_event = $conn->prepare("DELETE FROM events WHERE eid = ? AND created_by_cid = ?");
                $stmt_delete_event->bind_param("ss", $eid_param, $current_admin_cid);
            }
            
            if (!$stmt_delete_event->execute()) throw new Exception("Error deleting event record.");
            if ($stmt_delete_event->affected_rows === 0) throw new Exception("Permission denied or event not found.");
            $stmt_delete_event->close();
            
            if ($albumIdToDelete) {
                $album_dir_to_delete = $upload_base_dir . $albumIdToDelete;
                $stmt_get_paths = $conn->prepare("SELECT image_path FROM album_images WHERE AlbumId = ?");
                $stmt_get_paths->bind_param("s", $albumIdToDelete);
                $stmt_get_paths->execute();
                $result_paths = $stmt_get_paths->get_result();
                while ($row = $result_paths->fetch_assoc()) {
                    if (file_exists($row['image_path'])) {
                        @unlink($row['image_path']);
                    }
                }
                $stmt_get_paths->close();
                if (is_dir($album_dir_to_delete)) {
                    @rmdir($album_dir_to_delete);
                }
                $conn->prepare("DELETE FROM album_images WHERE AlbumId = ?")->execute([$albumIdToDelete]);
                $conn->prepare("DELETE FROM albums WHERE AlbumId = ?")->execute([$albumIdToDelete]);
            }
            
            $message = "Event and all associated images deleted successfully.";
            $message_type = 'success';
        }
        
        elseif ($action === 'activate' || $action === 'deactivate') {
            if (empty($eid_param)) throw new Exception("Event ID not provided for status change.");
            
            $new_status = ($action === 'activate') ? 1 : 0;
            $action_text = ($action === 'activate') ? 'activated' : 'deactivated';
            
            // --- MODIFICATION 4: Allow SAdmin to change status of ANY event ---
            if ($is_super_admin) {
                $stmt = $conn->prepare("UPDATE events SET status=?, updated_at=NOW() WHERE eid=?");
                $stmt->bind_param("is", $new_status, $eid_param);
            } else {
                $stmt = $conn->prepare("UPDATE events SET status=?, updated_at=NOW() WHERE eid=? AND created_by_cid=?");
                $stmt->bind_param("iss", $new_status, $eid_param, $current_admin_cid);
            }
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "Event " . $action_text . " successfully.";
                    $message_type = 'success';
                } else {
                    $message = "Could not change status. You may not have permission for this item.";
                    $message_type = 'warning';
                }
            } else {
                throw new Exception("Error " . $action_text . "ing event: " . $stmt->error);
            }
            $stmt->close();
        }

        $conn->commit();

    } catch (Exception $e) {
        if (isset($conn) && $conn->ping()) {
            $conn->rollback();
        }
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
        error_log("Event Management Error: " . $e->getMessage());
    }
}


// --- Fetch Existing Events for Display ---
$events = [];
if (isset($conn)) {
    // --- MODIFICATION 5: Allow SAdmin to VIEW all events ---
    // Base query parts are defined for reusability.
    $sql_select = "SELECT e.eid, e.etitle, e.etag, e.etext, e.AlbumId, e.status, e.created_by_cid, e.created_at, e.updated_at,
                    a.album_name,
                    GROUP_CONCAT(DISTINCT ai.image_path SEPARATOR '||') as album_image_paths";
    $sql_from = " FROM events e
                      LEFT JOIN albums a ON e.AlbumId = a.AlbumId
                      LEFT JOIN album_images ai ON a.AlbumId = ai.AlbumId";
    $sql_group_order = " GROUP BY e.eid ORDER BY e.created_at DESC";

    // If super admin, fetch all events. Otherwise, fetch only user-created events.
    if ($is_super_admin) {
        $sql = $sql_select . $sql_from . $sql_group_order;
        $stmt_fetch = $conn->prepare($sql);
    } else {
        $sql = $sql_select . $sql_from . " WHERE e.created_by_cid = ?" . $sql_group_order;
        $stmt_fetch = $conn->prepare($sql);
        if ($stmt_fetch) {
            $stmt_fetch->bind_param("s", $current_admin_cid);
        }
    }
    
    if ($stmt_fetch) {
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $events[] = $row;
            }
        }
        $stmt_fetch->close();
    } else {
        if(empty($message)) {
            $message = "Error preparing statement to fetch events: " . $conn->error;
            $message_type = 'error';
        }
    }
}

// Generate a new unique ID for the Add New Event form
$new_event_id = 'evt_' . uniqid();
?>

<h2>Event Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="form-section">
        <h3 id="formTitle">Add New Event</h3>
        <form id="eventForm" action="event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="insert">
            <input type="hidden" name="existing_album_id" id="existing_album_id" value="">
            <div id="images_to_delete_container"></div>
            <input type="text" id="eid" name="eid" value="<?php echo htmlspecialchars($new_event_id); ?>" required readonly>
            <label for="etitle">Event Title:</label>
            <input type="text" id="etitle" name="etitle" required>
            <label for="etag">SEO Tags (comma-separated):</label>
            <input type="text" id="etag" name="etag">
            <label for="etext">Event Description:</label>
            <textarea id="etext" name="etext" class="tinymce"></textarea>
            <div id="current-images-section" style="display:none;">
                <label>Current Images:</label>
                <div id="current_eimgs_preview_container" class="image-preview-container"></div>
            </div>
            <label for="eimgs" id="eimgs-label">Add Event Images (select multiple):</label>
            <input type="file" id="eimgs" name="eimgs[]" multiple accept="image/jpeg,image/png,image/gif,image/webp">
            <small>Max file size: 5MB. Allowed types: JPG, JPEG, PNG, GIF, WEBP.</small>
            <div class="button-group" style="margin-top: 20px;">
                <button type="submit" id="submitButton">Save Event</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </div>
        </form>
    </div>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ccc;">

    <div class="event-list-section">
        <h3><?php echo $is_super_admin ? 'All Events' : 'My Existing Events'; ?></h3>
        <div class="event-card-container">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <div class="event-card-header">
                            <?php echo htmlspecialchars($event['etitle']); ?>
                        </div>
                        <div class="event-card-body">
                            <p><strong>ID:</strong> <?php echo htmlspecialchars($event['eid']); ?></p>
                            <p><strong>Tags:</strong> <?php echo nl2br(htmlspecialchars($event['etag'])); ?></p>
                            <p><strong>Description:</strong> 
                                <?php 
                                $desc = strip_tags($event['etext']); 
                                echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : ''); 
                                ?>
                            </p>
                            <p><strong>Images:</strong></p>
                            <div class="event-card-images">
                                <?php
                                if (!empty($event['album_image_paths'])) {
                                    $image_paths = explode('||', $event['album_image_paths']);
                                    $image_paths = array_unique(array_filter($image_paths));
                                    foreach ($image_paths as $path) {
                                        if (file_exists($path)) {
                                            echo '<img src="' . htmlspecialchars($path) . '?t=' . @filemtime($path) . '" alt="Event Image">';
                                        }
                                    }
                                } else {
                                    echo '<span>No Images</span>';
                                }
                                ?>
                            </div>
                            <p><strong>Status:</strong> <span class="event-card-status status-<?php echo $event['status'] == 1 ? 'active' : 'inactive'; ?>"><?php echo $event['status'] == 1 ? 'Active' : 'Inactive'; ?></span></p>
                            <?php if ($is_super_admin): ?>
                                <p><strong>Created By:</strong> <?php echo htmlspecialchars($event['created_by_cid']); ?></p>
                            <?php endif; ?>
                            <p><strong>Created At:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($event['created_at']))); ?></p>
                            <p><strong>Updated At:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($event['updated_at']))); ?></p>
                        </div>
                        <div class="event-card-footer">
                            <button class="btn btn-edit"
                                data-eid="<?php echo htmlspecialchars($event['eid']); ?>"
                                data-etitle="<?php echo htmlspecialchars($event['etitle']); ?>"
                                data-etag="<?php echo htmlspecialchars($event['etag']); ?>"
                                data-etext="<?php echo htmlspecialchars($event['etext']); ?>"
                                data-albumid="<?php echo htmlspecialchars($event['AlbumId'] ?? ''); ?>"
                                data-images="<?php echo htmlspecialchars($event['album_image_paths'] ?? ''); ?>"
                                type="button">Edit</button>
                            <form action="event.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="eid" value="<?php echo htmlspecialchars($event['eid']); ?>">
                                <button type="submit" name="action" value="delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this event? This will also delete all associated images and cannot be undone.');">Delete</button>
                            </form>
                            <form action="event.php" method="POST" style="display: inline-block;">
                                <input type="hidden" name="eid" value="<?php echo htmlspecialchars($event['eid']); ?>">
                                <?php if ($event['status'] == 1): ?>
                                    <button type="submit" name="action" value="deactivate" class="btn btn-deactivate">Deactivate</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="activate" class="btn btn-activate">Activate</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-events-message">
                    You have not created any events yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div> 

<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // YOUR JAVASCRIPT IS UNCHANGED
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'code lists link image media table wordcount fullscreen preview searchreplace help',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code | fullscreen preview | searchreplace | help',
        height: 300,
        menubar: 'file edit view insert format tools table help'
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('eventForm');
        const formTitle = document.getElementById('formTitle');
        const formActionInput = document.getElementById('formAction');
        const existingAlbumIdInput = document.getElementById('existing_album_id');
        const eidInput = document.getElementById('eid');
        const etitleInput = document.getElementById('etitle');
        const etagInput = document.getElementById('etag');
        const submitButton = document.getElementById('submitButton');
        const cancelEditButton = document.getElementById('cancelEdit');
        const currentImagesPreviewContainer = document.getElementById('current_eimgs_preview_container');
        const imagesToDeleteContainer = document.getElementById('images_to_delete_container');
        const newImagesInput = document.getElementById('eimgs');
        const newImagesLabel = document.getElementById('eimgs-label');
        const currentImagesSection = document.getElementById('current-images-section');

        function resetFormToDefaults() {
            form.reset();
            formTitle.textContent = 'Add New Event';
            formActionInput.value = 'insert';
            existingAlbumIdInput.value = '';
            eidInput.value = 'evt_' + Date.now().toString(36) + Math.random().toString(36).substring(2);
            eidInput.readOnly = false;
            etitleInput.value = '';
            etagInput.value = '';
            if (tinymce.get('etext')) {
                tinymce.get('etext').setContent('');
            }
            currentImagesPreviewContainer.innerHTML = '';
            imagesToDeleteContainer.innerHTML = '';
            newImagesInput.value = '';
            currentImagesSection.style.display = 'none';
            newImagesLabel.textContent = 'Add Event Images (select multiple):';
            cancelEditButton.style.display = 'none';
            submitButton.textContent = 'Save Event';
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
        }
        cancelEditButton.addEventListener('click', function() {
            resetFormToDefaults();
        });
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const eventData = this.dataset;
                formTitle.textContent = 'Edit Event: ' + eventData.etitle;
                formActionInput.value = 'update';
                existingAlbumIdInput.value = eventData.albumid || '';
                eidInput.value = eventData.eid;
                eidInput.readOnly = true;
                etitleInput.value = eventData.etitle;
                etagInput.value = eventData.etag;
                if (tinymce.get('etext')) {
                    tinymce.get('etext').setContent(eventData.etext || '');
                } else {
                    document.getElementById('etext').value = eventData.etext || '';
                }
                submitButton.textContent = 'Update Event';
                cancelEditButton.style.display = 'inline-block';
                newImagesLabel.textContent = 'Add/Replace Event Images:';
                currentImagesPreviewContainer.innerHTML = '';
                imagesToDeleteContainer.innerHTML = '';
                newImagesInput.value = '';
                if (eventData.images) {
                    const imagePaths = eventData.images.split('||').filter(path => path.trim() !== '');
                    if (imagePaths.length > 0) {
                        currentImagesSection.style.display = 'block';
                        imagePaths.forEach(path => {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'image-preview-item';
                            const img = document.createElement('img');
                            img.src = path + '?t=' + new Date().getTime();
                            img.alt = 'Existing image';
                            img.className = 'image-preview';
                            const deleteBtn = document.createElement('button');
                            deleteBtn.type = 'button';
                            deleteBtn.className = 'delete-existing-image-btn';
                            deleteBtn.innerHTML = '&times;';
                            deleteBtn.title = 'Mark for deletion';
                            deleteBtn.addEventListener('click', function() {
                                previewItem.classList.add('marked-for-deletion');
                                this.disabled = true;
                                this.textContent = '✓';
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'images_to_delete[]';
                                hiddenInput.value = path;
                                imagesToDeleteContainer.appendChild(hiddenInput);
                            });
                            previewItem.appendChild(img);
                            previewItem.appendChild(deleteBtn);
                            currentImagesPreviewContainer.appendChild(previewItem);
                        });
                    } else {
                        currentImagesSection.style.display = 'none';
                    }
                } else {
                    currentImagesSection.style.display = 'none';
                }
                window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
            });
        });
    });
</script>

<script src="https://cdn.tiny.cloud/1/0b4l260nbwgikhaerenongs5zgl39j7pja3yimxlbjkkfrs6/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<script>
  tinymce.init({
    selector: 'textarea',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until Oct 17, 2025:
      'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    uploadcare_public_key: 'd785658a904451b6b50a',
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