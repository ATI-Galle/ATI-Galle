<?php
// ALWAYS AT THE VERY TOP FOR DEBUGGING
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('include/header.php');

echo "\n";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Organization Structure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- MODERN CSS STYLES --- */
        :root {
            --primary-color:rgb(34, 129, 238); /* Vibrant Teal */
            --primary-light: #e8f8f5;
            --dark-color: #2c3e50;    /* Dark Slate Blue */
            --text-color:rgb(243, 255, 22);     /* Wet Asphalt */
            --light-gray: #ecf0f1;   /* Clouds */
            --white-color: #ffffff;
            --border-radius-md: 12px;
            --border-radius-sm: 8px;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-gray);
            color: var(--text-color);
        }

        /* Debug Styles (kept for your convenience) */
        .debug-output {
            background-color: #fffecb; border: 1px solid #e6db55; padding: 10px;
            margin: 10px auto; max-width: 1360px; box-sizing: border-box; font-size: 0.9em;
        }
        .debug-output pre { white-space: pre-wrap; word-break: break-all; }
        .debug-error { background-color: #ffecec; border: 1px solid #f5c6cb; color: #721c24; }

        /* Main Container */
        .org-structure-container {
            max-width: 1400px;
            margin: 2rem auto;
            background-color: var(--white-color);
            padding: 2rem;
            box-shadow: var(--shadow);
            border-radius: var(--border-radius-md);
        }

        /* Layer Styles */
        .org-layer {
            margin-bottom: 3rem;
        }
        .org-layer:last-child {
            margin-bottom: 0;
        }
        .layer-title {
            text-align: center;
            font-size: 2em;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0 auto 2rem auto;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
            width: auto;
            display: block;
            width: fit-content;
        }

        /* Card Scrolling Section */
        .staff-section-container {
            position: relative;
        }
        .cards-wrapper {
            overflow: hidden;
        }
        .staff-cards-container {
            display: flex;
            scroll-behavior: smooth;
            overflow-x: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE, Edge */
            padding-bottom: 15px; /* Space for shadow */
            margin: 0 -10px; /* Counteract card margin */
            padding-left: 10px;
            padding-right: 10px;
        }
        .staff-cards-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari */
        }
        .staff-cards-container.expanded {
            overflow-x: visible;
            flex-wrap: wrap;
            justify-content: center;
            padding-bottom: 0;
            margin: 0 -10px; /* Counteract card margin */
        }

        /* NEW Staff Card Design */
        .staff-card {
            flex: 0 0 280px;
            width: 280px;
            margin: 0 10px;
            background-color: var(--white-color);
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .staff-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .staff-cards-container.expanded .staff-card {
            margin: 10px;
        }
        .card-image {
            height: 250px;
            background-size: cover;
            background-position: center;
        }
        .card-content-wrapper {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1; /* Allows content to fill space */
            text-align: center;
        }
        .staff-position {
            font-size: 0.9em;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0 0 0.25rem 0;
        }
        .staff-name {
            font-size: 1.25em;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            line-height: 1.2;
        }
        .staff-qualifications {
            font-size: 0.8em;
            color: var(--text-color);
            opacity: 0.8;
            margin-top: 0.5rem;
            flex-grow: 1; /* Pushes CID label down */
        }
        .cid-label {
            background-color: var(--primary-light);
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 5px 12px;
            font-size: 0.8em;
            border-radius: 50px; /* Pill shape */
            margin: 1rem auto 0 auto;
            display: inline-block;
            font-weight: 600;
        }

        /* Navigation Arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            color: var(--dark-color);
            border: 1px solid var(--light-gray);
            cursor: pointer;
            z-index: 10;
            font-size: 1.5em;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-arrow:hover {
            background-color: var(--primary-color);
            color: var(--white-color);
            border-color: var(--primary-color);
        }
        .left-arrow { left: -15px; }
        .right-arrow { right: -15px; }
        .nav-arrow.hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* See More Link */
        .see-more-link {
            display: block;
            text-align: right;
            margin-top: 1.5rem;
            font-size: 1.1em;
            font-weight: 600;
            color: var(--primary-color);
            text-decoration: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .see-more-link:hover {
            text-decoration: underline;
            color: var(--dark-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .org-structure-container { padding: 1rem; margin: 1rem; }
            .layer-title { font-size: 1.6em; }
            .staff-card { flex: 0 0 250px; width: 250px; }
            .nav-arrow { width: 40px; height: 40px; font-size: 1.2em; }
            .left-arrow { left: 0px; }
            .right-arrow { right: 0px; }
        }




        
    </style>
</head>
<body>
    <div class="org-structure-container">
        <?php
        include('include/config.php');

        if (!isset($con) || !($con instanceof mysqli)) {
            die("<div class='debug-output debug-error'>Database connection failed.</div>");
        }

        $staff_data_by_position = [
            'layer1' => [], 'layer2' => [], 'layer3' => [], 'layer4' => [], 'layer5' => [], 'layer6' => []
        ];

        $position_mapping = [
            'Dire'  => ['text' => 'Director', 'layer' => 'layer1'],
            'Regi'  => ['text' => 'Registrar', 'layer' => 'layer1'],
            'AReg'  => ['text' => 'Asst. Registrar', 'layer' => 'layer1'],
            'Acct'  => ['text' => 'Accountant', 'layer' => 'layer1'],
            'Legal' => ['text' => 'Legal Officer', 'layer' => 'layer1'],
            'Audit' => ['text' => 'Internal Auditor', 'layer' => 'layer1'],
            'HOD'   => ['text' => 'Head of Department', 'layer' => 'layer2'],
            'SLect' => ['text' => 'Senior Lecturer', 'layer' => 'layer3'],
            'Lect'  => ['text' => 'Lecturer', 'layer' => 'layer4'],
            'Lib'   => ['text' => 'Librarian', 'layer' => 'layer5'],
            'Demo'  => ['text' => 'Demonstrator', 'layer' => 'layer5'],
            'Offi'  => ['text' => 'Office Staff', 'layer' => 'layer6'],
        ];

        $sql = "SELECT stid, sname, spos, cid, sed, stimg FROM staff WHERE status = '1'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($position_mapping[$row['spos']])) {
                    $layer_key = $position_mapping[$row['spos']]['layer'];
                    $staff_data_by_position[$layer_key][] = $row;
                } else {
                    echo "<div class='debug-output debug-error'>Warning: Staff member '{$row['sname']}' has an unmapped position '{$row['spos']}'.</div>";
                }
            }
            mysqli_free_result($result);
        } else {
            echo "<div class='debug-output debug-error'><strong>Database Query Error:</strong> " . htmlspecialchars(mysqli_error($con)) . "</div>";
        }

        function render_staff_layer($layer_key, $layer_title, $staff_data_by_position, $position_mapping) {
            $layer_id_num = substr($layer_key, -1);
            $staff_data = $staff_data_by_position[$layer_key] ?? [];

            echo "<section class='org-layer layer-{$layer_id_num}'>";
            echo "<h2 class='layer-title'>{$layer_title}</h2>";
            
            if (empty($staff_data)) {
                echo "<p style='text-align:center; padding:20px;'>No staff found for this category.</p>";
            } else {
                echo "<div class='staff-section-container'>";
                echo "<button class='nav-arrow left-arrow' onclick=\"scrollLayer('layer-{$layer_id_num}-cards', -1)\" aria-label='Scroll Left'>&#9664;</button>";
                echo "<button class='nav-arrow right-arrow' onclick=\"scrollLayer('layer-{$layer_id_num}-cards', 1)\" aria-label='Scroll Right'>&#9654;</button>";
                echo "<div class='cards-wrapper'>";
                echo "<div class='staff-cards-container' id='layer-{$layer_id_num}-cards'>";

                foreach ($staff_data as $staff_member) {
                    $display_position = $position_mapping[$staff_member['spos']]['text'];
                    $image_path = "admin/" . $staff_member['stimg'];
                    $image_url = !empty($staff_member['stimg']) && file_exists($image_path) 
                                 ? htmlspecialchars($image_path) 
                                 : "https://via.placeholder.com/280x350/ecf0f1/bdc3c7?text=" . urlencode($display_position);

                    echo "<div class='staff-card'>";
                    echo "    <div class='card-image' style=\"background-image: url('{$image_url}');\"></div>";
                    echo "    <div class='card-content-wrapper'>";
                    echo "        <div class='staff-position'>" . htmlspecialchars($display_position) . "</div>";
                    echo "        <div class='staff-name'>" . htmlspecialchars($staff_member['sname']) . "</div>";
                    echo "        <div class='staff-qualifications'>" . htmlspecialchars($staff_member['sed']) . "</div>";
                    
                    $positions_to_skip_label = ['Dire', 'Regi', 'Lib'];
                    if (!in_array($staff_member['spos'], $positions_to_skip_label) && !empty($staff_member['cid'])) {
                        echo "        <div class='cid-label'>" . htmlspecialchars($staff_member['cid']) . "</div>";
                    }

                    echo "    </div>"; // end card-content-wrapper
                    echo "</div>"; // end staff-card
                }

                echo "</div>"; // end staff-cards-container
                echo "</div>"; // end cards-wrapper

                if (count($staff_data) > 4) {
                    echo "<a href='#' class='see-more-link' data-target='layer-{$layer_id_num}-cards'>See All " . htmlspecialchars($layer_title) . " &gt;</a>";
                }
                echo "</div>"; // end staff-section-container
            }

            echo "</section>";
        }

        render_staff_layer('layer1', 'Executive Leadership', $staff_data_by_position, $position_mapping);
        render_staff_layer('layer2', 'Heads of Department', $staff_data_by_position, $position_mapping);
        render_staff_layer('layer3', 'Senior Lecturers', $staff_data_by_position, $position_mapping);
        render_staff_layer('layer4', 'Lecturers & Faculty', $staff_data_by_position, $position_mapping);
        render_staff_layer('layer5', 'Academic Support', $staff_data_by_position, $position_mapping);
        render_staff_layer('layer6', 'Administrative & Office Staff', $staff_data_by_position, $position_mapping);

        if (isset($con) && $con instanceof mysqli) {
            mysqli_close($con);
        }
        ?>
    </div> 

    <script>
        // --- JAVASCRIPT IS UNCHANGED, IT WORKS PERFECTLY WITH THE NEW STRUCTURE ---
        function scrollLayer(containerId, direction) {
            const cardsContainer = document.getElementById(containerId);
            if (!cardsContainer || cardsContainer.classList.contains('expanded')) {
                return;
            }
            const scrollDistance = cardsContainer.clientWidth * 0.8;
            cardsContainer.scrollBy({
                left: direction * scrollDistance,
                behavior: 'smooth'
            });
        }

        function toggleLayerArrows(containerId) {
            const cardsContainer = document.getElementById(containerId);
            if (!cardsContainer) { return; }
            const staffSectionContainer = cardsContainer.closest('.staff-section-container');
            if (!staffSectionContainer) return;
            const leftArrow = staffSectionContainer.querySelector('.left-arrow');
            const rightArrow = staffSectionContainer.querySelector('.right-arrow');
            if (!leftArrow || !rightArrow) return;

            if (cardsContainer.classList.contains('expanded')) {
                leftArrow.classList.add('hidden');
                rightArrow.classList.add('hidden');
                return;
            }
            const isScrollable = cardsContainer.scrollWidth > cardsContainer.clientWidth + 1;
            if (!isScrollable) {
                leftArrow.classList.add('hidden');
                rightArrow.classList.add('hidden');
                return;
            }
            const tolerance = 2;
            const isAtStart = cardsContainer.scrollLeft <= tolerance;
            const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - tolerance;
            leftArrow.classList.toggle('hidden', isAtStart);
            rightArrow.classList.toggle('hidden', isAtEnd);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const seeMoreLinks = document.querySelectorAll('.see-more-link');
            seeMoreLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const targetContainerId = link.dataset.target;
                    const cardsContainer = document.getElementById(targetContainerId);
                    if (cardsContainer) {
                        cardsContainer.classList.add('expanded');
                        link.style.display = 'none';
                        toggleLayerArrows(targetContainerId);
                    }
                });
            });

            const layerContainers = document.querySelectorAll('.staff-cards-container');
            layerContainers.forEach(container => {
                const containerId = container.id;
                if (!containerId) return;
                toggleLayerArrows(containerId);
                container.addEventListener('scroll', () => toggleLayerArrows(containerId), { passive: true });
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        toggleLayerArrows(containerId);
                    }, 200);
                });
            });
        });
    </script>

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