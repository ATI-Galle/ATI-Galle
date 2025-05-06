<?php


include ("include/config.php");



// Optional: Set character set
$conn->set_charset("utf8mb4");

// --- PHP Logic for Handling Actions (Insert, Update, Delete, Activate/Deactivate) ---
$message = ''; // Variable to store success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'insert':
            $stitle = $_POST['stitle'] ?? '';
            $stext = $_POST['stext'] ?? '';
            $simg = ''; // Initialize image path
            $status = 1; // Default status to active

            // Handle image upload
            if (isset($_FILES['simg']) && $_FILES['simg']['error'] == 0) {
                $target_dir = "uploads/"; // Create an 'uploads' directory
                // Ensure the uploads directory exists
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $target_file = $target_dir . basename($_FILES["simg"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $uploadOk = 1;

                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["simg"]["tmp_name"]);
                if($check === false) {
                    $message = "File is not an image.";
                    $uploadOk = 0;
                }

                // Check file size (optional, adjust as needed)
                if ($_FILES["simg"]["size"] > 5000000) { // 5MB limit
                    $message = "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $message = "Sorry, your file was not uploaded. " . $message;
                } else {
                    // if everything is ok, try to upload file
                    if (move_uploaded_file($_FILES["simg"]["tmp_name"], $target_file)) {
                        $simg = $target_file;
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                    }
                }
            }

            // Prepare and bind
            if (empty($message) || (!empty($message) && $simg != '')) { // Only insert if no upload error or if image uploaded
                 $stmt = $conn->prepare("INSERT INTO slider (stitle, stext, simg, created_at, updated_at, status) VALUES (?, ?, ?, NOW(), NOW(), ?)");
                 $stmt->bind_param("sssi", $stitle, $stext, $simg, $status);

                 if ($stmt->execute()) {
                     $message = "New record created successfully";
                 } else {
                     $message = "Error: " . $stmt->error;
                 }
                 $stmt->close();
            }
            break;

        case 'update':
            $sid = $_POST['sid'] ?? '';
            $stitle = $_POST['stitle'] ?? '';
            $stext = $_POST['stext'] ?? '';
            $existing_simg = $_POST['existing_simg'] ?? '';
            $simg = $existing_simg; // Keep existing image by default

            // Handle new image upload
            if (isset($_FILES['simg']) && $_FILES['simg']['error'] == 0) {
                 $target_dir = "uploads/";
                 // Ensure the uploads directory exists
                 if (!is_dir($target_dir)) {
                     mkdir($target_dir, 0777, true);
                 }
                 $target_file = $target_dir . basename($_FILES["simg"]["name"]);
                 $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                 $uploadOk = 1;

                 // Check if image file is a actual image or fake image
                 $check = getimagesize($_FILES["simg"]["tmp_name"]);
                 if($check === false) {
                     $message = "New file is not an image.";
                     $uploadOk = 0;
                 }

                 // Check file size (optional, adjust as needed)
                 if ($_FILES["simg"]["size"] > 5000000) { // 5MB limit
                     $message = "Sorry, your new file is too large.";
                     $uploadOk = 0;
                 }

                 // Allow certain file formats
                 if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                 && $imageFileType != "gif" ) {
                     $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed for new image.";
                     $uploadOk = 0;
                 }

                 // Check if $uploadOk is set to 0 by an error
                 if ($uploadOk == 0) {
                     $message = "Sorry, your new file was not uploaded. " . $message;
                 } else {
                     // if everything is ok, try to upload file
                     if (move_uploaded_file($_FILES["simg"]["tmp_name"], $target_file)) {
                         $simg = $target_file;
                         // Optional: Delete old image if it exists
                         if (!empty($existing_simg) && file_exists($existing_simg)) {
                             unlink($existing_simg);
                         }
                     } else {
                         $message = "Sorry, there was an error uploading your new file.";
                     }
                 }
            }

            // Prepare and bind
            if (!empty($sid)) { // Ensure SID is present for update
                $stmt = $conn->prepare("UPDATE slider SET stitle=?, stext=?, simg=?, updated_at=NOW() WHERE sid=?");
                $stmt->bind_param("sssi", $stitle, $stext, $simg, $sid);

                if ($stmt->execute()) {
                    $message = "Record updated successfully";
                } else {
                    $message = "Error updating record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                 $message = "Error: Slider ID not provided for update.";
            }
            break;

        case 'delete':
            $sid = $_POST['sid'] ?? '';

            if (!empty($sid)) {
                // Optional: Get image path before deleting the record to delete the image file
                $stmt_img = $conn->prepare("SELECT simg FROM slider WHERE sid = ?");
                $stmt_img->bind_param("i", $sid);
                $stmt_img->execute();
                $stmt_img->bind_result($image_path);
                $stmt_img->fetch();
                $stmt_img->close();

                // Prepare and bind for deletion
                $stmt = $conn->prepare("DELETE FROM slider WHERE sid = ?");
                $stmt->bind_param("i", $sid);

                if ($stmt->execute()) {
                    // Optional: Delete the image file
                    if (!empty($image_path) && file_exists($image_path)) {
                        unlink($image_path);
                    }
                    $message = "Record deleted successfully";
                } else {
                    $message = "Error deleting record: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Error: Slider ID not provided for deletion.";
            }
            break;

        case 'activate':
            $sid = $_POST['sid'] ?? '';
             if (!empty($sid)) {
                // Prepare and bind
                $stmt = $conn->prepare("UPDATE slider SET status=1, updated_at=NOW() WHERE sid=?");
                $stmt->bind_param("i", $sid);

                if ($stmt->execute()) {
                    $message = "Slider activated successfully";
                } else {
                    $message = "Error activating slider: " . $stmt->error;
                }
                $stmt->close();
            } else {
                 $message = "Error: Slider ID not provided for activation.";
            }
            break;

        case 'deactivate':
            $sid = $_POST['sid'] ?? '';
             if (!empty($sid)) {
                // Prepare and bind
                $stmt = $conn->prepare("UPDATE slider SET status=0, updated_at=NOW() WHERE sid=?");
                $stmt->bind_param("i", $sid);

                if ($stmt->execute()) {
                    $message = "Slider deactivated successfully";
                } else {
                    $message = "Error deactivating slider: " . $stmt->error;
                }
                $stmt->close();
            } else {
                 $message = "Error: Slider ID not provided for deactivation.";
            }
            break;

        default:
            // No action or invalid action - do nothing
            break;
    }
    // No redirection needed as we are on the same page
}

// --- HTML Structure with Embedded CSS and JavaScript ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #333;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }


        .form-container {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #e9e9e9;
            border-radius: 5px;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container textarea,
        .form-container input[type="file"] {
            width: calc(100% - 22px); /* Adjust for padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }

        .form-container button:hover {
            background-color: #4cae4c;
        }

        #cancelEdit {
            background-color: #d9534f;
        }

        #cancelEdit:hover {
            background-color: #c9302c;
        }


        .slider-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .slider-list th, .slider-list td {
            border: 1px solid #ddd;
            width:200px;
            padding: 8px;
            text-align: left;
        }

        .slider-list th {
            background-color: #f2f2f2;
        }

        .slider-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .slider-list img {
            display: block;
            max-width: 50px;
            height: auto;
        }

        .slider-list button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .slider-list button.edit-btn {
            background-color: #f0ad4e;
            color: white;
        }

        .slider-list button.edit-btn:hover {
            background-color: #ec971f;
        }

        .slider-list form {
            display: inline-block;
        }

        .slider-list form button[type="submit"] {
            background-color: #d9534f;
            color: white;
        }

        .slider-list form button[type="submit"]:hover {
            background-color: #c9302c;
        }

        .slider-list form button[type="submit"]:not([name="action"][value="delete"]) {
             background-color: #0275d8;
             color: white;
        }

        .slider-list form button[type="submit"]:not([name="action"][value="delete"]):hover {
             background-color: #025aa5;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Slider Management</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo (strpos($message, 'Error') !== false || strpos($message, 'Sorry') !== false) ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <h3>Add New Slider</h3>
            <form id="sliderForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="insert">
                <input type="hidden" name="sid" id="sid" value="">

                <label for="stitle">Title:</label>
                <input type="text" id="stitle" name="stitle" required>

                <label for="stext">Text:</label>
                <textarea id="stext" name="stext" ></textarea>

                <label for="simg">Image:</label>
                <input type="file" id="simg" name="simg" accept="image/*">
                <input type="hidden" name="existing_simg" id="existing_simg" value="">
                <img id="current_simg_preview" src="" alt="Current Image" style="max-width: 100px; display: none;">


                <button type="submit">Save Slider</button>
                <button type="button" id="cancelEdit" style="display: none;">Cancel Edit</button>
            </form>
        </div>

        <hr>

        <div class="slider-list">
            <h3>Existing Sliders</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th >Text</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM slider ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["sid"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["stitle"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["stext"]) . "</td>";
                            echo "<td>";
                            if (!empty($row["simg"])) {
                                echo "<img src='" . htmlspecialchars($row["simg"]) . "' alt='Slider Image' width='50'>";
                            } else {
                                echo "No Image";
                            }
                            echo "</td>";
                            echo "<td>" . ($row["status"] == 1 ? "Active" : "Inactive") . "</td>";
                            echo "<td>" . $row["created_at"] . "</td>";
                            echo "<td>" . $row["updated_at"] . "</td>";
                            echo "<td>";
                            // Edit Button
                            echo "<button class='edit-btn' data-sid='" . $row["sid"] . "' data-stitle='" . htmlspecialchars($row["stitle"]) . "' data-stext='" . htmlspecialchars($row["stext"]) . "' data-simg='" . htmlspecialchars($row["simg"]) . "' data-status='" . $row["status"] . "'>Edit</button>";

                            // Delete Form
                            echo "<form action='' method='POST' style='display:inline-block;' onsubmit='return confirm(\"Are you sure you want to delete this slider?\");'>";
                            echo "<input type='hidden' name='action' value='delete'>";
                            echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                            echo "<button type='submit'>Delete</button>";
                            echo "</form>";

                            // Activate/Deactivate Forms
                            if ($row["status"] == 1) {
                                echo "<form action='' method='POST' style='display:inline-block;'>";
                                echo "<input type='hidden' name='action' value='deactivate'>";
                                echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                                echo "<button type='submit'>Deactivate</button>";
                                echo "</form>";
                            } else {
                                echo "<form action='' method='POST' style='display:inline-block;'>";
                                echo "<input type='hidden' name='action' value='activate'>";
                                echo "<input type='hidden' name='sid' value='" . $row["sid"] . "'>";
                                echo "<button type='submit'>Activate</button>";
                                echo "</form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No sliders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderForm = document.getElementById('sliderForm');
            const actionInput = document.getElementById('action');
            const sidInput = document.getElementById('sid');
            const stitleInput = document.getElementById('stitle');
            const stextInput = document.getElementById('stext');
            const simgInput = document.getElementById('simg');
            const existingSimgInput = document.getElementById('existing_simg');
            const currentSimgPreview = document.getElementById('current_simg_preview');
            const formTitle = document.querySelector('.form-container h3');
            const submitButton = sliderForm.querySelector('button[type="submit"]');
            const cancelButton = document.getElementById('cancelEdit');

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const sid = this.getAttribute('data-sid');
                    const stitle = this.getAttribute('data-stitle');
                    const stext = this.getAttribute('data-stext');
                    const simg = this.getAttribute('data-simg');
                    // const status = this.getAttribute('data-status'); // Status not needed for editing form fields

                    // Populate the form
                    formTitle.textContent = 'Edit Slider';
                    actionInput.value = 'update';
                    sidInput.value = sid;
                    stitleInput.value = stitle;
                    stextInput.value = stext;
                    existingSimgInput.value = simg; // Store existing image path

                    // Display current image preview
                    if (simg && simg !== 'No Image') { // Check if simg is not empty and not the default text
                        currentSimgPreview.src = simg;
                        currentSimgPreview.style.display = 'block';
                    } else {
                        currentSimgPreview.src = '';
                        currentSimgPreview.style.display = 'none';
                    }

                    submitButton.textContent = 'Update Slider';
                    cancelButton.style.display = 'inline-block'; // Show cancel button

                    // Scroll to the form
                    sliderForm.scrollIntoView({ behavior: 'smooth' });
                });
            });

            cancelButton.addEventListener('click', function() {
                // Reset the form to add mode
                formTitle.textContent = 'Add New Slider';
                actionInput.value = 'insert';
                sidInput.value = '';
                stitleInput.value = '';
                stextInput.value = '';
                simgInput.value = ''; // Clear the file input
                existingSimgInput.value = '';
                currentSimgPreview.src = '';
                currentSimgPreview.style.display = 'none';
                submitButton.textContent = 'Save Slider';
                cancelButton.style.display = 'none'; // Hide cancel button
            });
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderForm = document.getElementById('sliderForm');
        const actionInput = document.getElementById('action');
        const sidInput = document.getElementById('sid');
        const stitleInput = document.getElementById('stitle');
        const stextInput = document.getElementById('stext'); // The original textarea
        const simgInput = document.getElementById('simg');
        const existingSimgInput = document.getElementById('existing_simg');
        const currentSimgPreview = document.getElementById('current_simg_preview');
        const formTitle = document.querySelector('.form-container h3');
        const submitButton = sliderForm.querySelector('button[type=submit]');
        const cancelButton = document.getElementById('cancelEdit');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sid = this.getAttribute('data-sid');
                const stitle = this.getAttribute('data-stitle');
                const stext = this.getAttribute('data-stext'); // Get text data
                const simg = this.getAttribute('data-simg');

                // Populate the form
                formTitle.textContent = 'Edit Slider';
                actionInput.value = 'update';
                sidInput.value = sid;
                stitleInput.value = stitle;
                existingSimgInput.value = simg; // Store existing image path

                // *** FIX: Use TinyMCE API to set content ***
                if (tinymce.get('stext')) { // Check if the editor instance exists
                    tinymce.get('stext').setContent(stext || ''); // Set content, default to empty string if null/undefined
                } else {
                    // Fallback if TinyMCE isn't ready (should ideally not happen with DOMContentLoaded)
                    stextInput.value = stext;
                    console.error("TinyMCE editor 'stext' not found when trying to set content.");
                }
                // *** End FIX ***

                // Display current image preview
                if (simg && simg !== 'No Image') {
                    currentSimgPreview.src = simg;
                    currentSimgPreview.style.display = 'block';
                } else {
                    currentSimgPreview.src = '';
                    currentSimgPreview.style.display = 'none';
                }

                submitButton.textContent = 'Update Slider';
                cancelButton.style.display = 'inline-block'; // Show cancel button

                // Scroll to the form
                sliderForm.scrollIntoView({ behavior: 'smooth' });
            });
        });

        cancelButton.addEventListener('click', function() {
            // Reset the form to add mode
            formTitle.textContent = 'Add New Slider';
            actionInput.value = 'insert';
            sidInput.value = '';
            stitleInput.value = '';
            simgInput.value = ''; // Clear the file input
            existingSimgInput.value = '';
            currentSimgPreview.src = '';
            currentSimgPreview.style.display = 'none';
            submitButton.textContent = 'Save Slider';
            cancelButton.style.display = 'none'; // Hide cancel button

            // *** FIX: Use TinyMCE API to clear content ***
            if (tinymce.get('stext')) {
                tinymce.get('stext').setContent('');
            } else {
                 stextInput.value = ''; // Fallback
                 console.error("TinyMCE editor 'stext' not found when trying to clear content.");
            }
             // *** End FIX ***
        });
    });
</script>
</body>
</html>
<?php
// Close the database connection at the end
$conn->close();
?>