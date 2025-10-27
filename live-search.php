<?php
// PART 1: SERVER-SIDE PHP (AJAX ENDPOINT)
// This block runs only when the JavaScript sends a search query.
if (isset($_POST['query'])) {
    
    // Make sure you have your database connection file
    include('include/config.php');

    if (!$con) {
        echo "Database connection failed.";
        exit();
    }

    $output = '';
    $query = trim($_POST['query']);
    
    // Prepare the search term for a LIKE query to find partial matches
    $searchTerm = "%" . $query . "%";

    // --- Search Courses ---
    $stmt_courses = $con->prepare("SELECT cid, cname, ctext FROM course WHERE (cname LIKE ? OR ctext LIKE ?) AND status = 1 LIMIT 3");
    $stmt_courses->bind_param("ss", $searchTerm, $searchTerm);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();

    if ($result_courses->num_rows > 0) {
        $output .= '<h3>Courses</h3><ul class="results-list">';
        while ($row = $result_courses->fetch_assoc()) {
            // Course link remains unchanged as per request
            $output .= '<li><a href="course.php?cid=' . $row['cid'] . '">' . htmlspecialchars($row['cname']) . '</a></li>';
        }
        $output .= '</ul>';
    }

    // --- Search News ---
    $stmt_news = $con->prepare("SELECT nid, ntitle FROM news WHERE (ntitle LIKE ? OR ntext LIKE ?) AND status = 1 LIMIT 3");
    $stmt_news->bind_param("ss", $searchTerm, $searchTerm);
    $stmt_news->execute();
    $result_news = $stmt_news->get_result();
    
    if ($result_news->num_rows > 0) {
        $output .= '<h3>News</h3><ul class="results-list">';
        while ($row = $result_news->fetch_assoc()) {
            // MODIFIED: Link changed to news.php?nid=...
            $output .= '<li><a href="news.php?nid=' . $row['nid'] . '">' . htmlspecialchars($row['ntitle']) . '</a></li>';
        }
        $output .= '</ul>';
    }

    // --- Search Events ---
    $stmt_events = $con->prepare("SELECT eid, etitle FROM events WHERE (etitle LIKE ? OR etext LIKE ?) AND status = 1 LIMIT 3");
    $stmt_events->bind_param("ss", $searchTerm, $searchTerm);
    $stmt_events->execute();
    $result_events = $stmt_events->get_result();
    
    if ($result_events->num_rows > 0) {
        $output .= '<h3>Events</h3><ul class="results-list">';
        while ($row = $result_events->fetch_assoc()) {
            // MODIFIED: Link changed to event.php?eid=...
            $output .= '<li><a href="event.php?eid=' . $row['eid'] . '">' . htmlspecialchars($row['etitle']) . '</a></li>';
        }
        $output .= '</ul>';
    }

    // If no results were found in any table
    if (empty($output)) {
        $output = '<div class="no-results">No results found.</div>';
    }

    echo $output;
    
    // Stop the script here to prevent the HTML below from being sent with the AJAX response
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Data Search</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .search-container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        #live-search-box {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box; /* Important for padding */
        }
        #search-results {
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        #search-results h3 {
            color: #fec524; /* Your theme's primary color */
            margin: 15px 0 10px 0;
            font-size: 1.2rem;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 5px;
        }
        .results-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .results-list li {
            padding: 10px 5px;
            border-bottom: 1px solid #f9f9f9;
        }
        .results-list li a {
            text-decoration: none;
            color: #0056b3;
            display: block;
        }
        .results-list li a:hover {
            background-color: #f4f4f4;
        }
        .no-results {
            padding: 20px;
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="search-container">
        <h1>Live Search for Courses, News & Events</h1>
        <input type="text" id="live-search-box" placeholder="Start typing to search..." autocomplete="off">
        <div id="search-results">
            </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            // Function to perform search
            function performSearch() {
                var query = $('#live-search-box').val();

                // Only search if the query is at least 2 characters long
                if (query.length > 1) {
                    $.ajax({
                        url: 'live-search.php', // Sends the request to this same file
                        method: 'POST',
                        data: {
                            query: query // The data we are sending
                        },
                        beforeSend: function() {
                            // Optional: show a loading indicator
                            $('#search-results').html('<div class="no-results">Searching...</div>');
                        },
                        success: function(data) {
                            // 'data' is the HTML response from the PHP script
                            $('#search-results').html(data);
                        }
                    });
                } else {
                    // Clear the results if the search box is empty or short
                    $('#search-results').html('');
                }
            }

            // Trigger the search on each key press
            $('#live-search-box').on('keyup', function() {
                performSearch();
            });
        });
    </script>

</body>
</html>