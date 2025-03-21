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
        <a class="nav-link <?php echo ($currentPage == 'Dashboard') ? 'custom-btn active' : ''; ?>" href="dashboard.php" data-path="Dashboard">
            <span class="icon">
                <i class="bi bi-bar-chart-line"></i>
            </span>
            <span class="description">Dashboard</span>
        </a>
        <a class="nav-link <?php echo ($currentPage == 'Client Management') ? 'custom-btn active' : ''; ?>" href="client_management.php" data-path="Client Management">
            <span class="icon">
                <i class="bi bi-person"></i>
            </span>
            <span class="description">Client Management</span>
        </a>
        <a class="nav-link <?php echo ($currentPage == 'Certificate Management') ? 'custom-btn active' : ''; ?>" href="certificate_management.php" data-path="Certificate Management">
            <span class="icon">
                <i class="bi bi-award"></i>
            </span>
            <span class="description">Certificate Management</span>
        </a>
        <a class="nav-link <?php echo ($currentPage == 'Sheets') ? 'custom-btn active' : ''; ?>" href="sheets.php" data-path="Sheets">
            <span class="icon">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </span>
            <span class="description">Sheets</span>
        </a>
    </nav>
</div>
