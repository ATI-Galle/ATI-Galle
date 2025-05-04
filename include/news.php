<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Section</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            line-height: 1.6;
            color: #333;
        }

        .news-section-container {
            background-color: #ffc107; /* Yellow/Orange background */
            padding: 40px 20px; /* Add padding */
            text-align: center; /* Center align contents like title and button */
        }

        .section-title {
            color: #333; /* Dark color for the title */
            margin-top: 0;
            margin-bottom: 30px;
            font-size: 2.5em; /* Adjust font size */
            position: relative;
            display: inline-block; /* Allow centering */
        }

         /* Optional: Underline effect for the title */
        .section-title::after {
            content: '';
            display: block;
            width: 60px; /* Width of the underline */
            height: 3px; /* Thickness of the underline */
            background-color: #333; /* Underline color */
            margin: 10px auto 0; /* Center the underline below the title */
        }


        .news-grid {
            display: grid;
            /* Responsive grid: 2 columns minimum 280px, fills space */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; /* Gap between grid items */
            max-width: 1200px; /* Limit grid width */
            margin: 0 auto 30px auto; /* Center grid and add bottom margin */
        }

        .news-card {
            background-color: #fff; /* White background for cards */
            border-radius: 8px; /* Soften corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            display: flex; /* Use Flexbox to arrange image and content */
            padding: 15px;
            text-align: left; /* Align text left within the card */
        }

        .news-thumbnail {
            width: 80px; /* Fixed width for thumbnail */
            height: 80px; /* Fixed height for thumbnail */
            object-fit: cover; /* Cover the area without distorting */
            border-radius: 4px; /* Slight rounded corners for image */
            margin-right: 15px; /* Space between image and text */
            flex-shrink: 0; /* Prevent thumbnail from shrinking */
        }

        .news-content {
            flex-grow: 1; /* Allow content to take remaining space */
            display: flex;
            flex-direction: column; /* Stack title, text, link */
            justify-content: space-between; /* Space out content */
        }

        .news-content h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.1em; /* Adjust title font size */
            color: #1a237e; /* Example: Use a theme color */
        }

        .news-content p {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 0.9em; /* Adjust text font size */
            /* Limit lines if needed */
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 3; /* Show max 3 lines of text */
        }

        .read-more {
            display: inline-block; /* Treat as block for margin */
            margin-top: auto; /* Push to the bottom of the flex container */
            color: #673ab7; /* Example: Use a theme color for link */
            text-decoration: none; /* Remove underline */
            font-weight: bold;
            font-size: 0.9em;
            transition: color 0.3s ease;
        }

        .read-more:hover {
            color: #5e35b1; /* Darker color on hover */
            text-decoration: underline; /* Add underline on hover */
        }

        .view-all-button {
            display: inline-block; /* Centered block */
            background-color: #333; /* Dark background for button */
            color: white;
            padding: 12px 25px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* If using <a> tag */
            transition: background-color 0.3s ease, transform 0.1s ease;
            margin-top: 20px; /* Space above the button */
        }

        .view-all-button:hover {
            background-color: #555; /* Darker on hover */
            transform: translateY(-2px); /* Slight lift */
        }

        /* Optional: Adjustments for very small screens */
        @media (max-width: 480px) {
             .section-title {
                 font-size: 2em;
             }
            .news-card {
                flex-direction: column; /* Stack image and text on very small screens */
                text-align: center;
            }
            .news-thumbnail {
                margin: 0 auto 10px auto; /* Center image and add bottom margin */
            }
             .news-content h3,
             .news-content p {
                 text-align: center; /* Center text when stacked */
             }
             .read-more {
                 align-self: center; /* Center the read more link */
             }
        }

    </style>
</head>
<body>
<h2 class="section-title" style="float:left; margin-left:155px; margin-top:20px;">News</h2>

    <section class="news-section-container">

        <br><br>

        <div class="news-grid">
            <div class="news-card">
                <img src="img/ati/ati.jpg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>SENIOR PROF. P.A. JAYANTHA ASSUMED DUTIES AS THE...</h3>
                    <p>Senior Prof P.A. Jayantha assumed duties as the 9th Vice Chancellor of the University of Ruhuna on...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

            <div class="news-card">
                <img src="img/ati/ati2.jpeg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>CONVOCATION OF THE UNIVERSITY OF RUHUNA WAS...</h3>
                    <p>Convocation of the University of Ruhuna December 12th and 13th, 2024 The much anticipated convoca...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

             <div class="news-card">
                <img src="img/ati/ati.jpg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>CASH AWARDS AND CERTIFICATES FOR THE WINNERS OF...</h3>
                    <p>Cash Awards and Certificates for the Winners of Photographic Competition organisedby the UOR Hig...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

            <div class="news-card">
                <img src="img/ati/ati.jpg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>UNIVERSITY OF RUHUNA ALUMNUS AJMAL ABDUL AZEES SHINES...</h3>
                    <p>University of Ruhuna Alumnus Ajmal Abdul Azees Shines as Finalist at 2024 Walayon International Ed...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

             <div class="news-card">
                <img src="img/ati/ati2.jpeg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>TEAM SSJC SECURES ALL-ISLAND FIRST PLACE IN UNIVERSITY...</h3>
                    <p>Team SSJC Secures All-Island First Place in University Category at SLUSA 2024 for Innovative Enginee...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

             <div class="news-card">
                <img src="img/ati/ati.jpg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>OPENING OF THE NEW BUILDING COMPLEX OF FACULTY...</h3>
                    <p>Building Complex of Faculty Health Sciences, University of Ruhuna The ne...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

            <div class="news-card">
                <img src="img/ati/ati.jpg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>BATTLE OF BROTHERHOOD IN SRI LANKAN CRICKET HISTORY...</h3>
                    <p>The Historic Inauguration of the Battle of Brotherhood in Sri Lankan Cricket History is a landmark ...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

            <div class="news-card">
                <img src="img/ati/ati2.jpeg" alt="News Thumbnail" class="news-thumbnail"> <div class="news-content">
                    <h3>TEAM UOR ENLIGHTENED THE PUBLIC ON BENEFITS OF...</h3>
                    <p>Team UOR Enlightened the Public on Benefits of Chicken Meat and Eggs as a Quality Animal Source Food...</p>
                    <a href="#" class="read-more">Read more</a> </div>
            </div>

            </div>

        <button class="view-all-button">View All</button> </section>

    <script>
        // No specific JavaScript needed for the grid layout shown in the image.
        // If you want interactive features later, add your JS here.
        // Example: Making the "View All" button a link
        /*
        document.querySelector('.view-all-button').addEventListener('click', function() {
            window.location.href = 'your-news-archive-page.html'; // Replace with your news archive URL
        });
        */
    </script>

</body>
</html>