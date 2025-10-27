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
    display: flex;
    margin-right: auto;
    background-color: #f0f2f5; /* A slightly softer background color */
}

/* Adjust main content area to account for the fixed sidebar */
.page-container {
    margin-left: 300px;
    margin-right:100px;
    padding: 20px;
    width: calc(100% - 400px);
    min-width: 800px;
    height:100%;
    box-sizing: border-box;
    max-width: none;
    margin-top: 10px;
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-top: 5px solid var(--primary-color);
    position: relative;
    flex-grow: 1;
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
    --border-radius: 0.4rem;
    --box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 600;
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

/* --- NEW MODERN CARD VIEW STYLES FOR REVIEWS --- */
.review-card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); /* Responsive grid */
    gap: 20px;
    margin-top: 20px;
}

.review-card {
    background-color: #fff;
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden; /* Ensures content respects border radius */
}

.review-card-header {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e9ecef;
}
.review-card-header .user-name {
    font-weight: 600;
    color: var(--dark-color);
}
.review-card-header .rating {
    background-color: var(--warning-color);
    color: #333;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: bold;
}

.review-card-body {
    padding: 15px;
    color: #555;
    font-size: 0.95em;
    line-height: 1.6;
    flex-grow: 1; /* Allows body to take up available space */
}

.review-card-footer {
    padding: 10px 15px;
    background-color: var(--light-color);
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8em;
    color: var(--secondary-color);
}

.status-indicator {
    padding: 4px 10px;
    border-radius: 5px;
    font-weight: bold;
}
.status-indicator.approved { background-color: #d4edda; color: #155724; }
.status-indicator.pending { background-color: #fff3cd; color: #856404; }

.action-buttons a {
    text-decoration: none;
    padding: 6px 12px;
    border-radius: var(--border-radius);
    color: white;
    font-size: 0.9em;
    margin-left: 5px;
    transition: opacity 0.2s ease;
}
.action-buttons a:hover {
    opacity: 0.85;
}
.btn-approve { background-color: var(--success-color); }
.btn-delete { background-color: var(--danger-color); }

</style>

<?php include('include/sidebar.php'); ?>

<div class="page-container">
    <h2>Review Management</h2>

<?php
include('../include/config.php');

// --- Message Variables ---
$message = '';
$error = '';

// This block checks if the page was accessed by clicking a link (a GET request)
// and if the 'action' and 'id' parameters are present in the URL.
// This is how you process the "Approve" and "Delete" actions.
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $review_id = intval($_GET['id']);
    $action = $_GET['action'];

    $conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        $error = "Database Connection failed: " . $conn->connect_error;
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
                }
                $stmt->close();
            }
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $review_id);
                if ($stmt->execute()) {
                    $message = "Review deleted successfully.";
                } else {
                    $error = "Error deleting review: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $conn->close();
    }
}

// ========================================================================
// FETCH ALL REVIEWS - This part runs every time the page loads
// ========================================================================
$reviews = [];
$conn_select = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if ($conn_select->connect_error) {
    $error = "DB Connect Error (fetch reviews): " . $conn_select->connect_error;
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
    }
    $conn_select->close();
}
// THE EXTRA '}' WAS HERE. IT HAS BEEN REMOVED.
?>

    <?php if ($message): ?>
        <div class="message-area success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message-area error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="review-card-container">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-card-header">
                        <span class="user-name"><?php echo htmlspecialchars($review['user_name']); ?></span>
                        <span class="rating">⭐ <?php echo htmlspecialchars($review['rating']); ?></span>
                    </div>
                    <div class="review-card-body">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </div>
                    <div class="review-card-footer">
                        <div class="meta-info">
                            <span class="status-indicator <?php echo $review['approved'] ? 'approved' : 'pending'; ?>">
                                <?php echo $review['approved'] ? 'Approved' : 'Pending'; ?>
                            </span>
                            <span class="date" style="margin-left: 10px;">
                                <?php echo (new DateTime($review['created_at']))->format('Y-m-d'); ?>
                            </span>
                        </div>
                        <div class="action-buttons">
                            <?php if (!$review['approved']): ?>
                                <a href="?action=approve&id=<?php echo $review['id']; ?>" class="btn-approve" title="Approve">Approve</a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $review['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1;">No reviews found.</p>
        <?php endif; ?>
    </div>

</div> </body>
</html>