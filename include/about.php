<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Section</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            line-height: 1.6;
            color: #333;
        }

        .university-section-container {
            display: flex; /* Use Flexbox for the two-column layout */
            flex-direction: column; /* Stack columns by default */
            max-width: 1200px; /* Limit max width */
            margin: 40px auto; /* Center the section */
            border: 1px solid #eee; /* Add a subtle border around the section */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
            background-color: #fff; /* White background */
            position: relative; /* Needed for positioning absolute elements */
            overflow: hidden; /* Hide anything overflowing */
        }

        /* Faded background image for the left section */
        .university-section-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 60%; /* Cover the left side */
            height: 100%;
            background-image: url('faded-background.png'); /* Replace with your faded background image */
            background-size: cover;
            background-position: center;
            opacity: 0.1; /* Adjust opacity as needed */
            z-index: 0; /* Ensure it's behind content */
        }


        .content-wrapper {
            display: flex; /* Use Flexbox for the main content area */
            flex-direction: column; /* Stack content inside wrapper by default */
            width: 100%;
            position: relative; /* Ensure content is above the pseudo-element background */
            z-index: 1;
        }


        .university-info {
            flex: 1; /* Allow this column to grow */
            padding: 30px;
            box-sizing: border-box;
        }

        .university-info h1 {
            color: #000; /* Dark color for the title */
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 2.5em; /* Adjust font size */
        }

        .university-image {
            width: 100%; /* Make image responsive */
            max-width: 400px; /* Limit max width of the image */
            height: auto;
            display: block; /* Remove extra space below image */
            margin: 0 auto 20px auto; /* Center image and add bottom margin */
            border-radius: 5px; /* Optional: slight rounded corners */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: image shadow */
        }

        .university-info p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .vice-chancellor-info {
            flex: 0 0 300px; /* Fixed width for the VC column on larger screens */
            background-color: #1a237e; /* Dark blue background color */
            color: white;
            padding: 30px;
            box-sizing: border-box;
            text-align: center; /* Center align content */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center items horizontally */
            justify-content: center; /* Center items vertically */
        }

        .vice-chancellor-info h2 {
            color: white;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .vc-image {
            width: 150px; /* Adjust image size */
            height: 150px; /* Adjust image size */
            border-radius: 50%; /* Circular image */
            object-fit: cover; /* Ensure image covers the area */
            margin-bottom: 15px;
            border: 3px solid white; /* Optional: white border around image */
        }

        .vc-name {
            font-size: 1.1em;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .vc-title {
            font-size: 0.9em;
            opacity: 0.8; /* Slightly less prominent */
        }

        /* Social Media Icons */
        .social-sidebar {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10; /* Ensure icons are on top */
        }

        .social-icon {
            display: block;
            width: 40px;
            height: 40px;
            margin-bottom: 5px;
            color: white;
            text-align: center;
            line-height: 40px; /* Vertically center icon */
            font-size: 1.2em;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .social-icon.facebook { background-color: #3b5998; }
        .social-icon.twitter { background-color: #00acee; }
        .social-icon.youtube { background-color: #ff0000; }


        /* Talk to VC button */
        .talk-to-vc {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 80px; /* Size of the circle */
            height: 80px; /* Size of the circle */
            background-color: #673ab7; /* Purple background color */
            border-radius: 50%; /* Make it circular */
            display: flex;
            flex-direction: column; /* Stack icon and text */
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            font-size: 0.8em;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 10; /* Ensure it's on top */
            text-decoration: none; /* Remove underline if it's a link */
            transition: transform 0.3s ease;
        }

        .talk-to-vc:hover {
            transform: scale(1.05); /* Slight zoom on hover */
        }

        .talk-to-vc i {
            font-size: 1.8em; /* Icon size */
            margin-bottom: 5px;
        }


        /* Responsive layout */
        @media (min-width: 768px) {
            .content-wrapper {
                flex-direction: row; /* Arrange columns side-by-side on larger screens */
            }

            .university-info {
                 padding: 40px; /* Increase padding */
            }

             .vice-chancellor-info {
                /* Flex: 0 0 300px is already set */
                padding: 40px;
            }

             .university-section-container::before {
                /* Maintain width covering the left side */
                width: 60%;
             }
        }

         @media (max-width: 767px) {
            .university-section-container {
                margin: 20px auto; /* Adjust margin on small screens */
            }
             .university-section-container::before {
                /* Make the background cover the whole container on small screens */
                width: 100%;
             }

            .university-info,
            .vice-chancellor-info {
                padding: 20px; /* Reduce padding on small screens */
                flex: 1 1 auto; /* Allow both columns to grow and shrink */
                width: 100%; /* Ensure columns take full width when stacked */
            }

             .vice-chancellor-info {
                 /* Adjust vertical spacing when stacked */
                 padding-top: 30px;
                 padding-bottom: 30px;
             }

             .social-sidebar {
                 /* Adjust position for smaller screens if needed, or hide */
                 top: auto;
                 bottom: 110px; /* Position above the Talk to VC button */
                 right: 10px;
                 transform: none;
                 flex-direction: row; /* Arrange social icons horizontally */
                 background-color: rgba(0, 0, 0, 0.5); /* Add background for visibility */
                 padding: 5px;
                 border-radius: 5px;
             }
             .social-sidebar .social-icon {
                 margin: 0 5px; /* Space out horizontal icons */
                 width: 30px;
                 height: 30px;
                 line-height: 30px;
                 font-size: 1em;
                 background-color: transparent !important; /* Use icon color instead of background */
             }
              .social-sidebar .social-icon.facebook { color: #3b5998; }
              .social-sidebar .social-icon.twitter { color: #00acee; }
              .social-sidebar .social-icon.youtube { color: #ff0000; }


            .talk-to-vc {
                width: 60px;
                height: 60px;
                bottom: 10px;
                right: 10px;
                font-size: 0.7em;
            }
             .talk-to-vc i {
                 font-size: 1.5em;
             }
        }
    </style>
</head>
<body>

    <section class="university-section-container">
        <div class="content-wrapper">
            <div class="university-info">
            <h2 class="section-title">About Us</h2> 
            <img src="img/ati/ati.jpg" alt="University Building" class="university-image"> <p>

            Located in Labuduwa, Galle, the Advanced Technological Institute (ATI) is part of the Sri Lanka Institute of Advanced Technological Education (SLIATE). SLIATE is a national body offering Higher National Diplomas through a network of provincial ATIs like Labuduwa, aimed at expanding access to advanced technical education across Sri Lanka.
                
    </p>
    
    <p>

    ATI Labuduwa provides a variety of Higher National Diploma (HND) programs. These cover key areas such as Accountancy, Agriculture, Business Studies, English, Engineering, Information Technology, Management, Quantity Surveying, and Tourism. The institute focuses on delivering practical, industry-relevant education.

                </p>
            </div>

            <div class="vice-chancellor-info">
                <h2>Director</h2>
                <img src="img/staff/Dire.jpeg" alt="Director Image" class="vc-image"> <div class="vc-name">Mr. Thamara Waidyarathne</div>
                <div class="vc-title">MSc (IT), BSc (IT)</div>
            </div>
        </div>

     

     

    </section>

    <script>
        // No specific JavaScript is required for this layout.
        // You could add JS here for smooth scrolling if the "Talk to VC" button
        // links to an anchor on the page, or for other interactive elements.
        // Example of smooth scrolling (basic):
        /*
        document.querySelector('.talk-to-vc').addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
        */
    </script>

</body>
</html>