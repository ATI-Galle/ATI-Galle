<?php
// reviews_component.php - Designed to be included in another PHP file

// --- Prerequisites Check ---
// Ensure config.php is included by the parent script or include it here
// Make sure config.php establishes a $con MySQLi connection
if (!isset($con) || !($con instanceof mysqli) || $con->connect_error) {
    // Attempt to include config.php if it's not already done
    // Adjust path as necessary relative to the file including this component
    if (file_exists('config.php')) {
        include('config.php');
        if (!isset($con) || !($con instanceof mysqli) || $con->connect_error) {
            // Still no valid connection, handle gracefully
            error_log("Review Component Error: Database Connection failed after including config.php: " . ($con->connect_error ?? 'Unknown Error'));
            $message = "Error loading reviews: Database connection failed.";
            $message_type = 'error';
            $db_connected = false; // Flag to prevent further DB operations
        } else {
            $db_connected = true;
            // Set character set
            if (!$con->set_charset("utf8mb4")) {
                error_log("Review Component Error: Error loading character set utf8mb4: " . $con->error);
                // You might want to set a user message here too, but less critical
            }
        }
    } else {
        // config.php not found
        error_log("Review Component Error: config.php not found.");
        $message = "Error loading reviews: Configuration file not found.";
        $message_type = 'error';
        $db_connected = false; // Flag to prevent further DB operations
    }
} else {
    // Database connection already exists and is valid (presumably from parent script)
    $db_connected = true;
    // Optional: Ensure character set if parent script didn't
    if (!$con->set_charset("utf8mb4")) {
        error_log("Review Component Error: Error loading character set utf8mb4 (parent script connection): " . $con->error);
    }
}

// --- Determine the Item ID ---
// Assume the item ID comes from a GET or POST parameter, defaulting to 'general'.
// This should be robust to handle cases where no ID is passed.
$current_item_id = trim($_GET['item_id'] ?? $_POST['item_id'] ?? 'general');
// Sanitize for potential display or non-query use
$current_item_id = htmlspecialchars($current_item_id, ENT_QUOTES, 'UTF-8');

// --- Variables for Messages (handled within this component) ---
// Initialize variables if the parent script hasn't, or use parent's if they exist
$message = $message ?? '';
$message_type = $message_type ?? ''; // 'success' or 'error'

// --- Handle Review Submission ---
// Only process if DB is connected
if ($db_connected && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {

    // 1. Sanitize and Validate Input
    $user_name = trim($_POST['user_name'] ?? '');
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $review_text = trim($_POST['review_text'] ?? '');
    // Get item_id from the hidden form field, sanitize it just in case
    $submitted_item_id = htmlspecialchars(trim($_POST['item_id'] ?? 'general'), ENT_QUOTES, 'UTF-8');

    // Use the submitted item_id for this review insertion
    $item_id_to_insert = $submitted_item_id;

    // Basic Validation
    if (empty($user_name)) {
        $message = "Name is required.";
        $message_type = 'error';
    } elseif ($rating < 1 || $rating > 5) {
        $message = "Please select a rating between 1 and 5 stars.";
        $message_type = 'error';
    } elseif (empty($review_text)) {
        $message = "Review text is required.";
        $message_type = 'error';
    } else {
        // 2. Insert into Database
        // Use prepared statement to prevent SQL injection
        // Ensure your 'reviews' table has columns: item_id (VARCHAR), user_name (VARCHAR), rating (INT), review_text (TEXT), created_at (TIMESTAMP, default CURRENT_TIMESTAMP), approved (INT, default 0)
        // The `approved` column is assumed for moderation before display.
        $sql_insert = "INSERT INTO reviews (item_id, user_name, rating, review_text) VALUES (?, ?, ?, ?)";
        $stmt_insert = $con->prepare($sql_insert);

        if ($stmt_insert === false) {
            error_log("Review Component Error: Error preparing insert statement: " . $con->error);
            $message = "Database error: Could not prepare statement for insertion.";
            $message_type = 'error';
        } else {
            // Bind parameters: s = string, i = integer
            // Use the potentially sanitized submitted_item_id for insertion
            $bind_item_id = $submitted_item_id; // Use the sanitized ID from POST
            $bind_user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); // Sanitize name again for DB
            $bind_review_text = htmlspecialchars($review_text, ENT_QUOTES, 'UTF-8'); // Sanitize text again for DB

            $stmt_insert->bind_param("ssis", $bind_item_id, $bind_user_name, $rating, $bind_review_text);

            if ($stmt_insert->execute()) {
                // Success - Set success message and clear form data
                $message = "Your review has been submitted successfully! It will appear after moderation.";
                $message_type = 'success';
                // Clear submitted form values after success to prevent form resubmission and data persistence
                unset($_POST['user_name'], $_POST['rating'], $_POST['review_text']);

            } else {
                // Error executing insert
                error_log("Review Component Error: Error executing insert statement: " . $stmt_insert->error);
                $message = "Database error: Could not save your review. Please try again.";
                $message_type = 'error';
                // Keep submitted form values so user doesn't lose input in case of error
            }
            $stmt_insert->close(); // Close statement
        }
    }
    // If there was a validation or database error during insert,
    // the script continues to the HTML output section below, displaying the error message and form.
}


// --- Fetch Existing Reviews ---
$reviews = [];
$average_rating = 0;
$total_reviews = 0;

// Only fetch if DB is connected
if ($db_connected) {
    // Fetch approved reviews for the specific item_id, ordered by creation date (latest first)
    $sql_fetch = "SELECT user_name, rating, review_text, created_at FROM reviews WHERE approved = 1 AND item_id = ? ORDER BY created_at DESC";

    $stmt_fetch = $con->prepare($sql_fetch);

    if ($stmt_fetch === false) {
        error_log("Review Component Error: Error preparing fetch statement: " . $con->error);
        // Optionally set a user-facing error message:
        // $message = "Error loading reviews."; // Could overwrite previous error message
        // $message_type = 'error';
    } else {
        // Bind the item_id parameter for fetching
        $stmt_fetch->bind_param("s", $current_item_id); // Use the item_id determined at the start

        if ($stmt_fetch->execute()) {
            $result_fetch = $stmt_fetch->get_result();

            if ($result_fetch) {
                while ($row = $result_fetch->fetch_assoc()) {
                    $reviews[] = $row;
                }
                $result_fetch->free(); // Free result set
            } else {
                // This means the query executed but returned no rows, not an error.
                // error_log("Review Component Debug: Fetch statement returned no result set (0 reviews).");
            }
        } else {
            error_log("Review Component Error: Error executing fetch statement: " . $stmt_fetch->error);
            // Optionally set a user-facing error message:
            // $message = "Error loading reviews."; // Could overwrite previous error message
            // $message_type = 'error';
        }
        $stmt_fetch->close();
    }


    // --- Calculate Average Rating ---
    $total_reviews = count($reviews); // Count of *displayed* reviews (filtered by item_id and approved = 1)
    $sum_ratings = 0;

    if ($total_reviews > 0) {
        foreach($reviews as $review) {
            $sum_ratings += $review['rating'];
        }
        $average_rating = round($sum_ratings / $total_reviews, 1); // Round to 1 decimal place
    }
} else {
    // DB not connected, $reviews remains empty, $total_reviews remains 0, $average_rating remains 0
    // The connection error message will be displayed.
}


// --- HTML Output ---
// The following HTML will be rendered inline where this file is included.
?>

<div class="container my-4">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-8 reviews-list-column">
            <?php if ($total_reviews > 0): ?>
                <div class="average-rating-section p-3 mb-4 bg-light rounded text-center">
                    <h3>Overall Rating <?php echo !empty($current_item_id) && $current_item_id !== 'general' ? 'for ' . htmlspecialchars($current_item_id, ENT_QUOTES, 'UTF-8') : ''; ?></h3>
                    <span class="avg-score fs-4 fw-bold"><?php echo $average_rating; ?></span> / 5
                    <div class="display-rating mb-2">
                        <?php
                        // Display average stars
                        $avg_rating_rounded = round($average_rating);
                        for ($i = 0; $i < 5; $i++) {
                            if ($i < $avg_rating_rounded) {
                                echo '<i class="fas fa-star"></i>'; // Filled star
                            } else {
                                echo '<i class="far fa-star"></i>'; // Empty star
                            }
                        }
                        ?>
                    </div>
                    <p class="mb-0">(Based on <?php echo $total_reviews; ?> reviews)</p>
                </div>
            <?php endif; ?>

            <h2 class="mb-4">User Reviews <?php echo !empty($current_item_id) && $current_item_id !== 'general' ? 'for ' . htmlspecialchars($current_item_id, ENT_QUOTES, 'UTF-8') : ''; ?></h2>
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): // Loop through all reviews fetched ?>
                    <div class="review-item border p-3 mb-4 rounded shadow-sm">
                        <div class="display-rating mb-2">
                            <?php
                            // Display stars based on individual review rating
                            $rating = intval($review['rating']);
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $rating) {
                                    echo '<i class="fas fa-star"></i>'; // Filled star
                                } else {
                                    echo '<i class="far fa-star"></i>'; // Empty star
                                }
                            }
                            ?>
                        </div>
                        <div class="review-content">
                            <span class="quote-icon">&ldquo;</span>
                            <span class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8')); ?></span>
                        </div>
                        <div class="user-name fw-bold mt-2"><?php echo htmlspecialchars($review['user_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <small class="text-muted"><?php echo (new DateTime($review['created_at']))->format('Y-m-d'); ?></small>
                    </div>
                <?php endforeach; ?>

            <?php elseif (!$db_connected): ?>
                <p class="text-danger">Cannot display reviews due to a database error.</p>
            <?php else: ?>
                <p>No reviews yet<?php echo !empty($current_item_id) && $current_item_id !== 'general' ? ' for this item' : ''; ?>. Be the first to leave one!</p>
            <?php endif; ?>
        </div>

        <div class="col-md-4 review-form-column p-4 rounded">
            <h2 class="text-center mb-4">RATE US</h2>
            <?php if ($db_connected): // Only show form if DB is connected ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($current_item_id, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="mb-3">
                        <label class="form-label">Your Rating:</label>
                        <div class="star-rating">
                            <input type="radio" id="form-star5" name="rating" value="5" required <?php echo (isset($_POST['rating']) && $_POST['rating'] == 5 && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type != 'success') ? 'checked' : ''; ?>>
                            <label for="form-star5" title="5 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="form-star4" name="rating" value="4" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 4 && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type != 'success') ? 'checked' : ''; ?>>
                            <label for="form-star4" title="4 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="form-star3" name="rating" value="3" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 3 && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type != 'success') ? 'checked' : ''; ?>>
                            <label for="form-star3" title="3 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="form-star2" name="rating" value="2" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 2 && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type != 'success') ? 'checked' : ''; ?>>
                            <label for="form-star2" title="2 stars"><i class="fas fa-star"></i></label>

                            <input type="radio" id="form-star1" name="rating" value="1" <?php echo (isset($_POST['rating']) && $_POST['rating'] == 1 && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type != 'success') ? 'checked' : ''; ?>>
                            <label for="form-star1" title="1 star"><i class="fas fa-star"></i></label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="user_name" class="form-label">enter your Name</label>
                        <input type="text" class="form-control" id="user_name" name="user_name" required value="<?php echo htmlspecialchars($_POST['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="review_text" class="form-label">Enter Your Review</label>
                        <textarea class="form-control" id="review_text" name="review_text" rows="3" required><?php echo htmlspecialchars($_POST['review_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <input type="submit" name="submit_review" value="Submit" class="btn btn-warning w-100">
                </form>
            <?php else: ?>
                <p class="text-danger">Review submission is unavailable due to a database error.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
    /* Custom CSS - Minimal, mainly for star rating interaction and quote icon */

    /* Ensure body styles from main page aren't overridden unless necessary */
    /* body {
        background-color: #f4f4f4; /* Keep light background *//* } */

    /* Specific Star Rating styles (for the form) */
    .review-form-column .star-rating { /* Scope to form column */
        display: inline-block;
        direction: rtl; /* Right-to-left for star selection */
        margin-bottom: 1rem; /* Use Bootstrap spacing unit */
    }
    .review-form-column .star-rating label { /* Scope to form column */
        font-size: 1.5rem; /* Use rem unit */
        color: #ccc !important; /* Override default label color */
        cursor: pointer;
        margin-left: 0.3rem; /* Space between stars */
        transition: color 0.2s ease;
    }
    .review-form-column .star-rating label:hover, /* Scope to form column */
    .review-form-column .star-rating label:hover ~ label, /* Scope to form column */
    .review-form-column .star-rating input[type="radio"]:checked ~ label { /* Scope to form column */
        color: #ffc107 !important; /* Gold color on hover/checked */
    }
    /* Fix for right-to-left: first star should be closest to label */
    .review-form-column .star-rating label:first-child { /* Scope to form column */
          margin-left: 0;
    }
    .review-form-column .star-rating input[type="radio"] { /* Scope to form column */
        display: none; /* Hide the radio buttons */
     }


    /* Display Star Rating styles (for review items and average) */
    /* Use a parent class or ID if multiple star displays exist */
    .display-rating .fa-star {
        color: #ffc107; /* Gold color for filled stars */
        font-size: 1.2em; /* Keep relative size */
    }
    .display-rating .far.fa-star {
        color: #ccc; /* Color for empty stars */
        font-size: 1.2em; /* Keep relative size */
    }
    /* Adjust empty star color for average rating if needed */
    .average-rating-section .display-rating .far.fa-star {
        color: rgba(255, 193, 7, 0.5); /* Lighter gold for empty average stars */
    }


    /* Quote Icon */
    .review-item .quote-icon { /* Scope to review item */
        font-size: 2em; /* Larger quote icon */
        color: #555;
        margin-right: 0.3rem;
        line-height: 1;
        display: inline-block;
        vertical-align: top;
        font-family: serif; /* Use a serif font for quote */
    }
     .review-item .review-text { /* Scope to review item */
        display: inline-block; /* Place next to quote icon */
        width: calc(100% - 30px); /* Adjust width based on quote icon size */
        vertical-align: top;
      }


    /* Optional: Styling for the review list to add some spacing */
    .review-item {
        /* Border, padding, margin handled by Bootstrap classes below */
        background-color: #fff;
    }

     /* Custom border color for the form */
     .review-form-column {
        border: 1px solid black; /* Keep black border */
        background-color: #f9f9f9; /* Light background */
      }
      .review-form-column label {
          color: #ffc107; /* Yellow color for labels */
          font-weight: bold;
       }
        .review-form-column .form-control {
          border-color: #ffc107; /* Yellow border */
          box-shadow: 0 0 5px rgba(255, 193, 7, 0.5); /* Yellow shadow */
      }

      /* Styles for the scrollable reviews list column */
      .reviews-list-column {
          overflow-y: auto; /* Enable vertical scrolling when content overflows */
          max-height: 500px; /* Set the maximum height */
       }

    /* Responsive Adjustment: Stack columns on smaller screens */
    @media (max-width: 768px) {
        .container {
            padding-left: 15px; /* Add horizontal padding on small screens */
            padding-right: 15px; /* Add horizontal padding on small screens */
        }
        .reviews-list-column,
        .review-form-column {
            flex: none; /* Remove flex grow/shrink */
            width: 100%; /* Make columns take full width */
            min-width: unset; /* Remove min-width restriction */
            padding-right: 0; /* Remove custom right padding */
            max-height: unset; /* Ensure it expands on mobile if needed */
        }
         .review-item .review-text {
             width: calc(100% - 35px); /* Adjust width based on quote size */
        }
        .reviews-list-column h2 {
            text-align: center; /* Center title on small screens */
        }
        /* Ensure the scrollable column doesn't get unwanted padding on small screens */
        .reviews-list-column {
            padding-right: var(--bs-gutter-x, 0.75rem); /* Use Bootstrap gutter variable */
        }
    }
</style>
<?php
// The database connection $con is NOT closed here.
// It is assumed that the parent script (the one including this file)
// will handle closing the connection if necessary, or rely on PHP's automatic cleanup.
?>