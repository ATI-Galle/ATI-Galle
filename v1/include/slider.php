
    <!--====== SEARCH BOX PART START ======-->
    
    <div class="search-box">
        <div class="serach-form">
            <div class="closebtn">
                <span></span>
                <span></span>
            </div>
            <form action="#">
                <input type="text" placeholder="Search by keyword">
                <button><i class="fa fa-search"></i></button>
            </form>
        </div> <!-- serach form -->
    </div>
    
    <!--====== SEARCH BOX PART ENDS ======-->
   
    <!--====== SLIDER PART START ======-->
    
    <section id="slider-part" class="slider-active">

    <?php
    $sql = mysqli_query($con, "select * from slider"); // Select all slider items
    while ($row = mysqli_fetch_array($sql)) {
        ?>

        <div class="single-slider bg_cover pt-150" style="background-image: url(admin/<?php echo $row['simg']; ?>); height:550px; margin-bottom:30px;" data-overlay="4">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-9">
                        <div class="slider-cont"  style="top:0px;">


                            <h1 data-animation="bounceInLeft" data-delay="1s"><?php echo $row['stitle']; ?></h1>
                            <p data-animation="bounceInLeft" data-delay="1.1s"><?php echo $row['stext']; ?></p>
                            <ul>
                                <li><a data-animation="fadeInUp" data-delay="1.6s" class="main-btn" href="#">Read More</a></li>
                                <li><a data-animation="fadeInUp" data-delay="1.9s" class="main-btn main-btn-2" href="#">Get Started</a></li>
                            </ul>
                        </div>
                    </div>
                </div> </div> </div> <?php
    } // End of the while loop
    ?>

</section>


    
    <!--====== SLIDER PART ENDS ======-->
   

   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /*
        Add this CSS to your existing style block or in your CSS file.
        This styles the search box to be a fullscreen overlay popup.
        */

        .search-box {
            position: fixed; /* Fix the position */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8); /* Semi-transparent dark background */
            z-index: 9999; /* Ensure it's on top of everything */
            display: flex; /* Use flexbox to center the form */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            visibility: hidden; /* Hide by default */
            opacity: 0; /* Start invisible for fade-in effect */
            transition: opacity 0.3s ease, visibility 0.3s ease; /* Smooth transition */
        }

        .search-box.open {
            visibility: visible;
            opacity: 1;
        }

        .serach-form {
            background-color: #fff; /* White background for the form area */
            padding: 30px;
            border-radius: 8px;
            position: relative; /* Needed for close button positioning */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%; /* Adjust width as needed */
            max-width: 600px; /* Max width for larger screens */
        }

        .closebtn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            overflow: hidden; /* To hide the spans overflowing if not positioned correctly */
        }

        .closebtn span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background-color: #333; /* Color of the cross lines */
            border-radius: 2px;
            left: 0;
            transition: transform 0.3s ease;
        }

        .closebtn span:first-child {
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
        }

        .closebtn span:last-child {
            top: 50%;
            transform: translateY(-50%) rotate(-45deg);
        }

        .serach-form form {
            display: flex; /* Arrange input and button side-by-side */
        }

        .serach-form input[type="text"] {
            flex-grow: 1; /* Allow input to fill space */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px; /* Rounded left corners */
            font-size: 1em;
            outline: none; /* Remove default outline */
        }

        .serach-form input[type="text"]:focus {
             border-color: #007bff; /* Highlight on focus */
        }


        .serach-form button {
            background-color: #007bff; /* Example button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 0 4px 4px 0; /* Rounded right corners */
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .serach-form button:hover {
            background-color: #0056b3; /* Darker on hover */
        }

        .serach-form button i {
             margin-right: 5px; /* Space between icon and text if any */
        }

         /* Responsive adjustments for smaller screens */
         @media (max-width: 500px) {
             .serach-form {
                 padding: 20px; /* Reduce padding */
             }
             .serach-form form {
                 flex-direction: column; /* Stack input and button */
             }
             .serach-form input[type="text"] {
                 border-radius: 4px; /* Full rounded corners when stacked */
                 margin-bottom: 10px; /* Space below input */
             }
              .serach-form button {
                  border-radius: 4px; /* Full rounded corners when stacked */
              }
         }

    </style>
</head>
<body>

    

    <script>
        /*
        Add this JavaScript code towards the end of your body tag,
        before the closing </body>.
        */
        document.addEventListener('DOMContentLoaded', () => {
            const searchBox = document.querySelector('.search-box');
            const closeBtn = document.querySelector('.search-box .closebtn');
            // You need to get a reference to the element that will open the search box
            // For example, if you have a button or icon with class 'search-trigger':
            const searchTrigger = document.querySelector('.search-trigger'); // Replace with your actual trigger selector

            // Function to open the search box
            function openSearchBox() {
                searchBox.classList.add('open');
                 // Optional: Focus on the input field when the box opens
                 searchBox.querySelector('input[type="text"]').focus();
            }

            // Function to close the search box
            function closeSearchBox() {
                searchBox.classList.remove('open');
                 // Optional: Clear the input field when closing
                 // searchBox.querySelector('input[type="text"]').value = '';
            }

            // Event listener to open the search box (attach to your trigger element)
            // Check if the trigger element exists before adding listener
            if (searchTrigger) {
                searchTrigger.addEventListener('click', openSearchBox);
            } else {
                console.warn("Search trigger element not found. Add an element with class 'search-trigger' (or update the JS selector) to open the search box.");
                 // Example: You could automatically open it for testing
                 // openSearchBox();
            }


            // Event listener to close the search box when the close button is clicked
            closeBtn.addEventListener('click', closeSearchBox);

            // Optional: Close the search box when clicking outside the form
            searchBox.addEventListener('click', (e) => {
                // Check if the click target is the search-box background itself, not inside the form
                if (e.target === searchBox) {
                    closeSearchBox();
                }
            });

            // Optional: Close the search box when the Escape key is pressed
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && searchBox.classList.contains('open')) {
                    closeSearchBox();
                }
            });
        });
    </script>

</body>
</html>