<?php
$pageTitle = "Dashboard";
$currentPage = "Dashboard";
include('header.php');
include('sidebar.php');

// Get available years from the database
$yearQuery = "SELECT DISTINCT YEAR(date_uploaded) as year FROM clients ORDER BY year ASC";
$yearResult = $conn->query($yearQuery);
$availableYears = [];
if ($yearResult && $yearResult->num_rows > 0) {
    while ($row = $yearResult->fetch_assoc()) {
        $availableYears[] = $row['year'];
    }
}

// If no years found in database, provide default range
if (empty($availableYears)) {
    $availableYears = range(2000, 2025);
}

// Get selected year (default to current year if not specified)
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$selectedYearRange = isset($_GET['year_range']) ? intval($_GET['year_range']) : 5;

// Get monthly certificate counts for the selected year
$monthlyQuery = "SELECT MONTH(date_uploaded) as month, COUNT(*) as count
                FROM clients
                WHERE YEAR(date_uploaded) = ?
                GROUP BY MONTH(date_uploaded)
                ORDER BY month ASC";
               
$stmt = $conn->prepare($monthlyQuery);
$stmt->bind_param("i", $selectedYear);
$stmt->execute();
$monthlyResult = $stmt->get_result();

// Initialize array with zeros for all months
$monthlyData = array_fill(1, 12, 0);

// Fill in actual data
if ($monthlyResult && $monthlyResult->num_rows > 0) {
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthlyData[$row['month']] = intval($row['count']);
    }
}

// Get weekly certificate counts for the selected year and month
$weeklyQuery = "SELECT
                    CASE
                        WHEN DAY(date_uploaded) BETWEEN 1 AND 7 THEN 1
                        WHEN DAY(date_uploaded) BETWEEN 8 AND 14 THEN 2
                        WHEN DAY(date_uploaded) BETWEEN 15 AND 21 THEN 3
                        ELSE 4
                    END as week,
                    COUNT(*) as count
                FROM clients
                WHERE YEAR(date_uploaded) = ? AND MONTH(date_uploaded) = ?
                GROUP BY week
                ORDER BY week ASC";
$stmt = $conn->prepare($weeklyQuery);
$stmt->bind_param("ii", $selectedYear, $selectedMonth);
$stmt->execute();
$weeklyResult = $stmt->get_result();

// Initialize array with zeros for all weeks
$weeklyData = array_fill(1, 4, 0);

// Fill in actual data
if ($weeklyResult && $weeklyResult->num_rows > 0) {
    while ($row = $weeklyResult->fetch_assoc()) {
        $weeklyData[$row['week']] = intval($row['count']);
    }
}

// Get yearly certificate counts
$endYear = date('Y');
$startYear = $endYear - $selectedYearRange + 1;
$yearlyQuery = "SELECT YEAR(date_uploaded) as year, COUNT(*) as count
               FROM clients
               WHERE YEAR(date_uploaded) BETWEEN ? AND ?
               GROUP BY YEAR(date_uploaded)
               ORDER BY year ASC";
               
$stmt = $conn->prepare($yearlyQuery);
$stmt->bind_param("ii", $startYear, $endYear);
$stmt->execute();
$yearlyResult = $stmt->get_result();

// Initialize array with zeros for all years in range
$yearlyData = array_fill_keys(range($startYear, $endYear), 0);

// Fill in actual data
if ($yearlyResult && $yearlyResult->num_rows > 0) {
    while ($row = $yearlyResult->fetch_assoc()) {
        $yearlyData[$row['year']] = intval($row['count']);
    }
}

// Get total sheets count (XLSX files uploaded) from the files table
$sheetQuery = "SELECT COUNT(*) as total_sheets FROM files";
$sheetResult = $conn->query($sheetQuery);
$totalSheets = 0;
if ($sheetResult && $sheetResult->num_rows > 0) {
    $row = $sheetResult->fetch_assoc();
    $totalSheets = $row["total_sheets"];
}

// Get total certificates count
$certificateQuery = "SELECT COUNT(*) as total_certificates FROM clients";
$certificateResult = $conn->query($certificateQuery);
$totalCertificates = 0;
if ($certificateResult && $certificateResult->num_rows > 0) {
    $row = $certificateResult->fetch_assoc();
    $totalCertificates = $row["total_certificates"];
}

// Close the database connection
$conn->close();

// Convert data to JSON for JavaScript
$monthlyDataJSON = json_encode(array_values($monthlyData));
$weeklyDataJSON = json_encode(array_values($weeklyData));
$yearlyDataJSON = json_encode(array_values($yearlyData));
$yearlyLabelsJSON = json_encode(array_keys($yearlyData));
$monthNames = json_encode(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']);
$weekNames = json_encode(['Week 1 (1-7)', 'Week 2 (8-14)', 'Week 3 (15-21)', 'Week 4 (22-31)']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTI E-Cert Reports</title>
    <link rel="stylesheet" href="style2.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/img/OIP.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom styles for dashboard.php to make it fit on one screen */
.main-content {
    background-color: #f5f7fa;
    padding: 15px;
    height: calc(100vh - 120px);
    overflow: hidden;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.dashboard-header {
    margin-bottom: 10px;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dashboard-header h1 {
    color: #2c3e50;
    font-weight: 600;
    font-size: 22px;
    margin: 0;
}

.date {
    color: #7f8c8d;
    font-size: 14px;
}

.navigation {
    margin-bottom: 10px;
    display: flex;
    gap: 10px;
}

.btn {
    background-color: #3498db;
    border: none;
    border-radius: 5px;
    padding: 6px 10px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    font-size: 13px;
}

.btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.summary-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 10px;
}

.summary-card {
    background-color: white;
    border-radius: 10px;
    padding: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    text-align: left;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

/* Different colors for summary cards */
.summary-container .summary-card:nth-child(1) {
    border-top: 4px solid #002147; /* Blue */
}

.summary-container .summary-card:nth-child(2) {
    border-top: 4px solid #008000; /* Green */
}

.summary-container .summary-card:nth-child(3) {
    border-top: 4px solid #8b0000; /* Red */
}

/* Different text colors for summary values */
.summary-container .summary-card:nth-child(1) .summary-value {
    color: #002147; /* Blue */
}

.summary-container .summary-card:nth-child(2) .summary-value {
    color: #008000; /* Green */
}

.summary-container .summary-card:nth-child(3) .summary-value {
    color: #8b0000; /* Red */
}

.summary-title {
    font-size: 13px;
    color: #7f8c8d;
    margin-bottom: 5px;
    font-weight: 500;
    text-align: center;
}

.summary-value {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
}

.filter-container {
    background-color: white;
    border-radius: 10px;
    padding: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 10px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
}

.filter-label {
    color: #2c3e50;
    font-weight: 500;
    margin-right: 5px;
    margin-left: 10px;
    font-size: 12px;
}

.dropdown {
    padding: 4px 8px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    color: #2c3e50;
    background-color: #f8f9fa;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.dropdown:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    outline: none;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    flex-grow: 1;
    overflow: hidden;
}

.chart-container {
    background-color: white;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.chart-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

/* Style report titles with colored backgrounds and white text */
.report-title {
    color: white;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 10px;
    text-align: center;
    padding: 8px 10px;
    border-radius: 5px 5px 0 0; /* Rounded corners only at the top */
    width: 100%; /* Ensure full width coverage */
    box-sizing: border-box;
}

#weekly-section .report-title {
    background-color: #002147; /* Darker blue background */
}

#monthly-section .report-title {
    background-color: #008000; /* Darker green background */
}

#yearly-section .report-title {
    background-color: #8b0000; /* Darker red background */
}

/* Style report titles with colored backgrounds and white text - with small space */
.report-title {
    color: white;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 0; /* Remove bottom margin */
    text-align: center;
    padding: 8px 10px;
    border-radius: 5px 5px 0 0; /* Rounded corners only at the top */
    width: 100%; /* Ensure full width coverage */
    box-sizing: border-box;
    border-bottom: none; /* Ensure no border at bottom */
}

#weekly-section .report-title {
    background-color: #002147; /* Darker blue background */
}

#monthly-section .report-title {
    background-color: #008000; /* Darker green background */
}

#yearly-section .report-title {
    background-color: #8b0000; /* Darker red background */
}

/* Ensure no borders or lines in the chart container */
.chart-container {
    background-color: white;
    border-radius: 10px;
    padding: 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: none;
}

/* Add a small space between title and chart */
.chart-wrapper {
    flex-grow: 1;
    position: relative;
    padding: 15px 10px 10px 10px; /* Add 15px top padding for spacing */
    margin-top: 0;
    border-top: none;
}

/* Adjust chart wrapper to remove any gap */
.chart-wrapper {
    flex-grow: 1;
    position: relative;
    padding: 10px 10px 0 10px; /* Remove top padding to eliminate gap */
    margin-top: 0; /* Ensure no margin at top */
    border-top: none; /* Ensure no border at top */
}


canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100% !important;
    height: 100% !important;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .main-content {
        height: auto;
        overflow: auto;
    }
    
    .chart-container {
        height: 250px;
    }
}

@media (max-width: 768px) {
    .filter-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-label {
        margin: 5px 0 3px 0;
    }
    
    .dropdown {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .summary-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .dashboard-header h1 {
        font-size: 20px;
        margin-bottom: 5px;
    }
    
    .summary-value {
        font-size: 22px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 5px;
        justify-content: center;
    }
    
    .navigation {
        flex-direction: column;
        width: 100%;
    }
}

    </style>
</head>
<body>
<div class="main-content" style="margin-top: 120px;">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><b>DTI E-CERTIFCATE DASHBOARD</b></h1>
            <div class="date"><?php echo date('F d, Y'); ?></div>
        </div>
        
        <div class="summary-container">
            <div class="summary-card">
                <div class="summary-title">Total Sheets Uploaded</div>
                <div class="summary-value"><?php echo $totalSheets; ?></div>
            </div>
            
            <div class="summary-card">
                <div class="summary-title">Total Certificates Processed</div>
                <div class="summary-value"><?php echo $totalCertificates; ?></div>
            </div>
            
            <div class="summary-card">
                <div class="summary-title">Certificates in <?php echo $selectedYear; ?></div>
                <div class="summary-value"><?php echo array_sum($monthlyData); ?></div>
            </div>
        </div>
        
        <div class="filter-container">
            <div class="filter-label">Select Month:</div>
            <select name="month" class="dropdown" onchange="updateFilters(this.value, 'month')">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $selectedMonth) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
            
            <div class="filter-label">Select Year:</div>
            <select name="year" class="dropdown" onchange="updateFilters(this.value, 'year')">
                <?php foreach (range(2000, 2025) as $year): ?>
                    <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="filter-label">Year Range:</div>
            <select name="year_range" class="dropdown" onchange="updateFilters(this.value, 'year_range')">
                <option value="5" <?php echo ($selectedYearRange == 5) ? 'selected' : ''; ?>>Last 5 Years</option>
                <option value="10" <?php echo ($selectedYearRange == 10) ? 'selected' : ''; ?>>Last 10 Years</option>
                <option value="15" <?php echo ($selectedYearRange == 15) ? 'selected' : ''; ?>>Last 15 Years</option>

            </select>
        </div>
        
        <div class="charts-grid">
            <!-- Weekly Chart Section -->
            <div id="weekly-section">
                <div class="chart-container">
                    <h2 class="report-title">Weekly Certificates (<?php echo date('F', mktime(0, 0, 0, $selectedMonth, 1)); ?>)</h2>
                    <div class="chart-wrapper">
                        <canvas id="weeklyChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Chart Section -->
            <div id="monthly-section">
                <div class="chart-container">
                    <h2 class="report-title">Monthly Certificates (<?php echo $selectedYear; ?>)</h2>
                    <div class="chart-wrapper">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Yearly Chart Section -->
            <div id="yearly-section">
                <div class="chart-container">
                    <h2 class="report-title">Yearly Certificates (<?php echo $startYear; ?>-<?php echo $endYear; ?>)</h2>
                    <div class="chart-wrapper">
                        <canvas id="yearlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        // Function to update filters and reload page
        function updateFilters(value, type) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set(type, value);
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }
        
        // Chart initialization
document.addEventListener('DOMContentLoaded', function() {
    // Set chart defaults
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.font.size = 11;
    
    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyData = <?php echo $weeklyDataJSON; ?>;
    const weekNames = <?php echo $weekNames; ?>;
    
    // Find the maximum value to set appropriate y-axis scale for weekly chart
    const weeklyMaxValue = Math.max(...weeklyData);
    const weeklyYAxisMax = weeklyMaxValue > 0 ? (Math.ceil(weeklyMaxValue / 50) * 50) : 50;
    
    const weeklyChart = new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: weekNames,
            datasets: [{
                label: 'Certificates',
                data: weeklyData,
                backgroundColor: '#000039', // Blue
                borderColor: '#000039',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: weeklyYAxisMax,
                    ticks: {
                        stepSize: Math.max(10, Math.ceil(weeklyYAxisMax / 5))
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(52, 152, 219, 0.9)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    padding: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].label + ' of ' +
                                   '<?php echo date('F', mktime(0, 0, 0, $selectedMonth, 1)) . ' ' . $selectedYear; ?>';
                        }
                    }
                }
            }
        }
    });
    
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = <?php echo $monthlyDataJSON; ?>;
    const monthNames = <?php echo $monthNames; ?>;
    
    // Find the maximum value to set appropriate y-axis scale for monthly chart
    const monthlyMaxValue = Math.max(...monthlyData);
    const monthlyYAxisMax = monthlyMaxValue > 0 ? (Math.ceil(monthlyMaxValue / 50) * 50) : 50;
    
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'Certificates',
                data: monthlyData,
                backgroundColor: '#008000', // Green
                borderColor: '#008000 ',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: monthlyYAxisMax,
                    ticks: {
                        stepSize: Math.max(10, Math.ceil(monthlyYAxisMax / 5))
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(46, 204, 113, 0.9)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    padding: 8,
                    displayColors: false,
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems[0].label + ' ' + <?php echo $selectedYear; ?>;
                        }
                    }
                }
            }
        }
    });
    
    // Yearly Chart
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    const yearlyData = <?php echo $yearlyDataJSON; ?>;
    const yearlyLabels = <?php echo $yearlyLabelsJSON; ?>;
    
    // Find the maximum value to set appropriate y-axis scale for yearly chart
    const yearlyMaxValue = Math.max(...yearlyData);
    const yearlyYAxisMax = yearlyMaxValue > 0 ? (Math.ceil(yearlyMaxValue / 50) * 50) : 50;
    
    const yearlyChart = new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: yearlyLabels,
            datasets: [{
                label: 'Certificates',
                data: yearlyData,
                backgroundColor: '#8b0000', // Red
                borderColor: '#8b0000',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: yearlyYAxisMax,
                    ticks: {
                        stepSize: Math.max(10, Math.ceil(yearlyYAxisMax / 5))
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(231, 76, 60, 0.9)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    padding: 8,
                    displayColors: false
                }
            }
        }
    });
    
    // Handle window resize to ensure charts fit properly
    window.addEventListener('resize', function() {
        weeklyChart.resize();
        monthlyChart.resize();
        yearlyChart.resize();
    });
    
    // Make sure all charts are properly sized on load
    setTimeout(function() {
        weeklyChart.resize();
        monthlyChart.resize();
        yearlyChart.resize();
    }, 100);
});

// Improve touch experience on mobile devices
document.addEventListener('touchstart', function() {}, {passive: true});

        
        // Improve touch experience on mobile devices
        document.addEventListener('touchstart', function() {}, {passive: true});
    </script>
<?php
// Include the footer
include('footer.php');
?>
</body>
</html>
