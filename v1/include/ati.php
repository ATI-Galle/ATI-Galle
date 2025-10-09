<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Locations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif; /* Or your preferred font */
            background-color: #f8f8f8; /* Light background color */
        }

        .campus-section-container {
            padding: 40px 20px; /* Add some padding around the section */
            max-width: 1200px; /* Limit max width */
            margin: 0 auto; /* Center the container */
        }

        .campus-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive grid columns */
            gap: 20px; /* Gap between grid items */
        }

        .campus-card {
            background-color: #fff; /* White background for the card */
            border-radius: 10px; /* Rounded corners */
            overflow: hidden; /* Hide parts of the background image or content that exceed bounds */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            display: flex;
            flex-direction: column; /* Stack content vertically */
            position: relative; /* Needed for absolute positioning of footer */
            height: 350px; /* Fixed height for cards - adjust as needed */
            background-size: cover;
            background-position: center;
        }

        /* Add specific background images - replace with your actual image paths */
        .campus-card.colombo { background-image: url('colombo-campus.jpg'); }
        .campus-card.rajagiriya { background-image: url('rajagiriya-campus.jpg'); }
        .campus-card.kandy { background-image: url('kandy-campus.jpg'); }
        .campus-card.kurunegala { background-image: url('kurunegala-campus.jpg'); }
        .campus-card.galle { background-image: url('galle-campus.jpg'); }
        .campus-card.matara { background-image: url('matara-campus.jpg'); }
        .campus-card.kirulapone { background-image: url('kirulapone-nic.jpg'); }
        .campus-card.kandy-kic { background-image: url('kandy-kic.jpg'); }


        .card-footer {
            position: absolute; /* Position at the bottom */
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.6)); /* Semi-transparent gradient */
            padding: 15px;
            color: #333; /* Dark text color */
        }

        .card-footer h2 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.2em;
        }

        .card-footer p {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 0.9em;
            line-height: 1.4;
        }

        .social-icons {
            display: flex;
            gap: 10px; /* Space between icons */
        }

        .social-icons a {
            color: #333; /* Icon color */
            font-size: 1.1em;
            text-decoration: none; /* Remove underline from links */
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #007bff; /* Change color on hover - example blue */
        }

        /* Optional: Media query for smaller screens if needed */
        @media (max-width: 600px) {
            .campus-grid {
                grid-template-columns: 1fr; /* Stack cards on very small screens */
            }

            .campus-card {
                height: 300px; /* Adjust height for smaller screens */
            }

            .card-footer h2 {
                font-size: 1.1em;
            }

            .card-footer p {
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>

    <section class="campus-section-container">
        <div class="campus-grid">
            <div class="campus-card colombo">
                <div class="card-footer">
                    <h2>Colombo Campus</h2>
                    <p>Powering Great Minds</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a> </div>
                </div>
            </div>

            <div class="campus-card rajagiriya">
                 <div class="card-footer">
                    <h2>Rajagiriya Campus</h2>
                    <p>Powering Great Minds</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card kandy">
                 <div class="card-footer">
                    <h2>Kandy Campus</h2>
                    <p>Enterprising Hearts</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card kurunegala">
                 <div class="card-footer">
                    <h2>Kurunegala Campus</h2>
                    <p>Home of Inspired Solutions</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card galle">
                 <div class="card-footer">
                    <h2>Galle Campus</h2>
                    <p>Cultivating the Future of Southerners</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card matara">
                 <div class="card-footer">
                    <h2>Matara Campus</h2>
                    <p>Cultivating the Future of Southerners</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card kirulapone">
                 <div class="card-footer">
                    <h2>Kirulapone NIC</h2>
                    <p>Coloring Passionate Lives</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            <div class="campus-card kandy-kic">
                 <div class="card-footer">
                    <h2>Kandy KIC</h2>
                    <p>Coloring Passionate Lives</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>

            </div>
    </section>

    <script>
        // You can add JavaScript here for future interactions if needed,
        // but it's not necessary for the basic grid layout and styling shown.
        // Example: Making cards clickable
        // document.querySelectorAll('.campus-card').forEach(card => {
        //     card.addEventListener('click', () => {
        //         alert('Card clicked!'); // Replace with navigation logic
        //     });
        // });
    </script>

</body>
</html>