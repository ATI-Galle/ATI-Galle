<?php
// Include your standard header file if you have one
include_once('include/header.php');

// Or, if you don't use a header file, include your database connection
include('include/config.php'); // Make sure this path is correct

$results_by_session = [];
$page_title = "Exam Results";

if ($con) {
    // Fetch all active results, ordered by session (most recent first) and then by date
    $sql = "SELECT result_id, exam_title, exam_session, exam_type, upload_date, file_path 
            FROM results 
            WHERE is_active = 1 
            ORDER BY exam_session DESC, upload_date DESC";
            
    $result = mysqli_query($con, $sql);

    if ($result) {
        // Group the results by the 'exam_session' column
        while ($row = mysqli_fetch_assoc($result)) {
            $session = htmlspecialchars($row['exam_session']);
            $results_by_session[$session][] = $row;
        }
    } else {
        // Handle potential SQL query error
        $error_message = "Could not fetch results at this time.";
    }
} else {
    $error_message = "Database connection failed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Link to your main stylesheet or use the styles below -->
    <!-- <link rel="stylesheet" href="css/style.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1340px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px 0;
            background-color: #ffffff;
            border-bottom: 4px solid #fec524; /* Your theme's yellow color */
        }
        .page-header h1 {
            font-size: 2.8rem;
            color: #212529;
            margin: 0;
        }
        .session-block {
            margin-bottom: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden; /* Ensures the border radius is applied to the table */
        }
        .session-header {
            background-color: #212529; /* Your theme's dark color */
            color: #ffffff;
            padding: 15px 25px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        .results-table th,
        .results-table td {
            padding: 15px 25px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .results-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .results-table tbody tr:last-child td {
            border-bottom: none;
        }
        .results-table tbody tr:hover {
            background-color: #f1f3f5;
        }
        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #fec524; /* Yellow color from your theme */
            color: #212529;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .download-btn:hover {
            background-color: #e5b220;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .no-results-message {
            text-align: center;
            padding: 50px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .results-table {
                display: block;
                width: 100%;
            }
            .results-table thead, .results-table tbody, .results-table tr, .results-table th, .results-table td {
                display: block;
            }
            .results-table thead {
                display: none; /* Hide table headers */
            }
            .results-table tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            .results-table td {
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%; /* Make space for the label */
                text-align: right;
            }
            .results-table td::before {
                content: attr(data-label); /* Use data-label for mobile view */
                position: absolute;
                left: 15px;
                width: calc(50% - 30px);
                text-align: left;
                font-weight: bold;
                white-space: nowrap;
            }
            .download-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="page-header">
        <h1><?php echo $page_title; ?></h1>
    </div>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="no-results-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php elseif (empty($results_by_session)): ?>
            <div class="no-results-message">
                <p>No exam results are available at this time. Please check back later.</p>
            </div>
        <?php else: ?>
            <!-- Loop through each session block -->
            <?php foreach ($results_by_session as $session => $results): ?>
                <div class="session-block">
                    <div class="session-header">
                        <i class="fas fa-calendar-alt"></i> Session: <?php echo $session; ?>
                    </div>
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Exam Title</th>
                                <th>Exam Type</th>
                                <th>Published Date</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop through each result within the session -->
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <td data-label="Title"><?php echo htmlspecialchars($row['exam_title']); ?></td>
                                    <td data-label="Type"><?php echo htmlspecialchars($row['exam_type']); ?></td>
                                    <td data-label="Date"><?php echo date("F j, Y", strtotime($row['upload_date'])); ?></td>
                                    <td data-label="Link">
                                        <a href="<?php echo 'admin/uploads_pdf/'.htmlspecialchars($row['file_path']); ?>" class="download-btn" target="_blank" download>
                                            <i class="fas fa-file-pdf"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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
// Include your standard footer file if you have one
include_once('include/footer.php'); 
?>