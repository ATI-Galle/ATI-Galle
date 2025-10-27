<?php 
include ('include/config.php');
error_reporting(0);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>HNDIT - Higher National Diploma in Information Technology</title>
    
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/nice-select.css">
    <link rel="stylesheet" href="css/jquery.nice-number.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/default.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/chart.umd.min.js"></script>
    <script>
        "use strict";
        !function() {
          var t = window.driftt = window.drift = window.driftt || [];
          if (!t.init) {
            if (t.invoked) return void (window.console && console.error && console.error("Drift snippet included twice."));
            t.invoked = !0, t.methods = [ "identify", "config", "track", "reset", "debug", "show", "ping", "page", "hide", "off", "on" ], 
            t.factory = function(e) {
              return function() {
                var n = Array.prototype.slice.call(arguments);
                return n.unshift(e), t.push(n), t;
              };
            }, t.methods.forEach(function(e) {
              t[e] = t.factory(e);
            }), t.load = function(t) {
              var e = 3e5, n = Math.ceil(new Date() / e) * e, o = document.createElement("script");
              o.type = "text/javascript", o.async = !0, o.crossorigin = "anonymous", o.src = "https://js.driftt.com/include/" + n + "/" + t + ".js";
              var i = document.getElementsByTagName("script")[0];
              i.parentNode.insertBefore(o, i);
            };
          }
        }();
        drift.SNIPPET_VERSION = '0.3.1';
        drift.load('cszcvrhysx35');
    </script>

    <style>

        /* --- CSS to Hide Scrollbar Arrows and Horizontal Scrollbar --- */

.dropdown-menu {
    /* Existing: Set max height and enable vertical scroll */
    max-height: 350px; 
    overflow-y: auto;
    
    /* NEW: Hide horizontal scrollbar */
    overflow-x: hidden; /* This is the key for hiding horizontal scroll */

    /* For Firefox: Makes scrollbar thin and sets color (optional, but good practice) */
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}

/* For Webkit browsers (Chrome, Safari, Edge, etc.) */

/* Hide the scrollbar arrows (buttons) for vertical scrollbar */
.dropdown-menu::-webkit-scrollbar-button {
    display: none;
}

/* OPTIONAL: Style the rest of the vertical scrollbar for a cleaner look (thumb and track) */
.dropdown-menu::-webkit-scrollbar {
    width: 8px; /* Width of the vertical scrollbar */
}
.dropdown-menu::-webkit-scrollbar-track {
    background: #f1f1f1; 
}
.dropdown-menu::-webkit-scrollbar-thumb {
    background: #888;      
    border-radius: 4px; 
}
.dropdown-menu::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* NEW: Hide horizontal scrollbar specifically for Webkit */
.dropdown-menu::-webkit-scrollbar:horizontal {
    display: none;
}

/* --- Hides the scrollbar but keeps scrolling enabled --- */
.dropdown-menu {
    /* For Webkit browsers (Chrome, Safari, Edge) */
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
    width: 450px;
}

.dropdown-menu::-webkit-scrollbar {
    display: none; /* Chrome, Safari, and Opera */
}
        .live-search-wrapper {
            position: relative;
            width: 100%;
        }
        #live-search-input {
            width: 100%;
            height: 40px;
            padding: 0 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
        }
        #live-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            min-width: 250px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }
        #live-search-results h3 {
            font-size: 14px;
            color: #333;
            background-color: #f7f7f7;
            padding: 8px 12px;
            margin: 0;
            border-bottom: 1px solid #eee;
        }
        #live-search-results .results-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 300px;
            overflow-y: auto;
        }
        #live-search-results .results-list li a {
            background-color: transparent !important;
            color: #555 !important;
            display: block;
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
            text-decoration: none;
            border: none;
            border-bottom: 1px solid #f2f2f2;
            border-radius: 0;
            width: 100%;
        }
        #live-search-results .results-list li a:hover {
            background-color: #fec524 !important;
            color: #fff !important;
        }
        #live-search-results .no-results {
            padding: 15px;
            text-align: center;
            color: #888;
        }

        /* --- NEW NAVIGATION STYLES --- */
        .navbar-nav .nav-item .nav-link {
            position: relative;
            padding-bottom: 8px;
            transition: color 0.3s ease;
            font-weight: 100;
        }

        .navbar-nav .nav-item .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background-color: #fec524; /* Yellow color */
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-item .nav-link:hover::after,
        .navbar-nav .nav-item .nav-link.active::after {
            width: 100%; /* Underline expands on hover or active */
        }

        .navbar-nav .nav-item .nav-link.active,
        .navbar-nav .nav-item .nav-link:hover {
            color:rgb(77, 7, 7); /* Dark blue for active/hover text */
        }
        
        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 5px;
            margin-top: 10px; /* Adds a little space between link and menu */
        }

        .dropdown-item {
            padding: 10px 20px;
            color: #333 !important;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #fec524 !important; /* Yellow background on hover */
            color: #fff !important; /* White text on hover */
        }
        
        /* --- CSS For Floating Navigation Button --- */
        .nav-item-btn {
            margin-left: 25px; /* Adds space between the last link and the button */
            align-self: center; /* Vertically aligns the button in the middle of the navbar */
        }

    </style>
</head>

<body>
    <header id="header-part">
        <div class="header-top d-none d-lg-block">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="header-contact text-lg-left text-center">
                            <ul>
                                <?php 
                                $sql=mysqli_query($con,"select * from websitedetails WHERE Website_ID='1' ");
                                while($row=mysqli_fetch_array($sql)) {
                                ?>
                                <li><img src="img/all-icon/map.png" alt="icon"><span><?php echo htmlentities($row['Contact_Information']);?></span></li>
                                <li><img src="img/all-icon/email.png" alt="icon"><span>info@atigalle.x10.bz</span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6"></div>
                </div>
            </div>
        </div>

        <div class="header-logo-support pt-10 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="logo">
                            <a href="index.php">
                                <img src="img/logo/logo2.png" alt="Logo" height="70px">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="support-button float-right d-none d-md-block">
                            <div class="support float-left">
                                <div class="icon"></div>
                                <div class="cont">
                                    <p>Need Help? call us free</p>
                                    <span>091 224 617 9</span>
                                </div>
                            </div>
                            <div class="button float-left">
                                <a href="https://lms.sliate.ac.lk/" class="main-btn">Login to LMS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="navigation pt-10 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-md-10 col-sm-9 col-8">
                        <nav class="navbar navbar-expand-lg pt-10 pb-10">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            
                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="index.php">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="about-us.php">About</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Courses
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="max-height: 400px; overflow-y: auto; font-size: 10px;">
                                            <a class="dropdown-item" href="course.php"  style="height:1px; font-size:10px;" >All Courses</a>
                                            <div class="dropdown-divider"></div>
                                            
                                            <?php
                                            $course_query = mysqli_query($con, "SELECT * FROM course ORDER BY cname ASC");
                                            while ($course_row = mysqli_fetch_array($course_query)) {
                                            ?>
                                                <a class="dropdown-item"  style="height:1px; font-size:10px;"   href="course.php?cid=<?php echo $course_row['cid']; ?>">
                                                    <?php echo htmlentities($course_row['cname']); ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </li>

                                    

            
                                    <li class="nav-item">
                                        <a class="nav-link" href="staff.php">Staff</a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a class="nav-link" href="exam-results.php">Results</a>
                                    </li>
                                  
                                    <li class="nav-item">
                                        <a class="nav-link" href="tenders.php">Tenders</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="research.php"  style="color:30px;"><i class="fas fa-flask"></i> Research</a>
                                    </li>

                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Blog
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown" style="max-height: 400px; width:100px; overflow-y: auto; font-size: 10px;">
                                            
                                            
                                           
                                            <a class="dropdown-item"  style="height:1px; font-size:10px;"   href="all-news.php">News</a>
                                            <a class="dropdown-item"  style="height:1px; font-size:10px;"   href="all-events.php">Events</a>
                                         
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php#contact">Contact Us</a>
                                    </li>
                                    
                                    </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-4">
                        <div class="right-icon text-right">
                            <div class="live-search-wrapper">
                                <input type="text" id="live-search-input" placeholder="Search..." autocomplete="off">
                                <div id="live-search-results"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#live-search-input').on('keyup', function() {
                var query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: 'live-search.php',
                        method: 'POST',
                        data: { query: query },
                        success: function(data) {
                            $('#live-search-results').html(data).show();
                        }
                    });
                } else {
                    $('#live-search-results').hide().html('');
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.live-search-wrapper').length) {
                    $('#live-search-results').hide();
                }
            });
        });
    </script>

    <!--links are not clicble solved by script-->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Your live search javascript is here...
        });
    </script>
</body>
</html>