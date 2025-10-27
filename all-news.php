<?php

// Include your header, which should also include the database connection (config.php)
include_once('include/header.php');

// Database connection check
if (!isset($con) || !$con) {
    echo "<div class='message-area error'>Database connection error. Please check config.php.</div>";
}

// Set timezone for date comparisons
date_default_timezone_set('Asia/Colombo'); 

?>

<style>
/* Custom styles for the news page - Card View */
.news-container {
    padding: 30px 0;
    background-color: #f8f8f8;
}

.news-section-title {
    text-align: center;
    margin-bottom: 40px;
    color: #333;
    font-size: 2.5rem;
    font-weight: 700;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
}

.news-card {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.news-card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}

.news-card-body {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.news-card-category {
    font-size: 0.85rem;
    color: #007bff;
    font-weight: 600;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.news-card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
    line-height: 1.3;
    flex-grow: 1;
}

.news-card-text {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
    margin-bottom: 15px;
}

.news-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
    margin-top: auto; /* Push footer to the bottom */
}

.news-card-date {
    font-size: 0.8rem;
    color: #999;
}

.read-more-btn {
    display: inline-block;
    background-color: #007bff;
    color: #fff;
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

.read-more-btn:hover {
    background-color: #0056b3;
    color: #fff; /* Ensure text remains white on hover */
    text-decoration: none;
}

.latest-news-tag {
    background-color: #dc3545; /* Red color */
    color: white;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 5px;
    margin-left: 10px;
    vertical-align: middle;
    display: inline-block;
    font-weight: bold;
}

/* Category Sections */
.news-category-section {
    margin-bottom: 50px;
}

.news-category-section h3 {
    text-align: center;
    margin-bottom: 30px;
    color: #007bff;
    font-size: 2rem;
    border-bottom: 2px solid #fec524;
    padding-bottom: 10px;
    display: inline-block;
    width: auto;
    margin-left: auto;
    margin-right: auto;
    display: block;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .news-grid {
        grid-template-columns: 1fr;
    }
    .news-section-title {
        font-size: 2rem;
    }
    .news-card-title {
        font-size: 1.3rem;
    }
    .news-category-section h3 {
        font-size: 1.7rem;
    }
}
</style>

<div class="news-container">
    <div class="container">
        <h2 class="news-section-title">Latest News & Updates</h2>

        <?php
        $categorized_news = [];

        // Fetch all news items, including the 'cid' column
        $news_query = mysqli_query($con, "SELECT nid, ntitle, cid, ntext, nimg, created_at, updated_at FROM news ORDER BY created_at DESC");

        if ($news_query && mysqli_num_rows($news_query) > 0) {
            while ($row = mysqli_fetch_assoc($news_query)) {
                
                // Determine category based on 'cid'
                if (!empty($row['cid'])) {
                    $category_name = htmlspecialchars(strtoupper($row['cid'])) . ' News';
                } else {
                    $category_name = 'General News'; // Default for empty or NULL cid
                }

                // Group news by category
                $categorized_news[$category_name][] = $row;
            }
        } else {
            echo "<div class='alert alert-info text-center'>No news articles found.</div>";
        }

        // Display categorized news
        foreach ($categorized_news as $category => $news_items) {
            echo "<div class='news-category-section'>";
            echo "<h3>{$category}</h3>";
            echo "<div class='news-grid'>";
            foreach ($news_items as $news) {
                $is_latest = false;
                $created_timestamp = strtotime($news['created_at']);
                $updated_timestamp = strtotime($news['updated_at']);

                // Consider "latest" if created/updated within the last 48 hours
                if ((time() - $created_timestamp) < (2 * 24 * 60 * 60) || (time() - $updated_timestamp) < (2 * 24 * 60 * 60)) {
                    $is_latest = true;
                }


           

                // Sanitize description for preview
                $description_preview = strip_tags($news['ntext']);
                $description_preview = mb_substr($description_preview, 0, 150) . (mb_strlen($description_preview) > 150 ? '...' : '');
                ?>
                <div class="news-card">
                <img src="admin/<?php echo htmlspecialchars($news['nimg']); ?>" alt="<?php echo htmlspecialchars($news['ntitle']); ?>" class="news-card-image">                    <div class="news-card-body">
                        <div class="news-card-category"><?php echo htmlspecialchars($category); ?></div>
                        <h4 class="news-card-title">
                            <?php echo htmlspecialchars($news['ntitle']); ?>
                            <?php if ($is_latest): ?>
                                <span class="latest-news-tag">LATEST</span>
                            <?php endif; ?>
                        </h4>
                        <p class="news-card-text"><?php echo htmlspecialchars($description_preview); ?></p>
                        <div class="news-card-footer">
                            <span class="news-card-date">Published: <?php echo date('Y-m-d', strtotime($news['created_at'])); ?></span>
                            <a href="news.php?nid=<?php echo htmlspecialchars($news['nid']); ?>" class="read-more-btn">Read More</a>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo "</div>"; // Close news-grid
            echo "</div>"; // Close news-category-section
        }
        ?>
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