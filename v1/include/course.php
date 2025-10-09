
    <style>
       
        .controls {
            margin-bottom: 1rem;
            text-align: center;
        }
        /* Main container for the slider */
        .pure-slider-container {
            position: relative;
            width: 100%;
            max-width: 1200px; /* Max width */
            margin: auto;
            overflow: hidden; /* Hide overflowing cards */
            border-radius: 0.5rem; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        /* Wrapper for all cards - enables flexbox and transitions */
        .pure-slider-container #slider-wrapper {
            display: flex;
        }
        /* Individual card styling */
        .pure-slider-container .card-item {
            flex-shrink: 0;
            width: 100%; /* Default width for mobile */
            padding: 0.5rem;
            box-sizing: border-box;
        }
        /* Inner card structure */
        .pure-slider-container .card {
            border-radius: 0.5rem;
            overflow: hidden;
            height: 400px; /* Fixed height for consistency */
            display: flex;
            flex-direction: column;
            color: #fff;
            position: relative;
        }
        /* Card Header */
        .pure-slider-container .card-header {
            padding: 0.6rem 1rem;
            height: 85px; /* Fixed height for consistency */
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            z-index: 3;
            position: relative;
            background-color: inherit;
            text-transform: uppercase;
        }
        /* Card Image Area */
        .pure-slider-container .card-image-area {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
        }
        .pure-slider-container .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        /* Card Body - Content overlay */
        .pure-slider-container .card-body {
            margin-top: 150px; /* Push to bottom */
            padding: 1rem;
            position: relative;
            z-index: 2;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.7) 40%, rgba(0, 0, 0, 0) 70%);
        }
        .pure-slider-container .card-text {
            font-size: 0.85rem;
            line-height: 1.4;
            min-height: 6em; /* Approx 4 lines */
            color: #fff;
        }
        .pure-slider-container .card-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #fff;
            background-color: transparent;
            color: #fff;
            text-align: center;
        }
        .pure-slider-container .card-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        /* --- Specific card background colors --- */
        .pure-slider-container .card.bg-maroon { background-color: #8B0000; }
        .pure-slider-container .card.bg-purple { background-color: #6f42c1; }
        .pure-slider-container .card.bg-orange { background-color: #fd7e14; }
        .pure-slider-container .card.bg-blue { background-color: #0dcaf0; }
        .pure-slider-container .card.bg-green { background-color: #198754; }
        .pure-slider-container .card.bg-teal { background-color: #20c997; }
        .pure-slider-container .card.bg-indigo { background-color: #6610f2; }
        /* Hide scrollbar but allow scrolling */
        .pure-slider-container .hide-scrollbar {
            -ms-overflow-style: none; scrollbar-width: none;
        }
        .pure-slider-container .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Navigation button styling */
        .pure-slider-container .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            transition: background-color 0.3s ease;
            line-height: 0;
            width: 45px;
            height: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pure-slider-container .slider-btn:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
        .pure-slider-container .slider-btn:disabled {
            cursor: not-allowed; opacity: 0.5;
        }
        .pure-slider-container #prev-btn { left: 0.5rem; }
        .pure-slider-container #next-btn { right: 0.5rem; }
        /* Responsive widths using media queries */
        @media (min-width: 576px) { .pure-slider-container .card-item { width: 50%; } }
        @media (min-width: 768px) { .pure-slider-container .card-item { width: 33.3333%; } }
        @media (min-width: 992px) { .pure-slider-container .card-item { width: 25%; } }
        @media (min-width: 1200px) { .pure-slider-container .card-item { width: 20%; } }
    </style>


    <div class="controls">
        <h2 class="section-title">Departments</h2>
    </div>

    <div class="pure-slider-container">
        <div id="slider-container" class="hide-scrollbar">
            <div id="slider-wrapper">
                <?php
                // To make this code work without a database, I am creating sample data here.
                // If this works, your problem is with the database connection in 'config.php'.
                // To switch back to your database, DELETE this "Sample Data" section and
                // UNCOMMENT the "Database Fetching Code" section below it.

                // --- START: Sample Data (for testing) ---
                $courses = [
                    ['cid' => 1, 'cname' => 'HND in IT', 'ctext' => 'Covers software development, networking, and web technologies.', 'cimg' => 'images/sample1.jpg'],
                    ['cid' => 2, 'cname' => 'HND in Business', 'ctext' => 'Focuses on management, marketing, and financial principles.', 'cimg' => 'images/sample2.jpg'],
                    ['cid' => 3, 'cname' => 'HND in Engineering', 'ctext' => 'Provides skills in civil, mechanical, and electrical engineering.', 'cimg' => 'images/sample3.jpg'],
                    ['cid' => 4, 'cname' => 'HND in Accountancy', 'ctext' => 'Prepares students for a career in finance and accounting.', 'cimg' => 'images/sample4.jpg'],
                    ['cid' => 5, 'cname' => 'HND in Tourism', 'ctext' => 'Explore the dynamic world of travel and hospitality management.', 'cimg' => 'images/sample5.jpg'],
                    ['cid' => 6, 'cname' => 'HND in Agriculture', 'ctext' => 'Learn modern techniques in sustainable farming and agribusiness.', 'cimg' => 'images/sample6.jpg'],
                ];
                // --- END: Sample Data ---


                
                // --- START: Database Fetching Code (UNCOMMENT to use your database) ---
                include_once("config.php");
                $courses = [];
                $sql = "SELECT cid, cname, ctext, cimg FROM course WHERE status='1'";
                if (isset($con)) {
                    $result = $con->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $courses[] = $row;
                        }
                    }
                }
                // --- END: Database Fetching Code ---
                


                // Check if there are any courses to display
                if (!empty($courses)) {
                    // Define an array of available background color classes from your CSS
                    $bg_colors = ['bg-maroon', 'bg-purple', 'bg-orange', 'bg-blue', 'bg-green', 'bg-teal', 'bg-indigo'];

                    foreach ($courses as $course) {
                        // Sanitize data for security
                        $cname = htmlspecialchars($course['cname']);
                        $cimg_url = 'admin/'.htmlspecialchars($course['cimg']);
                        $ctext = htmlspecialchars(strip_tags($course['ctext']));
                        $cid = $course['cid'];

                        // Randomly select a color class from the array for each card
                        $card_bg_class = $bg_colors[array_rand($bg_colors)];
                ?>
                        <div class="card-item">
                            <div class="card <?php echo $card_bg_class; ?>">
                                <div class="card-header"><?php echo $cname; ?></div>
                                <div class="card-image-area">
                                    <img src="<?php echo $cimg_url; ?>" class="card-image" alt="<?php echo $cname; ?>" onerror="this.src='https://placehold.co/400x450/cccccc/000000?text=Image+Not+Found';">
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo $ctext; ?></p>
                                    <a href="course.php?cid=<?php echo $cid; ?>" class="card-button">MORE</a>
                                </div>
                            </div>
                        </div>
                <?php
                    } // End of foreach loop
                } else {
                    // Display a message if no courses are found
                    echo "<div style='width:100%; text-align:center; padding: 20px; color: #333;'><p>No courses available.</p></div>";
                }
                ?>
            </div>
        </div>
        <button id="prev-btn" class="slider-btn" title="Previous">&#9664;</button>
        <button id="next-btn" class="slider-btn" title="Next">&#9654;</button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const sliderContainer = document.querySelector('.pure-slider-container');
        if (!sliderContainer) {
            console.error("Slider container not found.");
            return;
        }
        
        const sliderWrapper = sliderContainer.querySelector('#slider-wrapper');
        const prevBtn = sliderContainer.querySelector('#prev-btn');
        const nextBtn = sliderContainer.querySelector('#next-btn');

        if (!sliderWrapper || !prevBtn || !nextBtn) {
            console.error("Slider components (wrapper or buttons) not found.");
            return;
        }

        let cardWidth = 0;
        let currentIndex = 0;
        let cardsToClone = 0;
        let originalCardCount = 0;
        let isTransitioning = false;
        let visibleCards = 1;

        const getVisibleCardsCount = () => {
            const firstOriginalCard = sliderWrapper.querySelector('.card-item:not([data-is-clone="true"])');
            if (!firstOriginalCard) return 1;

            const cardItemStyle = window.getComputedStyle(firstOriginalCard);
            const cardWidthPercentMatch = cardItemStyle.width.match(/(\d+(\.\d+)?)%/);

            if (cardWidthPercentMatch) {
                const cardWidthPercent = parseFloat(cardWidthPercentMatch[1]);
                return cardWidthPercent > 0 ? Math.max(1, Math.floor(99.9 / cardWidthPercent)) : 1;
            } else {
                const scrollContainerWidth = sliderWrapper.parentElement.offsetWidth;
                const cardWidthPx = firstOriginalCard.offsetWidth;
                return cardWidthPx > 0 ? Math.max(1, Math.floor(scrollContainerWidth / cardWidthPx)) : 1;
            }
        };

        const updateSliderPosition = (animate = true) => {
            if (!sliderWrapper) return;
            sliderWrapper.style.transition = animate ? 'transform 0.5s ease-in-out' : 'none';
            sliderWrapper.style.transform = `translateX(${-cardWidth * currentIndex}px)`;
        };

        const handleTransitionEnd = () => {
            isTransitioning = false;
            let adjusted = false;
            if (currentIndex >= originalCardCount + cardsToClone) {
                currentIndex = cardsToClone;
                adjusted = true;
            } else if (currentIndex < cardsToClone) {
                currentIndex = originalCardCount + cardsToClone - 1;
                 adjusted = true;
            }

            if (adjusted) {
                updateSliderPosition(false);
            }
        };

        const moveSlider = (direction) => {
            if (isTransitioning || originalCardCount <= visibleCards) return;
            isTransitioning = true;
            currentIndex += (direction === 'next' ? 1 : -1);
            updateSliderPosition(true);
        };

        const initializeSlider = () => {
            isTransitioning = false;
            
            const clones = sliderWrapper.querySelectorAll('.card-item[data-is-clone="true"]');
            clones.forEach(clone => clone.remove());
            
            const originalCards = Array.from(sliderWrapper.querySelectorAll('.card-item:not([data-is-clone="true"])'));
            originalCardCount = originalCards.length;

            if (originalCardCount === 0) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                return;
            } else {
                 prevBtn.style.display = 'flex';
                 nextBtn.style.display = 'flex';
            }

            visibleCards = getVisibleCardsCount();
            
            if (originalCardCount > visibleCards) {
                prevBtn.disabled = false;
                nextBtn.disabled = false;
                cardsToClone = visibleCards;

                for (let i = 0; i < cardsToClone; i++) {
                    const startClone = originalCards[i].cloneNode(true);
                    startClone.setAttribute('data-is-clone', 'true');
                    sliderWrapper.appendChild(startClone);

                    const endClone = originalCards[originalCardCount - 1 - i].cloneNode(true);
                    endClone.setAttribute('data-is-clone', 'true');
                    sliderWrapper.prepend(endClone);
                }
            } else {
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                cardsToClone = 0;
            }

            const allCards = sliderWrapper.querySelectorAll('.card-item');
            if (allCards.length > 0) {
                cardWidth = allCards[0].offsetWidth;
            }

            currentIndex = cardsToClone;
            updateSliderPosition(false);

            sliderWrapper.removeEventListener('transitionend', handleTransitionEnd);
            if (cardsToClone > 0) {
                sliderWrapper.addEventListener('transitionend', handleTransitionEnd);
            }
        };
        
        prevBtn.addEventListener('click', () => moveSlider('prev'));
        nextBtn.addEventListener('click', () => moveSlider('next'));

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(initializeSlider, 250);
        });
        
        initializeSlider();
    });
    </script>
