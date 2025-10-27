<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session and include database configuration
require_once "include/config.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Ensure the logged-in user has the 'super_admin' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: index.php"); 
    exit();
}

// --- START: PROCESSING LOGIC FOR APPROVE/REJECT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['announcement_id']) && isset($_POST['action'])) {
        $announcement_id = trim($_POST['announcement_id']);
        $action = trim($_POST['action']);
        $new_status = null;
        $message = "";

        if ($action === 'approve') {
            $new_status = 1;
            $message = "Announcement (ID: " . htmlspecialchars($announcement_id) . ") has been approved and published.";
            $message_type = "success";
        } elseif ($action === 'reject') {
            $new_status = 0;
            $message = "Announcement (ID: " . htmlspecialchars($announcement_id) . ") has been rejected.";
            $message_type = "warning";
        } else {
            $message = "Invalid action specified.";
            $message_type = "danger";
        }

        if ($new_status !== null) {
            $sql_update = "UPDATE university_announcements SET status = ? WHERE announcement_id = ?";
            if ($stmt = $conn->prepare($sql_update)) {
                $stmt->bind_param("is", $new_status, $announcement_id);
                if (!$stmt->execute()) {
                    $message = "Error updating record: " . $stmt->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                $message = "Database query preparation failed: " . $conn->error;
                $message_type = "danger";
            }
        }
        
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $message_type;
        header("Location: review_announcements.php");
        exit();
    }
}
// --- END: PROCESSING LOGIC ---

// --- START: DISPLAY LOGIC ---
$pending_announcements = [];
$sql_select = "SELECT * FROM university_announcements WHERE status = 2 ORDER BY created_at ASC";
if ($query = mysqli_query($conn, $sql_select)) {
    $pending_announcements = mysqli_fetch_all($query, MYSQLI_ASSOC);
} else {
    die("Database Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Review Announcements</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <style>
        .modal-body img { max-width: 100%; height: auto; border-radius: 8px; }
        .announcement-content { white-space: pre-wrap; word-wrap: break-word; }
        .action-form { display: inline-flex; gap: 5px; }
        /* NEW STYLE FOR IMAGE PREVIEW IN TABLE */
        .table-img-preview {
            width: 100px;
            height: 60px;
            object-fit: cover; /* Ensures the image covers the area without distortion */
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php include 'include/header.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include 'include/sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            <span class="page-title-icon bg-gradient-primary text-white mr-2">
                                <i class="mdi mdi-bell-check"></i>
                            </span> Review Pending Announcements
                        </h3>
                    </div>

                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Pending Submissions</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Image</th>
                                                    <th>Title</th>
                                                    <th>Target Audience</th>
                                                    <th>Submitted On</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($pending_announcements)): ?>
                                                    <?php foreach ($pending_announcements as $announcement): ?>
                                                        <tr>
                                                            <td><strong><?php echo htmlspecialchars($announcement['announcement_id']); ?></strong></td>
                                                            
                                                            <td>
                                                                <?php if (!empty($announcement['image_path'])): ?>
                                                                    <img src="uploads/announcements/<?php echo htmlspecialchars($announcement['image_path']); ?>" 
                                                                         alt="<?php echo htmlspecialchars($announcement['title']); ?>" 
                                                                         class="table-img-preview">
                                                                <?php else: ?>
                                                                    <span class="text-muted">No Image</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            
                                                            <td><?php echo htmlspecialchars(substr($announcement['title'], 0, 40)); ?>...</td>
                                                            <td><label class="badge badge-gradient-info"><?php echo htmlspecialchars($announcement['target_audience']); ?></label></td>
                                                            <td><?php echo date("M d, Y h:i A", strtotime($announcement['created_at'])); ?></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-info btn-sm view-details-btn" 
                                                                        data-toggle="modal" 
                                                                        data-target="#detailsModal"
                                                                        data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                                                        data-content="<?php echo htmlspecialchars($announcement['content']); ?>"
                                                                        data-image="<?php echo htmlspecialchars($announcement['image_path']); ?>"
                                                                        data-audience="<?php echo htmlspecialchars($announcement['target_audience']); ?>"
                                                                        data-publish="<?php echo !empty($announcement['publish_datetime']) ? date("M d, Y h:i A", strtotime($announcement['publish_datetime'])) : 'Not Set'; ?>"
                                                                        data-expiry="<?php echo !empty($announcement['expiry_datetime']) ? date("M d, Y h:i A", strtotime($announcement['expiry_datetime'])) : 'Not Set'; ?>">
                                                                    View Details
                                                                </button>
                                                                
                                                                <form action="review_announcements.php" method="POST" class="action-form ml-2">
                                                                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['announcement_id']); ?>">
                                                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">🎉 Great job! No announcements are currently pending review.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php // include 'include/footer.php'; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="detailsModalLabel">Announcement Details</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body">
                    <h4 id="modal-title"></h4><hr>
                    <p><strong>Target Audience:</strong> <span id="modal-audience"></span></p>
                    <p><strong>Publish On:</strong> <span id="modal-publish"></span></p>
                    <p><strong>Expires On:</strong> <span id="modal-expiry"></span></p><hr>
                    <div id="modal-image-container" class="mb-3 text-center"></div>
                    <p class="announcement-content" id="modal-content"></p>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var viewButtons = document.querySelectorAll('.view-details-btn');
        viewButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var title = this.getAttribute('data-title');
                var content = this.getAttribute('data-content');
                var imagePath = this.getAttribute('data-image');
                var audience = this.getAttribute('data-audience');
                var publish = this.getAttribute('data-publish');
                var expiry = this.getAttribute('data-expiry');
                document.getElementById('modal-title').textContent = title;
                document.getElementById('modal-content').textContent = content;
                document.getElementById('modal-audience').textContent = audience;
                document.getElementById('modal-publish').textContent = publish;
                document.getElementById('modal-expiry').textContent = expiry;
                var imageContainer = document.getElementById('modal-image-container');
                imageContainer.innerHTML = '';
                if (imagePath && imagePath !== 'NULL' && imagePath.trim() !== '') {
                    var img = document.createElement('img');
                    img.src = 'uploads/announcements/' + imagePath; 
                    img.alt = title;
                    img.className = 'img-fluid rounded';
                    imageContainer.appendChild(img);
                }
            });
        });
    });
    </script>
</body>
</html>