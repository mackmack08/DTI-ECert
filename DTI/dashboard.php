<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header class="header">
        <h3>DEPARTMENT OF TRADE AND INDUSTRY - CEBU PROVINCIAL OFFICE</h3>
        <div class="nav-right">
            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenu" aria-expanded="false" aria-controls="submenu">
                <span class="icon">
                    <i class="bi bi-person-circle"></i>
                </span>
            </a>
            <div class="sub-menu collapse" id="submenu">
                <a class="nav-link" href="#">
                    <span class="icon">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    <span class="description">Administrator</span>
                </a>
        
                <a class="nav-link" href="#">
                    <span class="description">Logout</span>
                </a>
            </div>
        </div>
    </header>

    <div class="sidebar">
        <button class="btn btn-primary fs-3" style="margin-right: -20px;" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <nav class="nav flex-column">
            <a class="nav-link" href="#">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
