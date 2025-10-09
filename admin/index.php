<?php


require_once "include/config.php"; // Adjust path as needed

// Set the timezone to Asia/Colombo
date_default_timezone_set('Asia/Colombo');
$current_datetime = date("Y-m-d H:i:s");

// Fetch basic statistics
$sql_albums = "SELECT COUNT(*) AS total_albums FROM albums";
$result_albums = mysqli_query($conn, $sql_albums);
$total_albums = mysqli_fetch_assoc($result_albums)['total_albums'] ?? 0;

$sql_news = "SELECT COUNT(*) AS total_news FROM news";
$result_news = mysqli_query($conn, $sql_news);
$total_news = mysqli_fetch_assoc($result_news)['total_news'] ?? 0;

$sql_events_active = "SELECT COUNT(*) AS active_events FROM events WHERE status = 1";
$result_events_active = mysqli_query($conn, $sql_events_active);
$active_events = mysqli_fetch_assoc($result_events_active)['active_events'] ?? 0;

$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $sql_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'] ?? 0;

// Fetch latest albums
$sql_latest_albums = "SELECT AlbumId, album_name, created_at FROM albums ORDER BY created_at DESC LIMIT 5";
$result_latest_albums = mysqli_query($conn, $sql_latest_albums);
$latest_albums = mysqli_fetch_all($result_latest_albums, MYSQLI_ASSOC);

// Fetch latest news
$sql_latest_news = "SELECT nid, ntitle, created_at FROM news ORDER BY created_at DESC LIMIT 5";
$result_latest_news = mysqli_query($conn, $sql_latest_news);
$latest_news = mysqli_fetch_all($result_latest_news, MYSQLI_ASSOC);

// Fetch most viewed news
$sql_most_viewed_news = "SELECT nid, ntitle, count FROM news ORDER BY count DESC LIMIT 5";
$result_most_viewed_news = mysqli_query($conn, $sql_most_viewed_news);
$most_viewed_news = mysqli_fetch_all($result_most_viewed_news, MYSQLI_ASSOC);

// Fetch most viewed events (assuming you might want to track views - you'd need to add a 'view_count' column to the 'events' table and update it on view)
// For now, I'll order by created_at as there's no view count. Adjust if you add a view_count column.
$sql_most_viewed_events = "SELECT eid, etitle, created_at FROM events ORDER BY created_at DESC LIMIT 5";
$result_most_viewed_events = mysqli_query($conn, $sql_most_viewed_events);
$most_viewed_events = mysqli_fetch_all($result_most_viewed_events, MYSQLI_ASSOC);

// Fetch user login times (you would typically log this in a separate table or update a 'last_login' column in the 'users' table on login)
// Since there's no such data in your provided 'users' table, I'll fetch the creation times as a placeholder.
$sql_user_logins = "SELECT id, username, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$result_user_logins = mysqli_query($conn, $sql_user_logins);
$user_logins = mysqli_fetch_all($result_user_logins, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style>

body{
    margin-top: 0px; /* Adjust as needed */

}

        .container-scroller{
            width:1400px;
            /* margin:60px; Consider reducing this if the overall top space is too much */
            /* For example, to reduce only top margin: */
            margin-top: 0px; /* Adjust as needed */
            margin-left: 60px;
            margin-right: 60px;
            margin-bottom: 60px;
        }
        /* Target the content-wrapper to reduce its top padding */
        .main-panel .content-wrapper {
            /* Skydash default padding-top can be around 1.5rem to 2.5rem. */
            /* Adjust this value to reduce the space. e.g., 1rem, 15px, or even 0. */
            padding-top: 0px; /* Example: 1rem of padding. Adjust as needed. */
        }

        /* Specifically target the welcome heading to ensure no extra top margin */
        .welcome-heading {
            margin-top: 0 !important; /* Remove any top margin from the h3 */
            /* If you still want a little space, use a small value like 0.5rem */
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
                        <div class="col-md-12 grid-margin">
                            <div class="row">
                                <div class="col-12 col-lg-8 mb-2 mb-lg-0">
                                    <h3 class="font-weight-bold">Welcome <?php echo htmlspecialchars($_SESSION["username"]); ?></h3>
                                    <h6 class="font-weight-normal mb-0">Here's a summary of your system.</h6>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="d-flex align-items-center justify-content-md-end">
                                        <p class="mb-0">
                                            Current Date & Time: <?php echo $current_datetime; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 stretch-card grid-margin">
                            <div class="card bg-gradient-danger card-img-holder text-white">
                                <div class="card-body">
                                    <img src="uploads/circle.svg" class="card-img-absolute" alt="circle-image" />
                                    <h4 class="font-weight-normal mb-3">Total Albums <i class="mdi mdi-folder-image mdi-24px float-right"></i>
                                    </h4>
                                    <h2 class="mb-5"><?php echo $total_albums; ?></h2>
                                    <h6 class="card-text">Created albums</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 stretch-card grid-margin">
                            <div class="card bg-gradient-info card-img-holder text-white">
                                <div class="card-body">
                                    <img src="uploads/circle.svg" class="card-img-absolute" alt="circle-image" />
                                    <h4 class="font-weight-normal mb-3">Total News <i class="mdi mdi-newspaper mdi-24px float-right"></i>
                                    </h4>
                                    <h2 class="mb-5"><?php echo $total_news; ?></h2>
                                    <h6 class="card-text">Published news articles</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 stretch-card grid-margin">
                            <div class="card bg-gradient-success card-img-holder text-white">
                                <div class="card-body">
                                    <img src="uploads/circle.svg" class="card-img-absolute" alt="circle-image" />
                                    <h4 class="font-weight-normal mb-3">Active Events <i class="mdi mdi-calendar-check mdi-24px float-right"></i>
                                    </h4>
                                    <h2 class="mb-5"><?php echo $active_events; ?></h2>
                                    <h6 class="card-text">Currently active events</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 stretch-card grid-margin">
                            <div class="card bg-gradient-warning card-img-holder text-white">
                                <div class="card-body">
                                    <img src="uploads/circle.svg" class="card-img-absolute" alt="circle-image" />
                                    <h4 class="font-weight-normal mb-3">Total Users <i class="mdi mdi-account-multiple mdi-24px float-right"></i>
                                    </h4>
                                    <h2 class="mb-5"><?php echo $total_users; ?></h2>
                                    <h6 class="card-text">Registered users</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Latest Albums</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Album ID</th>
                                                    <th>Name</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($latest_albums)): ?>
                                                    <?php foreach ($latest_albums as $album): ?>
                                                        <tr>
                                                            <td class="py-1"><?php echo htmlspecialchars($album['AlbumId']); ?></td>
                                                            <td><?php echo htmlspecialchars($album['album_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($album['created_at']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3">No albums found.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Latest News</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>News ID</th>
                                                    <th>Title</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($latest_news)): ?>
                                                    <?php foreach ($latest_news as $news_item): ?>
                                                        <tr>
                                                            <td class="py-1"><?php echo htmlspecialchars($news_item['nid']); ?></td>
                                                            <td><?php echo htmlspecialchars($news_item['ntitle']); ?></td>
                                                            <td><?php echo htmlspecialchars($news_item['created_at']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3">No news found.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Most Viewed News</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>News ID</th>
                                                    <th>Title</th>
                                                    <th>View Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($most_viewed_news)): ?>
                                                    <?php foreach ($most_viewed_news as $news_item): ?>
                                                        <tr>
                                                            <td class="py-1"><?php echo htmlspecialchars($news_item['nid']); ?></td>
                                                            <td><?php echo htmlspecialchars($news_item['ntitle']); ?></td>
                                                            <td><?php echo htmlspecialchars($news_item['count']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3">No news views recorded.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Most Recent Events (Placeholder for Most Viewed)</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Event ID</th>
                                                    <th>Title</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($most_viewed_events)): ?>
                                                    <?php foreach ($most_viewed_events as $event): ?>
                                                        <tr>
                                                            <td class="py-1"><?php echo htmlspecialchars($event['eid']); ?></td>
                                                            <td><?php echo htmlspecialchars($event['etitle']); ?></td>
                                                            <td><?php echo htmlspecialchars($event['created_at']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3">No events found.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Recent User Registrations (Placeholder for Login Times)</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>User ID</th>
                                                    <th>Username</th>
                                                    <th>Registered At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($user_logins)): ?>
                                                    <?php foreach ($user_logins as $user): ?>
                                                        <tr>
                                                            <td class="py-1"><?php echo htmlspecialchars($user['id']); ?></td>
                                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="3">No users found.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Current Date & Time (Colombo)</h4>
                                    <p class="card-text">
                                        <i class="mdi mdi-calendar-clock"></i> <?php echo $current_datetime; ?> (Asia/Colombo)
                                    </p>
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
    <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="assets/js/dataTables.select.min.js"></script>
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/template.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>