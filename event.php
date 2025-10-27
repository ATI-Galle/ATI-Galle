<?php include_once("include/header.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>News Article Page</title>
  <style>
    /* Default Desktop Styles */
    body {
        margin: 0;
        font-family: sans-serif;
        background-color: #f0f2f5;
    }
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
    }
    .image-album {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 15px 0;
    }
    .image-album .thumb {
        width: 100px;
        height: 70px;
        object-fit: cover;
        border-radius: 4px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .image-album .thumb:hover {
        transform: scale(1.05);
    }
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
    }
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
    }
    .author-footer {
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }
    .sidebar {
        flex: 1;
    }
    .widget {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .widget h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        border-left: 3px solid #f4b400;
        padding-left: 8px;
    }
    .socials {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .socials li {
        padding: 8px 10px;
        margin: 5px 0;
        border-radius: 4px;
        color: #fff;
    }
    .facebook { background: #3b5998; }
    .twitter { background: #1da1f2; }
    .linkedin { background: #0077b5; }
    .instagram { background: #c32aa3; }
    .youtube { background: #ff0000; }
    .vimeo { background: #1ab7ea; }
    .trending .news-item {
        background: #f9f9f9;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .trending .news-item:last-child {
        margin-bottom: 0;
    }
    .trending .news-item .tag {
        font-size: 10px;
        padding: 3px 6px;
    }
    .trending .news-item a {
        font-size: 12px;
        color: #007bff;
        text-decoration: none;
    }

    /* --- [START] MOBILE RESPONSIVE STYLES --- */
    @media (max-width: 768px) {
        .container1 {
            /* Stack main content and sidebar vertically */
            flex-direction: column;
            margin: 10px auto;
            padding: 0 10px;
        }

        .main-content,
        .sidebar {
            /* Make both sections full-width */
            flex: 1;
            width: 100%;
        }

        h1 {
            font-size: 20px; /* Adjust heading size for smaller screens */
        }

        .article-text {
            font-size: 15px; /* Adjust text for better readability */
        }
        
        .image-album .thumb {
            width: 80px; /* Slightly smaller thumbnails on mobile */
            height: 60px;
        }
    }
    /* --- [END] MOBILE RESPONSIVE STYLES --- */
  </style>
</head>
<body>

<?php
// include('include/config.php'); // Make sure your DB connection is included
error_reporting(0);

$eventId = isset($_GET['eid']) ? $_GET['eid'] : null;
$eventDetails = null;
$albumImages = [];

if ($eventId !== null) {
    // Get event details
    $sqlEvent = "SELECT e.eid, e.etitle, e.etext, e.etag, e.created_at, e.updated_at, e.AlbumId
                 FROM events e
                 WHERE e.eid = ? AND e.status = '1'";

    if ($stmt = $con->prepare($sqlEvent)) {
        $stmt->bind_param("s", $eventId);
        $stmt->execute();
        $resultEvent = $stmt->get_result();
        if ($resultEvent->num_rows > 0) {
            $eventDetails = $resultEvent->fetch_assoc();
        } else {
            echo "<p>Event not found or is not active.</p>";
            exit;
        }
        $stmt->close();
    } else {
        echo "Error preparing event query: " . $con->error;
        exit;
    }

    // Get album images
    if ($eventDetails && !empty($eventDetails['AlbumId'])) {
        $sqlImages = "SELECT image_path FROM album_images WHERE AlbumId = ?";
        if ($stmt = $con->prepare($sqlImages)) {
            $stmt->bind_param("s", $eventDetails['AlbumId']);
            $stmt->execute();
            $resultImages = $stmt->get_result();
            while ($row = $resultImages->fetch_assoc()) {
                $albumImages[] = $row['image_path'];
            }
            $stmt->close();
        } else {
            echo "Error preparing images query: " . $con->error;
            exit;
        }
    }
}
?>

<div class="container1">
    <div class="main-content">
        <?php if ($eventDetails): ?>
            
            <?php if (!empty($albumImages)): ?>
                <img src="admin/<?php echo htmlspecialchars($albumImages[0]); ?>" alt="<?php echo htmlspecialchars($eventDetails['etitle']); ?>" class="main-image" />
                <div class="image-album">
                    <?php foreach ($albumImages as $image): ?>
                        <img src="admin/<?php echo htmlspecialchars($image); ?>" alt="Thumbnail" class="thumb" onclick="changeMainImage(this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="tags">
                <?php if (!empty($eventDetails['etag'])): ?>
                    <?php
                    $tags = explode(',', $eventDetails['etag']);
                    foreach ($tags as $tag):
                        $tag = trim($tag);
                        echo '<span class="tag dark">' . htmlspecialchars($tag) . '</span>';
                    endforeach;
                    ?>
                <?php endif; ?>
                <span class="date"><?php echo date("d F Y", strtotime($eventDetails['created_at'])); ?></span>
            </div>

            <h1><?php echo htmlspecialchars($eventDetails['etitle']); ?></h1>

            <div class="article-text">
                <?php echo $eventDetails['etext']; ?>
            </div>

            <div class="author-footer">
                <img src="avatar.png" alt="Author" class="avatar">
                <span>Admin</span> 
            </div>

        <?php else: ?>
            <p>No details available for this event.</p>
        <?php endif; ?>
    </div>

    <aside class="sidebar">
        <div class="widget">
            <h3>Follow Us</h3>
            <ul class="socials">
                <li class="facebook">📘 32,000 Followers</li>
                <li class="linkedin">🔗 12,345 Connects</li>
                <li class="youtube">▶️ 9,700 Subscribers</li>
            </ul>
        </div>
        <div class="widget trending">
            <h3>Trending News</h3>
            <?php
            $sqlTrending = "SELECT nid, ntitle, created_at FROM news WHERE status = '1' ORDER BY count DESC LIMIT 3";
            $resultTrending = $con->query($sqlTrending);
            if ($resultTrending && $resultTrending->num_rows > 0):
                while ($trending = $resultTrending->fetch_assoc()):
                    ?>
                    <div class="news-item">
                        <span class="tag dark">Trending</span>
                        <span class="date"><?php echo date("Y-m-d", strtotime($trending['created_at'])); ?></span>
                        <p><?php echo htmlspecialchars($trending['ntitle']); ?></p>
                        <a href="news.php?nid=<?php echo htmlspecialchars($trending['nid']);?>">Read More →</a>
                    </div>
                    <?php
                endwhile;
            endif;
            ?>
        </div>
    </aside>
</div>

<script>
    function changeMainImage(thumb) {
        const mainImage = document.querySelector('.main-image');
        if (mainImage) {
            mainImage.src = thumb.src;
        }
    }
</script>
  <!--links are not clicble solved by script-->

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Your live search javascript is here...
        });
    </script>

    
<?php include_once("include/footer.php"); ?>

</body>
</html>