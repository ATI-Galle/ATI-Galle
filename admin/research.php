<?php
// --- HEADER AND SIDEBAR INCLUDES ---
include('include/header.php');
include('include/sidebar.php');

// --- DATABASE CONNECTION ---
include('include/config.php');

// --- PHP LOGIC (HANDLES ALL ACTIONS FOR PUBLICATIONS, AUTHORS, & CATEGORIES) ---
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $action = $_POST['action'] ?? '';

    // Use a transaction to ensure data integrity
    $conn->begin_transaction();
    try {
        // --- AUTHOR MANAGEMENT ACTIONS ---
        if (in_array($action, ['add_author', 'update_author', 'delete_author'])) {
            $author_name = $_POST['author_name'] ?? '';
            $author_id = $_POST['author_id'] ?? null;

            if ($action === 'add_author') {
                $stmt = $conn->prepare("INSERT INTO authors (author_name) VALUES (?)");
                $stmt->bind_param("s", $author_name);
                $stmt->execute();
                $message = "Author added successfully.";
                $message_type = 'success';
            }
            elseif ($action === 'update_author') {
                $stmt = $conn->prepare("UPDATE authors SET author_name = ? WHERE author_id = ?");
                $stmt->bind_param("si", $author_name, $author_id);
                $stmt->execute();
                $message = "Author updated successfully.";
                $message_type = 'success';
            }
            elseif ($action === 'delete_author') {
                $stmt = $conn->prepare("DELETE FROM authors WHERE author_id = ?");
                $stmt->bind_param("i", $author_id);
                $stmt->execute();
                $message = "Author deleted successfully.";
                $message_type = 'success';
            }
            if (isset($stmt)) $stmt->close();
        }

        // --- CATEGORY MANAGEMENT ACTIONS ---
        elseif (in_array($action, ['add_category', 'update_category', 'delete_category'])) {
            $category_name = $_POST['category_name'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            if ($action === 'add_category') {
                $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
                $stmt->bind_param("s", $category_name);
                $stmt->execute();
                $message = "Category added successfully.";
                $message_type = 'success';
            }
            elseif ($action === 'update_category') {
                $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
                $stmt->bind_param("si", $category_name, $category_id);
                $stmt->execute();
                $message = "Category updated successfully.";
                $message_type = 'success';
            }
            elseif ($action === 'delete_category') {
                // WARNING: Your DB schema has ON DELETE CASCADE for this.
                $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
                $stmt->bind_param("i", $category_id);
                $stmt->execute();
                $message = "Category and all associated publications deleted.";
                $message_type = 'success';
            }
             if (isset($stmt)) $stmt->close();
        }

        // --- PUBLICATION MANAGEMENT ACTIONS ---
        elseif (in_array($action, ['insert', 'update', 'delete'])) {
            $publication_id = $_POST['publication_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $conference_details = $_POST['conference_details'] ?? '';
            $publication_date = $_POST['publication_date'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $abstract = $_POST['abstract'] ?? '';
            $selected_authors = $_POST['authors'] ?? [];
            $existing_file = $_POST['existing_full_text_url'] ?? '';
            $full_text_url = $existing_file;

            // File Upload Logic
            $uploadOk = 1;
            if (isset($_FILES['full_text_file']) && $_FILES['full_text_file']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "uploads/publications/";
                if (!is_dir($target_dir)) { @mkdir($target_dir, 0755, true); }
                $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9\.\_\-]/", "_", basename($_FILES["full_text_file"]["name"]));
                $target_file = $target_dir . $file_name;
                $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if ($_FILES["full_text_file"]["size"] > 10000000) { $message = "File is too large (Max 10MB)."; $uploadOk = 0; }
                if ($fileType != "pdf") { $message = "Only PDF files are allowed."; $uploadOk = 0; }

                if ($uploadOk && move_uploaded_file($_FILES["full_text_file"]["tmp_name"], $target_file)) {
                    $full_text_url = $target_file;
                    if ($action === 'update' && !empty($existing_file) && file_exists($existing_file)) { @unlink($existing_file); }
                } elseif($uploadOk) { $message = "Error uploading your file."; $uploadOk = 0; }
                if ($uploadOk == 0) $message_type = 'error';
            }

            if ($uploadOk) {
                if ($action === 'insert') {
                    $stmt = $conn->prepare("INSERT INTO publications (title, conference_details, publication_date, category_id, abstract, full_text_url) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssiis", $title, $conference_details, $publication_date, $category_id, $abstract, $full_text_url);
                    $stmt->execute();
                    $new_publication_id = $conn->insert_id;
                    $stmt_authors = $conn->prepare("INSERT INTO publication_authors (publication_id, author_id) VALUES (?, ?)");
                    foreach ($selected_authors as $author_id) {
                        $stmt_authors->bind_param("ii", $new_publication_id, $author_id);
                        $stmt_authors->execute();
                    }
                    $message = "Publication added successfully."; $message_type = 'success';
                }
                elseif ($action === 'update') {
                    $stmt = $conn->prepare("UPDATE publications SET title=?, conference_details=?, publication_date=?, category_id=?, abstract=?, full_text_url=? WHERE publication_id=?");
                    $stmt->bind_param("sssiisi", $title, $conference_details, $publication_date, $category_id, $abstract, $full_text_url, $publication_id);
                    $stmt->execute();
                    
                    // Sync authors
                    $conn->query("DELETE FROM publication_authors WHERE publication_id = $publication_id");
                    $stmt_authors = $conn->prepare("INSERT INTO publication_authors (publication_id, author_id) VALUES (?, ?)");
                    foreach ($selected_authors as $author_id) {
                        $stmt_authors->bind_param("ii", $publication_id, $author_id);
                        $stmt_authors->execute();
                    }
                    $message = "Publication updated successfully."; $message_type = 'success';
                }
                elseif ($action === 'delete') {
                    $stmt = $conn->prepare("DELETE FROM publications WHERE publication_id = ?");
                    $stmt->bind_param("i", $publication_id);
                    $stmt->execute();
                    $message = "Publication deleted."; $message_type = 'success';
                }
            } else {
                 if(empty($message)) $message = "File upload error.";
                 throw new Exception($message);
            }
        }
        $conn->commit(); // If all good, commit changes
    } catch (Exception $e) {
        $conn->rollback(); // If error, rollback
        $message = "Database Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// --- Fetch Data for Display ---
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetch_all(MYSQLI_ASSOC);
$authors = $conn->query("SELECT * FROM authors ORDER BY author_name ASC")->fetch_all(MYSQLI_ASSOC);
$publications_sql = "SELECT p.*, c.category_name, 
                    GROUP_CONCAT(DISTINCT a.author_name ORDER BY a.author_name SEPARATOR ', ') AS author_names,
                    GROUP_CONCAT(DISTINCT a.author_id) AS author_ids
                    FROM publications p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN publication_authors pa ON p.publication_id = pa.publication_id
                    LEFT JOIN authors a ON pa.author_id = a.author_id
                    GROUP BY p.publication_id ORDER BY p.publication_date DESC";
$publications = $conn->query($publications_sql)->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* --- MODERN UI STYLES --- */
:root {
    --primary-color: #007bff; --secondary-color: #6c757d; --success-color: #28a745; --danger-color: #dc3545;
    --warning-color: #ffc107; --light-color: #f8f9fa; --dark-color: #343a40; --border-radius: 0.5rem;
    --shadow: 0 4px 12px rgba(0,0,0,0.08);
}
body { display: flex; font-family: 'Poppins', sans-serif; /* A nice modern font */ }
.page-container { margin-left: 265px; padding: 25px; width: calc(100% - 265px); flex-grow: 1; }
h2 { color: var(--dark-color); text-align: left; margin-bottom: 2rem; font-weight: 600; }
.message-area { padding: 1rem; margin-bottom: 1.5rem; border-radius: var(--border-radius); border: 1px solid transparent; }
.message-area.success { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; }
.message-area.error { background-color: #f8d7da; color: #842029; border-color: #f5c2c7; }

/* Main layout grid */
.management-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2-column grid for manage cards */
    gap: 30px;
}
.card { background: #fff; padding: 25px; border-radius: var(--border-radius); box-shadow: var(--shadow); }
.card h3 { margin-top: 0; color: var(--primary-color); border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem; }

/* New layout classes */
.publication-form-card {
    grid-column: 1 / -1; /* Make publication form span full width */
}
.publications-section {
    grid-column: 1 / -1; /* Make list span full width */
    margin-top: 2rem;
}

/* Responsive layout */
@media (max-width: 992px) {
    .management-grid {
        grid-template-columns: 1fr; /* Stack everything on tablets and mobile */
    }
}

/* Form styles */
.form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
.form-group input, .form-group textarea, .form-group select {
    width: 100%; padding: 12px; margin-bottom: 1rem; border: 1px solid #ccc;
    border-radius: var(--border-radius); box-sizing: border-box; font-size: 0.95rem; transition: border-color 0.3s;
}
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--primary-color); outline: none; }
.button-group { display: flex; gap: 10px; margin-top: 1rem; }
.btn {
    padding: 12px 25px; border: none; border-radius: var(--border-radius); cursor: pointer;
    font-size: 1rem; font-weight: 500; transition: background-color 0.3s ease; color: white;
}
.btn-primary { background-color: var(--primary-color); }
.btn-primary:hover { background-color: #0056b3; }
.btn-secondary { background-color: var(--secondary-color); }
.btn-secondary:hover { background-color: #5a6268; }

/* List Styles (used for authors and categories) */
.item-list ul { list-style: none; padding: 0; margin: 0; max-height: 250px; overflow-y: auto; }
.item-list li { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f0f0f0; }
.item-list li:last-child { border-bottom: none; }
.item-list .actions { display: flex; gap: 10px; }
.icon-btn { background: none; border: none; cursor: pointer; font-size: 1rem; padding: 5px; transition: color 0.3s; }
.icon-btn.edit { color: var(--warning-color); }
.icon-btn.edit:hover { color: #d99c00; }
.icon-btn.delete { color: var(--danger-color); }
.icon-btn.delete:hover { color: #b02a37; }

/* Publication Card View */
.publication-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
.publication-card {
    background-color: #fff; border-radius: var(--border-radius); box-shadow: var(--shadow);
    display: flex; flex-direction: column; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;
}
.publication-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
.publication-card-body { padding: 20px; flex-grow: 1; }
.publication-card h4 { margin: 0 0 10px 0; color: var(--dark-color); font-size: 1.1rem; }
.publication-card .meta { font-size: 0.85rem; color: #777; margin-bottom: 15px; }
.publication-card .meta span { display: block; margin-bottom: 5px; }
.publication-card .meta i { margin-right: 8px; color: var(--primary-color); }
.publication-card-footer {
    display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px;
    background-color: var(--light-color); border-top: 1px solid #eee;
}
.btn-sm { padding: 8px 15px; font-size: 0.9rem; }
</style>

<div class="page-container">
    <h2>Research Management</h2>

    <?php if (!empty($message)): ?>
        <div class="message-area <?php echo htmlspecialchars($message_type); ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="management-grid">
        <div class="card publication-form-card">
            <h3 id="formTitle">Add/Edit Publication</h3>
            <form id="publicationForm" action="research.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="insert">
                <input type="hidden" name="publication_id" id="publication_id">
                <input type="hidden" name="existing_full_text_url" id="existing_full_text_url">

                <div class="form-group"><label for="title">Title:</label><input type="text" id="title" name="title" required></div>
                <div class="form-group"><label for="authors">Authors:</label><select id="authors" name="authors[]" multiple required size="5"><?php foreach ($authors as $author): ?><option value="<?php echo $author['author_id']; ?>"><?php echo htmlspecialchars($author['author_name']); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label for="category_id">Category:</label><select id="category_id" name="category_id" required><option value="">-- Select --</option><?php foreach ($categories as $cat): ?><option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label for="publication_date">Date:</label><input type="date" id="publication_date" name="publication_date" required></div>
                <div class="form-group"><label for="conference_details">Conference/Journal:</label><textarea id="conference_details" name="conference_details" rows="2"></textarea></div>
                <div class="form-group"><label for="full_text_file">Full Text PDF:</label><input type="file" id="full_text_file" name="full_text_file" accept=".pdf"></div>
                <div id="current_file_link_container" style="margin-top: -10px; margin-bottom: 15px; display: none;">Current File: <a href="" id="current_file_link" target="_blank"></a></div>

                <div class="button-group">
                    <button type="submit" id="submitButton" class="btn btn-primary">Save Publication</button>
                    <button type="button" id="cancelEdit" class="btn btn-secondary" style="display: none;">Cancel</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h3 id="authorFormTitle">Manage Authors</h3>
            <form id="authorForm" action="research.php" method="POST">
                <input type="hidden" name="action" id="author_action" value="add_author">
                <input type="hidden" name="author_id" id="author_id">
                <div class="form-group"><label for="author_name">Author Name:</label><input type="text" id="author_name" name="author_name" required></div>
                <div class="button-group">
                    <button type="submit" id="authorSubmitButton" class="btn btn-primary">Add Author</button>
                    <button type="button" id="cancelAuthorEdit" class="btn btn-secondary" style="display: none;">Cancel</button>
                </div>
            </form>
            <hr style="margin: 2rem 0;">
            <div class="item-list">
                <ul>
                    <?php if(empty($authors)): ?>
                         <li>No authors found.</li>
                    <?php else: ?>
                        <?php foreach ($authors as $author): ?>
                        <li>
                            <span><?php echo htmlspecialchars($author['author_name']); ?></span>
                            <div class="actions">
                                <button type="button" class="icon-btn edit author-edit-btn" data-id="<?php echo $author['author_id']; ?>" data-name="<?php echo htmlspecialchars($author['author_name']); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <form action="research.php" method="POST" onsubmit="return confirm('Delete this author?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_author">
                                    <input type="hidden" name="author_id" value="<?php echo $author['author_id']; ?>">
                                    <button type="submit" class="icon-btn delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card">
            <h3 id="categoryFormTitle">Manage Categories</h3>
            <form id="categoryForm" action="research.php" method="POST">
                <input type="hidden" name="action" id="category_action" value="add_category">
                <input type="hidden" name="category_id" id="form_category_id">
                <div class="form-group"><label for="category_name">Category Name:</label><input type="text" id="category_name" name="category_name" required></div>
                <div class="button-group">
                    <button type="submit" id="categorySubmitButton" class="btn btn-primary">Add Category</button>
                    <button type="button" id="cancelCategoryEdit" class="btn btn-secondary" style="display: none;">Cancel</button>
                </div>
            </form>
            <hr style="margin: 2rem 0;">
            <div class="item-list">
                <ul>
                    <?php if(empty($categories)): ?>
                         <li>No categories found.</li>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <li>
                            <span><?php echo htmlspecialchars($cat['category_name']); ?></span>
                            <div class="actions">
                                <button type="button" class="icon-btn edit category-edit-btn" data-id="<?php echo $cat['category_id']; ?>" data-name="<?php echo htmlspecialchars($cat['category_name']); ?>"><i class="fas fa-pencil-alt"></i></button>
                                <form action="research.php" method="POST" onsubmit="return confirm('WARNING:\nDeleting this category will ALSO DELETE all publications associated with it.\n\nAre you sure you want to proceed?');" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                                    <button type="submit" class="icon-btn delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>


        <div class="publications-section">
            <h3>Existing Publications</h3>
            <div class="publication-list">
                <?php if (empty($publications)): ?>
                    <p>No publications found. Add one using the form above.</p>
                <?php else: ?>
                    <?php foreach ($publications as $pub): ?>
                    <div class="publication-card">
                        <div class="publication-card-body">
                            <h4><?php echo htmlspecialchars($pub['title']); ?></h4>
                            <div class="meta">
                                <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($pub['author_names'] ?? 'N/A'); ?></span>
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($pub['category_name'] ?? 'N/A'); ?></span>
                                <span><i class="fas fa-calendar-alt"></i> <?php echo date("F j, Y", strtotime($pub['publication_date'])); ?></span>
                            </div>
                        </div>
                        <div class="publication-card-footer">
                             <?php if (!empty($pub['full_text_url'])): ?>
                                <a href="<?php echo htmlspecialchars($pub['full_text_url']); ?>" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-file-pdf"></i> View PDF</a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-primary edit-publication-btn" data-publication='<?php echo htmlspecialchars(json_encode($pub), ENT_QUOTES, 'UTF-8'); ?>'><i class="fas fa-edit"></i> Edit</button>
                            <form action="research.php" method="POST" onsubmit="return confirm('Delete this publication?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="publication_id" value="<?php echo $pub['publication_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- PUBLICATION FORM LOGIC ---
    const pubForm = document.getElementById('publicationForm');
    const pubFormTitle = document.getElementById('formTitle');
    const pubSubmitBtn = document.getElementById('submitButton');
    const pubCancelBtn = document.getElementById('cancelEdit');
    const authorsSelect = document.getElementById('authors');
    const fileLinkContainer = document.getElementById('current_file_link_container');
    const fileLink = document.getElementById('current_file_link');
    
    function resetPublicationForm() {
        pubForm.reset();
        document.getElementById('action').value = 'insert';
        document.getElementById('publication_id').value = '';
        pubFormTitle.textContent = 'Add/Edit Publication';
        pubSubmitBtn.textContent = 'Save Publication';
        pubCancelBtn.style.display = 'none';
        fileLinkContainer.style.display = 'none';
        Array.from(authorsSelect.options).forEach(opt => opt.selected = false);
    }

    document.querySelectorAll('.edit-publication-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pubData = JSON.parse(this.dataset.publication);
            
            pubFormTitle.textContent = 'Edit Publication';
            pubSubmitBtn.textContent = 'Update Publication';
            pubCancelBtn.style.display = 'inline-block';
            
            document.getElementById('action').value = 'update';
            document.getElementById('publication_id').value = pubData.publication_id;
            document.getElementById('title').value = pubData.title;
            document.getElementById('publication_date').value = pubData.publication_date;
            document.getElementById('category_id').value = pubData.category_id;
            document.getElementById('conference_details').value = pubData.conference_details || '';
            document.getElementById('existing_full_text_url').value = pubData.full_text_url;
            
            // Set authors
            Array.from(authorsSelect.options).forEach(opt => opt.selected = false);
            if (pubData.author_ids) {
                pubData.author_ids.split(',').forEach(id => {
                    const option = authorsSelect.querySelector(`option[value="${id.trim()}"]`);
                    if (option) option.selected = true;
                });
            }
            
            // Show current file
            if (pubData.full_text_url) {
                fileLink.href = pubData.full_text_url;
                fileLink.textContent = pubData.full_text_url.split('/').pop();
                fileLinkContainer.style.display = 'block';
            } else {
                fileLinkContainer.style.display = 'none';
            }

            pubForm.scrollIntoView({ behavior: 'smooth' });
        });
    });
    pubCancelBtn.addEventListener('click', resetPublicationForm);

    // --- AUTHOR FORM LOGIC ---
    const authorForm = document.getElementById('authorForm');
    const authorSubmitBtn = document.getElementById('authorSubmitButton');
    const authorCancelBtn = document.getElementById('cancelAuthorEdit');

    function resetAuthorForm() {
        authorForm.reset();
        document.getElementById('author_action').value = 'add_author';
        document.getElementById('author_id').value = '';
        authorSubmitBtn.textContent = 'Add Author';
        authorCancelBtn.style.display = 'none';
    }

    document.querySelectorAll('.author-edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('author_action').value = 'update_author';
            document.getElementById('author_id').value = this.dataset.id;
            document.getElementById('author_name').value = this.dataset.name;
            authorSubmitBtn.textContent = 'Update Author';
            authorCancelBtn.style.display = 'inline-block';
            authorForm.scrollIntoView({ behavior: 'smooth' });
        });
    });
    authorCancelBtn.addEventListener('click', resetAuthorForm);

    // --- CATEGORY FORM LOGIC ---
    const categoryForm = document.getElementById('categoryForm');
    const categorySubmitBtn = document.getElementById('categorySubmitButton');
    const categoryCancelBtn = document.getElementById('cancelCategoryEdit');

    function resetCategoryForm() {
        categoryForm.reset();
        document.getElementById('category_action').value = 'add_category';
        document.getElementById('form_category_id').value = '';
        categorySubmitBtn.textContent = 'Add Category';
        categoryCancelBtn.style.display = 'none';
    }

    document.querySelectorAll('.category-edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('category_action').value = 'update_category';
            document.getElementById('form_category_id').value = this.dataset.id;
            document.getElementById('category_name').value = this.dataset.name;
            categorySubmitBtn.textContent = 'Update Category';
            categoryCancelBtn.style.display = 'inline-block';
            categoryForm.scrollIntoView({ behavior: 'smooth' });
        });
    });
    categoryCancelBtn.addEventListener('click', resetCategoryForm);
});
</script>