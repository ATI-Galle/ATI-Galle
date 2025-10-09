<?php
// It's good practice to start sessions or include global settings here if not in header.php
// session_start();
// error_reporting(E_ALL); // Recommended for development
// ini_set('display_errors', 1); // Recommended for development

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
/* Ensure TinyMCE editor respects border radius and standard border */
.tox-tinymce {
    border: 1px solid #ccc !important;
    border-radius: var(--border-radius) !important;
}
.form-section input[type="file"] {
    padding: 8px; /* Adjusted padding for file input */
    background-color: #fff; /* Ensure file input background is white */
}
.form-section .image-preview-container {
    margin-top: 10px;
    margin-bottom: 15px; /* Added margin for spacing */
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
.form-section .image-preview-item img { /* Target img tag directly */
    max-width: 100px;
    max-height: 100px;
    display: block; /* remove extra space below image */
    object-fit: cover;
    border-radius: calc(var(--border-radius) - 2px); /* Slightly smaller radius than container */
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
.form-section .delete-existing-image-btn:disabled {
    background-color: var(--secondary-color);
    cursor: not-allowed;
}
.form-section .image-preview-item.marked-for-deletion img {
    opacity: 0.5;
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
.event-table .actions-cell .edit-btn { /* Applied common styles to edit-btn too */
    padding: 6px 12px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.85rem;
    color: white;
    transition: background-color 0.2s ease;
    text-decoration: none; /* For edit button if it's an <a> styled as button */
    display: inline-block; /* For consistent spacing */
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







.review-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .review-table th, .review-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .review-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .action-buttons a {
            margin-right: 5px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .btn-approve { background-color: #28a745; color: white; }
        .btn-delete { background-color: #dc3545; color: white; }
        .status-indicator {
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .approved { background-color: #d4edda; color: #155724; }
        .pending { background-color: #f8d7da; color: #721c24; }








</style>

<?php include('include/sidebar.php'); // Assuming your sidebar.php ?>

<div class="page-container">

<h2>Event Management</h2>








<?php
include('../include/config.php');
error_reporting(0);

{
    // --- Message Variables ---
    $message = '';
    $error = '';

    // ========================================================================
    // HANDLE ACTIONS (APPROVE, DELETE)
    // ========================================================================
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $review_id = intval($_GET['id']);
        $action = $_GET['action'];

        $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            $error = "Database Connection failed: " . $conn->connect_error;
            error_log("DB Connect Error (action): " . $conn->connect_error);
        } else {
            $conn->set_charset("utf8mb4");

            if ($action === 'approve') {
                $stmt = $conn->prepare("UPDATE reviews SET approved = 1 WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $review_id);
                    if ($stmt->execute()) {
                        $message = "Review approved successfully.";
                    } else {
                        $error = "Error approving review: " . $stmt->error;
                        error_log("SQL Update Error (approve): " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $error = "Error preparing statement (approve): " . $conn->error;
                    error_log("SQL Prepare Error (approve): " . $conn->error);
                }
            } elseif ($action === 'delete') {
                $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $review_id);
                    if ($stmt->execute()) {
                        $message = "Review deleted successfully.";
                    } else {
                        $error = "Error deleting review: " . $stmt->error;
                        error_log("SQL Delete Error: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    $error = "Error preparing statement (delete): " . $conn->error;
                    error_log("SQL Prepare Error (delete): " . $conn->error);
                }
            }
            $conn->close();
        }
    }

    // ========================================================================
    // FETCH ALL REVIEWS
    // ========================================================================
    $reviews = [];
    $conn_select = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn_select->connect_error) {
        $error = "DB Connect Error (fetch reviews): " . $conn_select->connect_error;
        error_log("DB Select Connect Error: " . $conn_select->connect_error);
    } else {
        $conn_select->set_charset("utf8mb4");
        $sql_select = "SELECT id, item_id, user_name, rating, review_text, created_at, approved FROM reviews ORDER BY created_at DESC";
        $result = $conn_select->query($sql_select);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            $result->free();
        } else {
            $error = "Error fetching reviews: " . $conn_select->error;
            error_log("SQL Select Error (reviews): " . $conn_select->error);
        }
        $conn_select->close();
    }
?>
    <style>
    </style>
</head>
<body class="fixed-left">
    <div id="wrapper">
        <?php include('includes/topheader.php');?>
        <?php include('includes/leftsidebar.php');?>
        <div class="content-page">
            <div class="content">
                <div class="container">
                   
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($message): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                            <?php endif; ?>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>

                            <div class="card-box">
                                

                                <div class="table-responsive">
                                    <table class="review-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Item ID</th>
                                                <th>User Name</th>
                                                <th>Rating</th>
                                                <th>Review Text</th>
                                                <th>Created At</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($reviews)): ?>
                                                <?php foreach ($reviews as $review): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($review['id']); ?></td>
                                                        <td><?php echo htmlspecialchars($review['item_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($review['rating']); ?></td>
                                                        <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td>
                                                        <td><?php echo (new DateTime($review['created_at']))->format('Y-m-d H:i:s'); ?></td>
                                                        <td>
                                                            <span class="status-indicator <?php echo $review['approved'] ? 'approved' : 'pending'; ?>">
                                                                <?php echo $review['approved'] ? 'Approved' : 'Pending'; ?>
                                                            </span>
                                                        </td>
                                                        <td class="action-buttons">
                                                            <?php if (!$review['approved']): ?>
                                                                <a href="?action=approve&id=<?php echo $review['id']; ?>" class="btn-approve" title="Approve">Approve</a>
                                                            <?php endif; ?>
                                                            <a href="?action=delete&id=<?php echo $review['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="8" class="text-center">No reviews found.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                        
    
<?php } ?>





    
</div> <script src="https://cdn.tiny.cloud/1/9tftpew6nchs467m3z4d2v9e5xmvvvl8bis1m0g7iqt8w7bs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'code lists link image media table wordcount fullscreen preview searchreplace help',
        toolbar: 'undo redo | styleselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table | code | fullscreen preview | searchreplace | help',
        height: 300,
        menubar: 'file edit view insert format tools table help',
        // Removed image upload handler as it's handled by the main form file input
        // setup: function (editor) {
        //     editor.on('change', function () {
        //         tinymce.triggerSave();
        //     });
        // }
    });

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    const formTitle = document.getElementById('formTitle');
    const formActionInput = document.getElementById('formAction');
    const existingAlbumIdInput = document.getElementById('existing_album_id');
    const eidInput = document.getElementById('eid');
    const etitleInput = document.getElementById('etitle');
    const etagInput = document.getElementById('etag');
    // const etextTextarea = document.getElementById('etext'); // For TinyMCE, interact via its API
    const submitButton = document.getElementById('submitButton');
    const cancelEditButton = document.getElementById('cancelEdit');
    const currentImagesPreviewContainer = document.getElementById('current_eimgs_preview_container');
    const imagesToDeleteContainer = document.getElementById('images_to_delete_container');
    const newImagesInput = document.getElementById('eimgs');
    const currentImagesSection = document.getElementById('current-images-section');

    function resetFormToDefaults() {
        form.reset(); // Resets native form elements
        formTitle.textContent = 'Add New Event';
        formActionInput.value = 'insert';
        existingAlbumIdInput.value = '';
        eidInput.readOnly = false;
        eidInput.value = ''; 
        etitleInput.value = '';
        etagInput.value = '';
        if (tinymce.get('etext')) {
            tinymce.get('etext').setContent('');
        }
        currentImagesPreviewContainer.innerHTML = '';
        imagesToDeleteContainer.innerHTML = ''; // Clear hidden inputs for deletion
        newImagesInput.value = ''; // Clear file input
        currentImagesSection.style.display = 'none';
        cancelEditButton.style.display = 'none';
        submitButton.textContent = 'Save Event';
        window.scrollTo(0, form.offsetTop - 20); // Scroll to form
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
                // Fallback if TinyMCE hasn't initialized yet for some reason (should not happen with DOMContentLoaded)
                document.getElementById('etext').value = eventData.etext || '';
            }
            
            submitButton.textContent = 'Update Event';
            cancelEditButton.style.display = 'inline-block';
            currentImagesSection.style.display = 'block';

            // Clear previous previews and deletion markers
            currentImagesPreviewContainer.innerHTML = '';
            imagesToDeleteContainer.innerHTML = '';
            newImagesInput.value = ''; // Clear file input for new images

            if (eventData.images) {
                const imagePaths = eventData.images.split('||').filter(path => path.trim() !== '');
                if (imagePaths.length > 0) {
                    currentImagesSection.style.display = 'block';
                    imagePaths.forEach(path => {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'image-preview-item';

                        const img = document.createElement('img');
                        // Add a cache-busting query parameter to ensure fresh image is shown
                        img.src = path + '?t=' + new Date().getTime(); 
                        img.alt = 'Existing image';
                        img.className = 'image-preview'; // from your CSS

                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.className = 'delete-existing-image-btn';
                        deleteBtn.innerHTML = '&times;'; // 'X' symbol
                        deleteBtn.title = 'Mark for deletion';

                        deleteBtn.addEventListener('click', function() {
                            previewItem.classList.add('marked-for-deletion');
                            this.disabled = true; // Prevent multiple clicks
                            this.textContent = '✓'; // Indicate it's marked

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
                     currentImagesSection.style.display = 'none'; // Hide if no images
                }
            } else {
                 currentImagesSection.style.display = 'none'; // Hide if no images string
            }
            window.scrollTo(0, form.offsetTop - 20); // Scroll to form for better UX
        });
    });
});

</script>
<?php
// It's good practice to close the connection if it was opened.
// Assuming your footer.php might handle this or it's handled by PHP at script end.
// if (isset($conn) && $conn instanceof mysqli) {
// $conn->close();
// }
// include('include/footer.php'); // If you have a footer file
?>
</body>
</html>