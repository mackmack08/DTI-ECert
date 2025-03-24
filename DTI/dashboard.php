<?php
// Set page-specific variables
$pageTitle = "DTI Dashboard";
$currentPage = "Dashboard";

// Include the header
include('header.php');

// Include the sidebar
include('sidebar.php');
?>

<!-- Main Content Area -->
<div class="main-content" style="margin-top: 120px;">
    <h2>Welcome to DTI E-Certification System</h2>
    <p>Your one-stop dashboard for managing certificates and clients.</p>
   
    <!-- Key Numbers Section -->
    <div class="row mb-4">
        <!-- Total Certificates Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="dashboard-card">
                <div class="card-icon bg-primary">
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <div class="card-content">
                    <h3 class="counter">0</h3>
                    <p>Total Certificates</p>
                </div>
            </div>
        </div>
       
        <!-- Waiting for Approval Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="dashboard-card">
                <div class="card-icon bg-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="card-content">
                    <h3 class="counter">0</h3>
                    <p>Waiting for Approval</p>
                </div>
            </div>
        </div>
       
        <!-- Active Clients Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="dashboard-card">
                <div class="card-icon bg-success">
                    <i class="bi bi-people"></i>
                </div>
                <div class="card-content">
                    <h3 class="counter">2</h3>
                    <p>Active Clients</p>
                </div>
            </div>
        </div>
       
        <!-- Certificates About to Expire Card -->
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="dashboard-card">
                <div class="card-icon bg-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="card-content">
                    <h3 class="counter">0</h3>
                    <p>About to Expire</p>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Recent Activity and Weekly Summary -->
    <div class="row mb-4">
        <!-- Today's Activity -->
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h4><i class="bi bi-calendar-day"></i> Today's Activity</h4>
                </div>
                <div class="chart-body">
                    <canvas id="dailyChart"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="stat-item">
                        <span class="stat-label">Approved Today:</span>
                        <span class="stat-value">0</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">New Requests:</span>
                        <span class="stat-value">0</span>
                    </div>
                </div>
            </div>
        </div>
       
        <!-- This Week's Summary -->
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h4><i class="bi bi-calendar-week"></i> This Week's Summary</h4>
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

    <!-- Monthly Trends and Yearly Overview -->
    <div class="row">
        <!-- Monthly Trends -->
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h4><i class="bi bi-calendar-month"></i> Monthly Trends</h4>
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
       
        <!-- Yearly Overview -->
        <div class="col-md-6 mb-3">
            <div class="dashboard-chart-card">
                <div class="chart-header">
                    <h4><i class="bi bi-calendar4"></i> Yearly Overview</h4>
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
                        <span class="stat-label">System Status:</span>
                        <span class="stat-value">Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Function to initialize all charts
function initializeCharts() {
    // Make sure Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }
   
    // Set Chart.js defaults for better display
    Chart.defaults.font.family = "'Poppins', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;
    
    // Daily Chart - Empty chart ready for data
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        const dailyChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: ['Morning', 'Noon', 'Afternoon', 'Evening'],
                datasets: [{
                    label: 'Certificates Processed',
                    data: [0, 0, 0, 0],
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
                }
            }
        });
    }

    // Weekly Chart - Empty chart ready for data
    const weeklyCtx = document.getElementById('weeklyChart');
    if (weeklyCtx) {
        const weeklyChart = new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Certificates Processed',
                    data: [0, 0, 0, 0, 0],
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
                        suggestedMax: 5 // Set a small max value to make the chart look better when empty
                    }
                }
            }
        });
    }

    // Monthly Chart - Empty chart ready for data
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Certificates Processed',
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
                        suggestedMax: 5 // Set a small max value to make the chart look better when empty
                    }
                }
            }
        });
    }

    // Yearly Chart - Empty chart ready for data
    const yearlyCtx = document.getElementById('yearlyChart');
    if (yearlyCtx) {
        const yearlyChart = new Chart(yearlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Certificates Processed',
                    data: [0, 0, 0, 0, 0, 0],
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
                        suggestedMax: 5 // Set a small max value to make the chart look better when empty
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM loaded, initializing charts...");
    initializeCharts();
});
</script>

<?php
// Include the footer
include('footer.php');
?>
