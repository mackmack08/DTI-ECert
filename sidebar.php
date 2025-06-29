<!-- Sidebar Section -->
<div class="sidebar" id="sidebar">
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
        
        <a class="nav-link <?php echo ($currentPage == 'Sheet Management') ? 'custom-btn active' : ''; ?>" href="sheet.php" data-path="Sheets">
            <span class="icon">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            </span>
            <span class="description">Sheet Management</span>
        </a>
    </nav>
    
    <!-- Developer credits at the bottom of the sidebar -->
    <div class="developer-credits">
        <span>Developed by: <span class="dti-interns" id="dtiInterns">DTI-Interns</span></span>
    </div>
</div>

<!-- Separate popup for developer names -->
<div class="developer-names-popup" id="developerNamesPopup">
    <ul>
        <li class="school-name"><b>CTU - Main Campus: BS in Information Systems</b></li>
        <li><a href="https://www.facebook.com/mvt.08" target="_blank">Mark Vincent A. Tariman</a></li>
        <li><a href="https://www.facebook.com/Empure.rage" target="_blank">Nichols S. Lavajo</a></li>
        <li><a href="https://www.facebook.com/jimsarjii" target="_blank">James RG A. Caballero</a></li>
        <li><a href="https://www.facebook.com/darksykunno" target="_blank">Juferson N. Almeñe</a></li>
    </ul>
    <!-- Arrow pointing down to the text -->
    <div class="popup-arrow"></div>
</div>

<style>
    /* Developer credits styling */
    .developer-credits {
        position: absolute;
        bottom: 15px;
        left: 0;
        width: 100%;
        padding: 10px 15px;
        font-size: 12px;
        color: #1c2841;
        font-weight: 400;
        text-align: left;
        padding-left: 20px;
    }
    
    /* Hide developer credits when sidebar is collapsed */
    .sidebar.collapsed .developer-credits {
        display: none;
    }
    
    .dti-interns {
        cursor: pointer;
        color: #000039;
        font-weight: 700;
        transition: color 0.3s ease;
    }
    
    .dti-interns:hover {
        color: #003366;
    }
    
    /* Separate popup for developer names */
    .developer-names-popup {
        position: fixed;
        display: none;
        background-color: #01043A;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 10px 15px;
        width: 280px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }
    
    /* Arrow pointing down to the text */
    .popup-arrow {
        position: absolute;
        bottom: -10px;
        left: 30px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid #01043A;
    }
    
    .developer-names-popup ul {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }
    
    .developer-names-popup ul li {
        padding: 5px 0;
        color: white;
        font-size: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .developer-names-popup ul li:last-child {
        border-bottom: none;
    }
    
    .developer-names-popup ul li.school-name {
        color: #f8f9fa;
        font-size: 13px;
        padding-bottom: 8px;
    }
    
    .developer-names-popup ul li a {
        color: white;
        text-decoration: none;
        transition: color 0.2s ease;
        display: block;
    }
    
    .developer-names-popup ul li a:hover {
        color: #4e9af1;
        text-decoration: underline;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle hover for DTI-INTERNS text
    const dtiInterns = document.getElementById('dtiInterns');
    const popup = document.getElementById('developerNamesPopup');
    
    if (dtiInterns && popup) {
        dtiInterns.addEventListener('mouseenter', function(e) {
            // Get position of the DTI-INTERNS text
            const rect = dtiInterns.getBoundingClientRect();
            
            // Position the popup above the text
            popup.style.left = (rect.left - 30) + 'px'; // Align arrow with text
            popup.style.top = (rect.top - popup.offsetHeight - 200) + 'px'; // Position above with 10px space
            
            // Show the popup
            popup.style.display = 'block';
        });
        
        dtiInterns.addEventListener('mouseleave', function(e) {
            // Hide the popup after a short delay (to allow moving to the popup)
            setTimeout(function() {
                if (!isMouseOverPopup) {
                    popup.style.display = 'none';
                }
            }, 100);
        });
        
        // Track if mouse is over the popup
        let isMouseOverPopup = false;
        
        popup.addEventListener('mouseenter', function() {
            isMouseOverPopup = true;
        });
        
        popup.addEventListener('mouseleave', function() {
            isMouseOverPopup = false;
            popup.style.display = 'none';
        });
    }
});
</script>
