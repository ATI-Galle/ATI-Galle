
<?php 

include ('include/config.php');

?>


<!doctype html>
<html lang="en">
<head>
   
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!--====== Title ======-->
    <title>HNDIT - Higher National Diploma in Information Technology</title>
    
    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="images/favicon.png" type="image/png">

    <!--====== Slick css ======-->
    <link rel="stylesheet" href="css/slick.css">

    <!--====== Animate css ======-->
    <link rel="stylesheet" href="css/animate.css">
    
    <!--====== Nice Select css ======-->
    <link rel="stylesheet" href="css/nice-select.css">
    
    <!--====== Nice Number css ======-->
    <link rel="stylesheet" href="css/jquery.nice-number.min.css">

    <!--====== Magnific Popup css ======-->
    <link rel="stylesheet" href="css/magnific-popup.css">

    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!--====== Fontawesome css ======-->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <!--====== Default css ======-->
    <link rel="stylesheet" href="css/default.css">
    
    <!--====== Style css ======-->
    <link rel="stylesheet" href="css/style.css">
    
    <!--====== Responsive css ======-->
    <link rel="stylesheet" href="css/responsive.css">
  

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
  
</head>



 <!--====== HEADER PART START ======-->
    
 <header id="header-part">
       
       <div class="header-top d-none d-lg-block">
           <div class="container">
               <div class="row">
                   <div class="col-lg-6">
                       <div class="header-contact text-lg-left text-center">
                           <ul>
  <?php 
  $sql=mysqli_query($con,"select * from websitedetails WHERE Website_ID='1' ");
  while($row=mysqli_fetch_array($sql))

  {
    ?>
  
                               <li><img src="img/all-icon/map.png" alt="icon"><span><?php echo htmlentities($row['Contact_Information']);?></span></li>
                               <li><img src="img/all-icon/email.png" alt="icon"><span>info@myproject.x10.bz</span></li>
                               <?php }?>   </ul>
                       </div>
                   </div>
                   <div class="col-lg-6">
                       <div class="header-opening-time text-lg-right text-center">
                           <p>Opening Hours : Monday to Saturay - 8 Am to 5 Pm</p>
                       </div>
                   </div>
               </div> <!-- row -->
           </div> <!-- container -->
       </div> <!-- header top -->
       
       <div class="header-logo-support pt-30 pb-30">
           <div class="container">
               <div class="row">
                   <div class="col-lg-4 col-md-4">
                       <div class="logo">
                           <a href="index.php">
                               <img src="img/logo/logo2.png" alt="Logo">
                           </a>
                       </div>
                   </div>
                   <div class="col-lg-8 col-md-8">
                       <div class="support-button float-right d-none d-md-block">
                           <div class="support float-left">
                               <div class="icon">
                               </div>
                               <div class="cont">
                                   <p>Need Help? call us free</p>
                                   <span>075 879 41 42 </span>
                               </div>
                           </div>
                           <div class="button float-left">
                               <a href="../LMS" class="main-btn">Loging to LMS</a>
                           </div>
                       </div>
                   </div>
               </div> <!-- row -->
           </div> <!-- container -->
       </div> <!-- header logo support -->
       
       <div class="navigation">
           <div class="container">
               <div class="row">
                   <div class="col-lg-10 col-md-10 col-sm-9 col-8">
                       <nav class="navbar navbar-expand-lg">
                           <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                               <span class="icon-bar"></span>
                               <span class="icon-bar"></span>
                               <span class="icon-bar"></span>
                           </button>
                           

                           <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                               <ul class="navbar-nav mr-auto">
                                   <li class="nav-item">
                                       <a class="active" href="index.php">Home</a>
                                     
                                   </li>

                                   <li class="nav-item">
                                       <a class="" href="#about">About</a>
                                     
                                   </li>


                                   <li class="nav-item">
                                       <a class="" href="#teachers">Teachers </a>
                                     
                                   </li>
                                   
                                   <li class="nav-item">
                                       <a class="" href="#syllabus">Syllabus </a>
                                     
                                   </li>
                                   
                                   <li class="nav-item">
                                       <a class="" href="#news">News </a>
                                     
                                   </li>
                                   
                                   <li class="nav-item">
                                       <a class="" href="#contact">Contact </a>
                                     
                                   </li>
                               </ul>
                           </div>
                       </nav> <!-- nav -->
                   </div>
                   <div class="col-lg-2 col-md-2 col-sm-3 col-4">
                       <div class="right-icon text-right">
                          
                       </div> <!-- right icon -->
                   </div>
               </div> <!-- row -->
           </div> <!-- container -->
       </div>
       
   </header>
   
   <!--====== HEADER PART ENDS ======-->