<?php
include('include/header.php');
include('include/sidebar.php');
?>

<style>
/* --- Using the exact same CSS as your provided template --- */
/* --- Basic Layout for Fixed Sidebar --- */
body { display: flex; margin-right: auto; }
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
h2, h3 { color: var(--primary-color); margin-bottom: 1.5rem; text-align: center; font-weight: 600; }
h3 { color: var(--dark-color); margin-top: 2rem; margin-bottom: 1rem; text-align: left; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
.message-area { padding: 12px 18px; margin-bottom: 25px; border-radius: var(--border-radius); border: 1px solid transparent; font-size: 0.95rem; text-align: center; }
.message-area.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
.message-area.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.form-section { margin-bottom: 30px; padding: 25px; background-color: var(--light-color); border-radius: var(--border-radius); border: 1px solid #ddd; }
.form-section label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
.form-section input[type="text"], .form-section input[type="date"], .form-section input[type="datetime-local"], .form-section input[type="file"], .form-section select, .form-section textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: var(--border-radius); box-sizing: border-box; font-size: 1rem; }
.form-section input:read-only { background-color: #e9ecef; cursor: not-allowed; }
.form-section .button-group { margin-top: 15px; }
.form-section button { background-color: var(--primary-color); color: white; padding: 12px 25px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 1rem; transition: background-color 0.3s ease; margin-right: 10px; }
.form-section button:hover { background-color: #0056b3; }
.form-section button#cancelEditBtn { background-color: var(--secondary-color); }
.form-section button#cancelEditBtn:hover { background-color: #5a6268; }
.data-table-section { margin-top: 30px; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0 1px 5px rgba(0,0,0,0.08); }
.data-table th, .data-table td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: left; vertical-align: middle; }
.data-table th { background-color: #f2f5f8; font-weight: 600; color: #333; white-space: nowrap; }
.data-table tbody tr:nth-child(even) { background-color: var(--light-color); }
.data-table tbody tr:hover { background-color: #e9ecef; }
.data-table .actions-cell { white-space: nowrap; min-width: 180px; }
.data-table .actions-cell form { display: inline-block; margin-right: 5px; }
.data-table .actions-cell button, .data-table .actions-cell .edit-btn { padding: 6px 12px; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 0.85rem; color: white; transition: background-color 0.2s ease; text-decoration: none; }
.data-table .actions-cell .edit-btn { background-color: var(--warning-color); color: #333; }
.data-table .actions-cell button[value='delete'] { background-color: var(--danger-color); }
.two-column-layout { display: flex; gap: 20px; flex-wrap: wrap; }
.two-column-layout > div { flex: 1; min-width: 300px; }
</style>

<div class="page-container">
<?php
// --- Database Connection ---
include ('include/config.php');

// Enable error reporting to see the exact problem
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = '';
$message_type = '';
$upload_dir = "uploads/tenders/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- Form Submission Logic ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $tender_id = $_POST['tender_id'] ?? null;
    $reference_no = $_POST['reference_no'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $published_date = $_POST['published_date'] ?? null;
    $closing_date = $_POST['closing_date'] ?? null;
    $status = $_POST['status'] ?? 'Open';
    $existing_document = $_POST['existing_document'] ?? '';
    $document_path = $existing_document;

    // --- Document Upload Handling ---
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        if (!empty($existing_document) && file_exists($existing_document)) {
            @unlink($existing_document);
        }
        $file_name = preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES["document"]["name"]));
        $target_file = $upload_dir . time() . "_" . $file_name;
        if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_file)) {
            $document_path = $target_file;
        } else {
            $message = "Error uploading document.";
            $message_type = 'error';
        }
    }

    try {
        if ($message_type !== 'error') {
            switch ($action) {
                case 'insert':
                    $stmt_check = $conn->prepare("SELECT tender_id FROM tenders WHERE reference_no = ?");
                    $stmt_check->bind_param("s", $reference_no);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    if ($stmt_check->num_rows > 0) {
                        throw new Exception("Reference No '$reference_no' already exists.");
                    }
                    $stmt = $conn->prepare("INSERT INTO tenders (reference_no, title, description, category, published_date, closing_date, document_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $reference_no, $title, $description, $category, $published_date, $closing_date, $document_path, $status);
                    $stmt->execute();
                    $message = "New tender created successfully.";
                    $message_type = 'success';
                    break;
                
                case 'update':
                    $stmt = $conn->prepare("UPDATE tenders SET reference_no=?, title=?, description=?, category=?, published_date=?, closing_date=?, document_path=?, status=? WHERE tender_id=?");
                    $stmt->bind_param("ssssssssi", $reference_no, $title, $description, $category, $published_date, $closing_date, $document_path, $status, $tender_id);
                    $stmt->execute();
                    $message = "Tender updated successfully.";
                    $message_type = 'success';
                    break;
                
                case 'delete':
                    $stmt_doc = $conn->prepare("SELECT document_path FROM tenders WHERE tender_id = ?");
                    $stmt_doc->bind_param("i", $tender_id);
                    $stmt_doc->execute();
                    $stmt_doc->bind_result($doc_to_delete);
                    $stmt_doc->fetch();
                    $stmt_doc->close();

                    if (!empty($doc_to_delete) && file_exists($doc_to_delete)) {
                        @unlink($doc_to_delete);
                    }

                    $stmt_del = $conn->prepare("DELETE FROM tenders WHERE tender_id = ?");
                    $stmt_del->bind_param("i", $tender_id);
                    $stmt_del->execute();
                    $message = "Tender deleted successfully.";
                    $message_type = 'success';
                    break;
            }
        }
    } catch (Exception $e) {
        $message = "An error occurred: " . $e->getMessage();
        $message_type = 'error';
    }
}

// --- Fetch Existing Tenders ---
$tenders = [];
$result = $conn->query("SELECT * FROM tenders ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tenders[] = $row;
    }
}
?>

<h2>Tender Management</h2>

<?php if (!empty($message)): ?>
    <div class="message-area <?php echo htmlspecialchars($message_type); ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="form-section">
    <h3 id="formTitle">Add New Tender</h3>
    <form id="tenderForm" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="action" value="insert">
        <input type="hidden" name="tender_id" id="tender_id">
        <input type="hidden" name="existing_document" id="existing_document">
        
        <div class="two-column-layout">
            <div>
                <label for="reference_no">Reference No:</label>
                <input type="text" id="reference_no" name="reference_no" required>
            </div>
            <div>
                <label for="title">Tender Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
        </div>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4"></textarea>

        <div class="two-column-layout">
            <div>
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" placeholder="e.g., Procurement, Construction">
            </div>
            <div>
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                    <option value="Awarded">Awarded</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        
        <div class="two-column-layout">
            <div>
                <label for="published_date">Published Date:</label>
                <input type="date" id="published_date" name="published_date" required>
            </div>
            <div>
                <label for="closing_date">Closing Date & Time:</label>
                <input type="datetime-local" id="closing_date" name="closing_date" required>
            </div>
        </div>

        <label for="document">Tender Document (PDF, DOCX, etc.):</label>
        <input type="file" id="document" name="document">
        <div id="current_document_link" style="margin-top: -10px; margin-bottom: 15px;"></div>

        <div class="button-group">
            <button type="submit" id="submitButton">Save Tender</button>
            <button type="button" id="cancelEditBtn" style="display: none;">Cancel Edit</button>
        </div>
    </form>
</div>

<hr>

<div class="data-table-section">
    <h3>Existing Tenders</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Ref No.</th>
                <th>Title</th>
                <th>Category</th>
                <th>Closing Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tenders)): ?>
                <tr><td colspan="6" style="text-align: center;">No tenders found.</td></tr>
            <?php else: ?>
                <?php foreach ($tenders as $tender): ?>
                <tr>
                    <td><?php echo htmlspecialchars($tender['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($tender['title']); ?></td>
                    <td><?php echo htmlspecialchars($tender['category']); ?></td>
                    <td><?php echo date("Y-m-d H:i", strtotime($tender['closing_date'])); ?></td>
                    <td><?php echo htmlspecialchars($tender['status']); ?></td>
                    <td class="actions-cell">
                        <button class="edit-btn"
                            data-tender='<?php echo htmlspecialchars(json_encode($tender), ENT_QUOTES, 'UTF-8'); ?>'>Edit</button>
                        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this tender?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="tender_id" value="<?php echo $tender['tender_id']; ?>">
                            <button type="submit" value="delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tenderForm');
    const formTitle = document.getElementById('formTitle');
    const actionInput = document.getElementById('action');
    const tenderIdInput = document.getElementById('tender_id');
    const submitButton = document.getElementById('submitButton');
    const cancelButton = document.getElementById('cancelEditBtn');
    const currentDocLink = document.getElementById('current_document_link');

    function resetForm() {
        form.reset();
        formTitle.textContent = 'Add New Tender';
        actionInput.value = 'insert';
        tenderIdInput.value = '';
        submitButton.textContent = 'Save Tender';
        cancelButton.style.display = 'none';
        document.getElementById('reference_no').readOnly = false;
        currentDocLink.innerHTML = '';
    }

    cancelButton.addEventListener('click', resetForm);

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-tender'));
            
            formTitle.textContent = 'Edit Tender';
            actionInput.value = 'update';
            tenderIdInput.value = data.tender_id;
            
            document.getElementById('reference_no').value = data.reference_no;
            // NOTE: reference_no is not set to readonly here to allow edits. If it should be locked, add the line below.
            // document.getElementById('reference_no').readOnly = true; 
            document.getElementById('title').value = data.title;
            document.getElementById('description').value = data.description;
            document.getElementById('category').value = data.category;
            document.getElementById('status').value = data.status;
            
            if (data.published_date) {
                document.getElementById('published_date').value = data.published_date;
            }
            if (data.closing_date) {
                const dt = new Date(data.closing_date);
                const localDateTime = dt.getFullYear() + '-' + ('0' + (dt.getMonth() + 1)).slice(-2) + '-' + ('0' + dt.getDate()).slice(-2) + 'T' + ('0' + dt.getHours()).slice(-2) + ':' + ('0' + dt.getMinutes()).slice(-2);
                document.getElementById('closing_date').value = localDateTime;
            }
            
            document.getElementById('existing_document').value = data.document_path;
            if (data.document_path) {
                currentDocLink.innerHTML = `Current Document: <a href="${data.document_path}" target="_blank">${data.document_path.split('/').pop()}</a>`;
            } else {
                currentDocLink.innerHTML = '';
            }

            submitButton.textContent = 'Update Tender';
            cancelButton.style.display = 'inline-block';
            form.scrollIntoView({ behavior: 'smooth' });
        });
    });
});
</script>