<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest News Section with Carousel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            line-height: 1.6;
            color: #333;
            background-color: #f8f8f8; /* Light background color */
        }

        .latest-news-section-container {
            max-width: 1200px; /* Limit section width */
            margin: 40px auto; /* Center the section */
            padding: 0 20px; /* Add horizontal padding */
            box-sizing: border-box;
            position: relative; /* Kept relative for potential future use, but not strictly needed for inline arrows */
            /* overflow: hidden; /* Removed overflow hidden from main container */
        }

        .news-header {
            display: flex; /* Use Flexbox for header layout */
            justify-content: space-between; /* Space out title and controls */
            align-items: center; /* Vertically align items */
            margin-bottom: 20px; /* Space below header */
            padding: 0 0px; /* Padding is on the main container */
        }

        .section-title {
            font-size: 1.8em; /* Adjust title size */
            color: #333;
            margin: 0; /* Remove default margins */
        }

        .header-controls {
            display: flex; /* Use Flexbox for View All and arrows */
            align-items: center; /* Vertically align items */
            /* Added gap for spacing between items */
             gap: 10px;
        }

        .view-all-link {
            text-decoration: none; /* Remove underline */
            color: #1a237e; /* Example link color */
            font-size: 1em;
            transition: color 0.3s ease;
        }

        .view-all-link:hover {
            color: #673ab7; /* Change color on hover */
        }

        /* Navigation Arrows (Original styling for inline placement in header) */
        .news-nav-arrow {
            background: none; /* No background */
            border: 1px solid #ccc; /* Subtle border */
            color: #333; /* Icon color */
            padding: 8px; /* Adjust padding */
            cursor: pointer;
            font-size: 0.8em; /* Adjust icon size */
            /* margin-left: 5px; /* Space between arrows - replaced by gap on header-controls */
            border-radius: 4px; /* Slight rounded corners */
            transition: background-color 0.3s ease, border-color 0.3s ease, opacity 0.3s ease;
        }

        .news-nav-arrow:hover {
            background-color: #eee; /* Subtle background on hover */
            border-color: #aaa;
        }

        /* Style for disabled/hidden arrows */
         .news-nav-arrow.hidden {
            opacity: 0.5; /* Fade out */
            pointer-events: none; /* Prevent clicking */
            cursor: default;
         }


        .news-cards-wrapper {
            overflow: hidden; /* Hide parts of cards outside the container */
        }

        .news-cards-container {
            display: flex; /* Arrange cards horizontally */
            /* gap: 20px; /* Use margin-right on card for easier JS calc */
            scroll-behavior: smooth; /* Smooth scrolling */
            overflow-x: auto; /* Enable horizontal scrolling */
            scrollbar-width: none; /* Hide scrollbar Firefox */
            -ms-overflow-style: none;  /* Hide scrollbar IE/Edge */
            padding-bottom: 15px; /* Add padding for potential scrollbar space */
            /* Removed special padding/margins for absolute arrows */
        }

        .news-cards-container::-webkit-scrollbar {
            display: none; /* Hide scrollbar Chrome/Safari/Opera */
        }


        .news-item-card {
            flex: 0 0 auto; /* Prevent shrinking */
            width: 300px; /* Adjust card width */
            margin-right: 20px; /* Space between cards */
            background-color: #fff; /* White card background */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            overflow: hidden; /* Hide overflowing image/content */
            display: flex;
            flex-direction: column; /* Stack date, image, content */
            cursor: pointer; /* Indicate clickable */
        }

        .news-item-card:last-child {
            margin-right: 0; /* No margin on the last card */
        }

        .news-date {
            font-size: 0.8em;
            color: #666; /* Grey date color */
            padding: 10px 15px;
            background-color: #f1f1f1; /* Light background for date */
        }

        .news-item-image {
            width: 100%; /* Image takes full width of the card */
            height: 180px; /* Fixed height for the image - adjust as needed */
            object-fit: cover; /* Cover the area without distorting */
            display: block; /* Remove extra space below image */
        }

        .news-item-content {
            padding: 15px;
            flex-grow: 1; /* Allow content area to take remaining height */
            display: flex;
            flex-direction: column; /* Stack title, text, button */
        }

        .news-item-content h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.1em;
            color: #1a237e; /* Example: Theme color for title */
        }

        .news-item-content p {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 0.9em;
             /* Limit lines */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 3; /* Show max 3 lines */
        }

        .read-more-button {
            display: inline-block; /* For padding/margin */
            background: none; /* Transparent background */
            border: 1px solid #ccc; /* Subtle border */
            color: #333; /* Text color */
            padding: 8px 15px;
            font-size: 0.9em;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; /* If using <a> tag */
            transition: background-color 0.3s ease, border-color 0.3s ease;
            align-self: flex-start; /* Align to the left */
            margin-top: auto; /* Push button to the bottom */
        }

        .read-more-button:hover {
             background-color: #f1f1f1; /* Light background on hover */
             border-color: #aaa;
        }


        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .latest-news-section-container {
                margin: 20px auto;
                padding: 0 10px; /* Adjust padding */
            }

             .news-header {
                flex-direction: column; /* Stack header items */
                text-align: center;
                margin-bottom: 15px;
             }

             .section-title {
                font-size: 1.5em;
                margin-bottom: 10px;
             }

             .header-controls {
                margin-top: 10px;
                gap: 5px; /* Adjust gap */
             }

             .view-all-link {
                font-size: 0.9em;
             }

             .news-nav-arrow {
                 padding: 6px; /* Adjust padding */
                 font-size: 0.7em; /* Adjust size */
            }

             .news-cards-container {
                /* No special padding/margins for absolute arrows */
             }

            .news-item-card {
                width: 250px; /* Adjust card width */
                margin-right: 15px; /* Adjust space */
            }

             .news-item-image {
                 height: 150px; /* Adjust image height */
             }

            .news-item-content {
                 padding: 10px; /* Adjust padding */
             }

             .news-item-content h3 {
                 font-size: 1em;
             }

             .news-item-content p {
                 font-size: 0.8em;
                 margin-bottom: 10px;
             }

             .read-more-button {
                 padding: 6px 12px;
                 font-size: 0.8em;
             }
        }
    </style>
</head>
<body>

    <section class="latest-news-section-container">
        <div class="news-header">
            <h2 class="section-title">Latest Events</h2>
            <div class="header-controls">
                <a href="#" class="view-all-link">View All News</a>
                <button class="news-nav-arrow left-arrow"><i class="fas fa-chevron-left"></i></button>
                <button class="news-nav-arrow right-arrow"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="news-cards-wrapper">
            <div class="news-cards-container">

                <div class="news-item-card">
                    <div class="news-date">23 APRIL 2025</div>
                    <img src="img/ati/ati2.jpeg" alt="News Image" class="news-item-image">
                    <div class="news-item-content">
                        <h3>CINEC Campus and Anglia Ruskin University Partnership</h3>
                        <p>CINEC Campus has established an exclusive partnership with Anglia Ruskin University (ARU), a top-ranked institution based in Cambridge, UK. This colla...</p>
                        <button class="read-more-button"><a href="event.php">Read More</a></button>
                    </div>
                </div>

                <div class="news-item-card">
                    <div class="news-date">13 APRIL 2025</div>
                     <img src="img/ati/ati.jpg" alt="News Image" class="news-item-image">
                     <div class="news-item-content">
                        <h3>CINEC Campus Achieves Major Milestone: Listed in the World H...</h3>
                         <p>CINEC Campus has achieved a significant milestone in its academic journey by securing its place in the prestigious World Higher Education Database (WH...</p>
                         <button class="read-more-button"><a href="event.php">Read More</a></button>
                         </div>
                </div>

                 <div class="news-item-card">
                    <div class="news-date">08 APRIL 2025</div>
                     <img src="img/ati/ati.jpg" alt="News Image" class="news-item-image">
                     <div class="news-item-content">
                        <h3>CINEC Wasantha Udanaya 2025 Celebrates New Year with Grand P...</h3>
                         <p>In a dazzling celebration of Sinhala and Tamil New Year traditions, CINEC Campus hosted its prestigious "Wasantha Udanaya 2025" event, featu...</p>
                         <button class="read-more-button"><a href="event.php">Read More</a></button>
                         </div>
                </div>

                <div class="news-item-card">
                    <div class="news-date">05 APRIL 2025</div>
                     <img src="img/ati/ati2.jpeg" alt="News Image" class="news-item-image">
                     <div class="news-item-content">
                        <h3>Another Great News Title Here</h3>
                         <p>A short snippet describing the news article. This will be limited to a few lines to fit the card design...</p>
                         <button class="read-more-button"><a href="event.php">Read More</a></button>
                         </div>
                </div>

                 <div class="news-item-card">
                    <div class="news-date">01 APRIL 2025</div>
                     <img src="img/ati/ati.jpg" alt="News Image" class="news-item-image">
                     <div class="news-item-content">
                         <h3>Exciting Event Held on Campus</h3>
                         <p>Details about an exciting event that took place recently. Read more to find out all about it and see photos...</p>
                         <button class="read-more-button"><a href="event.php">Read More</a></button>
                         </div>
                </div>

                </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cardsContainer = document.querySelector('.news-cards-container');
            // Select arrows using their specific classes now that they are in the header
            const leftArrow = document.querySelector('.news-nav-arrow.left-arrow');
            const rightArrow = document.querySelector('.news-nav-arrow.right-arrow');

            // Function to scroll the container
            const scrollContainer = (distance) => {
                cardsContainer.scrollBy({
                    left: distance,
                    behavior: 'smooth'
                });
            };

            // Event listeners for arrows
            leftArrow.addEventListener('click', () => {
                // Calculate scroll distance (width of one card + its right margin)
                 const card = document.querySelector('.news-item-card');
                 if (!card) return; // Prevent error if no cards exist
                 const cardWidth = card.getBoundingClientRect().width;
                 const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                 const scrollDistance = cardWidth + cardMarginRight;

                 scrollContainer(-scrollDistance); // Scroll left
            });

            rightArrow.addEventListener('click', () => {
                 // Calculate scroll distance
                 const card = document.querySelector('.news-item-card');
                 if (!card) return; // Prevent error if no cards exist
                 const cardWidth = card.getBoundingClientRect().width;
                 const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
                 const scrollDistance = cardWidth + cardMarginRight;

                 scrollContainer(scrollDistance); // Scroll right
            });

            // Logic to hide/show arrows based on scroll position
            const toggleArrows = () => {
                 // Check if scrolled to the beginning or end
                 // Add a small tolerance (e.g., 1px) for potential subpixel issues
                 const isAtStart = cardsContainer.scrollLeft <= 1;
                 // Check if the right edge of the scrollable content is visible
                 // Use scrollWidth and clientWidth to determine the end point
                 const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - 1;

                 // Hide/show the arrows
                 leftArrow.classList.toggle('hidden', isAtStart);
                 rightArrow.classList.toggle('hidden', isAtEnd);
            };

            // Listen for scroll events on the container to update arrow visibility
            cardsContainer.addEventListener('scroll', toggleArrows);

            // Also check arrow visibility when the window is resized (in case cards per view changes)
            window.addEventListener('resize', toggleArrows);

            // Initial check on load to set the correct arrow visibility
            toggleArrows();
        });
    </script>

</body>
</html>