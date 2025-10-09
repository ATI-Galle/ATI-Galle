<?php
include_once ('include/header.php');

// --- 1. CONNECT TO THE DATABASE AND FETCH DATA ---
include_once ('include/config.php'); // Make sure this path is correct

$about = null;
$points = [];

if ($con) {
    // Fetch the main page content (we assume there's only one row with id=1)
    $result_about = $con->query("SELECT * FROM about_page_content WHERE id = 1");
    if ($result_about) {
        $about = $result_about->fetch_assoc();
    }

    // Fetch all the 'Why Choose Us' points that are active and order them
    $result_points = $con->query("SELECT * FROM choose_us_points WHERE is_active = TRUE ORDER BY display_order ASC");
    if ($result_points) {
        while ($row = $result_points->fetch_assoc()) {
            $points[] = $row;
        }
    }
}
// If the database connection fails or there's no data, the page will show placeholders.
?>

<style>
    :root {
        --primary-color: #fec524; /* Yellow from your buttons/news section */
        --dark-color: #212529;    /* Dark color for text and headings */
        --light-color: #ffffff;   /* White for card backgrounds */
        --text-gray: #6c757d;     /* A softer gray for body text */
    }

    /* Page Banner Section */
    .about-banner {
        /* The background image is now set inline using PHP */
        background-size: cover;
        background-position: center;
        padding: 100px 0;
        text-align: center;
        color: var(--light-color);
    }

    .about-banner h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .about-banner p {
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    /* General Section Styling */
    .about-section {
        padding: 80px 20px;
    }
    
    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 15px;
    }

    .section-title .divider {
        width: 100px;
        height: 4px;
        background-color: var(--primary-color);
        margin: 0 auto;
        border-radius: 2px;
    }

    .about-container {
        max-width: 1140px;
        margin: 0 auto;
    }

    /* Welcome Section */
    .welcome-section .row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }
    .welcome-section .col {
        flex: 1;
        padding: 20px;
        min-width: 300px;
    }
    .welcome-section img {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .welcome-section h3 {
        font-size: 2rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 20px;
    }
    .welcome-section p {
        color: var(--text-gray);
        line-height: 1.7;
    }

    /* Vision & Mission Section */
    .vision-mission-section {
        background-color: #f8f9fa; /* Light gray background */
    }
    .vision-mission-section .row {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
    }
    .vision-mission-section .card {
        flex: 1;
        background: var(--light-color);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
        min-width: 300px;
    }
    .vision-mission-section .card i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    .vision-mission-section .card h3 {
        font-size: 1.8rem;
        color: var(--dark-color);
        margin-bottom: 15px;
    }
    .vision-mission-section .card p {
        color: var(--text-gray);
        line-height: 1.6;
    }

    /* Why Choose Us Section */
    .choose-us-section .row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    .choose-us-card {
        background: var(--light-color);
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .choose-us-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .choose-us-card i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 20px;
    }
    .choose-us-card h4 {
        font-size: 1.5rem;
        color: var(--dark-color);
        margin-bottom: 15px;
    }
    .choose-us-card p {
        color: var(--text-gray);
        line-height: 1.6;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


<?php if ($about): // Only show the content if it was successfully fetched from the database ?>

<section class="about-banner" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo 'admin/'.htmlspecialchars($about['banner_image_url'] ?? 'https://placehold.co/1920x500'); ?>');">
    <div class="about-container">
        <h1><?php echo htmlspecialchars($about['banner_title']); ?></h1>
        <p><?php echo htmlspecialchars($about['banner_subtitle']); ?></p>
    </div>
</section>
<section class="about-section welcome-section">
    <div class="about-container">
        <div class="row">
            <div class="col">
                <img src="<?php echo 'admin/'.htmlspecialchars($about['welcome_image_url'] ?? 'https://placehold.co/600x400'); ?>" alt="<?php echo htmlspecialchars($about['welcome_heading']); ?>">
            </div>
            <div class="col">
                <h3><?php echo htmlspecialchars($about['welcome_heading']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($about['welcome_text'])); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="about-section vision-mission-section">
    <div class="about-container">
        <div class="section-title">
            <h2><?php echo htmlspecialchars($about['principles_heading']); ?></h2>
            <div class="divider"></div>
        </div>
        <div class="row">
            <div class="card">
                <i class="<?php echo htmlspecialchars($about['vision_icon']); ?>"></i>
                <h3><?php echo htmlspecialchars($about['vision_title']); ?></h3>
                <p><?php echo htmlspecialchars($about['vision_text']); ?></p>
            </div>
            <div class="card">
                <i class="<?php echo htmlspecialchars($about['mission_icon']); ?>"></i>
                <h3><?php echo htmlspecialchars($about['mission_title']); ?></h3>
                <p><?php echo htmlspecialchars($about['mission_text']); ?></p>
            </div>
        </div>
    </div>
</section>


<section class="about-section choose-us-section">
    <div class="about-container">
        <div class="section-title">
            <h2><?php echo htmlspecialchars($about['choose_us_heading']); ?></h2>
            <div class="divider"></div>
        </div>
        <div class="row">
            <?php if (!empty($points)): // Check if there are any points to display ?>
                <?php foreach ($points as $point): // Loop through each point from the database ?>
                    <div class="choose-us-card">
                        <i class="<?php echo htmlspecialchars($point['icon_class']); ?>"></i>
                        <h4><?php echo htmlspecialchars($point['title']); ?></h4>
                        <p><?php echo htmlspecialchars($point['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">Information coming soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php else: // If $about data could not be fetched, show a generic message ?>
    <section class="about-section">
        <div class="about-container">
            <h2 style="text-align: center;">Content Currently Unavailable</h2>
            <p style="text-align: center;">Please check back later, or contact the site administrator if this issue persists.</p>
        </div>
    </section>
<?php endif; ?>


<?php
include_once ('include/footer.php');
?>