<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Section</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            line-height: 1.6;
            color: #333;
        }

        .news-section-container {
            background-color: #ffc107; /* Yellow/Orange background */
            padding: 40px 20px; /* Add padding */
            text-align: center; /* Center align contents like title and button */
        }

        .section-title {
            color: #333; /* Dark color for the title */
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 2.5em; /* Adjust font size */
            position: relative;
            display: inline-block; /* Allow centering */
        }

         /* Optional: Underline effect for the title */
        .section-title::after {
            content: '';
            display: block;
            width: 60px; /* Width of the underline */
            height: 3px; /* Thickness of the underline */
            background-color: #333; /* Underline color */
            margin: 10px auto 0; /* Center the underline below the title */
        }


        .news-grid {
            display: grid;
            /* Responsive grid: 2 columns minimum 280px, fills space */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; /* Gap between grid items */
            max-width: 1200px; /* Limit grid width */
            margin: 0 auto 30px auto; /* Center grid and add bottom margin */
        }

        .news-card {
            background-color: #fff; /* White background for cards */
            border-radius: 8px; /* Soften corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            display: flex; /* Use Flexbox to arrange image and content */
            padding: 15px;
            text-align: left; /* Align text left within the card */
        }

        .news-thumbnail {
            width: 80px; /* Fixed width for thumbnail */
            height: 80px; /* Fixed height for thumbnail */
            object-fit: cover; /* Cover the area without distorting */
            border-radius: 4px; /* Slight rounded corners for image */
            margin-right: 15px; /* Space between image and text */
            flex-shrink: 0; /* Prevent thumbnail from shrinking */
        }

        .news-content {
            flex-grow: 1; /* Allow content to take remaining space */
            display: flex;
            flex-direction: column; /* Stack title, text, link */
            justify-content: space-between; /* Space out content */
        }

        .news-content h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.1em; /* Adjust title font size */
            color: #1a237e; /* Example: Use a theme color */
        }

        .news-content p {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 0.9em; /* Adjust text font size */
            /* Limit lines if needed */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 3; /* Show max 3 lines of text */
        }

        .read-more {
            display: inline-block; /* Treat as block for margin */
            margin-top: auto; /* Push to the bottom of the flex container */
            color: #673ab7; /* Example: Use a theme color for link */
            text-decoration: none; /* Remove underline */
            font-weight: bold;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .read-more:hover {
            color: #5e35b1; /* Darker color on hover */
            text-decoration: underline; /* Add underline on hover */
        }

        .view-all-button {
            display: inline-block; /* Centered block */
            background-color: #333; /* Dark background for button */
            color: white;
            padding: 12px 25px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* If using <a> tag */
            transition: background-color 0.3s ease, transform 0.1s ease;
            margin-top: 20px; /* Space above the button */
        }

        .view-all-button:hover {
            background-color: #555; /* Darker on hover */
            transform: translateY(-2px); /* Slight lift */
        }

        /* Optional: Adjustments for very small screens */
        @media (max-width: 480px) {
             .section-title {
                 font-size: 2em;
             }
            .news-card {
                flex-direction: column; /* Stack image and text on very small screens */
                text-align: center;
            }
            .news-thumbnail {
                margin: 0 auto 10px auto; /* Center image and add bottom margin */
            }
             .news-content h3,
             .news-content p {
                 text-align: center; /* Center text when stacked */
             }
             .read-more {
                 align-self: center; /* Center the read more link */
             }
        }

    </style>
</head>
<body>
<h2 class="section-title" style="float:left; margin-left:155px; margin-top:20px;">News</h2>

    <section class="news-section-container" id="news">

        <br><br>

        <div class="news-grid">
        <?php
// 1. Include your database configuration and connection file.
include_once ("config.php");

// --- PHP Logic to Fetch News Data ---

$newsItems = []; // Initialize an empty array to store news data

// 2. Prepare the SQL query to select news data.
$sql = "SELECT nid, ntitle, ntag, ntext, nimg, count, status, created_at, updated_at FROM news WHERE status = '1'"; // Assuming status '1' means published

// 3. Execute the query and fetch the data (MySQLi example)
if (isset($con)) {
    $result = $con->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Fetch all results into the $newsItems array
            while($row = $result->fetch_assoc()) {
                $newsItems[] = $row;
            }
        } else {
            // No news items found
            // You can set a message here if you want to display it later
            // echo "<p>No news found.</p>";
        }
        // Optional: $result->free();
    } else {
        // Query failed
        echo "Error executing query: " . $con->error;
        // In a production environment, you'd log this error rather than echoing it.
    }
    // It's generally good practice to close the connection when you're done with all database operations on a page.
    // However, if config.php handles connection management or if you have more queries later, you might not close it here.
    // $con->close(); // Assuming $con is your connection variable
} else {
    echo "Database connection variable (\$con) not found. Please check your config.php.";
    // Stop further execution if no DB connection
    exit;
}

// --- HTML Block with PHP Loop ---

// Check if there are any news items to display
if (!empty($newsItems)) {
    foreach ($newsItems as $news) {
        // Sanitize data before outputting to prevent XSS vulnerabilities
        $ntitle = htmlspecialchars($news['ntitle']);
        $nimg_url = htmlspecialchars($news['nimg']); // Assuming nimg contains a full URL or a placeholder

        // MODIFIED LINE: First strip tags, then apply htmlspecialchars to the text
        $ntext_short = htmlspecialchars(strip_tags(substr($news['ntext'], 0, 100) . '...'));

        // $nid = $news['nid']; // If you need the ID for the "Read more" link, for example
        $readMoreLink = 'news.php?nid=' . $news['nid']; // Example link

?>
    <div class="news-card">
        <img src="admin/<?php echo $nimg_url; // nimg column data in this place ?>" alt="News Thumbnail" class="news-thumbnail">
        <div class="news-content">
            <h3><?php echo $ntitle; // ntitle column data in this place ?></h3>
            <p><?php echo $ntext_short; // This will now echo the content with HTML tags removed and shortened ?></p>
            <a href="<?php echo $readMoreLink; ?>" class="read-more">Read more</a>
        </div>
    </div>
        <?php
    } // End of foreach loop
} else {
    // This part will be executed if the $newsItems array is empty (no records found or query failed silently before)
    // You can display a "no news available" message or a default card.
    ?>
        <div class="news-card">
            <img src="admin/default.jpg" alt="No News Thumbnail" class="news-thumbnail">
            <div class="news-content">
                <h3>No News Available</h3>
                <p>There are currently no news items to display. Please check back later.</p>
                <a href="#" class="read-more disabled">Read more</a>
            </div>
        </div>
    <?php
}

// If you opened the connection in config.php and it's not closed automatically,
// and you don't need it anymore, you might close it here.
// if (isset($con)) { $con->close(); }
?>
            

            </div>

        <button class="view-all-button">View All</button> </section>

    <script>
        // No specific JavaScript needed for the grid layout shown in the image.
        // If you want interactive features later, add your JS here.
        // Example: Making the "View All" button a link
        /*
        document.querySelector('.view-all-button').addEventListener('click', function() {
            window.location.href = 'your-news-archive-page.html'; // Replace with your news archive URL
        });
        */
    </script>

</body>
</html>