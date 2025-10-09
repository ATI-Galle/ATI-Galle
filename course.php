<?php
// Include the header which should handle the database connection and start the HTML document
include_once("include/header.php");

// Ensure the database connection variable $con is set by the header.php
if (!isset($con) || $con->connect_error) {
    // Handle database connection error more robustly
    echo "<div style='color: red; text-align: center; padding: 20px;'>Database connection error: " . ($con->connect_error ?? 'Could not connect') . "</div>";
    // Optionally include footer and exit, but header should ideally handle fatal errors before outputting body
    // include_once("include/footer.php"); // Footer would close the body/html tags
    exit(); // Stop script execution on critical error
}

// Fetch all courses for the sidebar
$all_courses = [];
$sql_all_courses = "SELECT cid, cname FROM course ORDER BY cname"; // Order alphabetically
$stmt_all_courses = $con->prepare($sql_all_courses);
if ($stmt_all_courses) {
    $stmt_all_courses->execute();
    $result_all_courses = $stmt_all_courses->get_result();
    while ($row = $result_all_courses->fetch_assoc()) {
        $all_courses[] = $row;
    }
    $stmt_all_courses->close();
} else {
    // Handle error if prepared statement fails
    error_log("Error preparing statement for all courses: " . $con->error);
    // Optionally set a user-facing error message here if fetching courses is critical
}

// Initialize variables for course data and modules
$course_data = null;
$modules_data_by_year_sem = []; // Store modules grouped by year and semester

// Check if cid is present in the URL
$selected_cid = $_GET['cid'] ?? null; // Use null coalescing operator for cleaner check

if ($selected_cid !== null) {
    // Fetch course details
    $sql_course = "SELECT cid, cname, ctext, cimg FROM course WHERE cid = ?";
    $stmt_course = $con->prepare($sql_course);
    if ($stmt_course) {
        $stmt_course->bind_param("s", $selected_cid); // Assuming 'cid' is a string
        $stmt_course->execute();
        $result_course = $stmt_course->get_result();
        $course_data = $result_course->fetch_assoc(); // Get the single row
        $stmt_course->close();

        // Only proceed to fetch modules if the course was found
        if ($course_data) {
            // Fetch ALL module details for the selected course, ordered by year and semester
            $sql_all_modules = "SELECT module_code, module_title, module_type, credits, status, year, semester FROM modules WHERE cid = ? ORDER BY year, semester, module_code";
            $stmt_all_modules = $con->prepare($sql_all_modules);
            if ($stmt_all_modules) {
                $stmt_all_modules->bind_param("s", $selected_cid); // Assuming 'cid' is a string
                $stmt_all_modules->execute();
                $result_all_modules = $stmt_all_modules->get_result();

                // Group modules by Year and Semester
                while ($module = $result_all_modules->fetch_assoc()) {
                    $year = $module['year'];
                    $semester = $module['semester'];
                    // Create nested arrays if they don't exist
                    if (!isset($modules_data_by_year_sem[$year])) {
                        $modules_data_by_year_sem[$year] = [];
                    }
                     if (!isset($modules_data_by_year_sem[$year][$semester])) {
                        $modules_data_by_year_sem[$year][$semester] = [];
                    }
                    // Add the module to the correct group
                    $modules_data_by_year_sem[$year][$semester][] = $module;
                }

                $result_all_modules->free(); // Free result set
                $stmt_all_modules->close();

                 // Sort years and semesters numerically
                 ksort($modules_data_by_year_sem);
                 foreach ($modules_data_by_year_sem as $year => $semesters) {
                     ksort($modules_data_by_year_sem[$year]);
                 }

            } else {
                 error_log("Error preparing statement for all modules: " . $con->error);
                 // Optionally set a user-facing error message here
            }
        }
    } else {
        error_log("Error preparing statement for course details: " . $con->error);
         // Optionally set a user-facing error message here
    }
}

// The header.php should have already opened the body tag
?>
    <div class="main-container" id="course">
        <div class="main-content">
            <?php if ($selected_cid !== null): // Display course details and modules only if cid is set ?>
                <?php if ($course_data): ?>
                    <h1 align="center"><?php echo htmlspecialchars($course_data['cname']); ?></h1>
                    <h4 style="margin:5px;">Entry Profile</h4>

                    <div class="course-info">
                        <?php if (!empty($course_data['cimg'])): // Check if image path is not empty ?>
                        <?php endif; ?>
                        <p><p><?php echo nl2br($course_data['ctext']); ?></p></p>
                    </div>

                    <h4>Subjects and Credits</h4>

                    

                    <?php if (!empty($modules_data_by_year_sem)): ?>
                        <?php foreach ($modules_data_by_year_sem as $year => $semesters): ?>
                            <?php foreach ($semesters as $semester => $modules_list): ?>
                                <h2>Year <?php echo htmlspecialchars($year); ?> - Semester <?php echo htmlspecialchars($semester); ?></h2>
                                <table class="module-table">
                                    <thead>
                                        <tr>
                                            <th>Module Code</th>
                                            <th>Module Title</th>
                                            <th>Module Type</th>
                                            <th>Credits</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_credits = 0;
                                        foreach ($modules_list as $module):
                                            $total_credits += $module['credits'];
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($module['module_code']); ?></td>
                                                <td><?php echo htmlspecialchars($module['module_title']); ?></td>
                                                <td><?php echo htmlspecialchars($module['module_type']); ?></td>
                                                <td><?php echo htmlspecialchars($module['credits']); ?></td>
                                                <td><?php echo htmlspecialchars($module['status'] == 1 ? 'Active' : 'Inactive'); ?></td> </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <th colspan="3" style="text-align: right;">TOTAL CREDITS</th>
                                            <th><?php echo htmlspecialchars($total_credits); ?></th>
                                            <th></th>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                         <?php if ($course_data): // Only show this if course exists but has no modules ?>
                             <p>No modules found for this course.</p>
                         <?php endif; ?>
                    <?php endif; ?>

                <?php else: ?>
                    <p>Course not found.</p>
                <?php endif; ?>
            <?php else: ?>
                 <h1>Select a Course</h1>
                 <p>Please select a course from the list on the right to view its details and modules.</p>
            <?php endif; ?>
        </div>

        <div class="sidebar">
            <h2>Courses</h2>
            <?php if (!empty($all_courses)): ?>
                <ul>
                    <?php foreach ($all_courses as $course): ?>
                        <?php
                            // Add 'active' class to the link if it's the currently selected course
                            $active_class = ($selected_cid == $course['cid']) ? ' class="active"' : '';
                        ?>
                        <li><a href="?cid=<?php echo urlencode($course['cid']); ?>"<?php echo $active_class; ?>><?php echo htmlspecialchars($course['cname']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No courses available.</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        /* Add or adjust CSS as needed */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .main-container {
            display: flex; /* Use flexbox for layout */
            max-width: 1200px; /* Adjust max-width as needed */
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px; /* Added some border-radius */
            overflow: hidden; /* Ensures child elements respect border-radius */
        }
        .main-content {
            flex-grow: 1; /* Allow main content to take up available space */
            padding: 20px;
            box-sizing: border-box; /* Include padding in the element's total width */
        }
        .sidebar {
            flex-basis: 250px; /* Fixed width for the sidebar */
            flex-shrink: 0; /* Prevent sidebar from shrinking */
            background-color: #eee; /* Light grey background for sidebar */
            padding: 20px;
            box-sizing: border-box; /* Include padding in the element's total width */
            border-left: 1px solid #ddd; /* Separator line */
        }
        .sidebar h2 {
            color: #333;
            margin-top: 0;
            border-bottom: 2px solid #00bcd4; /* Underline for the heading */
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar li {
            margin-bottom: 8px;
        }
        .sidebar a {
            text-decoration: none;
            color: #0056b3; /* Link color */
            display: block; /* Make the link fill the list item */
            padding: 5px 0;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }
        .sidebar a:hover {
            color: #00bcd4; /* Hover color */
        }
         .sidebar a.active {
             font-weight: bold;
             color: #00bcd4; /* Active color */
         }


        h1, h2, h3 {
            color: #00bcd4;
        }
         h2 { /* Style for Year/Semester headings */
             margin-top: 30px;
             margin-bottom: 15px;
             border-bottom: 1px solid #eee;
             padding-bottom: 5px;
         }
        .course-info {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #eee;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .module-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px; /* Added border-radius */
            overflow: hidden; /* Ensures border-radius applies */
        }
        .module-table th, .module-table td {
            padding: 10px 12px; /* Adjusted padding */
            text-align: left;
            border-bottom: 1px solid #eee;
             /* Added vertical alignment */
            vertical-align: top;
        }
        .module-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .module-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
         .module-table tbody tr:hover {
             background-color: #e9e9e9; /* Hover effect for rows */
         }
         .module-table td:first-child { font-weight: bold; } /* Bold module code */


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column; /* Stack columns on smaller screens */
                margin: 10px;
            }
            .sidebar {
                flex-basis: auto; /* Allow sidebar to take up full width */
                border-left: none;
                border-bottom: 1px solid #ddd; /* Add a bottom border */
            }
        }
    </style>

<?php
// Include the footer which should close the body and html tags
include_once("include/footer.php");

// It's generally good practice to close the connection at the end of the script
// However, if header.php establishes a persistent connection or footer.php closes it,
// you might not need this explicitly here.
// if (isset($con) && $con instanceof mysqli && !$con->connect_error) {
//     mysqli_close($con);
// }
// Assuming header establishes, and footer may or may not close.
// It's safer to handle connection closing where it's opened or via a dedicated function.
// Given the header inclusion structure, I'll rely on header/footer for connection management.
?>