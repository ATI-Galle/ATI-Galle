<?php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for development
ini_set('display_errors', 0);
error_reporting(E_ALL);


// Your database connection file
require_once 'include/config.php';


// Redirect to login if user is not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Check for a valid database connection
if (!$conn || $conn->connect_error) {
    die('Database Connection Failed: ' . ($conn ? $conn->connect_error : 'Connection object not found'));
}

// Set timezone
date_default_timezone_set('Asia/Colombo');


// --- (NEW) DEFINE CURRENT VIEW ---
// This controls what to show: 'dashboard', 'users', 'logs', or 'devices'
$view = $_GET['view'] ?? 'dashboard';


// --- HELPER FUNCTIONS ---
function run_query($connection, $sql) {
    $query = mysqli_query($connection, $sql);
    if (!$query) {
        die('SQL Error: ' . mysqli_error($connection) . '<br>Faulty Query: ' . $sql);
    }
    return $query;
}

// Function to format time nicely (CORRECTED)
function time_ago($datetime, $full = false) {
    if (!$datetime) {
        return 'N/A';
    }
    // Create the current time object in the script's default timezone ('Asia/Colombo')
    $now = new DateTime();

    // Create a time object from the database string, assuming it's in UTC
    $ago = new DateTime($datetime, new DateTimeZone('UTC'));

    // Convert the database time to the script's timezone for an accurate comparison
    $ago->setTimezone(new DateTimeZone(date_default_timezone_get()));

    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day',
        'h' => 'hour', 'i' => 'minute', 's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


// (NEW) Helper function to parse user agent (CORRECTED)
function parse_device_from_ua($ua) {
    $platform = 'Unknown';
    $browser = 'Unknown';

    // Get Platform
    if (preg_match('/linux/i', $ua)) {
        $platform = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
        $platform = 'Mac';
    } elseif (preg_match('/windows|win32/i', $ua)) {
        $platform = 'Windows';
    } elseif (preg_match('/android/i', $ua)) {
        $platform = 'Android';
    } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
        $platform = 'iOS';
    }
    
    // Get Browser
    if (preg_match('/MSIE/i', $ua) && !preg_match('/Opera/i', $ua)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $ua)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $ua)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $ua)) {
        $browser = 'Safari';
    } elseif (preg_match('/Opera/i', $ua)) {
        $browser = 'Opera';
    } elseif (preg_match('/Netscape/i', $ua)) {
        $browser = 'Netscape';
    }
    
    // Corrected return statement to concatenate the strings
    return $platform . ' / ' . $browser;
}


// --- DEFINE USER PERMISSIONS ---
$user_role = $_SESSION['role'] ?? 'user';
$user_cid = $_SESSION['cid'] ?? ''; // Assuming 'cid' is the session key
$is_super_admin = ($user_role === 'super_admin' || $user_cid === 'SAdmin');


// --- 1. KPI DATA FETCHING (Only for dashboard view) ---
if ($view === 'dashboard') {
    $total_courses = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM course WHERE status = 1"))['c'] ?? 0;
    $total_staff = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM staff WHERE status = 1"))['c'] ?? 0;
    $total_news = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM news WHERE status = 1"))['c'] ?? 0;
    $active_events = mysqli_fetch_assoc(run_query($conn, "SELECT COUNT(*) AS c FROM events WHERE status = 1"))['c'] ?? 0;

    // --- 2. ACTION CENTER DATA FETCHING ---
    $pending_reviews_query = run_query($conn, "SELECT id, user_name, rating FROM reviews WHERE approved = 0 ORDER BY created_at DESC LIMIT 5");
    $pending_reviews = mysqli_fetch_all($pending_reviews_query, MYSQLI_ASSOC);

    $closing_tenders_query = run_query($conn, "SELECT title, closing_date FROM tenders WHERE status = 'Open' AND closing_date >= NOW() ORDER BY closing_date ASC LIMIT 5");
    $closing_tenders = mysqli_fetch_all($closing_tenders_query, MYSQLI_ASSOC);

    $pending_announcements = [];
    if ($is_super_admin) {
        $pending_ann_query = run_query($conn, "SELECT announcement_id, title FROM university_announcements WHERE status = 2 ORDER BY created_at ASC LIMIT 5");
        $pending_announcements = mysqli_fetch_all($pending_ann_query, MYSQLI_ASSOC);
    }

    // --- 3. RECENT ACTIVITY FEED DATA ---
    $latest_staff_query = run_query($conn, "SELECT sname, cid, created_at FROM staff ORDER BY created_at DESC LIMIT 3");
    $latest_staff = mysqli_fetch_all($latest_staff_query, MYSQLI_ASSOC);

    $latest_results_query = run_query($conn, "SELECT exam_title, created_by_cid, upload_date FROM results WHERE is_active = 1 ORDER BY upload_date DESC LIMIT 3");
    $latest_results = mysqli_fetch_all($latest_results_query, MYSQLI_ASSOC);

    $latest_sliders_query = run_query($conn, "SELECT stitle, updated_by, updated_at FROM slider ORDER BY updated_at DESC LIMIT 3");
    $latest_sliders = mysqli_fetch_all($latest_sliders_query, MYSQLI_ASSOC);

    // Combine and sort all activities
    $activity_feed = [];

    foreach ($latest_staff as $item) {
        $activity_feed[] = [
            'type' => 'staff',
            'title' => 'New Staff Added',
            'details' => 'Dept: <b class="text-success">' . htmlspecialchars($item['cid']) . '</b>',
            'time' => $item['created_at'],
            'icon' => 'mdi-account-plus',
            'color' => 'text-success'
        ];
    }

    foreach ($latest_results as $item) {
        $activity_feed[] = [
            'type' => 'results',
            'title' => 'Results Published',
            'details' => 'Dept: <b class="text-primary">' . htmlspecialchars($item['created_by_cid']) . '</b>',
            'time' => $item['upload_date'],
            'icon' => 'mdi-file-check',
            'color' => 'text-primary'
        ];
    }

    foreach ($latest_sliders as $item) {
        $activity_feed[] = [
            'type' => 'slider',
            'title' => 'Slider Edited',
            'details' => 'by <b>' . htmlspecialchars($item['updated_by'] ?: 'N/A') . '</b>',
            'time' => $item['updated_at'],
            'icon' => 'mdi-image-area',
            'color' => 'text-info'
        ];
    }

    // Sort the combined feed by time
    usort($activity_feed, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });

    // Get the 6 most recent activities
    $activity_feed = array_slice($activity_feed, 0, 6);

} // --- END OF DASHBOARD-ONLY DATA FETCHING ---


// --- (NEW) DATA FETCHING FOR ADMIN VIEWS ---
$admin_users_list = [];
$admin_logs_list = [];
$admin_devices_list = [];

if ($is_super_admin) {
    if ($view === 'users') {
        // --- Adjust this query to match your 'admin' or 'users' table ---
        $users_query = run_query($conn, "SELECT id, username, role, cid, status, created_at FROM users ORDER BY id DESC");
        $admin_users_list = mysqli_fetch_all($users_query, MYSQLI_ASSOC);
    } 
    elseif ($view === 'logs') {
        // --- Adjust this query to match your 'login_logs' table ---
        $logs_query = run_query($conn, "SELECT id, user_cid, login_time, ip_address, location, user_agent FROM login_logs ORDER BY login_time DESC LIMIT 100");
        $admin_logs_list = mysqli_fetch_all($logs_query, MYSQLI_ASSOC);
    }
    elseif ($view === 'devices') {
         // --- Adjust this query to match your 'login_logs' table ---
        $devices_query = run_query($conn, "SELECT user_agent, user_cid, COUNT(*) as login_count, MAX(login_time) as last_seen FROM login_logs WHERE user_agent IS NOT NULL AND user_agent != '' GROUP BY user_agent, user_cid ORDER BY last_seen DESC LIMIT 100");
        $admin_devices_list = mysqli_fetch_all($devices_query, MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <style>
        :root {
            --bg-color: #f4f7fa; --card-bg-color: #ffffff; --text-color: #4A4A4A; --heading-color: #1f3bb3;
            --border-color: #e0e0e0; --shadow-color: rgba(0, 0, 0, 0.05); --sidebar-bg: #ffffff;
        }
        body.dark-mode {
            --bg-color: #121212; --card-bg-color: #1e1e1e; --text-color: #d0d2d6; --heading-color: #8950fc;
            --border-color: #323447; --shadow-color: rgba(0, 0, 0, 0.2); --sidebar-bg: #1e1e1e;
        }
        body { background-color: var(--bg-color); color: var(--text-color); }
        .card { background-color: var(--card-bg-color); border: none; box-shadow: 0 4px 15px var(--shadow-color); border-radius: 12px;  }
        .card .card-title { color: var(--heading-color); font-weight: 600; margin-bottom: 1.2rem; }
        .main-panel, .content-wrapper { background: var(--bg-color) !important; }
        .sidebar, .navbar { background: var(--sidebar-bg) !important; border-right: 1px solid var(--border-color); }
        .navbar { border-bottom: 1px solid var(--border-color); }
        .stat-card { position: relative; overflow: hidden; color: white; transition: transform 0.2s ease-in-out; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 3.5rem; position: absolute; right: 15px; bottom: -10px; opacity: 0.2; }
        .bg-gradient-info { background: linear-gradient(45deg, #3652AD, #28B2B8); }
        .bg-gradient-success { background: linear-gradient(45deg, #0A6847, #7ABA78); }
        .bg-gradient-danger { background: linear-gradient(45deg, #A91D3A, #F39F5A); }
        .bg-gradient-warning { background: linear-gradient(45deg, #E48F45, #FFC100); color: #fff; }
        .action-card .list-group-item { background-color: transparent; border-color: var(--border-color); }
        .time-warning { color: #D62828; font-weight: bold; }
        .theme-switch-wrapper { display: flex; align-items: center; }
        .theme-switch { display: inline-block; height: 24px; position: relative; width: 50px; margin: 0 10px; }
        .theme-switch input { display: none; }
        .slider { background-color: #ccc; bottom: 0; cursor: pointer; left: 0; position: absolute; right: 0; top: 0; transition: .4s; border-radius: 34px; }
        .slider:before { background-color: #fff; bottom: 4px; content: ""; height: 16px; left: 4px; position: absolute; transition: .4s; width: 16px; border-radius: 50%; }
        input:checked + .slider { background-color: var(--heading-color); }
        input:checked + .slider:before { transform: translateX(26px); }
        .activity-card {
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 5px var(--shadow-color);
            transition: all 0.2s ease;
            height: 100%;
        }
        .activity-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px var(--shadow-color);
        }
        .activity-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .activity-card-header {
            display: flex;
            align-items: center;
        }
        .activity-card-header i {
            font-size: 1.75rem;
            line-height: 1;
        }
        .activity-card-details {
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        /* (NEW) Styles for admin tables */
        .table thead th {
            color: var(--heading-color);
            font-weight: 600;
        }
        .table td {
            color: var(--text-color);
        }
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
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
                        <div class="col-12 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="font-weight-bold">Super Admin Dashboard</h3>
                                        <?php if ($view === 'dashboard'): ?>
                                            <p class="font-weight-light mb-0">Overview of the institute's web portal activity.</p>
                                        <?php else: ?>
                                            <p class="font-weight-light mb-0">Viewing system data: <?php echo htmlspecialchars(ucfirst($view)); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center">
                                            <div class="theme-switch-wrapper d-none d-md-flex">
                                                <i class="mdi mdi-weather-sunny mr-2"></i>
                                                <label class="theme-switch" for="checkbox">
                                                    <input type="checkbox" id="checkbox" />
                                                    <div class="slider round"></div>
                                                </label>
                                                <i class="mdi mdi-weather-night"></i>
                                            </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <?php if ($view === 'dashboard'): ?>

                        <div class="row">
                            <div class="col-md-3 grid-margin stretch-card">
                                <div class="card stat-card bg-gradient-info"><div class="card-body"><h4 class="font-weight-normal mb-3">Active Courses</h4><h2 class="mb-4"><?php echo $total_courses; ?></h2><i class="mdi mdi-school"></i></div></div>
                            </div>
                            <div class="col-md-3 grid-margin stretch-card">
                                <div class="card stat-card bg-gradient-success"><div class="card-body"><h4 class="font-weight-normal mb-3">Total Staff</h4><h2 class="mb-4"><?php echo $total_staff; ?></h2><i class="mdi mdi-account-multiple"></i></div></div>
                            </div>
                            <div class="col-md-3 grid-margin stretch-card">
                                <div class="card stat-card bg-gradient-danger"><div class="card-body"><h4 class="font-weight-normal mb-3">Published News</h4><h2 class="mb-4"><?php echo $total_news; ?></h2><i class="mdi mdi-newspaper"></i></div></div>
                            </div>
                            <div class="col-md-3 grid-margin stretch-card">
                                <div class="card stat-card bg-gradient-warning"><div class="card-body"><h4 class="font-weight-normal mb-3">Active Events</h4><h2 class="mb-4"><?php echo $active_events; ?></h2><i class="mdi mdi-calendar-star"></i></div></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12"><h4 class="mb-3">Action Center</h4></div>
                            <?php if (!empty($pending_announcements)): ?>
                            <div class="col-lg-4 grid-margin stretch-card">
                                <div class="card action-card"><div class="card-body"><h4 class="card-title"><i class="mdi mdi-bell-ring-outline text-primary"></i> Pending Announcements</h4><ul class="list-group list-group-flush"><?php foreach($pending_announcements as $ann): ?><li class="list-group-item d-flex justify-content-between align-items-center"><span><?php echo htmlspecialchars(substr($ann['title'], 0, 25)); ?>...</span><a href="announcement.php?id=<?php echo $ann['announcement_id']; ?>" class="btn btn-xs btn-outline-primary">Review</a></li><?php endforeach; ?></ul></div></div>
                            </div>
                            <?php endif; ?>
                            <div class="col-lg-4 grid-margin stretch-card">
                                <div class="card action-card"><div class="card-body"><h4 class="card-title"><i class="mdi mdi-star-half text-warning"></i> Pending Reviews</h4><?php if (empty($pending_reviews)): ?><p class="text-muted">No pending reviews.</p><?php else: ?><ul class="list-group list-group-flush"><?php foreach($pending_reviews as $review): ?><li class="list-group-item d-flex justify-content-between align-items-center">Review by <?php echo htmlspecialchars($review['user_name']); ?><span class="badge badge-warning ml-2"><?php echo $review['rating']; ?> ★</span><a href="reviews.php" class="btn btn-xs btn-outline-warning ml-auto">Manage</a></li><?php endforeach; ?></ul><?php endif; ?></div></div>
                            </div>
                            <div class="col-lg-4 grid-margin stretch-card">
                                <div class="card action-card"><div class="card-body"><h4 class="card-title"><i class="mdi mdi-gavel text-danger"></i> Tenders Closing Soon</h4><?php if (empty($closing_tenders)): ?><p class="text-muted">No open tenders.</p><?php else: ?><ul class="list-group list-group-flush"><?php foreach($closing_tenders as $tender): $closingDate = new DateTime($tender['closing_date']); $now = new DateTime(); $interval = $now->diff($closingDate); $daysLeft = $interval->format('%a'); ?><li class="list-group-item d-flex justify-content-between align-items-center"><span><?php echo htmlspecialchars($tender['title']); ?></span><span class="badge <?php echo ($daysLeft < 7) ? 'time-warning' : 'text-muted'; ?>"><?php echo $daysLeft; ?> days left</span></li><?php endforeach; ?></ul><?php endif; ?></div></div>
                            </div>
                        </div>
                        
                        <?php if ($is_super_admin): ?>
                        <div class="row">
                            <div class="col-lg-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title"><i class="mdi mdi-security-network text-danger"></i> Super Admin Tools</h4>
                                        <p class="card-description">
                                            Access user management and system login data.
                                        </p>
                                        <div class="mt-3">
                                            <a href="?view=users" class="btn btn-outline-primary btn-icon-text mr-2">
                                                <i class="mdi mdi-account-search-outline btn-icon-prepend"></i>
                                                View/Manage All Users
                                            </a>
                                            <a href="?view=logs" class="btn btn-outline-info btn-icon-text mr-2">
                                                <i class="mdi mdi-map-marker-radius btn-icon-prepend"></i>
                                                View User Login Logs
                                            </a>
                                            <a href="?view=devices" class="btn btn-outline-success btn-icon-text">
                                                <i class="mdi mdi-cellphone-link btn-icon-prepend"></i>
                                                View User Devices
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-lg-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">Latest Content Activity</h4>
                                        <div class="row">
                                            <?php if (empty($activity_feed)): ?>
                                                <div class="col-12">
                                                    <p class="text-muted">No recent activity.</p>
                                                </div>
                                            <?php else: ?>
                                                <?php foreach($activity_feed as $activity): ?>
                                                    <div class="col-md-6 col-lg-4 grid-margin stretch-card">
                                                        <div class="card activity-card">
                                                            <div class="card-body">
                                                                <div>
                                                                    <div class="activity-card-header">
                                                                        <i class="mdi <?php echo $activity['icon']; ?> <?php echo $activity['color']; ?> mr-3"></i>
                                                                        <div>
                                                                            <h6 class="mb-0"><?php echo $activity['title']; ?></h6>
                                                                            <small class="text-muted"><?php echo time_ago($activity['time']); ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <p class="activity-card-details mb-0">
                                                                        <?php echo $activity['details']; ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    
                    <?php elseif ($view === 'users' && $is_super_admin): ?>
                    
                        <div class="row">
                            <div class="col-lg-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0"><i class="mdi mdi-account-search-outline"></i> Manage All Users</h4>
                                            <a href="?" class="btn btn-light btn-icon-text">
                                                <i class="mdi mdi-arrow-left btn-icon-prepend"></i>
                                                Back to Dashboard
                                            </a>
                                        </div>
                                        <p class="card-description">
                                            View, edit, or manage administrator accounts.
                                            </p>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Username</th>
                                                        <th>Role</th>
                                                        <th>CID</th>
                                                        <th>Status</th>
                                                        <th>Created</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($admin_users_list)): ?>
                                                        <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach($admin_users_list as $user): ?>
                                                        <tr>
                                                            <td><?php echo $user['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                                            <td><?php echo htmlspecialchars($user['cid']); ?></td>
                                                            <td>
                                                                <?php if ($user['status'] == 1): ?>
                                                                    <span class="badge badge-success">Active</span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-danger">Inactive</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo time_ago($user['created_at']); ?></td>
                                                            <td>
                                                                <a href="Admin.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary m-1">Edit</a>
                                                                </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($view === 'logs' && $is_super_admin): ?>

                        <div class="row">
                            <div class="col-lg-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0"><i class="mdi mdi-map-marker-radius"></i> User Login Logs</h4>
                                            <a href="?" class="btn btn-light btn-icon-text">
                                                <i class="mdi mdi-arrow-left btn-icon-prepend"></i>
                                                Back to Dashboard
                                            </a>
                                        </div>
                                        <p class="card-description">
                                            Shows the 100 most recent login attempts.
                                            </p>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>User (CID)</th>
                                                        <th>IP Address</th>
                                                        <th>Location</th>
                                                        <th>Time</th>
                                                        <th>Device/Browser</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($admin_logs_list)): ?>
                                                        <tr><td colspan="5" class="text-center text-muted">No login logs found.</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach($admin_logs_list as $log): ?>
                                                        <tr>
                                                            <td><b><?php echo htmlspecialchars($log['user_cid']); ?></b></td>
                                                            <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                                            <td><?php echo htmlspecialchars($log['location'] ?? 'N/A'); ?></td>
                                                            <td><?php echo time_ago($log['login_time']); ?></td>
                                                            <td title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                                                <?php echo htmlspecialchars(parse_device_from_ua($log['user_agent'])); ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <?php elseif ($view === 'devices' && $is_super_admin): ?>

                        <div class="row">
                            <div class="col-lg-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0"><i class="mdi mdi-cellphone-link"></i> User Devices</h4>
                                            <a href="?" class="btn btn-light btn-icon-text">
                                                <i class="mdi mdi-arrow-left btn-icon-prepend"></i>
                                                Back to Dashboard
                                            </a>
                                        </div>
                                        <p class="card-description">
                                            Shows devices users have logged in with, grouped by user.
                                            </p>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>User (CID)</th>
                                                        <th>Device/Browser</th>
                                                        <th>Total Logins</th>
                                                        <th>Last Seen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (empty($admin_devices_list)): ?>
                                                        <tr><td colspan="4" class="text-center text-muted">No device data found.</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach($admin_devices_list as $device): ?>
                                                        <tr>
                                                            <td><b><?php echo htmlspecialchars($device['user_cid']); ?></b></td>
                                                            <td title="<?php echo htmlspecialchars($device['user_agent']); ?>">
                                                                <?php echo htmlspecialchars(parse_device_from_ua($device['user_agent'])); ?>
                                                            </td>
                                                            <td><?php echo $device['login_count']; ?></td>
                                                            <td><?php echo time_ago($device['last_seen']); ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($view !== 'dashboard'): ?>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <h4 class="alert-heading">Access Denied or Page Not Found</h4>
                                    <p>You either do not have permission to view this page, or the page (<?php echo htmlspecialchars($view); ?>) does not exist.</p>
                                    <hr>
                                    <a href="?" class="btn btn-dark">Go Back to Dashboard</a>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                    </div> <?php include 'include/footer.php'; ?>
            </div> </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        }
        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', switchTheme, false);
        }
    });
    </script>
</body>
</html>