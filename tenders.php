<?php
// Include your header, which should also include the database connection (config.php)
include_once('include/header.php');

$page_title = "Tenders & Procurements";
$open_tenders = [];
$closed_tenders = [];

// Set a default timezone to ensure consistency in any PHP date operations
date_default_timezone_set('Asia/Colombo');

if ($con) {
    // --- CORRECTED LOGIC ---

    // 1. Fetch only the tenders that are TRULY open.
    // The database will check the closing_date against its current time (NOW()).
    $sql_open = "SELECT * FROM tenders 
                 WHERE status = 'Open' AND closing_date > NOW() 
                 ORDER BY closing_date ASC";
                 
    $result_open = mysqli_query($con, $sql_open);
    if ($result_open) {
        while ($row = mysqli_fetch_assoc($result_open)) {
            $open_tenders[] = $row;
        }
    }

    // 2. Fetch all other tenders (Closed, Awarded, Cancelled, or Expired).
    $sql_closed = "SELECT * FROM tenders 
                   WHERE status != 'Open' OR closing_date <= NOW() 
                   ORDER BY published_date DESC";

    $result_closed = mysqli_query($con, $sql_closed);
    if ($result_closed) {
        while ($row = mysqli_fetch_assoc($result_closed)) {
            $closed_tenders[] = $row;
        }
    }

} else {
    $error_message = "Database connection could not be established.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - SLIATE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px 20px;
            background-color: #ffffff;
            border-bottom: 4px solid #fec524; /* Theme yellow */
        }
        .page-header h1 {
            font-size: 2.8rem;
            color: #212529; /* Theme dark */
            margin: 0;
        }
        .tenders-section {
            margin-bottom: 50px;
        }
        .tenders-section h2 {
            font-size: 2rem;
            color: #212529;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .tender-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
            border-radius: 8px;
            overflow: hidden;
        }
        .tender-table th, .tender-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .tender-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            color: #495057;
        }
        .tender-table tbody tr:last-child td {
            border-bottom: none;
        }
        .tender-table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-open { background-color: #d4edda; color: #155724; }
        .status-closing-soon { background-color: #fff3cd; color: #856404; }
        .status-closed { background-color: #e9ecef; color: #495057; }
        
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #fec524;
            color: #212529;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .download-btn:hover {
            background-color: #e5b220;
        }
        .no-tenders-message {
            padding: 40px;
            text-align: center;
            background-color: #fff;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="page-header">
        <h1>Tenders & Procurements</h1>
    </div>

    <div class="container">
        
        <div class="tenders-section">
            <h2><i class="fas fa-folder-open"></i> Open Tenders</h2>
            <?php if (!empty($open_tenders)): ?>
                <div class="table-responsive">
                    <table class="tender-table">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Tender Title</th>
                                <th>Category</th>
                                <th>Closing Date & Time</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($open_tenders as $tender): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tender['reference_no']); ?></td>
                                    <td><?php echo htmlspecialchars($tender['title']); ?></td>
                                    <td><?php echo htmlspecialchars($tender['category']); ?></td>
                                    <td>
                                        <?php
                                            $closing_date = new DateTime($tender['closing_date']);
                                            echo $closing_date->format('F j, Y, g:i A');
                                            
                                            $today = new DateTime();
                                            $interval = $today->diff($closing_date);
                                            // '%r' gives a sign (+/-), '%a' gives total days.
                                            $days_left = (int)$interval->format('%r%a');

                                            if ($days_left < 0) { // This case should not happen with the new SQL, but as a fallback
                                                 echo ' <span class="status-badge status-closed">Expired</span>';
                                            } elseif ($days_left <= 0) {
                                                echo ' <span class="status-badge status-closed">Closing Today</span>';
                                            } elseif ($days_left <= 7) {
                                                echo ' <span class="status-badge status-closing-soon">' . $days_left . ' days left</span>';
                                            } else {
                                                echo ' <span class="status-badge status-open">' . $days_left . ' days left</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo 'admin/'.htmlspecialchars($tender['document_path']); ?>" class="download-btn" target="_blank" download>
                                            <i class="fas fa-file-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-tenders-message">There are currently no open tenders.</p>
            <?php endif; ?>
        </div>

        <div class="tenders-section">
            <h2><i class="fas fa-archive"></i> Closed Tenders</h2>
            <?php if (!empty($closed_tenders)): ?>
                <div class="table-responsive">
                    <table class="tender-table">
                        <thead>
                            <tr>
                                <th>Reference No.</th>
                                <th>Tender Title</th>
                                <th>Category</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($closed_tenders as $tender): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tender['reference_no']); ?></td>
                                    <td><?php echo htmlspecialchars($tender['title']); ?></td>
                                    <td><?php echo htmlspecialchars($tender['category']); ?></td>
                                    <td>
                                        <span class="status-badge status-closed">
                                            <?php
                                                // If the tender has expired but is still marked 'Open', show 'Closed'. Otherwise, show its actual status.
                                                $closing_date = new DateTime($tender['closing_date']);
                                                if ($tender['status'] == 'Open' && $closing_date <= new DateTime()) {
                                                    echo 'Closed (Expired)';
                                                } else {
                                                    echo htmlspecialchars($tender['status']);
                                                }
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-tenders-message">There are no closed tenders to display.</p>
            <?php endif; ?>
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
</body>
</html>

<?php 
// Include your standard footer file
include_once('include/footer.php'); 
?>