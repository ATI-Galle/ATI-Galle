<?php include_once("include/header.php");?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HNDA Diploma Information</title>
    <style>
      

        .container1 {
            max-width: 960px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: colorrgba(108, 108, 108, 0.35);;
        }

      
        header h1 {
            margin: 0;
            color: #00bcd4; /* Teal */
            font-size: 2em;
        }

        .tab-navigation {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .tab-button {
            background-color: #eee;
            color: #333;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
            transition: background-color 0.3s ease; /* Added transition */
        }

        .tab-button:hover {
            background-color: #ddd; /* Hover effect */
        }

        .tab-button.active {
            background-color: #fff;
            border-bottom: none;
            font-weight: bold;
            color: #00bcd4; /* Active color */
        }

        .content-section {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            display: none; /* Initially hide all sections */
        }

        .content-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        /* --- Basic Layout --- */
        .main-layout {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Adjust proportions as needed */
            gap: 20px;
        }

        .main-content {
            padding-right: 20px;
        }

        .sidebar {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
        }

        .sidebar h2 {
            color: #00bcd4;
            margin-top: 0;
            font-size: 1.4em;
            border-bottom: 2px solid #b2ebf2;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin-bottom: 8px;
        }

        .sidebar a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 5px 0;
        }

        .search-box {
            margin-bottom: 20px;
        }

        .search-box input[type="text"] {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .latest-news .news-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dotted #ccc;
        }

        .latest-news .news-item a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .latest-news .news-item p {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .courses .courses-list li a {
            color: #007bff;
            font-weight: normal;
        }

        /* --- Admission Criteria --- */
        .admission-criteria h3 {
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .admission-criteria p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .admission-criteria .and-condition {
            font-weight: bold;
            color: #007bff; /* Example color */
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .admission-criteria .or-condition {
            font-weight: bold;
            color: #dc3545; /* Example color */
            margin-top: 5px;
            margin-bottom: 5px;
            text-align: center;
        }

        .admission-criteria ol {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .admission-criteria ol li {
            margin-bottom: 8px;
        }

        /* --- Subjects and Credits --- */
        .subjects-and-credits-content h3 {
            color: #333;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.2em;
        }

        .subjects-and-credits-content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .subjects-and-credits-content th,
        .subjects-and-credits-content td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .subjects-and-credits-content th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .subjects-and-credits-content tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .subjects-and-credits-content .total-row {
            font-weight: bold;
        }

        .related-links {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
        }

        .related-links ul {
            list-style: none;
            padding: 0;
        }

        .related-links li {
            margin-bottom: 8px;
        }

        .related-links li a {
            text-decoration: none;
            color: #007bff;
            display: block;
            padding: 5px 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-layout {
                grid-template-columns: 1fr; /* Stack columns on smaller screens */
            }

            .main-content {
                padding-right: 0;
            }

            .sidebar {
                margin-top: 20px;
            }

            .tab-navigation {
                flex-direction: column; /* Stack tabs vertically */
            }

            .tab-button {
                margin-right: 0;
                margin-bottom: 5px;
                border-radius: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container1">
        <header>
            <h1>HIGHER NATIONAL DIPLOMA IN ACCOUNTANCY - (HNDA)</h1><br>
        </header>

        <div class="tab-navigation">
            <button class="tab-button" onclick="showSection('home')" data-section="home">Home</button>
            <button class="tab-button" onclick="showSection('entryProfile')" data-section="entryProfile">Entry Profile</button>
            <button class="tab-button" onclick="showSection('subjectsCredits')" data-section="subjectsCredits">Subjects and Credits</button>
        </div>

        <div id="home" class="content-section">
            <div class="main-layout">
                <section class="main-content">
                    <p>The Higher National Diploma in Accountancy (HNDA) was established in 1943 in the name of National Diploma in Accountancy with the aim of detecting, preventing frauds, errors, and malpractices in Ceylon Tea Estates and Ceylon Railway; and this was the first professional accounting course introduced back then in Sri Lanka (Ceylon). Later, this course was transformed into the Higher National Diploma in Accountancy (HNDA) in 1946.</p>
                    <p>Gradually, this course gained popularity and in 1963 part-time evening classes were also introduced in order to meet the market demand. Per Public Administration Circular No.46/90 of 1990, the HNDA qualification is considering as an equivalent/( an alternate) to a Bachelors of Commerce degree (B. Com) offered by an accredited university, recognized by University Grant Commission (UGC) of Sri Lanka. This course is mandated for auditing enterprises, excluding public sector organizations and quoted companies. The certified auditors credential were granted for the HNDA holders in 1968. Beginning 2001, the HNDA program is conducting courses throughout the Island by the Advanced Technological Institute (ATIs) which is administered by the Sri Lanka Institute of Advanced Technological Education (SLIATE).</p>
                    <p>After completing the HNDA program, the HNDA holders can start their own careers as a registered auditor and can conduct the audit of companies except for public sector organizations and quoted companies.</p>
                </section>
                <aside class="sidebar">
                    <div class="search-box">
                        <h2>Search</h2>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="latest-news">
                        <h2>Latest news</h2>
                        <ul>
                            <li class="news-item"><a href="#">Examination Notice</a></li>
                            <li class="news-item"><a href="#">Late Application Opportunity for the 20th Diploma Awarding...</a></li>
                            <li class="news-item"><a href="#">Mr. M. C. L. Rodrigo officially assumed duties as the Director...</a></li>
                            <li class="news-item"><a href="#">Signing MOU between SLIATE and SLIM.</a></li>
                            <li class="news-item"><a href="#">Mr. H. Athauda Seneviratne assumed duties as the Director...</a></li>
                        </ul>
                    </div>
                    <div class="courses">
                        <h2>Courses</h2>
                        <ul class="courses-list">
                            <li><a href="#">Accountancy</a></li>
                            <li><a href="#">Agriculture</a></li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>

        <div id="entryProfile" class="content-section">
            <div class="admission-criteria">
                <h3>Full Time (The first and second year lectures are conducted as full time during the weekdays, while the third and fourth year lectures are conducted during the weekends.) ( Duration : 4 years)</h3>
                <p>Passes for all the subjects in one sitting at the G.C.E. (A/L) examination with credit pass for Economics and Accountancy..</p>
                <p>A credit pass in Mathematics at the G.C.E (O/L) is considered as an alternative for a credit pass in either Economics or Accountancy at the G.C.E (A/L) examination.</p>
                <p class="and-condition"><strong>AND</strong></p>
                <p>An ordinary pass in English Language at the G.C.E. (O/L) examination.</p>

                <h3>Part Time (Part time program will be conducted during the weekends) (Duration : 4 years)</h3>
                <p><strong>Those who have completed GCE (A/L) examination on or before 2012. Should have one of followings:</strong></p>
                <ol type="i">
                    <li>Passes for all four subjects / three subjects in one sitting at the G.C.E. (A/L) Examination
                        <p class="or-condition"><strong>OR</strong></p>
                    </li>
                    <li>Any of the certificate courses given below conducted by the Department of Technical Education & Training (DTET).
                        <ol type="A">
                            <li>Completion of National Certificate in Accounting Technicians.</li>
                            <li>Completion of National Certificate in Business Studies.</li>
                            <li>Completion of National Certificate in Accounting.</li>
                        </ol>
                    </li>
                </ol>
                <p class="and-condition"><strong>AND</strong></p>
                <p>Applicant should be employed in the relevant field in a government institution/public enterprise/recognized firm or self-employment (Entrepreneur).</p>
            </div>
        </div>

        <div id="subjectsCredits" class="content-section">
            <div class="subjects-and-credits-content">
                <h3>Year 1 - Semester 1</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Module Code</th>
                            <th>Module Title</th>
                            <th>Module Type</th>
                            <th>Credits</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>HNDA 1101</td>
                            <td>Fundamentals of Financial Accounting</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1102</td>
                            <td>Business Mathematics</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1103</td>
                            <td>Commercial Awareness</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1104</td>
                            <td>Business Communication I</td>
                            <td>Core</td>
                            <td>02</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1105</td>
                            <td>Introduction to Computers</td>
                            <td>Core</td>
                            <td>02</td>
                            <td>GPA</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3">TOTAL</td>
                            <td>16</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Year 1 - Semester 2</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Module Code</th>
                            <th>Module Title</th>
                            <th>Module Type</th>
                            <th>Credits</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>HNDA 1201</td>
                            <td>Intermediate Financial Accounting</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1202</td>
                            <td>Statistical Analysis for Management</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1203</td>
                            <td>Micro & Macro Economics</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1204</td>
                            <td>Business Communication II</td>
                            <td>Core</td>
                            <td>02</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 1205</td>
                            <td>Computer Applications</td>
                            <td>Core</td>
                            <td>02</td>
                            <td>GPA</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3">TOTAL</td>
                            <td>16</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Year 2 - Semester 1</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Module Code</th>
                            <th>Module Title</th>
                            <th>Module Type</th>
                            <th>Credits</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>HNDA 2101</td>
                            <td>Advanced Financial Accounting</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 2102</td>
                            <td>Operations Research</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 2103</td>
                            <td>Principles of Auditing & Taxation</td>
                            <td>Core</td>
                            <td>04</td>
                            <td>GPA</td>
                        </tr>
                        <tr>
                            <td>HNDA 2104</td>
                            <td>Business Communication III</td>
                            <td>Core</td>
                            <td>02</td>
                            <td>GPA</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="related-links">
            <ul>
                <li><a href="#">Information Technology</a></li>
                <li><a href="#">Food Technology</a></li>
                <li><a href="#">Quantity Survey</a></li>
                <li><a href="#">Tourism and Hospitality</a></li>
                <li><a href="#">Management</a></li>
                <li><a href="#">Comsumer Science and Product Technology</a></li>
                <li><a href="#">Mechanical</a></li>
            </ul>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all content sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show the requested section
            const activeSection = document.getElementById(sectionId);
            if (activeSection) {
                activeSection.style.display = 'block'; // Use block display
            }

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(button => {
                button.classList.remove('active');
            });

            // Add active class to the clicked button using data-section attribute
            const activeButton = document.querySelector(`.tab-button[data-section="${sectionId}"]`);
            if (activeButton) {
                activeButton.classList.add('active');
            }
        }

        // Show the home section by default on page load
        document.addEventListener('DOMContentLoaded', () => {
            showSection('home');
        });
    </script>
</body>
</html>


<?php include_once("include/footer.php");?>
