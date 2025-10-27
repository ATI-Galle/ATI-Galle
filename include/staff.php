<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Members Section</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color: #f8f8f8;
        }

        .staff-section-container {
            position: relative;
            width: 100%;
            max-width: 1200px;
            margin: 40px auto;
            overflow: hidden;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .section-title {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 30px;
            margin-top: 0;
        }


        .cards-wrapper {
            overflow: hidden;
        }

        .staff-cards-container {
            display: flex;
            scroll-behavior: smooth;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 15px;
        }

        .staff-cards-container::-webkit-scrollbar {
            display: none;
        }

        .staff-card {
            flex: 0 0 auto;
            width: 280px;
            height: 400px;
            margin-right: 20px;
            background-size: cover;
            background-position: center;
            color: white;
            position: relative;
            display: flex;
            flex-direction: column;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .staff-card:last-child {
            margin-right: 0;
        }

        .card-title-bar {
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: white;
            padding: 0 10px;
            box-sizing: border-box;
            flex-shrink: 0;
            text-align: center;
            background-color: #673ab7;
            font-size: 1.1em;
        }

        .card-overlay {
            position: absolute;
            top: 40px;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(255, 255, 255, 0));
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
        }


        .card-content {
            position: relative;
            z-index: 1;
        }

        .staff-name {
             font-size: 1.3em;
             font-weight: bold;
             margin-top: 0;
             margin-bottom: 5px;
        }

        .staff-qualifications {
             font-size: 0.9em;
             margin-top: 0;
             margin-bottom: 5px;
             opacity: 0.9;
        }

         .staff-position {
             font-size: 0.9em;
             margin-top: 0;
             margin-bottom: 15px;
             opacity: 0.9;
        }

        .cid-label {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 15px;
            font-size: 0.9em;
            align-self: flex-start;
            border-radius: 5px;
            margin-top: auto;
            display: inline-block;
            text-align: center;
            font-weight: bold;
        }

        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
            font-size: 1.5em;
            border-radius: 50%;
            transition: background-color 0.3s ease, opacity 0.3s ease;
        }

        .nav-arrow:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }


        .left-arrow {
            left: 0;
        }

        .right-arrow {
            right: 0;
        }

        .nav-arrow.hidden {
            opacity: 0;
            pointer-events: none;
        }


        @media (max-width: 768px) {
            .staff-section-container {
                margin: 20px auto;
                padding: 0 10px;
            }

            .staff-card {
                width: 250px;
                height: 350px;
                margin-right: 15px;
            }

            .card-title-bar {
                height: 35px;
                font-size: 1em;
            }

            .card-overlay {
                top: 35px;
                padding: 15px;
            }

            .staff-name {
                font-size: 1.1em;
            }

            .staff-qualifications,
            .staff-position {
                font-size: 0.8em;
            }
            
            .cid-label {
                padding: 6px 12px;
                font-size: 0.8em;
            }

            .nav-arrow {
                padding: 8px;
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>

    <section class="staff-section-container" id="staff">
        <h2 class="section-title">Our Staff</h2>
        <br><br>
        <button class="nav-arrow left-arrow" aria-label="Scroll Left"><i class="fas fa-chevron-left"></i></button>
        <button class="nav-arrow right-arrow" aria-label="Scroll Right"><i class="fas fa-chevron-right"></i></button>
        
        <div class="cards-wrapper">
            <div class="staff-cards-container">
                
        <?php
        include_once ("config.php");

        $position_mapping = [
            'Dire'  => ['text' => 'Director'],
            'Regi'  => ['text' => 'Registrar'],
            'AReg'  => ['text' => 'Asst. Registrar'],
            'Acct'  => ['text' => 'Accountant'],
            'Legal' => ['text' => 'Legal Officer'],
            'Audit' => ['text' => 'Internal Auditor'],
            'HOD'   => ['text' => 'Head of Department'],
            'SLect' => ['text' => 'Senior Lecturer'],
            'Lect'  => ['text' => 'Lecturer'],
            'Lib'   => ['text' => 'Librarian'],
            'Demo'  => ['text' => 'Demonstrator'],
            'Offi'  => ['text' => 'Office Staff'],
        ];

        $staffMembers = [];
        $sql = "SELECT stid, sname, spos, cid, sed, status, created_at, stimg, updated_at FROM staff WHERE status = '1'";

        if (isset($con)) {
            $result = $con->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $staffMembers[] = $row;
                }
            }
        } else {
            echo "Database connection variable (\$con) not found.";
            exit;
        }

        // --- NEW SORTING LOGIC STARTS HERE ---
        if (!empty($staffMembers)) {
            // 1. Define the desired order for positions. Lower numbers come first.
            $position_order = [
                'Dire'  => 1,
                'Regi'  => 2,
                'HOD'   => 3,
                'SLect' => 4,
                'Lect'  => 5,
                'Demo'  => 6
            ];

            // 2. Sort the $staffMembers array using our custom order.
            usort($staffMembers, function($a, $b) use ($position_order) {
                // Assign a high number (99) to any position not in our custom list, so they go to the end.
                $order_a = $position_order[$a['spos']] ?? 99;
                $order_b = $position_order[$b['spos']] ?? 99;

                // Compare the two order numbers.
                return $order_a <=> $order_b;
            });
        }
        // --- NEW SORTING LOGIC ENDS HERE ---

        if (!empty($staffMembers)) {
            foreach ($staffMembers as $staff) {
                $sname = htmlspecialchars($staff['sname']);
                $sed = htmlspecialchars($staff['sed']);
                $stimg_url = htmlspecialchars($staff['stimg']);
                
                $position_code = $staff['spos'];
                $display_position = isset($position_mapping[$position_code]) ? $position_mapping[$position_code]['text'] : $position_code;
                $spos_full_text = htmlspecialchars($display_position);

                $imagePath = 'admin/' . $stimg_url;
        ?>
                <div class="staff-card" style="background-image: url('<?php echo $imagePath; ?>');">
                    <div class="card-title-bar">
                        <?php echo $spos_full_text; ?>
                    </div>
                    <div class="card-overlay">
                        <div class="card-content">
                            <div class="staff-name"><?php echo $sname; ?></div>
                            <div class="staff-qualifications"><?php echo $sed; ?></div>
                            <div class="staff-position"><?php echo $spos_full_text; ?></div>

                            <?php
                                $positions_to_skip_label = ['Dire', 'Regi', 'Lib']; 
                                
                                if (!in_array($staff['spos'], $positions_to_skip_label)) {
                                    $cid_display = !empty($staff['cid']) ? htmlspecialchars($staff['cid']) : 'Demo';
                                    echo '<div class="cid-label">' . $cid_display . '</div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
        <?php
            } // End of foreach loop
        } else {
        ?>
            <div class="staff-card" style="background-image: url('img/staff/default.jpg');">
                <div class="card-title-bar">NO STAFF</div>
                <div class="card-overlay">
                    <div class="card-content">
                        <div class="staff-name">No Staff Available</div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
            </div>
        </div>

        <div style="float:right; margin-top: 15px;">
             <a href="staff.php">See More...</a>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardsContainer = document.querySelector('.staff-cards-container');
            const leftArrow = document.querySelector('.left-arrow');
            const rightArrow = document.querySelector('.right-arrow');

            if (!cardsContainer || !leftArrow || !rightArrow) return;

            const scrollContainer = (distance) => {
                cardsContainer.scrollBy({
                    left: distance,
                    behavior: 'smooth'
                });
            };

            leftArrow.addEventListener('click', () => {
                const scrollDistance = cardsContainer.clientWidth * 0.8;
                scrollContainer(-scrollDistance);
            });

            rightArrow.addEventListener('click', () => {
                const scrollDistance = cardsContainer.clientWidth * 0.8;
                scrollContainer(scrollDistance);
            });

            const toggleArrows = () => {
                const tolerance = 2;
                const isAtStart = cardsContainer.scrollLeft <= tolerance;
                const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - tolerance;
                
                const isScrollable = cardsContainer.scrollWidth > cardsContainer.clientWidth;
                if (!isScrollable) {
                    leftArrow.classList.add('hidden');
                    rightArrow.classList.add('hidden');
                    return;
                }

                leftArrow.classList.toggle('hidden', isAtStart);
                rightArrow.classList.toggle('hidden', isAtEnd);
            };

            cardsContainer.addEventListener('scroll', toggleArrows);
            window.addEventListener('resize', toggleArrows);
            toggleArrows();
        });
    </script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>