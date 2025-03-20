<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTI Dashboard</title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style2.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        
        <div class="nav-right">
            <!-- Navigation link to open the submenu -->
            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu" aria-expanded="false" aria-controls="submenu">
                <span class="icon">
                    <i class="bi bi-person-circle"></i> <!-- Profile icon -->
                </span>
            </a>
            <div class="sub-menu collapse" id="submenu">
                <!-- Submenu item for "Administrator" -->
                <a class="nav-link" href="#">
                    <span class="icon">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    <span class="description">Administrator</span>
                </a>
                <!-- Submenu item for "Logout" -->
                <a class="nav-link" href="index.php">
                    <span class="icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    <span class="description">LOGOUT</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="sidebar-header">
            <button class="btn fs-3" id="sidebarToggle">
                <i class="bi bi-list"></i> <!-- Hamburger icon -->
            </button>
            <div class="logo-container">
                <img src="https://ecpms.dti7.site/assets/logo-1858e98a.svg" alt="DTI Logo" class="dti-logo">
                <div class="logo-text">
                    <div class="logo-text-top">CEBU PROVINCIAL</div>
                    <div class="logo-text-bottom">OFFICE</div>
                </div>
            </div>
        </div>
        <!-- Red divider line at the bottom of the sidebar header -->
        <div class="mini-rectangle"></div>
        
        <nav class="nav flex-column">
            <!-- Sidebar links with icons and descriptions -->
            <a class="nav-link custom-btn active" href="#" data-path="Dashboard">
                <span class="icon">
                    <i class="bi bi-bar-chart-line"></i>
                </span>
                <span class="description">Dashboard</span>
            </a>
            <a class="nav-link" href="#" data-path="Client Management">
                <span class="icon">
                    <i class="bi bi-person"></i>
                </span>
                <span class="description">Client Management</span>
            </a>
            <a class="nav-link" href="#" data-path="Certificate Management">
                <span class="icon">
                    <i class="bi bi-award"></i>
                </span>
                <span class="description">Certificate Management</span>
            </a>
            <a class="nav-link" href="#" data-path="Sheets">
                <span class="icon">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                </span>
                <span class="description">Sheets</span>
            </a>
        </nav>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <h2>Welcome to DTI E-Certification System</h2>
        <p>This is your dashboard. Navigate using the sidebar menu.</p>
        <!-- Add your dashboard content here -->
    </div>

    <!-- Bootstrap JS for functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle the sidebar when the hamburger icon is clicked
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            
            // Update header margin when sidebar state changes
            const header = document.querySelector('.header');
            const sidebar = document.querySelector('.sidebar');
            if (sidebar.classList.contains('collapsed')) {
                header.style.marginLeft = 'var(--sidebar-collapsed-width)';
            } else {
                header.style.marginLeft = 'var(--sidebar-width)';
            }
        });

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            // Start with expanded sidebar on larger screens, collapsed on mobile
            const sidebar = document.querySelector('.sidebar');
            const header = document.querySelector('.header');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                header.style.marginLeft = '0';
            } else {
                header.style.marginLeft = 'var(--sidebar-width)';
            }
            
            // Add click event listeners to all navigation links
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Remove active class from all links
                    navLinks.forEach(l => l.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Update breadcrumb
                    updateBreadcrumb(this.getAttribute('data-path'));
                    
                    // Prevent default link behavior
                    e.preventDefault();
                });
            });
        });
        
        // Function to update the breadcrumb
        function updateBreadcrumb(path) {
            const breadcrumb = document.querySelector('.breadcrumb');
            
            // Clear existing items except Home
            while (breadcrumb.children.length > 1) {
                breadcrumb.removeChild(breadcrumb.lastChild);
            }
            
            // Add the new path
            const li = document.createElement('li');
            li.className = 'breadcrumb-item active';
            li.setAttribute('aria-current', 'page');
            li.textContent = path;
            breadcrumb.appendChild(li);
            
            // If path contains subpaths (e.g., "Client Management/Add Client")
            if (path.includes('/')) {
                const parts = path.split('/');
                
                // Clear again
                while (breadcrumb.children.length > 1) {
                    breadcrumb.removeChild(breadcrumb.lastChild);
                }
                
                // Add each part
                parts.forEach((part, index) => {
                    const li = document.createElement('li');
                    li.className = 'breadcrumb-item';
                    
                    if (index === parts.length - 1) {
                        li.classList.add('active');
                        li.setAttribute('aria-current', 'page');
                        li.textContent = part.trim();
                    } else {
                        const a = document.createElement('a');
                        a.href = '#';
                        a.textContent = part.trim();
                        li.appendChild(a);
                    }
                    
                    breadcrumb.appendChild(li);
                });
            }
        }
    </script>
</body>
</html>
