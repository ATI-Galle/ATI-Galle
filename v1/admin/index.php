<?php
// Enable error reporting to see all issues
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Your database connection file
require_once "include/config.php";

// Check if the connection object exists and is valid
if (!$conn || $conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Ensure session is started for username
if (session_status() == PHP_SESSION_NONE) {
}

// Set the timezone
date_default_timezone_set('Asia/Colombo');


// --- DATA FETCHING WITH ERROR CHECKING ---

// Helper function for running queries and handling errors
function run_query($connection, $sql) {
    $query = mysqli_query($connection, $sql);
    if (!$query) {
        // This will stop the script and show the exact SQL error
        die("SQL Error: " . mysqli_error($connection) . "<br>Faulty Query: " . $sql);
    }
    return $query;
}

// Basic statistics
$total_albums = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM albums"))['c'] ?? 0;
$total_news = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM news"))['c'] ?? 0;
$active_events = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM events WHERE status = 1"))['c'] ?? 0;
$total_users = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM users"))['c'] ?? 0;

// Latest content for the "Recent Activity" feed
$latest_albums_query = run_query($conn, "SELECT AlbumId, album_name, created_at FROM albums ORDER BY created_at DESC LIMIT 3");
$latest_albums = mysqli_fetch_all($latest_albums_query, MYSQLI_ASSOC);

$latest_news_query = run_query($conn, "SELECT nid, ntitle, created_at FROM news ORDER BY created_at DESC LIMIT 3");
$latest_news = mysqli_fetch_all($latest_news_query, MYSQLI_ASSOC);

// Most viewed news for Bar Chart
$most_viewed_news_query = run_query($conn, "SELECT `ntitle`, `count` FROM `news` WHERE `count` > 0 ORDER BY `count` DESC LIMIT 5");
$most_viewed_news = mysqli_fetch_all($most_viewed_news_query, MYSQLI_ASSOC);

// Prepare data for JavaScript charts. This prevents errors from quotes in titles.
$bar_chart_labels = json_encode(array_column($most_viewed_news, 'ntitle'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
$bar_chart_data = json_encode(array_column($most_viewed_news, 'count'));

// Recent user registrations
$user_logins_query = run_query($conn, "SELECT id, username, created_at FROM users ORDER BY created_at DESC LIMIT 5");
$user_logins = mysqli_fetch_all($user_logins_query, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Modern Admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <style>
        :root {
            --bg-color: #f4f7fa; --card-bg-color: #ffffff; --text-color: #333; --heading-color: #1f3bb3;
            --border-color: #e0e0e0; --shadow-color: rgba(0, 0, 0, 0.05); --sidebar-bg: #ffffff; --header-bg: #ffffff;
        }
        body.dark-mode {
            --bg-color: #1e1e2d; --card-bg-color: #27293d; --text-color: #d0d2d6; --heading-color: #8950fc;
            --border-color: #323447; --shadow-color: rgba(0, 0, 0, 0.2); --sidebar-bg: #1e1e2d; --header-bg: #27293d;
        }
        body { background-color: var(--bg-color); color: var(--text-color); transition: background-color 0.3s ease, color 0.3s ease; }
        .card { background-color: var(--card-bg-color); border: 1px solid var(--border-color); box-shadow: 0 4px 12px var(--shadow-color); border-radius: 12px; transition: all 0.3s ease; }
        .card .card-body { padding: 1.5rem; }
        .card .card-title { color: var(--heading-color); font-weight: 600; }
        .main-panel, .content-wrapper, .navbar, .sidebar { background: var(--bg-color) !important; }
        .sidebar, .navbar, .navbar .navbar-brand-wrapper { background: var(--sidebar-bg) !important; }
        .stat-card { position: relative; overflow: hidden; color: white; border: none; }
        .stat-card .card-body { z-index: 1; }
        .stat-card i { font-size: 3rem; position: absolute; right: 20px; top: 50%; transform: translateY(-50%); opacity: 0.2; transition: all 0.3s ease; }
        .stat-card:hover i { opacity: 0.4; transform: translateY(-50%) scale(1.1); }
        .bg-gradient-info { background: linear-gradient(45deg, #4747A1, #306998); }
        .bg-gradient-success { background: linear-gradient(45deg, #3E7C17, #55A630); }
        .bg-gradient-danger { background: linear-gradient(45deg, #D62828, #F77F00); }
        .bg-gradient-warning { background: linear-gradient(45deg, #FCA311, #FFC300); color: #fff; }
        .theme-switch-wrapper { display: flex; align-items: center; }
        .theme-switch { display: inline-block; height: 24px; position: relative; width: 50px; margin: 0 10px; }
        .theme-switch input { display: none; }
        .slider { background-color: #ccc; bottom: 0; cursor: pointer; left: 0; position: absolute; right: 0; top: 0; transition: .4s; border-radius: 34px; }
        .slider:before { background-color: #fff; bottom: 4px; content: ""; height: 16px; left: 4px; position: absolute; transition: .4s; width: 16px; border-radius: 50%; }
        input:checked + .slider { background-color: var(--heading-color); }
        input:checked + .slider:before { transform: translateX(26px); }
    </style>
</head>
<body>
    <div class="container-scroller">
        <?php include 'include/header.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include 'include/sidebar.php'; ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-end flex-wrap">
                                    <div class="mr-md-3 mr-xl-5">
                                        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION["username"] ?? 'Admin'); ?>!</h2>
                                        <p class="mb-md-0">Here's a summary of your system.</p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center mt-2 mt-md-0">
                                    <div class="theme-switch-wrapper">
                                      <i class="mdi mdi-weather-sunny"></i>
                                      <label class="theme-switch" for="checkbox">
                                        <input type="checkbox" id="checkbox" />
                                        <div class="slider round"></div>
                                      </label>
                                      <i class="mdi mdi-weather-night"></i>
                                    </div>
                                    <div id="live-clock" class="ml-3 font-weight-bold" style="min-width: 210px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-4 stretch-card transparent"><div class="card stat-card bg-gradient-danger"><div class="card-body"><p class="mb-4">Total Albums</p><p class="fs-30 mb-2"><?php echo $total_albums; ?></p><i class="mdi mdi-folder-multiple-image"></i></div></div></div>
                        <div class="col-md-3 mb-4 stretch-card transparent"><div class="card stat-card bg-gradient-info"><div class="card-body"><p class="mb-4">Total News Articles</p><p class="fs-30 mb-2"><?php echo $total_news; ?></p><i class="mdi mdi-newspaper"></i></div></div></div>
                        <div class="col-md-3 mb-4 stretch-card transparent"><div class="card stat-card bg-gradient-success"><div class="card-body"><p class="mb-4">Active Events</p><p class="fs-30 mb-2"><?php echo $active_events; ?></p><i class="mdi mdi-calendar-check"></i></div></div></div>
                        <div class="col-md-3 mb-4 stretch-card transparent"><div class="card stat-card bg-gradient-warning"><div class="card-body"><p class="mb-4">Total Users</p><p class="fs-30 mb-2"><?php echo $total_users; ?></p><i class="mdi mdi-account-multiple"></i></div></div></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-8 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Most Viewed News</h4>
                                    <canvas id="mostViewedNewsChart" style="height:250px"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Recent Activity</h4>
                                    <ul class="list-unstyled">
                                        <?php foreach ($latest_news as $item): ?>
                                        <li class="d-flex align-items-center mb-3"><div class="mr-3"><span class="btn btn-sm btn-info btn-icon"><i class="mdi mdi-newspaper"></i></span></div><div><strong>News:</strong> <?php echo htmlspecialchars(substr($item['ntitle'], 0, 25)); ?>...<br><small class="text-muted"><?php echo date("M d, Y", strtotime($item['created_at'])); ?></small></div></li>
                                        <?php endforeach; ?>
                                        <?php foreach ($latest_albums as $item): ?>
                                        <li class="d-flex align-items-center mb-3"><div class="mr-3"><span class="btn btn-sm btn-danger btn-icon"><i class="mdi mdi-folder-multiple-image"></i></span></div><div><strong>Album:</strong> <?php echo htmlspecialchars(substr($item['album_name'], 0, 25)); ?>...<br><small class="text-muted"><?php echo date("M d, Y", strtotime($item['created_at'])); ?></small></div></li>
                                        <?php endforeach; ?>
                                        <?php if (empty($latest_news) && empty($latest_albums)): ?>
                                        <li>No recent activity found.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Recent User Registrations</h4>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead><tr><th>User ID</th><th>Username</th><th>Registered On</th></tr></thead>
                                            <tbody>
                                            <?php if (!empty($user_logins)): ?>
                                                <?php foreach ($user_logins as $user): ?>
                                                <tr><td>#<?php echo htmlspecialchars($user['id']); ?></td><td><?php echo htmlspecialchars($user['username']); ?></td><td><?php echo date("Y-m-d h:i A", strtotime($user['created_at'])); ?></td></tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3" class="text-center">No recent user registrations found.</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php // include 'include/footer.php'; ?>
            </div>
        </div>
    </div>

    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/template.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- LIVE CLOCK ---
        const clockElement = document.getElementById('live-clock');
        function updateClock() {
            if (!clockElement) return;
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            clockElement.textContent = `Colombo Time: ${timeString}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- DARK MODE TOGGLE ---
        const toggleSwitch = document.getElementById('checkbox');
        const currentTheme = localStorage.getItem('theme');

        if (currentTheme) {
            document.body.classList.add(currentTheme);
            if (currentTheme === 'dark-mode' && toggleSwitch) {
                toggleSwitch.checked = true;
            }
        }

        function switchTheme(e) {
            document.body.classList.toggle('dark-mode', e.target.checked);
            localStorage.setItem('theme', e.target.checked ? 'dark-mode' : 'light-mode');
            renderMostViewedNewsChart();
        }

        if(toggleSwitch) {
            toggleSwitch.addEventListener('change', switchTheme, false);
        }

        // --- CHARTS.JS IMPLEMENTATION ---
        let mostViewedNewsChart = null; 

        function getChartColors() {
            const isDarkMode = document.body.classList.contains('dark-mode');
            return {
                gridColor: isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                textColor: isDarkMode ? '#d0d2d6' : '#666'
            };
        }

        function renderMostViewedNewsChart() {
            const canvas = document.getElementById('mostViewedNewsChart');
            if (!canvas) {
                console.error("Chart canvas element not found!");
                return; 
            }
            const ctx = canvas.getContext('2d');
            
            // This is the data from PHP. It is already safely encoded.
            const labels = <?php echo $bar_chart_labels; ?>;
            const data = <?php echo $bar_chart_data; ?>;
            
            // Destroy the old chart instance if it exists
            if (mostViewedNewsChart) {
                mostViewedNewsChart.destroy();
            }

            // If there's no data, display a message instead of an empty chart
            if (!labels || labels.length === 0) {
                 ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear previous drawings
                 ctx.fillStyle = getChartColors().textColor;
                 ctx.textAlign = 'center';
                 ctx.fillText("No view data available to display.", canvas.width / 2, 50);
                 return;
            }

            const colors = getChartColors();
            
            mostViewedNewsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Views',
                        data: data,
                        backgroundColor: 'rgba(252, 163, 17, 0.7)',
                        borderColor: 'rgba(252, 163, 17, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            grid: { color: colors.gridColor }, 
                            ticks: { color: colors.textColor, precision: 0 }
                        }, 
                        x: { 
                            grid: { display: false }, 
                            ticks: { color: colors.textColor }
                        }
                    },
                    plugins: { 
                        legend: { display: false }
                    }
                }
            });
        }
        
        // Initial render of the chart
        renderMostViewedNewsChart();
    });
    </script>
</body>
</html>