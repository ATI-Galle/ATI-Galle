<?php include("include/header.php");?>


   
    <style>
        /* Your provided CSS starts here */
      

        .org-structure-container {
            max-width: 1400px; /* Adjust max width for the whole structure */
            margin: 0 auto; /* Center the whole structure */
            background-color: #fff; /* White background for the structure */
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
            border-radius: 8px;
        }

        .org-layer {
            margin-bottom: 40px; /* Space between layers */
            border-bottom: 1px solid #eee; /* Separator between layers */
            padding-bottom: 30px;
        }

        .org-layer:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }


        .layer-title {
            text-align: center;
            font-size: 1.8em; /* Slightly smaller than section title */
            color: #333;
            margin-bottom: 20px;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #673ab7; /* Accent color line */
            display: inline-block; /* Shrink border to content width */
            padding-right: 20px;
            padding-left: 20px;
            margin-left: auto;
            margin-right: auto;
            display: block; /* Make it a block element again after margin:auto */
            width: fit-content; /* Or a specific width */
        }


        .staff-section-container {
            position: relative;
            width: 100%;
            margin: 0 auto; /* Center within the layer */
            overflow: hidden; /* Still hide initial overflow for arrows positioning */
            padding: 0;
            box-sizing: border-box;
        }

         /* This wrapper is primarily for the overflow: hidden needed for the scrollbar hiding trick */
         /* It's also where we'll control showing all cards */
        .cards-wrapper {
             overflow: hidden; /* Crucial for horizontal scrolling */
             padding: 0 20px; /* Add horizontal padding to match arrow spacing */
             box-sizing: border-box;
        }


        .staff-cards-container {
            display: flex;
            scroll-behavior: smooth; /* Smooth scrolling effect */
            overflow-x: auto; /* Enable horizontal scrolling by default */
            scrollbar-width: none; /* Hide scrollbar for Firefox */
            -ms-overflow-style: none;  /* Hide scrollbar for IE and Edge */
             padding-bottom: 15px; /* Add padding in case of scrollbar space issues */
        }

        .staff-cards-container::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
        }

        /* NEW: Style for when the container is expanded */
        .staff-cards-container.expanded {
            overflow-x: visible; /* Show all content */
            flex-wrap: wrap; /* Allow items to wrap to the next line */
            justify-content: center; /* Center cards in the wrapped layout */
            /* Remove scrollbar hiding - overflow: visible overrides it */
             padding-bottom: 0; /* No extra padding needed */
        }

         /* Reset margin for the last card when expanded */
         .staff-cards-container.expanded .staff-card:last-child {
            margin-right: 20px; /* Restore margin to match others */
         }
          /* Fix for the last card margin when wrap happens */
         .staff-cards-container.expanded .staff-card {
             margin-bottom: 20px; /* Add space between rows */
         }
         .staff-cards-container.expanded .staff-card:nth-last-child(-n + 5) { /* Adjust 5 based on cards per row in your typical grid */
             margin-bottom: 0; /* Remove bottom margin for the last row */
         }
         /* A simpler approach to spacing in expanded view */
         .staff-cards-container.expanded .staff-card {
             margin-right: 20px;
             margin-bottom: 20px;
         }
          .staff-cards-container.expanded .staff-card:nth-child(5n) { /* Adjust 5n if number of cards per row is different */
               /* margin-right: 0; This might make the last card of a full row stick to the edge */
          }


        .staff-card {
            flex: 0 0 auto; /* Prevent shrinking, allow basis based on content */
            width: 280px; /* Adjust card width as needed */
            height: 350px; /* Adjust card height as needed */
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
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transition for hover effect */
        }

        .staff-card:hover {
             transform: translateY(-5px); /* Lift card slightly on hover */
             box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* More pronounced shadow on hover */
        }


        .staff-card:last-child {
            margin-right: 0; /* No margin on the last card in the scroll view */
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

        /* You could add classes here if you want different colors for different layers/cards */
        .layer-1 .card-title-bar { background-color: #1a237e; } /* Darker blue for Director */
        .layer-2 .card-title-bar { background-color: #004d40; } /* Teal for HODs */
        .layer-3 .card-title-bar { background-color: #e65100; } /* Orange for Senior Staff */
        .layer-4 .card-title-bar { background-color: #33691e; } /* Green for Faculty/Staff */


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
            border-radius: 5px;
        }

        .more-button:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* Navigation Arrows (adjust positioning relative to the staff-section-container within each layer) */
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
            width: 40px; /* Fixed width for circular shape */
            height: 40px; /* Fixed height for circular shape */
            display: flex; /* Use flex to center arrow icon */
            justify-content: center;
            align-items: center;
        }

        .nav-arrow:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }


        .left-arrow {
            left: 10px; /* Position relative to the layer container */
        }

        .right-arrow {
            right: 10px; /* Position relative to the layer container */
        }

        /* CSS to hide arrows when needed */
        .nav-arrow.hidden {
            opacity: 0; /* Fade out the arrow */
            pointer-events: none; /* Prevent clicking when hidden */
        }

         .see-more-link {
            display: block; /* Ensure it's a block for text-align */
            text-align: right;
            margin-top: 15px;
            margin-right: 20px; /* Align with card padding */
            font-size: 1.1em;
            color: #673ab7; /* Match accent color */
            text-decoration: none;
            cursor: pointer; /* Indicate it's clickable */
            transition: color 0.3s ease;
        }

        .see-more-link:hover {
            text-decoration: underline;
             color: #512da8; /* Slightly darker hover color */
        }


        /* Basic Responsiveness */
        @media (max-width: 768px) {
             body {
                 padding: 10px;
             }
            .org-structure-container {
                padding: 10px;
            }

            .org-layer {
                margin-bottom: 30px;
                padding-bottom: 20px;
            }

             .layer-title {
                 font-size: 1.5em;
                 margin-bottom: 15px;
                 padding-right: 10px;
                 padding-left: 10px;
             }


            .staff-card {
                width: 220px; /* Adjust card width */
                height: 300px; /* Adjust card height */
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
                 width: 35px;
                 height: 35px;
            }

             .left-arrow {
                 left: 5px;
             }

             .right-arrow {
                 right: 5px;
             }

             .see-more-link {
                 margin-right: 10px;
                 font-size: 1em;
             }

              /* Responsive adjustments for expanded view */
             .staff-cards-container.expanded .staff-card {
                 margin-right: 15px; /* Match non-expanded margin */
                 margin-bottom: 15px; /* Add space between rows */
             }
             /* You might need to adjust nth-child rules for different screen sizes */

        }
        /* Your provided CSS ends here */
    </style>
</head>
<body>




    <div class="org-structure-container">

        <section class="org-layer layer-1"><br><br>
            <h2 class="layer-title">Director Level / Executive Leadership</h2>
            <div class="staff-section-container">
                 <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-1-cards', -1)" aria-label="Scroll Left">&#9664;</button> <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-1-cards', 1)" aria-label="Scroll Right">&#9654;</button> <div class="cards-wrapper">
                    <div class="staff-cards-container" id="layer-1-cards">
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Director');">
                             <div class="card-title-bar">Director</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Dr. Jane Doe</div>
                                    <div class="staff-qualifications">PhD, MBA</div>
                                    <div class="staff-position">Director</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Registrar');">
                             <div class="card-title-bar">Registrar</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. John Smith</div>
                                    <div class="staff-qualifications">M.Phil</div>
                                    <div class="staff-position">Registrar</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Asst+Registrar');">
                             <div class="card-title-bar">Asst. Registrar</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Sarah Lee</div>
                                    <div class="staff-qualifications">MA</div>
                                    <div class="staff-position">Assistant Registrar</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Accountant');">
                             <div class="card-title-bar">Accountant</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. David Green</div>
                                    <div class="staff-qualifications">ACA, BSc</div>
                                    <div class="staff-position">Chief Accountant</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Legal');">
                             <div class="card-title-bar">Legal Officer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Emily Parker</div>
                                    <div class="staff-qualifications">LLM</div>
                                    <div class="staff-position">Legal Officer</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/1a237e/ffffff?text=Auditor');">
                             <div class="card-title-bar">Internal Auditor</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. Ken Adams</div>
                                    <div class="staff-qualifications">FCA</div>
                                    <div class="staff-position">Internal Auditor</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                 <a href="#" class="see-more-link" data-target="layer-1-cards">See More Director Level Staff &gt;</a>
            </div>
        </section>

        <section class="org-layer layer-2">
            <h2 class="layer-title">Head of Departments (HODs)</h2>
             <div class="staff-section-container">
                 <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-2-cards', -1)" aria-label="Scroll Left">&#9664;</button>
                <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-2-cards', 1)" aria-label="Scroll Right">&#9654;</button>
                 <div class="cards-wrapper">
                    <div class="staff-cards-container" id="layer-2-cards">
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+IT');">
                             <div class="card-title-bar">HOD - IT</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. TW</div>
                                    <div class="staff-qualifications">MSc, BE</div>
                                    <div class="staff-position">Head of IT Department</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+Civil');">
                             <div class="card-title-bar">HOD - Civil</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. Chamith</div>
                                    <div class="staff-qualifications">Eng, PhD</div>
                                    <div class="staff-position">Head of Civil Engineering</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+English');">
                             <div class="card-title-bar">HOD - English</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. Anima</div>
                                    <div class="staff-qualifications">MA, BA Hons</div>
                                    <div class="staff-position">Head of English Department</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+Business');">
                             <div class="card-title-bar">HOD - Business</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Olivia Brown</div>
                                    <div class="staff-qualifications">MBA, BCom</div>
                                    <div class="staff-position">Head of Business Admin</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+Science');">
                             <div class="card-title-bar">HOD - Science</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Dr. Robert Lee</div>
                                    <div class="staff-qualifications">PhD</div>
                                    <div class="staff-position">Head of Science Dept</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                          <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/004d40/ffffff?text=HOD+Arts');">
                             <div class="card-title-bar">HOD - Arts</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Sophia Green</div>
                                    <div class="staff-qualifications">MA</div>
                                    <div class="staff-position">Head of Arts Dept</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                 <a href="#" class="see-more-link" data-target="layer-2-cards">See More HODs &gt;</a>
            </div>
        </section>

        <section class="org-layer layer-3">
            <h2 class="layer-title">Senior Faculty / Managers</h2>
             <div class="staff-section-container">
                  <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-3-cards', -1)" aria-label="Scroll Left">&#9664;</button>
                <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-3-cards', 1)" aria-label="Scroll Right">&#9654;</button>
                 <div class="cards-wrapper">
                    <div class="staff-cards-container" id="layer-3-cards">
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Professor');">
                             <div class="card-title-bar">Professor</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Prof. Emily Davis</div>
                                    <div class="staff-qualifications">PhD</div>
                                    <div class="staff-position">Professor of Physics</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Assoc+Prof');">
                             <div class="card-title-bar">Assoc. Professor</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Dr. James Wilson</div>
                                    <div class="staff-qualifications">PhD</div>
                                    <div class="staff-position">Associate Professor</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Deputy+Reg');">
                             <div class="card-title-bar">Deputy Registrar</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Linda Adams</div>
                                    <div class="staff-qualifications">MPA</div>
                                    <div class="staff-position">Deputy Registrar</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>

                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Program+Coord');">
                             <div class="card-title-bar">Program Coord.</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. Thomas Clark</div>
                                    <div class="staff-qualifications">MSc</div>
                                    <div class="staff-position">Program Coordinator</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Chief+Lib');">
                             <div class="card-title-bar">Chief Librarian</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Ms. Nancy King</div>
                                    <div class="staff-qualifications">MLIS</div>
                                    <div class="staff-position">Chief Librarian</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/e65100/ffffff?text=Admissions+Mgr');">
                             <div class="card-title-bar">Admissions Mgr</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Mr. George Miller</div>
                                    <div class="staff-qualifications">BA</div>
                                    <div class="staff-position">Admissions Manager</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                 <a href="#" class="see-more-link" data-target="layer-3-cards">See More Senior Staff &gt;</a>
            </div>
        </section>

        <section class="org-layer layer-4">
            <h2 class="layer-title">Faculty / Academic & Administrative Staff</h2>
             <div class="staff-section-container">
                  <button class="nav-arrow left-arrow" onclick="scrollLayer('layer-4-cards', -1)" aria-label="Scroll Left">&#9664;</button>
                <button class="nav-arrow right-arrow" onclick="scrollLayer('layer-4-cards', 1)" aria-label="Scroll Right">&#9654;</button>
                 <div class="cards-wrapper">
                    <div class="staff-cards-container" id="layer-4-cards">
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Lecturer');">
                             <div class="card-title-bar">Lecturer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Michael Blue</div>
                                    <div class="staff-qualifications">MSc, BE</div>
                                    <div class="staff-position">Lecturer, IT Dept</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Admin+Officer');">
                             <div class="card-title-bar">Admin Officer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Emily White</div>
                                    <div class="staff-qualifications">Dip. IT</div>
                                    <div class="staff-position">Administrative Officer</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Lab+Technician');">
                             <div class="card-title-bar">Lab Technician</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Chris Green</div>
                                    <div class="staff-qualifications">NVQ Level 5</div>
                                    <div class="staff-position">Lab Technician</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Asst+Lecturer');">
                             <div class="card-title-bar">Asst. Lecturer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Jessica Black</div>
                                    <div class="staff-qualifications">BSc Hons</div>
                                    <div class="staff-position">Assistant Lecturer</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>


                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Accounts+Asst');">
                             <div class="card-title-bar">Accounts Asst.</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Tom Adams</div>
                                    <div class="staff-qualifications">HNDA</div>
                                    <div class="staff-position">Accounts Assistant</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=IT+Support');">
                             <div class="card-title-bar">IT Support</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Kevin White</div>
                                    <div class="staff-qualifications">Dip. IT</div>
                                    <div class="staff-position">IT Support Officer</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                         <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Lecturer');">
                             <div class="card-title-bar">Lecturer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Anna Grey</div>
                                    <div class="staff-qualifications">PhD</div>
                                    <div class="staff-position">Lecturer, English Dept</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>
                        <div class="staff-card" style="background-image: url('https://via.placeholder.com/280x350/33691e/ffffff?text=Admin+Officer');">
                             <div class="card-title-bar">Admin Officer</div>
                            <div class="card-overlay">
                                <div class="card-content">
                                    <div class="staff-name">Peter Black</div>
                                    <div class="staff-qualifications">MBA</div>
                                    <div class="staff-position">Administrative Officer</div>
                                    <button class="more-button">MORE</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                 <a href="#" class="see-more-link" data-target="layer-4-cards">See More Faculty & Staff &gt;</a>
            </div>
        </section>

         </div> <script>
         // Function to scroll for a specific layer
        function scrollLayer(containerId, direction) {
            const cardsContainer = document.getElementById(containerId);
            if (!cardsContainer || cardsContainer.classList.contains('expanded')) return; // Don't scroll if expanded

            const card = cardsContainer.querySelector('.staff-card');
            if (!card) return;

            const cardWidth = card.getBoundingClientRect().width;
            const cardMarginRight = parseInt(getComputedStyle(card).marginRight);
            const scrollDistance = (cardWidth + cardMarginRight) * 2; // Scroll by about two cards

            cardsContainer.scrollBy({
                left: direction * scrollDistance,
                behavior: 'smooth'
            });
        }

        // Function to toggle arrow visibility for a given container
        function toggleLayerArrows(containerId) {
            const cardsContainer = document.getElementById(containerId);
            // Only toggle arrows if the container is NOT expanded
            if (!cardsContainer || cardsContainer.classList.contains('expanded')) {
                 const leftArrow = cardsContainer ? cardsContainer.closest('.staff-section-container').querySelector('.left-arrow') : null;
                 const rightArrow = cardsContainer ? cardsContainer.closest('.staff-section-container').querySelector('.right-arrow') : null;
                 if(leftArrow) leftArrow.classList.add('hidden');
                 if(rightArrow) rightArrow.classList.add('hidden');
                 return; // Exit if expanded or container not found
             }


            const leftArrow = cardsContainer.closest('.staff-section-container').querySelector('.left-arrow');
            const rightArrow = cardsContainer.closest('.staff-section-container').querySelector('.right-arrow');

            if (!leftArrow || !rightArrow) return; // Ensure arrows exist

            // Add a small tolerance (e.g., 1px) for potential subpixel issues
            const isAtStart = cardsContainer.scrollLeft <= 1;
            const isAtEnd = cardsContainer.scrollLeft + cardsContainer.clientWidth >= cardsContainer.scrollWidth - 1;

            leftArrow.classList.toggle('hidden', isAtStart);
            rightArrow.classList.toggle('hidden', isAtEnd);
        }

        // Handle "See More" click
        document.addEventListener('DOMContentLoaded', () => {
            const seeMoreLinks = document.querySelectorAll('.see-more-link');

            seeMoreLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault(); // Prevent default link behavior

                    const targetContainerId = link.dataset.target; // Get the target container ID from data attribute
                    const cardsContainer = document.getElementById(targetContainerId);

                    if (cardsContainer) {
                        cardsContainer.classList.add('expanded'); // Add the expanded class
                        link.style.display = 'none'; // Hide the "See More" link

                        // Hide the arrows for this layer
                        const parentSectionContainer = link.closest('.staff-section-container');
                        if (parentSectionContainer) {
                            const arrows = parentSectionContainer.querySelectorAll('.nav-arrow');
                            arrows.forEach(arrow => {
                                arrow.classList.add('hidden'); // Hide the arrows
                            });
                        }

                         // Re-evaluate arrow visibility (will hide them because it's expanded)
                        toggleLayerArrows(targetContainerId);
                    }
                });
            });

             // --- Existing JS for initial setup and scroll handling ---
             const layerContainers = document.querySelectorAll('.staff-cards-container');

            layerContainers.forEach(container => {
                const containerId = container.id;

                // Initial check
                toggleLayerArrows(containerId);

                // Add scroll listener
                container.addEventListener('scroll', () => toggleLayerArrows(containerId));

                // Add resize listener (Debounce this in a real application for performance)
                 window.addEventListener('resize', () => toggleLayerArrows(containerId));
            });
             // --- End existing JS ---

        });
    </script>
</body>


<?php include("include/footer.php");?>

    </html>
