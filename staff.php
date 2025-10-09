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
    <title>Organization Structure (MySQLi)</title>
    <style>
        /* Your provided CSS starts here */
        body { /* Basic body style for visibility */
            font-family: sans-serif;
            margin: 0;
            padding: 0; 
            background-color: #f4f4f4; 
        }
        .debug-output {
            background-color: #fffecb;
            border: 1px solid #e6db55;
            padding: 10px;
            margin: 10px auto; 
            max-width: 1360px; 
            box-sizing: border-box;
            font-size: 0.9em;
            line-height: 1.4;
        }
        .debug-output pre {
            white-space: pre-wrap;
            word-break: break-all;
        }
        .debug-error {
            background-color: #ffecec;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* --- PASTE YOUR FULL CSS FROM THE PROMPT HERE --- */
        /* (Same CSS as before - no changes needed here) */
        .org-structure-container {
            max-width: 1400px; margin: 0 auto; background-color: #fff; padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); border-radius: 8px;
        }
        .org-layer {
            margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 30px;
        }
        .org-layer:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
        .layer-title {
            text-align: center; font-size: 1.8em; color: #333; margin-bottom: 20px; margin-top: 0;
            padding-bottom: 10px; border-bottom: 2px solid #673ab7; display: block;
            width: fit-content; margin-left: auto; margin-right: auto; padding-right: 20px; padding-left: 20px;
        }
        .staff-section-container { position: relative; width: 100%; margin: 0 auto; overflow: hidden; padding: 0; box-sizing: border-box; }
        .cards-wrapper { overflow: hidden; padding: 0 20px; box-sizing: border-box; }
        .staff-cards-container {
            display: flex; scroll-behavior: smooth; overflow-x: auto; scrollbar-width: none;
            -ms-overflow-style: none; padding-bottom: 15px;
        }
        .staff-cards-container::-webkit-scrollbar { display: none; }
        .staff-cards-container.expanded {
            overflow-x: visible; flex-wrap: wrap; justify-content: center; padding-bottom: 0;
        }
        .staff-cards-container.expanded .staff-card { margin-right: 20px; margin-bottom: 20px; }
        .staff-card {
            flex: 0 0 auto; width: 280px; height: 350px; margin-right: 20px; background-size: cover;
            background-position: center; color: white; position: relative; display: flex; flex-direction: column;
            border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .staff-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
        .staff-card:last-child { margin-right: 0; } /* Non-expanded only */
        .staff-cards-container.expanded .staff-card:last-child { margin-right: 20px; } /* Expanded consistency */
        .card-title-bar {
            height: 40px; display: flex; justify-content: center; align-items: center; font-weight: bold;
            color: white; padding: 0 10px; box-sizing: border-box; flex-shrink: 0; text-align: center;
            background-color: #673ab7; font-size: 1.1em;
        }
        .layer-1 .card-title-bar { background-color: #1a237e; }
        .layer-2 .card-title-bar { background-color: #004d40; }
        .layer-3 .card-title-bar { background-color: #e65100; }
        .layer-4 .card-title-bar { background-color: #33691e; }
        .card-overlay {
            position: absolute; top: 40px; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.2));
            display: flex; flex-direction: column; justify-content: flex-end; padding: 20px; box-sizing: border-box;
        }
        .card-content { position: relative; z-index: 1; }
        .staff-name { font-size: 1.3em; font-weight: bold; margin-top: 0; margin-bottom: 5px; }
        .staff-qualifications, .staff-position { font-size: 0.9em; margin-top: 0; margin-bottom: 5px; opacity: 0.9; }
        .staff-position { margin-bottom: 15px; }
        .more-button {
            background-color: rgba(255,255,255,0.2); border: 2px solid white; color: white; padding: 8px 15px;
            font-size: 0.9em; cursor: pointer; transition: background-color 0.3s ease; align-self: flex-start;
            border-radius: 5px; margin-top: auto;
        }
        .more-button:hover { background-color: rgba(255,255,255,0.4); }
        .nav-arrow {
            position: absolute; top: 50%; transform: translateY(-50%); background-color: rgba(0,0,0,0.5);
            color: white; border: none; padding: 10px; cursor: pointer; z-index: 10; font-size: 1.5em;
            border-radius: 50%; transition: background-color 0.3s ease, opacity 0.3s ease;
            width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;
        }
        .nav-arrow:hover { background-color: rgba(0,0,0,0.8); }
        .left-arrow { left: 10px; }
        .right-arrow { right: 10px; }
        .nav-arrow.hidden { opacity: 0; pointer-events: none; }
        .see-more-link {
            display: block; text-align: right; margin-top: 15px; margin-right: 20px; font-size: 1.1em;
            color: #673ab7; text-decoration: none; cursor: pointer; transition: color 0.3s ease;
        }
        .see-more-link:hover { text-decoration: underline; color: #512da8; }

        @media (max-width: 768px) {
            .org-structure-container { padding: 10px; }
            .org-layer { margin-bottom: 30px; padding-bottom: 20px; }
            .layer-title { font-size: 1.5em; margin-bottom: 15px; padding-right: 10px; padding-left: 10px; }
            .cards-wrapper { padding: 0 10px; }
            .staff-card { width: 240px; height: 320px; margin-right: 15px; }
            .staff-cards-container.expanded .staff-card { margin-right: 15px; margin-bottom: 15px; }
            .staff-cards-container.expanded .staff-card:last-child { margin-right: 15px; }
            .card-title-bar { height: 35px; font-size: 1em; }
            .card-overlay { top: 35px; padding: 15px; }
            .staff-name { font-size: 1.1em; }
            .staff-qualifications, .staff-position { font-size: 0.8em; }
            .more-button { padding: 6px 12px; font-size: 0.8em; }
            .nav-arrow { padding: 8px; font-size: 1.2em; width: 35px; height: 35px; }
            .left-arrow { left: 5px; }
            .right-arrow { right: 5px; }
            .see-more-link { margin-right: 10px; font-size: 1em; }
        }
        /* --- END OF YOUR CSS --- */
    </style>
</head>
<body>
    <?php
    // Simulate header if include("include/header.php") is problematic for testing
    // Make sure your actual header.php doesn't output <html> or <body> tags if this file already does.
    if (file_exists("include/header.php")) {
        // include("include/header.php");
    } else {
    }
    ?>

    <div class="org-structure-container">

    <?php

    if (file_exists('include/config.php')) {
        include('include/config.php'); // This should define $con using mysqli_connect
    } else {
    }

    // Check if $con is a valid mysqli connection object
    if (!isset($con) || !($con instanceof mysqli)) {
    } else {
    }

    $staff_data_by_position = [
        'layer1' => [], 'layer2' => [], 'layer3' => [], 'layer4' => []
    ];

    $position_mapping = [
        'Dire' => ['text' => 'Director', 'layer' => 'layer1'],
        'Regi' => ['text' => 'Registrar', 'layer' => 'layer1'],
        'AReg' => ['text' => 'Asst. Registrar', 'layer' => 'layer1'],
        'Acct' => ['text' => 'Accountant', 'layer' => 'layer1'],
        'Legal' => ['text' => 'Legal Officer', 'layer' => 'layer1'],
        'Audit' => ['text' => 'Internal Auditor', 'layer' => 'layer1'],
        'HOD'  => ['text' => 'Head of Department', 'layer' => 'layer2'],
        'SLect' => ['text' => 'Senior Lecturer', 'layer' => 'layer3'],
        'Lect' => ['text' => 'Lecturer', 'layer' => 'layer4'],
        'Lib'  => ['text' => 'Librarian', 'layer' => 'layer4'],
        'Demo' => ['text' => 'Demonstrator', 'layer' => 'layer4'],
        'Offi' => ['text' => 'Office Staff', 'layer' => 'layer4'],
    ];

    $staff_results_array = []; // To store fetched rows

    
    // SQL Query - ensure s_order column exists or remove it from query
    // The IFNULL part is for robust sorting if s_order can be NULL.
    $sql = "SELECT stid, sname, spos, sed, stimg  
            FROM staff 
            WHERE status = '1' 
            ";
    // If you don't have s_order column, use this simpler query:
    // $sql = "SELECT stid, sname, spos, sed, stimg FROM staff WHERE status = 'Active' ORDER BY spos ASC, sname ASC";
    
    $result = mysqli_query($con, $sql);

    if ($result) {
        
        // Fetch all results into an array
        while ($row = mysqli_fetch_assoc($result)) {
            $staff_results_array[] = $row;
        }
        mysqli_free_result($result); // Free result set

        if (count($staff_results_array) > 0) {
            // echo "<div class='debug-output'><pre>Fetched staff data (first 2 records):\n" . htmlspecialchars(print_r(array_slice($staff_results_array,0,2), true)) . "</pre></div>";
        }

        $unmapped_positions_found = [];
        foreach ($staff_results_array as $staff_member) { // Use the fetched array
            if (isset($position_mapping[$staff_member['spos']])) {
                $layer_key = $position_mapping[$staff_member['spos']]['layer'];
                $staff_data_by_position[$layer_key][] = $staff_member;
            } else {
                 $unmapped_positions_found[$staff_member['spos']] = ($unmapped_positions_found[$staff_member['spos']] ?? 0) + 1;
                 echo "<div class='debug-output debug-error'>Warning: Staff member '{$staff_member['sname']}' (ID: {$staff_member['stid']}) has an unmapped position '{$staff_member['spos']}'. This staff member will not be displayed.</div>";
            }
        }
        if(!empty($unmapped_positions_found)){
             echo "<div class='debug-output debug-error'>Summary of unmapped positions: <pre>" . htmlspecialchars(print_r($unmapped_positions_found, true)) . "</pre>Ensure these 'spos' codes are in your \$position_mapping array.</div>";
        }

    } else {
        echo "<div class='debug-output debug-error'><strong>Database Query Error:</strong> " . htmlspecialchars(mysqli_error($con)) . "<br>SQL Tried: <code>".htmlspecialchars($sql)."</code></div>";
    }
    ?>

    <section class="org-layer layer-1">
        <br><br> 
        <h2 class="layer-title">Director Level / Executive Leadership</h2>
        <div class="staff-section-container">
            <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-1-cards', -1)" aria-label="Scroll Left">&#9664;</button>
            <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-1-cards', 1)" aria-label="Scroll Right">&#9654;</button>
            <div class="cards-wrapper">
                <div class="staff-cards-container" id="layer-1-cards">
                    <?php if (!empty($staff_data_by_position['layer1'])): ?>
                        <?php foreach ($staff_data_by_position['layer1'] as $staff_member): ?>
                            <?php
                            $display_position = $position_mapping[$staff_member['spos']]['text'];
                            $image_path = "admin/".$staff_member['stimg']; 
                            $image_url = !empty($image_path) ? htmlspecialchars($image_path) : "https://via.placeholder.com/280x350/1a237e/ffffff?text=" . urlencode($display_position);
                            ?>
                            <div class="staff-card" style="background-image: url('<?php echo $image_url; ?>');">
                                <div class="card-title-bar"><?php echo htmlspecialchars($display_position); ?></div>
                                <div class="card-overlay">
                                    <div class="card-content">
                                        <div class="staff-name"><?php echo htmlspecialchars($staff_member['sname']); ?></div>
                                        <div class="staff-qualifications"><?php echo htmlspecialchars($staff_member['sed']); ?></div>
                                        <div class="staff-position"><?php echo htmlspecialchars($display_position); ?></div>
                                        <button class="more-button" data-stid="<?php echo htmlspecialchars($staff_member['stid']); ?>">MORE</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style='text-align:center; padding:20px; width:100%;'>No Director Level staff found matching criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (count($staff_data_by_position['layer1'] ?? []) > 4): ?>
                <a href="#" class="see-more-link" data-target="layer-1-cards">See More Director Level Staff &gt;</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="org-layer layer-2">
        <h2 class="layer-title">Head of Departments (HODs)</h2>
        <div class="staff-section-container">
            <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-2-cards', -1)" aria-label="Scroll Left">&#9664;</button>
            <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-2-cards', 1)" aria-label="Scroll Right">&#9654;</button>
            <div class="cards-wrapper">
                <div class="staff-cards-container" id="layer-2-cards">
                    <?php if (!empty($staff_data_by_position['layer2'])): ?>
                        <?php foreach ($staff_data_by_position['layer2'] as $staff_member): ?>
                            <?php
                            $display_position = $position_mapping[$staff_member['spos']]['text'];
                            $image_path = "admin/".$staff_member['stimg'];
                            $image_url = !empty($image_path) ? htmlspecialchars($image_path) : "https://via.placeholder.com/280x350/004d40/ffffff?text=" . urlencode(substr($display_position, 0, 20));
                            ?>
                            <div class="staff-card" style="background-image: url('<?php echo $image_url; ?>');">
                                <div class="card-title-bar"><?php echo htmlspecialchars($display_position); ?></div>
                                <div class="card-overlay">
                                    <div class="card-content">
                                        <div class="staff-name"><?php echo htmlspecialchars($staff_member['sname']); ?></div>
                                        <div class="staff-qualifications"><?php echo htmlspecialchars($staff_member['sed']); ?></div>
                                        <div class="staff-position"><?php echo htmlspecialchars($display_position); ?></div>
                                        <button class="more-button" data-stid="<?php echo htmlspecialchars($staff_member['stid']); ?>">MORE</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style='text-align:center; padding:20px; width:100%;'>No HODs found matching criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (count($staff_data_by_position['layer2'] ?? []) > 4): ?>
                <a href="#" class="see-more-link" data-target="layer-2-cards">See More HODs &gt;</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="org-layer layer-3">
        <h2 class="layer-title">Senior Faculty / Managers</h2>
        <div class="staff-section-container">
             <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-3-cards', -1)" aria-label="Scroll Left">&#9664;</button>
            <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-3-cards', 1)" aria-label="Scroll Right">&#9654;</button>
            <div class="cards-wrapper">
                <div class="staff-cards-container" id="layer-3-cards">
                    <?php if (!empty($staff_data_by_position['layer3'])): ?>
                        <?php foreach ($staff_data_by_position['layer3'] as $staff_member): ?>
                            <?php
                            $display_position = $position_mapping[$staff_member['spos']]['text'];
                            $image_path = "admin/".$staff_member['stimg'];
                            $image_url = !empty($image_path) ? htmlspecialchars($image_path) : "https://via.placeholder.com/280x350/e65100/ffffff?text=" . urlencode($display_position);
                            ?>
                            <div class="staff-card" style="background-image: url('<?php echo $image_url; ?>');">
                                <div class="card-title-bar"><?php echo htmlspecialchars($display_position); ?></div>
                                <div class="card-overlay">
                                    <div class="card-content">
                                        <div class="staff-name"><?php echo htmlspecialchars($staff_member['sname']); ?></div>
                                        <div class="staff-qualifications"><?php echo htmlspecialchars($staff_member['sed']); ?></div>
                                        <div class="staff-position"><?php echo htmlspecialchars($display_position); ?></div>
                                        <button class="more-button" data-stid="<?php echo htmlspecialchars($staff_member['stid']); ?>">MORE</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style='text-align:center; padding:20px; width:100%;'>No Senior Faculty / Managers found matching criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (count($staff_data_by_position['layer3'] ?? []) > 4): ?>
            <a href="#" class="see-more-link" data-target="layer-3-cards">See More Senior Staff &gt;</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="org-layer layer-4">
        <h2 class="layer-title">Faculty / Academic & Administrative Staff</h2>
        <div class="staff-section-container">
            <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-4-cards', -1)" aria-label="Scroll Left">&#9664;</button>
            <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-4-cards', 1)" aria-label="Scroll Right">&#9654;</button>
            <div class="cards-wrapper">
                <div class="staff-cards-container" id="layer-4-cards">
                     <?php if (!empty($staff_data_by_position['layer4'])): ?>
                        <?php foreach ($staff_data_by_position['layer4'] as $staff_member): ?>
                            <?php
                            $display_position = $position_mapping[$staff_member['spos']]['text'];
                            $image_path = "admin/".$staff_member['stimg'];
                            $image_url = !empty($image_path) ? htmlspecialchars($image_path) : "https://via.placeholder.com/280x350/33691e/ffffff?text=" . urlencode($display_position);
                            ?>
                            <div class="staff-card" style="background-image: url('<?php echo $image_url; ?>');">
                                <div class="card-title-bar"><?php echo htmlspecialchars($display_position); ?></div>
                                <div class="card-overlay">
                                    <div class="card-content">
                                        <div class="staff-name"><?php echo htmlspecialchars($staff_member['sname']); ?></div>
                                        <div class="staff-qualifications"><?php echo htmlspecialchars($staff_member['sed']); ?></div>
                                        <div class="staff-position"><?php echo htmlspecialchars($display_position); ?></div>
                                        <button class="more-button" data-stid="<?php echo htmlspecialchars($staff_member['stid']); ?>">MORE</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style='text-align:center; padding:20px; width:100%;'>No Faculty / Academic & Administrative staff found matching criteria.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (count($staff_data_by_position['layer4'] ?? []) > 4): ?>
            <a href="#" class="see-more-link" data-target="layer-4-cards">See More Faculty & Staff &gt;</a>
            <?php endif; ?>
        </div>
    </section>

    <?php
    if (isset($con) && $con instanceof mysqli) {
        mysqli_close($con); // Close MySQLi connection
    }
    echo "\n";
    ?>
    </div> <script>
        // JAVASCRIPT REMAINS THE SAME AS IN THE PREVIOUS PDO VERSION
        // (No changes needed to the JavaScript for toggling arrows or scrolling)

        // Function to scroll for a specific layer
        function scrollLayer(containerId, direction) {
            const cardsContainer = document.getElementById(containerId);
            if (!cardsContainer || cardsContainer.classList.contains('expanded')) {
                return;
            }
            const card = cardsContainer.querySelector('.staff-card');
            if (!card) {
                return; 
            }
            const scrollDistance = cardsContainer.clientWidth * 0.8; 
            cardsContainer.scrollBy({
                left: direction * scrollDistance,
                behavior: 'smooth'
            });
        }

        // Function to toggle arrow visibility
        function toggleLayerArrows(containerId) {
            const cardsContainer = document.getElementById(containerId);
            if (!cardsContainer) {
                const staffSection = document.querySelector(`.staff-section-container button[onclick*="'${containerId}'"]`)?.closest('.staff-section-container');
                if (staffSection) {
                    const leftArrow = staffSection.querySelector('.left-arrow');
                    const rightArrow = staffSection.querySelector('.right-arrow');
                    if(leftArrow) leftArrow.classList.add('hidden');
                    if(rightArrow) rightArrow.classList.add('hidden');
                }
                return;
            }
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

            const moreButtons = document.querySelectorAll('.more-button');
            moreButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.stopPropagation(); 
                    const staffId = button.dataset.stid;
                    alert('Staff ID: ' + staffId + ' - "More" button clicked. Implement action.');
                    console.log('More button clicked for staff ID: ' + staffId);
                });
            });
        });
    </script>

    <?php
    // Simulate footer if include("include/footer.php") is problematic for testing
    if (file_exists("include/footer.php")) {
        // include("include/footer.php");
    } else {
        echo "<div class='debug-output debug-error'>Warning: include/footer.php not found.</div>";
    }

    include('include/footer.php');

    ?>
</body>
</html>