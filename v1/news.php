<?php
// Assuming include/header.php contains your database connection ($con)
// and potentially starts the HTML document and includes necessary head/body tags.
// You might need to adjust the include path based on your file structure.
include_once("include/header.php");

error_reporting(E_ALL); // Display all errors during development
ini_set('display_errors', 1);

// Ensure the database connection is established
if (!isset($con) || $con->connect_error) {
    die("Database connection failed: " . (isset($con->connect_error) ? $con->connect_error : "Not established"));
}

// --- PHP Logic to Fetch News Details and Increment View Count ---

// Assuming you have an 'nid' (news ID) to identify which news article to fetch.
// You might get this from a URL parameter (e.g., ?nid=123).
$newsId = isset($_GET['nid']) ? intval($_GET['nid']) : null;

$newsDetails = null;

if ($newsId !== null) {
    // First, increment the view count for this news item
    $sqlIncrementCount = "UPDATE news SET count = count + 1 WHERE nid = ?";
    if ($stmtIncrement = $con->prepare($sqlIncrementCount)) {
        $stmtIncrement->bind_param("i", $newsId);
        $stmtIncrement->execute();
        $stmtIncrement->close();
    } else {
        // Log or handle the error appropriately, but don't stop the page load
        error_log("Error preparing count increment query: " . $con->error);
    }

    // Now, fetch the news details (including the updated count)
    $sqlNews = "SELECT nid, ntitle, ntag, ntext, nimg, count, created_at, updated_at
                FROM news
                WHERE nid = ? AND status = '1'";

    if ($stmt = $con->prepare($sqlNews)) {
        $stmt->bind_param("i", $newsId);
        $stmt->execute();
        $resultNews = $stmt->get_result();

        if ($resultNews->num_rows > 0) {
            $newsDetails = $resultNews->fetch_assoc();
        } else {
            // News not found or not active
             echo "<div class='container1'><p>News article not found or is not active.</p></div>";
            // You might redirect to a 404 page here
             include_once("include/footer.php");
             exit; // Stop execution if news is not found
        }
        $stmt->close();
    } else {
        echo "<div class='container1'><p>Error preparing news query: " . $con->error . "</p></div>";
         include_once("include/footer.php");
        exit; // Stop execution on query error
    }
} else {
    // No news ID provided
    echo "<div class='container1'><p>No news article ID provided.</p></div>";
    // You might redirect to a news listing page
     include_once("include/footer.php");
    exit; // Stop execution if no ID is provided
}

// --- HTML Block (Starts after the main PHP logic) ---
// The rest of your HTML structure follows here...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?php echo $newsDetails ? htmlspecialchars($newsDetails['ntitle']) : 'News Article'; ?></title>
    <style>
        /* Your CSS styles provided in the example */
        .container1 {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 20px;
            padding: 0 15px;
        }

        .main-content {
            flex: 3;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }

        .main-image {
            width: 100%;
            height: auto;
            border-radius: 6px;
            object-fit: cover; /* Ensure the image covers the area */
        }

        /* Removed .image-album and .thumb styles as we only have one image */


        .tags {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }

        .tag {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            margin-right: 5px;
            color: #fff;
            font-size: 12px;
            margin-bottom: 5px; /* Added some bottom margin for tags */
        }

        .yellow { background: #f4b400; }
        .dark { background: #333; }

        h1 {
            margin: 15px 0;
            font-size: 24px;
            color: #222;
        }

        .article-text {
            font-size: 16px;
            line-height: 1.7;
            color: #444;
            margin-bottom: 20px; /* Added margin below text */
        }

        .author-footer {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #555; /* Slightly darker color for footer text */
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover; /* Ensure avatar looks good */
        }

        .views {
            margin-left: auto; /* Push views to the right */
            font-weight: bold;
            color: #007bff; /* Highlight views count */
        }

        .sidebar {
            flex: 1;
        }

        .widget {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Added subtle shadow */
        }

        .widget h3 {
            margin-bottom: 15px;
            font-size: 18px;
            border-left: 3px solid #f4b400;
            padding-left: 8px;
            color: #333;
        }

        .socials {
            list-style: none;
            padding: 0;
        }

        .socials li {
            padding: 8px 10px;
            margin: 5px 0;
            border-radius: 4px;
            color: #fff;
            font-size: 14px; /* Slightly larger font for social items */
            cursor: pointer; /* Indicate they are clickable */
            transition: opacity 0.2s ease;
        }
        .socials li:hover {
            opacity: 0.9;
        }


        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .linkedin { background: #0077b5; }
        .instagram { background: #c32aa3; }
        .youtube { background: #ff0000; }
        .vimeo { background: #1ab7ea; }

        .trending .news-item {
            background: #f9f9f9;
            padding: 12px; /* Increased padding */
            border-radius: 6px;
            margin-bottom: 10px; /* Margin between items */
            border: 1px solid #eee; /* Subtle border */
        }

        .trending .news-item .tag {
            font-size: 10px;
            padding: 3px 6px;
            margin-bottom: 5px; /* Added bottom margin */
        }
         .trending .news-item p {
             margin: 5px 0; /* Added margin to trending title */
             font-size: 14px;
             line-height: 1.4;
         }

        .trending .news-item a {
            font-size: 12px;
            color: #007bff;
            text-decoration: none;
            display: inline-block; /* Allows margin/padding */
            margin-top: 5px;
        }
         .trending .news-item a:hover {
             text-decoration: underline;
         }

         /* Added styles for date and author in main content */
         .tags .date, .tags .author {
             margin-left: 10px;
             font-size: 13px;
             color: #888;
         }
         .tags .date {
             font-weight: bold;
             color: #666;
         }

         /* Basic responsiveness */
         @media (max-width: 768px) {
             .container1 {
                 flex-direction: column;
             }
             .main-content, .sidebar {
                 flex: none;
                 width: 100%;
             }
         }
    </style>
</head>
<body>

<div class="container1">
    <div class="main-content">
        <?php if ($newsDetails): ?>
            <?php
            // Construct the image path. Assuming nimg stores just the filename or relative path.
            // You might need to adjust the base path ('admin/') depending on where images are stored.
            $imagePath = !empty($newsDetails['nimg']) ? "admin/" . htmlspecialchars($newsDetails['nimg']) : 'placeholder.jpg'; // Use a placeholder if no image
            ?>
            <img src="<?php echo $imagePath; ?>"
                 alt="<?php echo htmlspecialchars($newsDetails['ntitle']); ?>"
                 class="main-image" />

            <?php
            // Removed the image-album div as we only have one image per news item
            ?>

            <div class="tags">
                <?php if (!empty($newsDetails['ntag'])): ?>
                    <?php
                    $tags = explode(',', $newsDetails['ntag']);
                    foreach ($tags as $tag):
                        $tag = trim($tag);
                        if (!empty($tag)): // Ensure tag is not empty after trim
                            $tagClass = 'dark'; // Default tag style
                            // You can add logic here to assign different classes based on tag content
                            // e.g., if ($tag == 'Politics') $tagClass = 'blue';
                            echo '<span class="tag ' . htmlspecialchars($tagClass) . '">' . htmlspecialchars($tag) . '</span>';
                        endif;
                    endforeach;
                    ?>
                <?php endif; ?>
                <span class="date">Published: <?php echo date("d F Y H:i", strtotime($newsDetails['created_at'])); ?></span>
                 <?php if ($newsDetails['created_at'] != $newsDetails['updated_at']): // Only show updated if different ?>
                <span class="author">
                    Last Updated: <?php echo date("d F Y H:i", strtotime($newsDetails['updated_at'])); ?>
                </span>
                 <?php endif; ?>
            </div>

            <h1><?php echo htmlspecialchars($newsDetails['ntitle']); ?></h1>

            <div class="article-text">
                <?php echo $newsDetails['ntext']; ?>
            </div>

            <div class="author-footer">
                <img src="avatar.png" alt="Author" class="avatar"> <span>Admin</span> <span class="views">
                    👁️ <?php echo number_format($newsDetails['count']); ?> Views
                </span>
            </div>
        <?php else: ?>
            <p>Unable to load news article details.</p>
        <?php endif; ?>
    </div>

    <aside class="sidebar">
        <div class="widget">
            <h3>Follow Us</h3>
            <ul class="socials">
                <li class="facebook">📘 12,345 Fans</li>
                <li class="twitter">🐦 12,345 Followers</li>
                <li class="linkedin">🔗 12,345 Connects</li>
                <li class="instagram">📸 12,345 Followers</li>
                <li class="youtube">▶️ 12,345 Subscribers</li>
                <li class="vimeo">🎞️ 12,345 Followers</li>
            </ul>
        </div>

        <div class="widget trending">
            <h3>Trending News</h3>
            <?php
            // Example of fetching trending news (adapt to your actual logic)
            // Fetching top 5 trending news based on count, excluding the current news item
            $sqlTrending = "SELECT nid, ntitle, created_at FROM news WHERE status = '1' AND nid != ? ORDER BY count DESC LIMIT 5";
            if ($stmtTrending = $con->prepare($sqlTrending)) {
                $stmtTrending->bind_param("i", $newsId);
                $stmtTrending->execute();
                $resultTrending = $stmtTrending->get_result();

                if ($resultTrending && $resultTrending->num_rows > 0):
                    while ($trending = $resultTrending->fetch_assoc()):
                        ?>
                        <div class="news-item">
                            <span class="tag dark">Trending</span>
                            <span class="date"><?php echo date("Y-m-d", strtotime($trending['created_at'])); ?></span>
                            <p><?php echo htmlspecialchars($trending['ntitle']); ?></p>
                            <a href="?nid=<?php echo htmlspecialchars($trending['nid']); ?>">Read More →</a>
                        </div>
                        <?php
                    endwhile;
                 else:
                     echo "<p>No trending news found.</p>";
                endif;
                 $stmtTrending->close();
            } else {
                 error_log("Error preparing trending news query: " . $con->error);
                 echo "<p>Error loading trending news.</p>";
            }
            ?>
        </div>
    </aside>
</div>

<?php
// Assuming include/footer.php closes the HTML document and includes footer content
include_once("include/footer.php");
// It's good practice to close the database connection when done
$con->close();
?>

</body>
</html>