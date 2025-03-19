<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <!-- Link to Bootstrap CSS for basic styling and responsiveness -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Link to Bootstrap Icons for icon usage -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet"> 
    <!-- Link to custom stylesheet -->
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <h4>DEPARTMENT OF TRADE AND INDUSTRY - CEBU PROVINCIAL OFFICE</h4>
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
                <a class="nav-link" href="#">
                    <span class="description">LOGOUT</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <button class="btn btn-primary fs-3" id="sidebarToggle">
            <i class="bi bi-list"></i> <!-- Hamburger icon -->
        </button>
        <div class="mini-rectangle"></div>
        <nav class="nav flex-column">
            <!-- Sidebar links with icons and descriptions -->
            <a class="nav-link custom-btn" href="#"> <!-- Custom button with new styling -->
                <span class="icon">
                    <i class="bi bi-bar-chart-line"></i>
                </span>
                <span class="description">Dashboard</span>
            </a>
            <a class="nav-link" href="#">
                <span class="icon">
                    <i class="bi bi-person"></i>
                </span>
                <span class="description">Client Management</span>
            </a>
            <a class="nav-link" href="#">
                <span class="icon">
                    <i class="bi bi-award"></i>
                </span>
                <span class="description">Certificate Management</span>
            </a>
            <a class="nav-link" href="#">
                <span class="icon">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                </span>
                <span class="description">Sheets</span>
            </a>
        </nav>
    </div>

    <!-- Bootstrap JS for functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle the sidebar when the hamburger icon is clicked
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
