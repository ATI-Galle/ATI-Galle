<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest News Section with Carousel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            line-height: 1.6;
            color: #333;
            background-color: #f8f8f8; /* Light background color */
        }

        .latest-news-section-container {
            max-width: 1200px; /* Limit section width */
            margin: 40px auto; /* Center the section */
            padding: 0 20px; /* Add horizontal padding */
            box-sizing: border-box;
            position: relative; /* Kept relative for potential future use, but not strictly needed for inline arrows */
            /* overflow: hidden; /* Removed overflow hidden from main container */
        }

        .news-header {
            display: flex; /* Use Flexbox for header layout */
            justify-content: space-between; /* Space out title and controls */
            align-items: center; /* Vertically align items */
            margin-bottom: 20px; /* Space below header */
            padding: 0 0px; /* Padding is on the main container */
        }

        .section-title {
            font-size: 1.8em; /* Adjust title size */
            color: #333;
            margin: 0; /* Remove default margins */
        }

        .header-controls {
            display: flex; /* Use Flexbox for View All and arrows */
            align-items: center; /* Vertically align items */
            /* Added gap for spacing between items */
             gap: 10px;
        }

        .view-all-link {
            text-decoration: none; /* Remove underline */
            color: #1a237e; /* Example link color */
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .view-all-link:hover {
            color: #673ab7; /* Change color on hover */
        }

        /* Navigation Arrows (Original styling for inline placement in header) */
        .news-nav-arrow {
            background: none; /* No background */
            border: 1px solid #ccc; /* Subtle border */
            color: #333; /* Icon color */
            padding: 8px; /* Adjust padding */
            cursor: pointer;
            font-size: 0.8em; /* Adjust icon size */
            /* margin-left: 5px; /* Space between arrows - replaced by gap on header-controls */
            border-radius: 4px; /* Slight rounded corners */
            transition: background-color 0.3s ease, border-color 0.3s ease, opacity 0.3s ease;
        }

        .news-nav-arrow:hover {
            background-color: #eee; /* Subtle background on hover */
            border-color: #aaa;
        }

        /* Style for disabled/hidden arrows */
         .news-nav-arrow.hidden {
            opacity: 0.5; /* Fade out */
            pointer-events: none; /* Prevent clicking */
            cursor: default;
         }


        .news-cards-wrapper {
            overflow: hidden; /* Hide parts of cards outside the container */
        }

        .news-cards-container {
            display: flex; /* Arrange cards horizontally */
            /* gap: 20px; /* Use margin-right on card for easier JS calc */
            scroll-behavior: smooth; /* Smooth scrolling */
            overflow-x: auto; /* Enable horizontal scrolling */
            scrollbar-width: none; /* Hide scrollbar Firefox */
            -ms-overflow-style: none;  /* Hide scrollbar IE/Edge */
            padding-bottom: 15px; /* Add padding for potential scrollbar space */
            /* Removed special padding/margins for absolute arrows */
        }

        .news-cards-container::-webkit-scrollbar {
            display: none; /* Hide scrollbar Chrome/Safari/Opera */
        }


        .news-item-card {
            flex: 0 0 auto; /* Prevent shrinking */
            width: 300px; /* Adjust card width */
            margin-right: 20px; /* Space between cards */
            background-color: #fff; /* White card background */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            overflow: hidden; /* Hide overflowing image/content */
            display: flex;
            flex-direction: column; /* Stack date, image, content */
            cursor: pointer; /* Indicate clickable */
        }

        .news-item-card:last-child {
            margin-right: 0; /* No margin on the last card */
        }

        .news-date {
            font-size: 0.8em;
            color: #666; /* Grey date color */
            padding: 10px 15px;
            background-color: #f1f1f1; /* Light background for date */
        }

        .news-item-image {
            width: 100%; /* Image takes full width of the card */
            height: 180px; /* Fixed height for the image - adjust as needed */
            object-fit: cover; /* Cover the area without distorting */
            display: block; /* Remove extra space below image */
        }

        .news-item-content {
            padding: 15px;
            flex-grow: 1; /* Allow content area to take remaining height */
            display: flex;
            flex-direction: column; /* Stack title, text, button */
        }

        .news-item-content h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1em;
            color: #1a237e; /* Example: Theme color for title */
        }

        .news-item-content p {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 0.9em;
             /* Limit lines */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 3; /* Show max 3 lines */
        }

        .read-more-button {
            display: inline-block; /* For padding/margin */
            background: none; /* Transparent background */
            border: 1px solid #ccc; /* Subtle border */
            color: #333; /* Text color */
            padding: 8px 15px;
            font-size: 0.9em;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; /* If using <a> tag */
            transition: background-color 0.3s ease, border-color 0.3s ease;
            align-self: flex-start; /* Align to the left */
            margin-top: auto; /* Push button to the bottom */
        }

        .read-more-button:hover {
             background-color: #f1f1f1; /* Light background on hover */
             border-color: #aaa;
        }


        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .latest-news-section-container {
                margin: 20px auto;
                padding: 0 10px; /* Adjust padding */
            }

             .news-header {
                flex-direction: column; /* Stack header items */
                text-align: center;
                margin-bottom: 15px;
             }

             .section-title {
                font-size: 1.5em;
                margin-bottom: 10px;
             }

             .header-controls {
                margin-top: 10px;
                gap: 5px; /* Adjust gap */
             }

             .view-all-link {
                font-size: 0.9em;
             }

             .news-nav-arrow {
                 padding: 6px; /* Adjust padding */
                 font-size: 0.7em; /* Adjust size */
            }

             .news-cards-container {
                /* No special padding/margins for absolute arrows */
             }

            .news-item-card {
                width: 250px; /* Adjust card width */
                margin-right: 15px; /* Adjust space */
            }

             .news-item-image {
                 height: 150px; /* Adjust image height */
             }

            .news-item-content {
                 padding: 10px; /* Adjust padding */
             }

             .news-item-content h3 {
                 font-size: 1em;
             }

             .news-item-content p {
                 font-size: 0.8em;
                 margin-bottom: 10px;
             }

             .read-more-button {
                 padding: 6px 12px;
                 font-size: 0.8em;
             }
        }
    </style>
</head>
<body>

    <section class="latest-news-section-container" id="event">
        <div class="news-header">
            <h2 class="section-title">Latest Events</h2>
            <div class="header-controls">
                <button class="news-nav-arrow left-arrow"><i class="fas fa-chevron-left"></i></button>
                <button class="news-nav-arrow right-arrow"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="news-cards-wrapper">
            <div class="news-cards-container">

            <?php
// 1. Include your database configuration and connection file.
include_once ("config.php");

// --- PHP Logic to Fetch Events Data ---

$eventsItems = []; // Initialize an empty array to store events data

// 2. Prepare the SQL query to select events data and the first image from the album.
$sql = "SELECT
    e.eid,
    e.etitle,
    e.etext,
    e.created_at,
    al.AlbumId,
    al.album_name,
    (SELECT ai.image_path FROM album_images ai WHERE ai.AlbumId = e.AlbumId ORDER BY ai.uploaded_at ASC LIMIT 1) AS first_image_path
FROM events e
LEFT JOIN albums al ON e.AlbumId = al.AlbumId
WHERE e.status = '1'
ORDER BY e.created_at DESC";

// 3. Execute the query and fetch the data (MySQLi example)
if (isset($con)) {
    $result = $con->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            // Fetch all results into the $eventsItems array
            while($row = $result->fetch_assoc()) {
                $eventsItems[] = $row;
            }
        } else {
            // No events found
            echo "<p>No events found.</p>";
        }
        // Optional: $result->free();
    } else {
        // Query failed
        echo "Error executing query: " . $con->error;
    }
    // Optional: $con->close();
} else {
    echo "Database connection variable (\$con) not found. Please check your config.php.";
    exit;
}

// --- HTML Block with PHP Loop ---

// Check if there are any events to display
if (!empty($eventsItems)) {
    foreach ($eventsItems as $event) {
        // Sanitize data before outputting
        $etitle = htmlspecialchars($event['etitle']);
        $etext_short = htmlspecialchars(substr(strip_tags($event['etext']), 0, 150) . '...');
        $imagePath = 'admin/'; // Assuming your album images are in this directory
        if (!empty($event['first_image_path'])) {
            $imagePath .= htmlspecialchars($event['first_image_path']);
        } else {
            $imagePath .= 'default_event.jpg'; // Placeholder image if no album image
        }

        // Format the created_at date
        $eventDate = date("d F Y", strtotime($event['created_at']));

        // Generate the link to the full event details
        $readMoreLink = 'event.php?eid=' . $event['eid'];
?>
    <div class="news-item-card">
        <div class="news-date"><?php echo $eventDate; ?></div>
        <img src="<?php echo $imagePath; ?>" alt="<?php echo $etitle; ?>" class="news-item-image">
        <div class="news-item-content">
            <h3><?php echo $etitle; ?></h3>
            <p><?php echo $etext_short; ?></p>
            <button class="read-more-button"><a href="<?php echo $readMoreLink; ?>">Read More</a></button>
        </div>
    </div>
<?php
    } // End of foreach loop
} else {
    // Display a message if no events are found
    ?>
    <div class="news-item-card">
        <p>No events available at the moment.</p>
    </div>
    <?php
}

// Optional: if (isset($con)) { $con->close(); }
?>

                

                </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardsContainer = document.querySelector('.news-cards-container');
            // Select arrows using their specific classes now that they are in the header
            const leftArrow = document.querySelector('.news-nav-arrow.left-arrow');
            const rightArrow = document.querySelector('.news-nav-arrow.right-arrow');

            // Function to scroll the container
            const scrollContainer = (distance) => {
                cardsContainer.scrollBy({
                    left: distance,
                    behavior: 'smooth'
                });
            };

            // Event listeners for arrows
            leftArrow.addEventListener('click', () => {
                // Calculate scroll distance (width of one card + its right margin)
                 const card = document.querySelector('.news-item-card');
                 if (!card) return; // Prevent error if no cards exist
                 const cardWidth = card.getBoundingClientRect().width;
                 const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                 const scrollDistance = cardWidth + cardMarginRight;

                 scrollContainer(-scrollDistance); // Scroll left
            });

            rightArrow.addEventListener('click', () => {
                 // Calculate scroll distance
                 const card = document.querySelector('.news-item-card');
                 if (!card) return; // Prevent error if no cards exist
                 const cardWidth = card.getBoundingClientRect().width;
                 const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                 const scrollDistance = cardWidth + cardMarginRight;

                 scrollContainer(scrollDistance); // Scroll right
            });

            // Logic to hide/show arrows based on scroll position
            const toggleArrows = () => {
                 // Check if scrolled to the beginning or end
                 // Add a small tolerance (e.g., 1px) for potential subpixel issues
                 const isAtStart = cardsContainer.scrollLeft <= 1;
                 // Check if the right edge of the scrollable content is visible
                 // Use scrollWidth and clientWidth to determine the end point
                 const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - 1;

                 // Hide/show the arrows
                 leftArrow.classList.toggle('hidden', isAtStart);
                 rightArrow.classList.toggle('hidden', isAtEnd);
            };

            // Listen for scroll events on the container to update arrow visibility
            cardsContainer.addEventListener('scroll', toggleArrows);

            // Also check arrow visibility when the window is resized (in case cards per view changes)
            window.addEventListener('resize', toggleArrows);

            // Initial check on load to set the correct arrow visibility
            toggleArrows();
        });
    </script>

</body>
</html>