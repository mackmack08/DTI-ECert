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
    <!-- Dashboard Specific CSS -->
    <link rel="stylesheet" href="style3.css?v=<?php echo time(); ?>">
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
        
      <!-- Top 4 Dashboard Cards -->
<div class="row mb-4">
    <!-- Certificate Issued Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card">
            <div class="card-icon bg-primary">
                <i class="bi bi-file-earmark-check"></i>
            </div>
            <div class="card-content">
                <h3 class="counter">0</h3>
                <p>Certificates Issued</p>
            </div>
        </div>
    </div>
    
    <!-- Certificates Pending Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card">
            <div class="card-icon bg-warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="card-content">
                <h3 class="counter">0</h3>
                <p>Certificates Pending</p>
            </div>
        </div>
    </div>
    
    <!-- Registered Clients Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card">
            <div class="card-icon bg-success">
                <i class="bi bi-people"></i>
            </div>
            <div class="card-content">
                <h3 class="counter">0</h3>
                <p>Registered Clients</p>
            </div>
        </div>
    </div>
    
    <!-- Expired Certificates Card -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dashboard-card">
            <div class="card-icon bg-danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="card-content">
                <h3 class="counter">0</h3>
                <p>Expired Certificates</p>
            </div>
        </div>
    </div>
</div>

        
       <!-- Daily and Weekly Dashboard Cards -->
<div class="row mb-4">
    <!-- Daily Statistics Card -->
    <div class="col-md-6 mb-3">
        <div class="dashboard-chart-card">
            <div class="chart-header">
                <h4><i class="bi bi-calendar-day"></i> Daily Statistics</h4>
            </div>
            <div class="chart-body">
                <canvas id="dailyChart"></canvas>
            </div>
            <div class="chart-footer">
                <div class="stat-item">
                    <span class="stat-label">Issued Today:</span>
                    <span class="stat-value">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pending Today:</span>
                    <span class="stat-value">0</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weekly Statistics Card -->
    <div class="col-md-6 mb-3">
        <div class="dashboard-chart-card">
            <div class="chart-header">
                <h4><i class="bi bi-calendar-week"></i> Weekly Statistics</h4>
            </div>
            <div class="chart-body">
                <canvas id="weeklyChart"></canvas>
            </div>
            <div class="chart-footer">
                <div class="stat-item">
                    <span class="stat-label">This Week:</span>
                    <span class="stat-value">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Last Week:</span>
                    <span class="stat-value">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly and Yearly Dashboard Cards -->
<div class="row">
    <!-- Monthly Statistics Card -->
    <div class="col-md-6 mb-3">
        <div class="dashboard-chart-card">
            <div class="chart-header">
                <h4><i class="bi bi-calendar-month"></i> Monthly Statistics</h4>
            </div>
            <div class="chart-body">
                <canvas id="monthlyChart"></canvas>
            </div>
            <div class="chart-footer">
                <div class="stat-item">
                    <span class="stat-label">This Month:</span>
                    <span class="stat-value">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Last Month:</span>
                    <span class="stat-value">0</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Yearly Statistics Card -->
    <div class="col-md-6 mb-3">
        <div class="dashboard-chart-card">
            <div class="chart-header">
                <h4><i class="bi bi-calendar4"></i> Yearly Statistics</h4>
            </div>
            <div class="chart-body">
                <canvas id="yearlyChart"></canvas>
            </div>
            <div class="chart-footer">
                <div class="stat-item">
                    <span class="stat-label">This Year:</span>
                    <span class="stat-value">0</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Last Year:</span>
                    <span class="stat-value">0</span>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Bootstrap JS for functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Add Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Toggle the sidebar when the hamburger icon is clicked
document.getElementById('sidebarToggle').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    
    // Update header margin when sidebar state changes
    const header = document.querySelector('.header');
    const sidebar = document.querySelector('.sidebar');
    const sidebarHeader = document.querySelector('.sidebar-header');
    const miniRectangle = document.querySelector('.mini-rectangle');
    
    if (sidebar.classList.contains('collapsed')) {
        header.style.marginLeft = 'var(--sidebar-collapsed-width)';
        sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
        miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
    } else {
        header.style.marginLeft = 'var(--sidebar-width)';
        sidebarHeader.style.width = 'var(--sidebar-width)';
        miniRectangle.style.width = 'var(--sidebar-width)';
    }
});

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
    // Start with expanded sidebar on larger screens, collapsed on mobile
    const sidebar = document.querySelector('.sidebar');
    const header = document.querySelector('.header');
    const sidebarHeader = document.querySelector('.sidebar-header');
    const miniRectangle = document.querySelector('.mini-rectangle');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        header.style.marginLeft = 'var(--sidebar-collapsed-width)';
        sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
        miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
    } else {
        header.style.marginLeft = 'var(--sidebar-width)';
        sidebarHeader.style.width = 'var(--sidebar-width)';
        miniRectangle.style.width = 'var(--sidebar-width)';
    }
    
    // Handle window resize to adjust fixed elements
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            header.style.marginLeft = 'var(--sidebar-collapsed-width)';
            sidebarHeader.style.width = 'var(--sidebar-collapsed-width)';
            miniRectangle.style.width = 'var(--sidebar-collapsed-width)';
        } else {
            if (!sidebar.classList.contains('collapsed')) {
                header.style.marginLeft = 'var(--sidebar-width)';
                sidebarHeader.style.width = 'var(--sidebar-width)';
                miniRectangle.style.width = 'var(--sidebar-width)';
            }
        }
    });
    
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
    
    // Initialize all charts
    initializeCharts();
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

// Function to initialize all charts
function initializeCharts() {
    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: ['8AM', '10AM', '12PM', '2PM', '4PM', '6PM'],
            datasets: [{
                label: 'Certificates Issued',
                data: [0, 0, 0, 0, 0, 0],
                backgroundColor: '#0038A8',
                borderColor: '#0038A8',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'No data available yet';
                        }
                    }
                }
            }
        }
    });

    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Certificates Issued',
                data: [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(207, 10, 44, 0.2)',
                borderColor: '#CF0A2C',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'No data available yet';
                        }
                    }
                }
            }
        }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Certificates Issued',
                data: [0, 0, 0, 0],
                backgroundColor: '#28A745',
                borderColor: '#28A745',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'No data available yet';
                        }
                    }
                }
            }
        }
    });

    // Yearly Chart
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    const yearlyChart = new Chart(yearlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Certificates Issued',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(0, 56, 168, 0.2)',
                borderColor: '#0038A8',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'No data available yet';
                        }
                    }
                }
            }
        }
    });
}

    </script>
</body>
</html>
