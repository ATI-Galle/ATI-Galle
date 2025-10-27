<?php
// session_start(); // Uncomment if you use sessions for admin authentication
error_reporting(E_ALL); // Recommended for development
ini_set('display_errors', 1); // Recommended for development

include('include/header.php'); // Ensure this path is correct
?>

<style>
/* --- Basic Layout for Fixed Sidebar (using your provided styles) --- */
body {
    display: flex; 
    margin-right: auto;
}
.page-container {
    margin-left: 300px; 
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px); 
    min-width: 800px; 
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px;
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
    margin-bottom: 30px; 
}
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
.form-section select,
.form-section textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: var(--border-radius);
    box-sizing: border-box;
    font-size: 1rem;
}
.form-section .image-preview-container {
    margin-top: 10px;
    margin-bottom: 15px;
}
.form-section .image-preview-item img {
    max-width: 150px;
    max-height: 150px;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
}
.form-section button {
    background-color: var(--primary-color);
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 1rem;
    margin-right: 10px;
}
.form-section button#cancelPointEditBtn { background-color: var(--secondary-color); }
.announcements-list-section { margin-top: 30px; overflow-x: auto; }
.announcements-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    box-shadow: 0 1px 5px rgba(0,0,0,0.08);
}
.announcements-table th, .announcements-table td {
    border: 1px solid #e0e0e0;
    padding: 10px 12px;
    text-align: left;
    vertical-align: middle;
}
.announcements-table th {
    background-color: #f2f5f8;
    font-weight: 600;
    color: #333;
}
.announcements-table .actions-cell { white-space: nowrap; }
.announcements-table .actions-cell form { display: inline-block; margin-right: 5px; }
.announcements-table .actions-cell button,
.announcements-table .actions-cell .edit-btn {
    padding: 6px 10px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.8rem;
    color: white;
    text-decoration: none;
    display: inline-block;
}
.announcements-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.announcements-table .actions-cell button[value='delete'] { background-color: var(--danger-color); }
.announcements-table .actions-cell button[value='activate'] { background-color: var(--success-color); }
.announcements-table .actions-cell button[value='deactivate'] { background-color: var(--secondary-color); }
.status-active { color: var(--success-color); font-weight: bold; }
.status-inactive { color: var(--secondary-color); font-weight: bold; }
.two-column-layout { display: flex; gap: 20px; }
.two-column-layout > div { flex: 1; }
</style>

<?php include('include/sidebar.php'); // Ensure this path is correct ?>

<div class="page-container">
<?php
include ('include/config.php'); // Ensure this path connects to your database

if (!$conn) {
    echo "<div class='message-area error'>Database connection failed. Check config.php.</div>";
    exit;
}
$conn->set_charset("utf8mb4");

$message = '';
$message_type = ''; 
$upload_dir = "uploads/about_page/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Helper function for image upload
function handle_image_upload($file_input_name, $current_image_path, $delete_flag, $upload_dir) {
    $image_db_path = $current_image_path;
    if ($delete_flag) {
        if (!empty($image_db_path) && file_exists($image_db_path)) {
            @unlink($image_db_path);
        }
        $image_db_path = null;
    }
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
        if (!empty($current_image_path) && file_exists($current_image_path)) {
            @unlink($current_image_path);
        }
        $tmp_name = $_FILES[$file_input_name]['tmp_name'];
        $image_name = time() . "_" . basename($_FILES[$file_input_name]['name']);
        $target_path = $upload_dir . $image_name;
        if (move_uploaded_file($tmp_name, $target_path)) {
            $image_db_path = $target_path;
        } else {
            throw new Exception("Error uploading file for '$file_input_name'.");
        }
    }
    return $image_db_path;
}


// --- FORM SUBMISSION LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->begin_transaction();

        // --- Handle Main Content Form ---
        if (isset($_POST['form_type']) && $_POST['form_type'] === 'main_content') {
            $banner_image_path = handle_image_upload('banner_image_url', $_POST['current_banner_image'], isset($_POST['delete_banner_image']), $upload_dir);
            $welcome_image_path = handle_image_upload('welcome_image_url', $_POST['current_welcome_image'], isset($_POST['delete_welcome_image']), $upload_dir);
            
            $stmt = $conn->prepare("UPDATE about_page_content SET 
                banner_title=?, banner_subtitle=?, banner_image_url=?, 
                welcome_heading=?, welcome_text=?, welcome_image_url=?, 
                principles_heading=?, vision_icon=?, vision_title=?, vision_text=?, 
                mission_icon=?, mission_title=?, mission_text=?, choose_us_heading=?
                WHERE id = 1");
            
            $stmt->bind_param("ssssssssssssss", 
                $_POST['banner_title'], $_POST['banner_subtitle'], $banner_image_path,
                $_POST['welcome_heading'], $_POST['welcome_text'], $welcome_image_path,
                $_POST['principles_heading'], $_POST['vision_icon'], $_POST['vision_title'], $_POST['vision_text'],
                $_POST['mission_icon'], $_POST['mission_title'], $_POST['mission_text'], $_POST['choose_us_heading']
            );
            $stmt->execute();
            $message = "Main page content updated successfully.";
            $message_type = 'success';
        }

        // --- Handle 'Why Choose Us' Points Form ---
        if (isset($_POST['form_type']) && $_POST['form_type'] === 'choose_us_point') {
            $action = $_POST['action'];
            $point_id = $_POST['point_id'] ?? null;

            if ($action === 'insert' || $action === 'update') {
                $stmt = $action === 'insert' 
                    ? $conn->prepare("INSERT INTO choose_us_points (icon_class, title, description, display_order) VALUES (?, ?, ?, ?)")
                    : $conn->prepare("UPDATE choose_us_points SET icon_class=?, title=?, description=?, display_order=? WHERE id=?");

                if ($action === 'insert') {
                    $stmt->bind_param("sssi", $_POST['icon_class'], $_POST['title'], $_POST['description'], $_POST['display_order']);
                } else {
                    $stmt->bind_param("sssis", $_POST['icon_class'], $_POST['title'], $_POST['description'], $_POST['display_order'], $point_id);
                }
                $stmt->execute();
                $message = "Point " . ($action === 'insert' ? 'added' : 'updated') . " successfully.";
                $message_type = 'success';

            } elseif ($action === 'delete') {
                $stmt = $conn->prepare("DELETE FROM choose_us_points WHERE id = ?");
                $stmt->bind_param("i", $point_id);
                $stmt->execute();
                $message = "Point deleted successfully.";
                $message_type = 'success';

            } elseif ($action === 'activate' || $action === 'deactivate') {
                $new_status = $action === 'activate' ? 1 : 0;
                $stmt = $conn->prepare("UPDATE choose_us_points SET is_active = ? WHERE id = ?");
                $stmt->bind_param("ii", $new_status, $point_id);
                $stmt->execute();
                $message = "Point status updated successfully.";
                $message_type = 'success';
            }
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
    }
}

// --- FETCH DATA FOR DISPLAY ---
$about_content = $conn->query("SELECT * FROM about_page_content WHERE id = 1")->fetch_assoc();
$choose_us_points = $conn->query("SELECT * FROM choose_us_points ORDER BY display_order ASC, id ASC")->fetch_all(MYSQLI_ASSOC);

?>

<h2>Manage "About Us" Page Content</h2>

<?php if (!empty($message)): ?>
    <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="form-section">
    <h3>Main Page Content</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form_type" value="main_content">
        <input type="hidden" name="current_banner_image" value="<?php echo htmlspecialchars($about_content['banner_image_url'] ?? ''); ?>">
        <input type="hidden" name="current_welcome_image" value="<?php echo htmlspecialchars($about_content['welcome_image_url'] ?? ''); ?>">

        <h4>Banner Section</h4>
        <label for="banner_title">Banner Title:</label>
        <input type="text" id="banner_title" name="banner_title" value="<?php echo htmlspecialchars($about_content['banner_title'] ?? ''); ?>" required>
        
        <label for="banner_subtitle">Banner Subtitle:</label>
        <input type="text" id="banner_subtitle" name="banner_subtitle" value="<?php echo htmlspecialchars($about_content['banner_subtitle'] ?? ''); ?>">

        <label>Banner Background Image:</label>
        <?php if(!empty($about_content['banner_image_url'])): ?>
        <div class="image-preview-container">
            <div class="image-preview-item"><img src="<?php echo htmlspecialchars($about_content['banner_image_url']); ?>" alt="Banner Image"></div>
            <label><input type="checkbox" name="delete_banner_image" value="1"> Delete current image</label>
        </div>
        <?php endif; ?>
        <input type="file" name="banner_image_url" accept="image/*">

        <hr style="margin: 20px 0;">
        <h4>Welcome Section</h4>
        <label for="welcome_heading">Welcome Heading:</label>
        <input type="text" id="welcome_heading" name="welcome_heading" value="<?php echo htmlspecialchars($about_content['welcome_heading'] ?? ''); ?>" required>
        
        <label for="welcome_text">Welcome Text:</label>
        <textarea name="welcome_text" rows="5"><?php echo htmlspecialchars($about_content['welcome_text'] ?? ''); ?></textarea>
        
        <label>Welcome Image:</label>
        <?php if(!empty($about_content['welcome_image_url'])): ?>
        <div class="image-preview-container">
            <div class="image-preview-item"><img src="<?php echo htmlspecialchars($about_content['welcome_image_url']); ?>" alt="Welcome Image"></div>
            <label><input type="checkbox" name="delete_welcome_image" value="1"> Delete current image</label>
        </div>
        <?php endif; ?>
        <input type="file" name="welcome_image_url" accept="image/*">

        <hr style="margin: 20px 0;">
        <h4>Guiding Principles Section</h4>
        <label for="principles_heading">Section Heading:</label>
        <input type="text" id="principles_heading" name="principles_heading" value="<?php echo htmlspecialchars($about_content['principles_heading'] ?? ''); ?>">

        <div class="two-column-layout">
            <div>
                <label for="vision_icon">Vision Icon (FontAwesome class):</label>
                <input type="text" id="vision_icon" name="vision_icon" value="<?php echo htmlspecialchars($about_content['vision_icon'] ?? 'fas fa-eye'); ?>">
                <label for="vision_title">Vision Title:</label>
                <input type="text" id="vision_title" name="vision_title" value="<?php echo htmlspecialchars($about_content['vision_title'] ?? ''); ?>">
                <label for="vision_text">Vision Text:</label>
                <textarea name="vision_text" rows="4"><?php echo htmlspecialchars($about_content['vision_text'] ?? ''); ?></textarea>
            </div>
            <div>
                <label for="mission_icon">Mission Icon (FontAwesome class):</label>
                <input type="text" id="mission_icon" name="mission_icon" value="<?php echo htmlspecialchars($about_content['mission_icon'] ?? 'fas fa-bullseye'); ?>">
                <label for="mission_title">Mission Title:</label>
                <input type="text" id="mission_title" name="mission_title" value="<?php echo htmlspecialchars($about_content['mission_title'] ?? ''); ?>">
                <label for="mission_text">Mission Text:</label>
                <textarea name="mission_text" rows="4"><?php echo htmlspecialchars($about_content['mission_text'] ?? ''); ?></textarea>
            </div>
        </div>

        <hr style="margin: 20px 0;">
        <h4>'Why Choose Us' Section</h4>
        <label for="choose_us_heading">Section Heading:</label>
        <input type="text" id="choose_us_heading" name="choose_us_heading" value="<?php echo htmlspecialchars($about_content['choose_us_heading'] ?? ''); ?>">

        <button type="submit">Save All Changes</button>
    </form>
</div>

<hr style="margin: 40px 0; border-top: 1px solid #ccc;">

<div class="form-section">
    <h3 id="pointFormTitle">Add New 'Why Choose Us' Point</h3>
    <form id="pointForm" action="" method="POST">
        <input type="hidden" name="form_type" value="choose_us_point">
        <input type="hidden" name="action" id="pointAction" value="insert">
        <input type="hidden" name="point_id" id="point_id" value="">
        
        <div class="two-column-layout">
            <div>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="icon_class">Icon (FontAwesome class e.g., 'fas fa-cogs'):</label>
                <input type="text" id="icon_class" name="icon_class" required>
            </div>
        </div>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3" required></textarea>
        
        <label for="display_order">Display Order:</label>
        <input type="number" id="display_order" name="display_order" value="0" required>
        
        <button type="submit" id="pointSubmitBtn">Add Point</button>
        <button type="button" id="cancelPointEditBtn" style="display: none;">Cancel Edit</button>
    </form>
</div>

<div class="announcements-list-section">
    <h3>Existing 'Why Choose Us' Points</h3>
    <table class="announcements-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Icon</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($choose_us_points)): ?>
                <tr><td colspan="5" style="text-align:center;">No points found. Add one above.</td></tr>
            <?php else: ?>
                <?php foreach ($choose_us_points as $point): ?>
                <tr>
                    <td><?php echo htmlspecialchars($point['display_order']); ?></td>
                    <td><i class="<?php echo htmlspecialchars($point['icon_class']); ?>"></i> (<?php echo htmlspecialchars($point['icon_class']); ?>)</td>
                    <td><?php echo htmlspecialchars($point['title']); ?></td>
                    <td>
                        <span class="status-<?php echo $point['is_active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $point['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button type="button" class="edit-btn edit-point-btn" 
                            data-id="<?php echo $point['id']; ?>"
                            data-title="<?php echo htmlspecialchars($point['title']); ?>"
                            data-icon="<?php echo htmlspecialchars($point['icon_class']); ?>"
                            data-description="<?php echo htmlspecialchars($point['description']); ?>"
                            data-order="<?php echo htmlspecialchars($point['display_order']); ?>">Edit</button>
                        
                        <form action="" method="POST">
                            <input type="hidden" name="form_type" value="choose_us_point">
                            <input type="hidden" name="point_id" value="<?php echo $point['id']; ?>">
                            <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                        
                        <form action="" method="POST">
                             <input type="hidden" name="form_type" value="choose_us_point">
                            <input type="hidden" name="point_id" value="<?php echo $point['id']; ?>">
                            <?php if ($point['is_active']): ?>
                                <button type="submit" name="action" value="deactivate">Deactivate</button>                            
                            <?php else: ?>
                                <button type="submit" name="action" value="activate">Activate</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div> <script>
document.addEventListener('DOMContentLoaded', function() {
    const pointForm = document.getElementById('pointForm');
    const pointFormTitle = document.getElementById('pointFormTitle');
    const pointAction = document.getElementById('pointAction');
    const pointIdInput = document.getElementById('point_id');
    const pointSubmitBtn = document.getElementById('pointSubmitBtn');
    const cancelPointEditBtn = document.getElementById('cancelPointEditBtn');

    function resetPointForm() {
        pointForm.reset();
        pointFormTitle.textContent = "Add New 'Why Choose Us' Point";
        pointAction.value = 'insert';
        pointIdInput.value = '';
        pointSubmitBtn.textContent = 'Add Point';
        cancelPointEditBtn.style.display = 'none';
    }

    cancelPointEditBtn.addEventListener('click', resetPointForm);

    document.querySelectorAll('.edit-point-btn').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            pointFormTitle.textContent = "Edit Point: " + data.title;
            pointAction.value = 'update';
            pointIdInput.value = data.id;
            
            document.getElementById('title').value = data.title;
            document.getElementById('icon_class').value = data.icon;
            document.getElementById('description').value = data.description;
            document.getElementById('display_order').value = data.order;
            
            pointSubmitBtn.textContent = 'Update Point';
            cancelPointEditBtn.style.display = 'inline-block';

            pointForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
});
</script>

<?php
 //include('include/footer.php'); // Ensure this path is correct
?>
</body>
</html>