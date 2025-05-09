<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Sidebar Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            margin-top:100px;        }

        .content-wrapper {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background-color:rgb(255, 255, 255);
            color: #ecf0f1;
            overflow-y: auto;
            transition: left 0.3s ease, width 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(153, 153, 153, 0.93);
        }

        .sidebar-header {
            padding: 20px;
            background-color:rgb(255, 255, 255);
            display: flex;
            align-items: center;
            justify-content: space-between;
            
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #ecf0f1;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin: 4px 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #ecf0f1;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background-color:rgb(254, 246, 91);
            border-left: 3px solidrgb(8, 109, 251);
        }

        .nav-link.active {
            background-color:rgb(245, 245, 245);
            border-left: 3px solidrgb(8, 107, 255);
        }

        .menu-icon {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        .menu-title {
            font-size: 0.95rem;
            flex-grow: 1;
        }

        .menu-arrow {
            transition: transform 0.2s ease;
        }

        .nav-link[aria-expanded="true"] .menu-arrow {
            transform: rotate(90deg);
        }

        .sub-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color:rgb(255, 239, 93);
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .sub-menu.show {
            max-height: 300px;
        }

        .sub-menu .nav-link {
            padding: 10px 20px 10px 52px;
            font-size: 0.9rem;
        }

        /* Toggle Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color:rgb(42, 171, 246);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            display: none;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }
            
            .sidebar.open {
                left: 0;
            }
            
            .content-wrapper {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background-color: #2c3e50;
                padding: 8px 12px;
                border-radius: 4px;
            }
            
            .sidebar-toggle.open {
                left: 270px;
            }
        }
        
        /* Collapsed Sidebar */
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar.collapsed .menu-title,
        .sidebar.collapsed .menu-arrow {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-header h3 {
            display: none;
        }
        
        .sidebar.collapsed ~ .content-wrapper {
            margin-left: 60px;
        }
        
        .collapse-toggle {
            background: none;
            border: none;
            color: #ecf0f1;
            cursor: pointer;
        }
        
        /* Accessibility */
        .nav-link:focus {
            outline: 2px solid #3498db;
        }
        
        /* Visual feedback for current page */
        .nav-link.current {
            background-color: #3498db;
            border-left: 3px solid #ecf0f1;
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="collapse-toggle" id="collapseToggle">
            </button>
        </div>
        
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link current" href="index.php">
                    <i class="fas fa-tachometer-alt menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#slidersMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-images menu-icon"></i>
                    <span class="menu-title">Sliders</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="slidersMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="slider.php">
                            <span class="menu-title">Manage Sliders</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#announcementsMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-bullhorn menu-icon"></i>
                    <span class="menu-title">Announcements</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="announcementsMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="announcement.php">
                            <span class="menu-title">Manage Announcements</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#courseMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-graduation-cap menu-icon"></i>
                    <span class="menu-title">Courses</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="courseMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="course.php">
                            <span class="menu-title">Manage Courses</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#staffMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-title">Staff</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="staffMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">
                            <span class="menu-title">Manage Staff</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#newsMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-newspaper menu-icon"></i>
                    <span class="menu-title">News</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="newsMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">
                            <span class="menu-title">Manage News</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#eventsMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-calendar-alt menu-icon"></i>
                    <span class="menu-title">Events</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="eventsMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="evnt.php">
                            <span class="menu-title">Manage Events</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#siteSettingsMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-cog menu-icon"></i>
                    <span class="menu-title">Site Settings</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </a>
                <ul class="sub-menu" id="siteSettingsMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="site-setting.php">
                            <span class="menu-title">Manage Site</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

  

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle mobile sidebar
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
                sidebarToggle.classList.toggle('open');
            });
            
            // Toggle collapse sidebar
            const collapseToggle = document.getElementById('collapseToggle');
            
            collapseToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                
                // Change icon direction
                const icon = collapseToggle.querySelector('i');
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                } else {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                }
            });
            
            // Toggle submenu
            const menuLinks = document.querySelectorAll('.nav-link[data-toggle="collapse"]');
            
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Toggle aria-expanded
                    const expanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !expanded);
                    
                    // Toggle submenu
                    const targetId = this.getAttribute('href').replace('#', '');
                    const submenu = document.getElementById(targetId);
                    submenu.classList.toggle('show');
                });
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    const clickedInsideSidebar = sidebar.contains(e.target);
                    const clickedOnToggle = sidebarToggle.contains(e.target);
                    
                    if (!clickedInsideSidebar && !clickedOnToggle && sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                        sidebarToggle.classList.remove('open');
                    }
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    sidebarToggle.classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>