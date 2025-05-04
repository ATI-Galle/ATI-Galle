
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




  <div class="container1">
    <div class="main-content">
      <img src="img/slider/s-1.jpg" alt="Wind Turbines" class="main-image" />
      <div class="tags">
        <span class="tag yellow">Engineering</span>
        <span class="tag dark">Television</span>
        <span class="date">Jan 01, 2045</span>
        <span class="author">Posted by admin on 2024-01-17 00:00:00 | Last Updated by admin on 2025-05-03 08:58:46</span>
      </div>
      <h1>UNS Jean Pierre Lacroix Thanks India for Contribution to Peacekeeping</h1>
      <p class="article-text">
        <!-- Repeat block just for visual mockup -->
        UNS Jean Pierre Lacroix thanks India for contribution to peacekeeping...
      </p>
      <div class="author-footer">
        <img src="avatar.png" alt="Author" class="avatar">
        <span>John Doe</span>
        <span class="views">👁️ 31</span>
      </div>
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
        <div class="news-item">
          <span class="tag dark">National</span>
          <span class="date">2024-01-18</span>
          <p>Shah Holds Meeting With NE States Leaders in Manipur</p>
          <a href="#">Read More →</a>
        </div>

        <br>
        <div class="news-item">
          <span class="tag dark">National</span>
          <span class="date">2024-01-18</span>
          <p>Shah Holds Meeting With NE States Leaders in Manipur</p>
          <a href="#">Read More →</a>
        </div>

        <br>

        <div class="news-item">
          <span class="tag dark">National</span>
          <span class="date">2024-01-18</span>
          <p>Shah Holds Meeting With NE States Leaders in Manipur</p>
          <a href="#">Read More →</a>
        </div>

        
      </div>

      
    </aside>
  </div>

<?php include_once("include/footer.php");?>


</body>
</html>
