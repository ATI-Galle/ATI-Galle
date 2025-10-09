<?php
include('config.php');

// Fetch the top 5 most priority announcements, including target audience
$sqlan = "SELECT announcement_id, title, content, image_path, target_audience FROM university_announcements WHERE status = '1' AND expiry_datetime > NOW() ORDER BY priority ASC LIMIT 5";
$resultan = $con->query($sqlan);

$announcements = [];
if ($resultan->num_rows > 0) {
    while ($row = $resultan->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Function to sort announcements by priority (priority 1 at the beginning)
// Note: The SQL query already orders by priority, so this PHP sort might be redundant
// unless you're manipulating the array further before displaying.
// I'll keep it for now but it could potentially be removed if the SQL order is sufficient.
function sortByPriority($a, $b) {
    // Assuming 'priority' key exists in the fetched rows if needed for this sort.
    // If not directly fetched, this function won't work as intended.
    // Based on the SQL, 'priority' is used for ORDER BY, not SELECTed.
    // If you need to sort in PHP, ensure 'priority' is selected in the SQL query.
    // For now, relying on SQL ORDER BY.
    return 0; // Placeholder as SQL ORDER BY should handle this.
}

// Sort the announcements array by priority (This will rely on the SQL ORDER BY)
// If you need to sort based on a 'priority' column *not* selected, you'd need to fetch it.
// Assuming the SQL ORDER BY is sufficient for the initial fetch order.
// usort($announcements, 'sortByPriority'); // Commenting out as SQL ORDER BY should be enough
?>

<!DOCTYPE html>
<html>
<head>
<title>University Announcements</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        line-height: 1.6;
        color: #333;
    }

    #announcementPopupContainer {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #ffffff; /* White background */
        border: 1px solid #ddd; /* Lighter border */
        padding: 30px; /* Increased padding */
        z-index: 1000;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow */
        border-radius: 8px; /* More rounded corners */
        text-align: center;
        max-width: 600px; /* Max width for better readability */
        width: 90%; /* Responsive width */
        box-sizing: border-box; /* Include padding and border in the element's total width and height */
        overflow-y: auto; /* Add scroll if content is too long */
        max-height: 90vh; /* Max height to prevent overflow off the screen */
    }

    #announcementTitle {
        color: #0056b3; /* University blue */
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.8em; /* Larger title */
        font-weight: 700;
    }

    #announcementContent {
        margin-bottom: 20px;
        font-size: 1.1em;
        text-align: left; /* Align content to the left */
        word-wrap: break-word; /* Prevent long words from overflowing */
    }

    #announcementImage {
        max-width: 100%;
        height: auto;
        margin-top: 15px;
        margin-bottom: 15px;
        border-radius: 4px; /* Slightly rounded image corners */
        display: block; /* Ensure image is on its own line */
        margin-left: auto; /* Center image */
        margin-right: auto; /* Center image */
        /* Removed fixed width and height for better responsiveness */
        /* width: 450px; height: 200px; */
    }

    .target-audience {
        font-weight: 700;
        color: #28a745; /* Success green for emphasis */
        display: block;
        margin-top: 15px;
        font-size: 1em;
    }

    #closeButton, #prevButton, #nextButton {
        margin-top: 20px;
        padding: 10px 20px; /* Increased padding */
        cursor: pointer;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        transition: background-color 0.3s ease; /* Smooth transition for hover */
    }

    #closeButton {
        background-color: #dc3545; /* Danger red */
        color: white;
    }

    #closeButton:hover {
        background-color: #c82333;
    }

    #prevButton, #nextButton {
        background-color: #007bff; /* Primary blue */
        color: white;
        margin: 0 10px; /* Add space between buttons */
    }

    #prevButton:hover, #nextButton:hover {
        background-color: #0056b3;
    }

    #announcementOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Darker overlay */
        z-index: 999;
        cursor: pointer;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #announcementPopupContainer {
            width: 95%;
            padding: 20px;
        }

        #announcementTitle {
            font-size: 1.5em;
        }

        #announcementContent {
            font-size: 1em;
        }

        #closeButton, #prevButton, #nextButton {
            padding: 8px 15px;
            font-size: 0.9em;
        }
    }
</style>
</head>
<body>

<div id="announcementPopupContainer">
    <h2 id="announcementTitle"></h2>
    <div id="announcementContent"></div>
    <img id="announcementImage" src="" alt="Announcement Image">
    <div id="announcementTargetAudience" class="target-audience"></div>
    <div>
        <button id="prevButton">&laquo; Previous</button>
        <button id="nextButton">Next &raquo;</button>
    </div>
    <button id="closeButton">Close</button>
</div>

<div id="announcementOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var announcements = <?php echo json_encode($announcements); ?>;
        var popupContainer = document.getElementById('announcementPopupContainer');
        var popupTitle = document.getElementById('announcementTitle');
        var popupContent = document.getElementById('announcementContent');
        var popupImage = document.getElementById('announcementImage');
        var announcementTargetAudience = document.getElementById('announcementTargetAudience');
        var closeButton = document.getElementById('closeButton');
        var prevButton = document.getElementById('prevButton');
        var nextButton = document.getElementById('nextButton');
        var overlay = document.getElementById('announcementOverlay');
        var currentIndex = 0;

        function showAnnouncement(index) {
            if (announcements.length > 0 && index >= 0 && index < announcements.length) {
                var announcement = announcements[index];
                popupTitle.textContent = announcement.title;
                // Sanitize content if necessary before using innerHTML
                popupContent.innerHTML = announcement.content;
                if (announcement.image_path) {
                    // Ensure the image path is correct relative to the file displaying this popup
                    popupImage.src = "admin/" + announcement.image_path;
                    popupImage.style.display = 'block';
                } else {
                    popupImage.style.display = 'none';
                }
                announcementTargetAudience.textContent = "Announcement for: " + announcement.target_audience;

                // Show navigation buttons only if there's more than one announcement
                if (announcements.length > 1) {
                    prevButton.style.display = 'inline-block';
                    nextButton.style.display = 'inline-block';
                } else {
                    prevButton.style.display = 'none';
                    nextButton.style.display = 'none';
                }


                popupContainer.style.display = 'block';
                overlay.style.display = 'block';
            } else {
                // No announcements or index out of bounds - hide popup
                popupContainer.style.display = 'none';
                overlay.style.display = 'none';
            }
        }

        function nextAnnouncement() {
            currentIndex = (currentIndex + 1) % announcements.length;
            showAnnouncement(currentIndex);
        }

        function prevAnnouncement() {
            currentIndex = (currentIndex - 1 + announcements.length) % announcements.length;
            showAnnouncement(currentIndex);
        }

        // Initial check and display
        if (announcements.length > 0) {
            showAnnouncement(currentIndex); // Show the first announcement

            // Add event listeners only if announcements exist
            nextButton.addEventListener('click', nextAnnouncement);
            prevButton.addEventListener('click', prevAnnouncement);
        } else {
            // Display a message if no announcements are available
             popupTitle.textContent = "No Announcements";
             popupContent.textContent = "There are currently no announcements to display.";
             popupImage.style.display = 'none'; // Hide image area
             announcementTargetAudience.style.display = 'none'; // Hide target audience area
             prevButton.style.display = 'none'; // Hide navigation buttons
             nextButton.style.display = 'none';
             popupContainer.style.display = 'block'; // Show the container with the message
             overlay.style.display = 'block'; // Show the overlay
        }


        closeButton.addEventListener('click', function() {
            popupContainer.style.display = 'none';
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', function() {
            popupContainer.style.display = 'none';
            overlay.style.display = 'none';
        });
    });
</script>

</body>
</html>