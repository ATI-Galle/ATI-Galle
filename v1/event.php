<?php include_once("include/header.php");?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>News Article Page</title>
  <style>
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
  margin-bottom: 15px;
  font-size: 18px;
  border-left: 3px solid #f4b400;
  padding-left: 8px;
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
  </style>
</head>
<body>

<?php


error_reporting(0);

// 1. Include your database configuration and connection file.

// --- PHP Logic to Fetch Event Details and Album Images ---

// Assuming you have an 'eid' (event ID) to identify which event's details to fetch.
// You might get this from a URL parameter (e.g., ?eid=123).
$eventId = isset($_GET['eid']) ? intval($_GET['eid']) : null;

$eventDetails = null;
$albumImages = [];
if ($eventId !== null) {
  $sqlEvent = "SELECT e.eid, e.etitle, e.etext, e.etag, e.created_at, e.updated_at, e.AlbumId, al.AlbumId AS albumRefId, al.album_name
               FROM events e
               LEFT JOIN albums al ON e.AlbumId = al.AlbumId
               WHERE e.eid = ? AND e.status = '1'";

  if ($stmt = $con->prepare($sqlEvent)) {
      $stmt->bind_param("i", $eventId);
      $stmt->execute();
      $resultEvent = $stmt->get_result();

      if ($resultEvent->num_rows > 0) {
          $eventDetails = $resultEvent->fetch_assoc();

          // Output event and album info
      
      } else {
          echo "<p>Event not found.</p>";
          exit;
      }
      $stmt->close();
  } else {
      echo "Error preparing event query: " . $con->error;
      exit;
  }

  $gg=$eventDetails['AlbumId'];

    // Fetch album images
   // Fetch album images
if ($eventDetails && $eventDetails['AlbumId']) {
    $sqlImages = "SELECT image_path FROM album_images WHERE AlbumId = '$gg'"; // Use ?
    if ($stmt = $con->prepare($sqlImages)) {
        $stmt->bind_param("i", $eventDetails['AlbumId']); // Matches the single ?
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

// --- HTML Block ---
?>

<div class="container1">
<div class="main-content">
    <?php if ($eventDetails && !empty($albumImages)): ?>
        <img src="admin/<?php echo htmlspecialchars($albumImages[0]); ?>" 
             alt="<?php echo htmlspecialchars($eventDetails['etitle']); ?>" 
             class="main-image" />

        <div class="image-album">
            <?php foreach ($albumImages as $image): ?>
                <img src="admin/<?php echo htmlspecialchars($image); ?>" 
                     alt="Thumb" 
                     class="thumb" 
                     onclick="changeMainImage(this)">
            <?php endforeach; ?>
        </div>

        <div class="tags">
            <?php if (!empty($eventDetails['etag'])): ?>
                <?php
                $tags = explode(',', $eventDetails['etag']);
                foreach ($tags as $tag):
                    $tag = trim($tag);
                    $tagClass = 'dark'; // You can customize tag styles based on tag
                    echo '<span class="tag ' . htmlspecialchars($tagClass) . '">' . htmlspecialchars($tag) . '</span>';
                endforeach;
                ?>
            <?php endif; ?>
            <span class="date"><?php echo date("d F Y", strtotime($eventDetails['created_at'])); ?></span>
            <span class="author">
                Posted on <?php echo date("Y-m-d H:i:s", strtotime($eventDetails['created_at'])); ?> |
                Last Updated on <?php echo date("Y-m-d H:i:s", strtotime($eventDetails['updated_at'])); ?>
            </span>
        </div>

        <h1><?php echo htmlspecialchars($eventDetails['etitle']); ?></h1>

        <!-- ✅ etext now renders HTML directly -->
        <div class="article-text">
            <?php echo $eventDetails['etext']; ?>
        </div>

        <div class="author-footer">
            <img src="avatar.png" alt="Author" class="avatar">
            <span>Admin</span> 
            <span class="views">👁️ 
                <?php // echo htmlspecialchars($eventDetails['views']); ?>
            </span>
        </div>
    <?php else: ?>
        <p>No details or images available for this event.</p>
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
            $sqlTrending = "SELECT nid,ntitle, created_at FROM news WHERE status = '1' ORDER BY count DESC LIMIT 3";
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
    mainImage.src = thumb.src;
}
</script>

<?php include_once("include/footer.php");?>

<!-- Image Switcher Script -->
<script>
  function changeMainImage(el) {
    const mainImg = document.querySelector('.main-image');
    mainImg.src = el.src;
  }
</script>

</body>
</html>
