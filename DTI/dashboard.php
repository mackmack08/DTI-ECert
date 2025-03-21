<?php
// Set page-specific variables
$pageTitle = "DTI Dashboard";
$currentPage = "Dashboard";

// Include additional CSS if needed
$additionalCSS = '
    <!-- Any additional CSS specific to this page -->
';

// Include the header
include('header.php');

// Include the sidebar
include('sidebar.php');
?>

<!-- Main Content Area -->
<div class="main-content" style="margin-top: 120px;">
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

    <?php $additionalScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></scrip>'; ?>   
    <script>
    // Function to initialize all charts
function initializeCharts() {
    console.log("Initializing charts...");
    
    // Check if canvas elements exist
    console.log("Daily chart canvas:", document.getElementById('dailyChart'));
    console.log("Weekly chart canvas:", document.getElementById('weeklyChart'));
    console.log("Monthly chart canvas:", document.getElementById('monthlyChart'));
    console.log("Yearly chart canvas:", document.getElementById('yearlyChart'));

    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: ['8AM', '10AM', '12PM', '2PM', '4PM', '6PM'],
            datasets: [{
                label: 'Certificates Issued',
                data: [5, 12, 8, 3, 10, 7], // Test data instead of zeros
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
                            return context.dataset.label + ': ' + context.parsed.y;
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
                data: [8, 15, 12, 20, 18, 10, 5], // Test data instead of zeros
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
                            return context.dataset.label + ': ' + context.parsed.y;
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
                data: [25, 40, 35, 30], // Test data instead of zeros
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
                            return context.dataset.label + ': ' + context.parsed.y;
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
                data: [65, 80, 90, 75, 110, 95, 85, 100, 120, 105, 95, 115], // Test data instead of zeros
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
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
    
    console.log("Charts initialized!");
}

document.addEventListener('DOMContentLoaded', function() {
    // Your existing code...
    
    console.log("DOM loaded, initializing charts...");
    initializeCharts(); // Make sure this line is present
});
    </script>
;
<?php
// Add page-specific scripts if needed
$additionalScripts = '
    <!-- Any additional scripts specific to this page -->
    <script>
        // Client management specific JavaScript
    </script>
';

// Include the footer
include('footer.php');
?>
