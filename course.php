<?php

// Include the header (Corrected syntax with quotes)
include_once('include/header.php');

// Database connection check
if (!isset($con) || $con->connect_error) {
    echo "<div style='color: red; text-align: center; padding: 20px;'>Database connection error: " . ($con->connect_error ?? 'Could not connect') . "</div>";
    exit();
}

// Helper function
function get_full_position_title($abbr) {
    $positions = [
        'Dire' => 'Director',
        'Regi' => 'Registrar',
        'HOD'  => 'Head of Department',
        'SLect'=> 'Senior Lecturer',
        'Lec'  => 'Lecturer',
        'Demo' => 'Demonstrator'
    ];
    return $positions[$abbr] ?? $abbr;
}

// Fetch all courses for the sidebar
$all_courses = [];
$sql_all_courses = "SELECT cid, cname FROM course ORDER BY cname";
$stmt_all_courses = $con->prepare($sql_all_courses);
if ($stmt_all_courses) {
    $stmt_all_courses->execute();
    $result_all_courses = $stmt_all_courses->get_result();
    while ($row = $result_all_courses->fetch_assoc()) {
        $all_courses[] = $row;
    }
    $stmt_all_courses->close();
}

// Initialize variables
$course_data = null;
$modules_data_by_year_sem = [];
$staff_members = [];
$selected_cid = $_GET['cid'] ?? null;

if ($selected_cid !== null) {
    // Fetch course details
    $sql_course = "SELECT cid, cname, ctext FROM course WHERE cid = ?";
    $stmt_course = $con->prepare($sql_course);
    if ($stmt_course) {
        $stmt_course->bind_param("s", $selected_cid);
        $stmt_course->execute();
        $course_data = $stmt_course->get_result()->fetch_assoc();
        $stmt_course->close();
    }

    if ($course_data) {
        // Fetch modules
        $sql_all_modules = "SELECT module_code, module_title, module_type, credits, year, semester FROM modules WHERE cid = ? ORDER BY year, semester, module_code";
        $stmt_all_modules = $con->prepare($sql_all_modules);
        if ($stmt_all_modules) {
            $stmt_all_modules->bind_param("s", $selected_cid);
            $stmt_all_modules->execute();
            $result_all_modules = $stmt_all_modules->get_result();
            while ($module = $result_all_modules->fetch_assoc()) {
                $year = $module['year'];
                $semester = $module['semester'];
                if (!isset($modules_data_by_year_sem[$year])) $modules_data_by_year_sem[$year] = [];
                if (!isset($modules_data_by_year_sem[$year][$semester])) $modules_data_by_year_sem[$year][$semester] = [];
                $modules_data_by_year_sem[$year][$semester][] = $module;
            }
            $stmt_all_modules->close();
            
            // Sort the data
            ksort($modules_data_by_year_sem);
            foreach ($modules_data_by_year_sem as $year => &$semesters) {
                ksort($semesters);
            }
            // =========================================================
            //  THE FIX: Unset the reference to prevent data corruption
            // =========================================================
            unset($semesters);

        }

        // Fetch Staff
        $sql_staff = "SELECT sname, spos, stimg FROM staff WHERE cid = ? AND status = 1 ORDER BY CASE WHEN spos = 'HOD' THEN 1 ELSE 2 END, sname ASC";
        $stmt_staff = $con->prepare($sql_staff);
        if ($stmt_staff) {
            $stmt_staff->bind_param("s", $selected_cid);
            $stmt_staff->execute();
            $result_staff = $stmt_staff->get_result();
            while ($staff_row = $result_staff->fetch_assoc()) {
                $staff_members[] = $staff_row;
            }
            $stmt_staff->close();
        }
    }
}
?>

    <div class="main-container" id="course">
        <div class="sidebar">
            <h2>All Departments</h2>
            <nav class="department-nav">
                <ul>
                    <?php foreach ($all_courses as $course): ?>
                        <?php $active_class = ($selected_cid == $course['cid']) ? ' class="active"' : ''; ?>
                        <li><a href="?cid=<?php echo urlencode($course['cid']); ?>"<?php echo $active_class; ?>><?php echo htmlspecialchars($course['cname']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <?php if ($course_data): ?>
                <div class="page-header">
                    <h1><?php echo htmlspecialchars($course_data['cname']); ?></h1>
                </div>

                <div class="content-section">
                    <h2>Course Overview</h2>
                    <div class="course-overview-text">
                        <?php echo $course_data['ctext']; ?>
                    </div>
                </div>
                
                <?php if (!empty($modules_data_by_year_sem)): ?>
                    <div class="content-section">
                        <h2>Course Structure</h2>
                        <?php foreach ($modules_data_by_year_sem as $year => $semesters): ?>
                            <?php foreach ($semesters as $semester => $modules_list): ?>
                                <h3>Year <?php echo htmlspecialchars($year); ?> - Semester <?php echo htmlspecialchars($semester); ?></h3>
                                <table class="module-table">
                                    <thead>
                                        <tr>
                                            <th>Module Code</th>
                                            <th>Module Title</th>
                                            <th>Credits</th>
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
                                                <td><?php echo htmlspecialchars($module['credits']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="total-row">
                                            <td colspan="2"><b>Total Semester Credits</b></td>
                                            <td><b><?php echo htmlspecialchars($total_credits); ?></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($staff_members)): ?>
                    <div class="content-section">
                        <?php
                            $hod = null;
                            $other_staff = [];
                            foreach ($staff_members as $staff) {
                                if ($staff['spos'] == 'HOD') {
                                    $hod = $staff;
                                } else {
                                    $other_staff[] = $staff;
                                }
                            }
                        ?>

                        <?php if ($hod): ?>
                            <h2>Head of Department</h2>
                            <div class="hod-profile">
                                <img src="<?php echo !empty($hod['stimg']) ? 'admin/'.htmlspecialchars($hod['stimg']) : 'admin/uploads/staff/def.jpg'; ?>" alt="Photo of <?php echo htmlspecialchars($hod['sname']); ?>">
                                <div class="hod-details">
                                    <h3><?php echo htmlspecialchars($hod['sname']); ?></h3>
                                    <p><?php echo htmlspecialchars(get_full_position_title($hod['spos'])); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($other_staff)): ?>
                            <h2 style="margin-top: 40px;">Academic Staff</h2>
                            <div class="staff-grid">
                                <?php foreach ($other_staff as $staff): ?>
                                    <div class="staff-card">
                                        <img src="<?php echo !empty($staff['stimg']) ? 'admin/'.htmlspecialchars($staff['stimg']) : 'admin/uploads/staff/def.jpg'; ?>" alt="Photo of <?php echo htmlspecialchars($staff['sname']); ?>">
                                        <div class="staff-card-details">
                                            <h4><?php echo htmlspecialchars($staff['sname']); ?></h4>
                                            <p><?php echo htmlspecialchars(get_full_position_title($staff['spos'])); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="page-header">
                    <h1>Welcome</h1>
                </div>
                <p>Please select a department from the navigation menu to view its details.</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        /* --- FONT & GLOBAL STYLES --- */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        :root {
            --university-blue: #0d47a1;
            --accent-gold: #ffab00; /* New accent color */
            --light-blue: #e3f2fd;
            --dark-text: #212529;
            --light-text: #6c757d;
            --border-color: #dee2e6;
            --background-grey: #f8f9fa;
        }

        body {
            font-family: 'Roboto', sans-serif; /* Changed font */
            margin: 0;
            background-color: var(--background-grey);
            color: var(--dark-text);
        }

        /* --- LAYOUT --- */
        .main-container {
            display: flex;
            max-width: 1400px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 8px; /* Added rounded corners */
            box-shadow: 0 4px 25px rgba(0,0,0,0.07); /* Added shadow */
            overflow: hidden; /* Important for border-radius */
        }

        .sidebar {
            flex: 0 0 280px;
            background-color: #fff;
            border-right: 1px solid var(--border-color);
            padding: 25px;
        }

        .main-content {
            flex: 1;
            padding: 30px 45px;
        }

        /* --- HEADINGS & TEXT --- */
        .page-header {
            border-bottom: 3px solid var(--university-blue);
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        h1, h2, h3, h4 {
            color: var(--university-blue);
            font-weight: 700;
        }
        h1 { font-size: 2.5em; margin: 0; }
        h2 { 
            font-size: 1.8em; 
            border-bottom: 2px solid var(--accent-gold); /* Used accent color */
            padding-bottom: 10px; 
            margin-top: 40px; 
            display: inline-block; /* Makes border fit content */
        }
        h3 { font-size: 1.4em; margin-bottom: 1.2em;}
        h4 { font-size: 1.1em; color: var(--dark-text); }

        .content-section { margin-bottom: 40px; }
        .course-overview-text { line-height: 1.8; font-size: 1.1em; color: #343a40; }
        
        /* --- SIDEBAR NAVIGATION --- */
        .sidebar h2 { border: none; padding-bottom: 5px; }
        .department-nav ul { list-style: none; padding: 0; margin: 0; }
        .department-nav a {
            display: block;
            padding: 12px 18px;
            text-decoration: none;
            color: var(--dark-text);
            font-weight: 500;
            border-radius: 6px; /* Rounded corners for links */
            margin-bottom: 5px;
            transition: all 0.2s ease-in-out;
        }
        .department-nav a:hover {
            background-color: var(--light-blue);
            color: var(--university-blue);
            transform: translateX(5px);
        }
        .department-nav a.active {
            background-color: var(--university-blue);
            color: #fff;
            font-weight: 700;
        }

        /* --- MODULE TABLE --- */
        .module-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .module-table th, .module-table td {
            padding: 15px; /* Increased padding */
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .module-table thead th {
            background-color: var(--background-grey);
            font-weight: 700;
            color: var(--dark-text);
        }
        .module-table tbody tr:hover {
            background-color: var(--light-blue);
        }
        .module-table .total-row {
            background-color: var(--background-grey);
            font-weight: 700;
            color: var(--university-blue);
        }

        /* --- HOD PROFILE --- */
        .hod-profile {
            display: flex;
            align-items: center;
            background: linear-gradient(90deg, var(--light-blue) 0%, rgba(255,255,255,1) 100%);
            border-radius: 8px;
            padding: 24px;
            margin-top: 20px;
            border: 1px solid var(--border-color);
        }
        .hod-profile img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-right: 24px;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .hod-profile h3 { border: none; font-size: 1.6em; margin: 0 0 5px 0; }
        .hod-profile p { margin: 0; font-size: 1.1em; color: var(--dark-text); }

        /* --- ACADEMIC STAFF GRID --- */
        .staff-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .staff-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            text-align: center;
            padding: 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .staff-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--university-blue);
        }
        .staff-card img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid var(--background-grey);
        }
        .staff-card-details p {
            margin: 0;
            font-size: 0.9em;
            color: var(--light-text);
        }
        
        /* --- RESPONSIVE ADJUSTMENTS --- */
        @media (max-width: 992px) {
            .main-container { flex-direction: column; margin: 10px; box-shadow: none; border: 1px solid var(--border-color);}
            .sidebar { border-right: none; border-bottom: 1px solid var(--border-color); flex-basis: auto; }
            .main-content { padding: 25px; }
            .hod-profile { flex-direction: column; text-align: center; background: var(--light-blue); }
            .hod-profile img { margin-right: 0; margin-bottom: 20px; }
        }
    </style>

  <!--links are not clicble solved by script-->

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Your live search javascript is here...
        });
    </script>
<?php
// Corrected syntax with quotes
include_once('include/footer.php');
?>