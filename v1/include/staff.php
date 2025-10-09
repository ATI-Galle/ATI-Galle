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
            font-family: sans-serif; /* Or your preferred font */
            background-color: #f8f8f8; /* Light background color */
        }

        .staff-section-container {
            position: relative;
            width: 100%;
            max-width: 1200px; /* Adjust as needed */
            margin: 40px auto; /* Center the section */
            overflow: hidden; /* Hide parts of cards outside the container */
            padding: 0 20px; /* Add padding for arrows on the sides */
            box-sizing: border-box;
        }

         /* Section Title (Optional, but good for context) */
        .section-title {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 30px;
            margin-top: 0;
        }


        .cards-wrapper {
            overflow: hidden; /* Crucial for horizontal scrolling */
        }

        .staff-cards-container {
            display: flex;
            /* gap: 20px; Remove gap here and use margin-right on card for easier JS calculation */
            scroll-behavior: smooth; /* Smooth scrolling effect */
            overflow-x: auto; /* Enable horizontal scrolling */
            scrollbar-width: none; /* Hide scrollbar for Firefox */
            -ms-overflow-style: none;  /* Hide scrollbar for IE and Edge */
             padding-bottom: 15px; /* Add padding in case of scrollbar space issues */
        }

        .staff-cards-container::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
        }

        .staff-card {
            flex: 0 0 auto; /* Prevent shrinking, allow basis based on content */
            width: 280px; /* Adjust card width as needed */
            height: 400px; /* Adjust card height as needed */
            margin-right: 20px; /* Space between cards */
            background-size: cover;
            background-position: center;
            color: white;
            position: relative;
            display: flex; /* Use flexbox for stacking title and overlay */
            flex-direction: column; /* Stack items vertically */
            border-radius: 10px; /* Rounded corners */
            overflow: hidden; /* Hide content that overflows card bounds */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            cursor: pointer;
        }

        .staff-card:last-child {
            margin-right: 0; /* No margin on the last card */
        }

        /* --- Styling for the title bar --- */
        .card-title-bar {
            height: 40px; /* Height of the color bar */
            display: flex;
            justify-content: center; /* Center text horizontally */
            align-items: center; /* Center text vertically */
            font-weight: bold;
            color: white;
            padding: 0 10px;
            box-sizing: border-box;
            flex-shrink: 0; /* Prevent the title bar from shrinking */
            text-align: center;
            background-color: #673ab7; /* Example color - adjust as needed */
            font-size: 1.1em;
        }

        /* You could add classes here if you want different colors for different cards */
        /* .staff-card.director .card-title-bar { background-color: #1a237e; } */


        .card-overlay {
            position: absolute; /* Keep absolute to cover the background image below the title bar */
            top: 40px; /* Start below the title bar (match title bar height) */
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.2)); /* Dark gradient from bottom */
            display: flex;
            flex-direction: column;
            justify-content: flex-end; /* Align content to the bottom within the overlay */
            padding: 20px;
        }


        .card-content {
            position: relative;
            z-index: 1;
            /* Adjust spacing within the overlay */
        }

        /* Styling for Staff Details */
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
             opacity: 0.9; /* Slightly less prominent */
         }

         .staff-position {
             font-size: 0.9em;
             margin-top: 0;
             margin-bottom: 15px; /* Space before the button */
             opacity: 0.9;
         }


        .more-button {
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent white */
            border: 2px solid white;
            color: white;
            padding: 8px 15px; /* Adjust padding */
            font-size: 0.9em; /* Adjust font size */
            cursor: pointer;
            transition: background-color 0.3s ease;
            align-self: flex-start; /* Align button to the left */
            margin-top: auto; /* Push the button to the bottom if content is shorter */
        }

        .more-button:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* Navigation Arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10; /* Ensure arrows are above cards */
            font-size: 1.5em;
            border-radius: 50%; /* Circular arrows */
            transition: background-color 0.3s ease, opacity 0.3s ease; /* Add opacity transition */
        }

        .nav-arrow:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }


        .left-arrow {
            left: 0; /* Position at the very left of the container padding */
        }

        .right-arrow {
            right: 0; /* Position at the very right of the container padding */
        }

        /* CSS to hide arrows when needed */
        .nav-arrow.hidden {
            opacity: 0; /* Fade out the arrow */
            pointer-events: none; /* Prevent clicking when hidden */
        }


        /* Basic Responsiveness */
        @media (max-width: 768px) {
             .staff-section-container {
                 margin: 20px auto;
                 padding: 0 10px; /* Adjust padding */
             }

            .staff-card {
                width: 250px; /* Adjust card width */
                height: 350px; /* Adjust card height */
                margin-right: 15px; /* Adjust space */
            }

            .card-title-bar {
                 height: 35px;
                 font-size: 1em;
            }

             .card-overlay {
                 top: 35px; /* Match title bar height */
                 padding: 15px;
             }

            .staff-name {
                 font-size: 1.1em;
            }

             .staff-qualifications,
             .staff-position {
                 font-size: 0.8em;
             }

            .more-button {
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
        <div class="cards-wrapper">
            <div class="staff-cards-container">
                

        <?php
        // 1. Include your database configuration and connection file.
        include_once ("config.php");

        // --- ADDED: Position mapping array to translate codes to full text ---
        $position_mapping = [
            'Dire'  => ['text' => 'Director'],
            'Regi'  => ['text' => 'Registrar'],
            'AReg'  => ['text' => 'Asst. Registrar'],
            'Acct'  => ['text' => 'Accountant'],
            'Legal' => ['text' => 'Legal Officer'],
            'Audit' => ['text' => 'Internal Auditor'],
            'HOD'   => ['text' => 'Head of Department'],
            'SLect' => ['text' => 'Senior Lecturer'],
            'Lec'   => ['text' => 'Lecturer'],
            'Lib'   => ['text' => 'Librarian'],
            'Demo'  => ['text' => 'Demonstrator'],
            'Offi'  => ['text' => 'Office Staff'],
        ];

        $staffMembers = []; // Initialize an empty array to store staff data

        // 2. Prepare the SQL query to select staff data.
        $sql = "SELECT stid, sname, spos, sed, status, created_at, stimg, updated_at FROM staff WHERE status = '1'";

        // 3. Execute the query and fetch the data
        if (isset($con)) {
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $staffMembers[] = $row;
                }
            }
        } else {
            echo "Database connection variable (\$con) not found. Please check your config.php.";
            exit;
        }

        // --- HTML Block with PHP Loop to Display Staff Cards ---

        if (!empty($staffMembers)) {
            foreach ($staffMembers as $staff) {
                // Sanitize data
                $sname = htmlspecialchars($staff['sname']);
                $sed = htmlspecialchars($staff['sed']);
                $stimg_url = htmlspecialchars($staff['stimg']);
                
                // --- MODIFIED: Look up the full position text from the mapping array ---
                $position_code = $staff['spos'];
                // Use the full text if found, otherwise fall back to the code itself
                $display_position = isset($position_mapping[$position_code]) ? $position_mapping[$position_code]['text'] : $position_code;
                $spos_full_text = htmlspecialchars($display_position);

                // Construct the full image path
                $imagePath = 'admin/' . $stimg_url;
        ?>
            <div class="staff-card" style="background-image: url('<?php echo $imagePath; ?>');">
                <div class="card-title-bar">
                    <?php echo $spos_full_text; // MODIFIED: Display full position text ?>
                </div>
                <div class="card-overlay">
                    <div class="card-content">
                        <div class="staff-name"><?php echo $sname; ?></div>
                        <div class="staff-qualifications"><?php echo $sed; ?></div>
                        <div class="staff-position"><?php echo $spos_full_text; // MODIFIED: Display full position text ?></div>
                        <button class="more-button">MORE</button>
                    </div>
                </div>
            </div>
        <?php
            } // End of foreach loop
        } else {
            // Display a default card or message if no staff data is available
        ?>
            <div class="staff-card" style="background-image: url('img/staff/default.jpg');">
                <div class="card-title-bar">
                    NO STAFF
                </div>
                <div class="card-overlay">
                    <div class="card-content">
                        <div class="staff-name">No Staff Available</div>
                        <div class="staff-qualifications"></div>
                        <div class="staff-position"></div>
                        <button class="more-button" disabled>MORE</button>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
            </div>
        </div>

        <div style="float:right;">
            <a href="staff.php">see more</a>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardsContainer = document.querySelector('.staff-cards-container');
            const leftArrow = document.querySelector('.left-arrow');
            const rightArrow = document.querySelector('.right-arrow');

            // This entire script section does not need any changes.
            // It will work as intended.

            const scrollContainer = (distance) => {
                cardsContainer.scrollBy({
                    left: distance,
                    behavior: 'smooth'
                });
            };

            leftArrow.addEventListener('click', () => {
                const card = document.querySelector('.staff-card');
                if (!card) return;
                const cardWidth = card.getBoundingClientRect().width;
                const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                const scrollDistance = cardWidth + cardMarginRight;
                scrollContainer(-scrollDistance);
            });

            rightArrow.addEventListener('click', () => {
                const card = document.querySelector('.staff-card');
                if (!card) return;
                const cardWidth = card.getBoundingClientRect().width;
                const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                const scrollDistance = cardWidth + cardMarginRight;
                scrollContainer(scrollDistance);
            });

            const toggleArrows = () => {
                const isAtStart = cardsContainer.scrollLeft <= 1;
                const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - 1;
                leftArrow.classList.toggle('hidden', isAtStart);
                rightArrow.classList.toggle('hidden', isAtEnd);
            };

            cardsContainer.addEventListener('scroll', toggleArrows);
            window.addEventListener('resize', toggleArrows);
            toggleArrows();
        });
    </script>
</body>
</html>