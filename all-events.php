<?php

// Include your header, which should also include the database connection (config.php)
include_once('include/header.php');

// Database connection check
if (!isset($con) || !$con) {
    echo "<div class='message-area error'>Database connection error. Please check config.php.</div>";
}
?>

<style>
/* Using the same styles from your news page for a consistent look */
.events-container {
    padding: 40px 0;
    background-color: #f9f9f9;
}

.events-section-title {
    text-align: center;
    margin-bottom: 40px;
    color: #333;
    font-size: 2.5rem;
    font-weight: 700;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.event-card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.event-card-image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}

.event-card-body {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.event-card-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.3;
}

.event-card-text {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
}

.event-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
    margin-top: auto; /* Push footer to the bottom */
}

.event-card-date {
    font-size: 0.85rem;
    color: #999;
    font-weight: 500;
}

.view-event-btn {
    display: inline-block;
    background-color: #fec524; /* Using your site's yellow color */
    color: #333;
    font-weight: bold;
    padding: 8px 18px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.view-event-btn:hover {
    background-color: #333;
    color: #fff;
    text-decoration: none;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .events-grid {
        grid-template-columns: 1fr;
    }
    .events-section-title {
        font-size: 2rem;
    }
    .event-card-title {
        font-size: 1.4rem;
    }
}
</style>

<div class="events-container">
    <div class="container">
        <h2 class="events-section-title">Our Events</h2>

        <div class="events-grid">
            <?php
            // SQL query to get all active events and the first image from their corresponding album
            $events_query_sql = "
                SELECT 
                    e.eid, 
                    e.etitle, 
                    e.etext, 
                    e.created_at,
                    (SELECT image_path 
                     FROM album_images 
                     WHERE AlbumId = e.AlbumId 
                     ORDER BY image_id ASC 
                     LIMIT 1) AS first_image
                FROM 
                    events e
                WHERE 
                    e.status = 1
                ORDER BY 
                    e.created_at DESC
            ";

            $events_query = mysqli_query($con, $events_query_sql);

            if ($events_query && mysqli_num_rows($events_query) > 0) {
                while ($event = mysqli_fetch_assoc($events_query)) {
                    // --- IMAGE LOGIC ---
                    // Construct the full path to the image from the website's root
                    $full_image_path = 'admin/' . $event['first_image'];
                    
                    // Check if the image path is valid and if the file exists
                    $image_src = !empty($event['first_image']) && file_exists($full_image_path)
                        ? htmlspecialchars($full_image_path)
                        : 'images/default-event.jpg'; // Use a default image if none found

                    // Sanitize description for preview
                    $description_preview = strip_tags($event['etext']);
                    $description_preview = mb_substr($description_preview, 0, 120) . (mb_strlen($description_preview) > 120 ? '...' : '');
                    ?>
                    
                    <div class="event-card">
                        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($event['etitle']); ?>" class="event-card-image">
                        <div class="event-card-body">
                            <h4 class="event-card-title"><?php echo htmlspecialchars($event['etitle']); ?></h4>
                            <p class="event-card-text"><?php echo htmlspecialchars($description_preview); ?></p>
                            <div class="event-card-footer">
                                <span class="event-card-date">Posted on: <?php echo date('F j, Y', strtotime($event['created_at'])); ?></span>
                                <a href="event.php?eid=<?php echo htmlspecialchars($event['eid']); ?>" class="view-event-btn">View Event</a>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            } else {
                echo "<div class='alert alert-info text-center' style='grid-column: 1 / -1;'>There are no active events at the moment. Please check back later!</div>";
            }
            ?>
        </div>
    </div>
</div>
  <!--links are not clicble solved by script-->

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Your live search javascript is here...
        });
    </script>
<?php 
// Include your standard footer file
include_once('include/footer.php'); 
?>