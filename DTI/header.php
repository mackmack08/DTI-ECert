<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'DTI Dashboard'; ?></title>
    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style2.css?v=<?php echo time(); ?>">
    <!-- Dashboard Specific CSS -->
    <link rel="stylesheet" href="style3.css?v=<?php echo time(); ?>">
    <?php if (isset($additionalCSS)) echo $additionalCSS; ?>
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <!-- Breadcrumb navigation -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo isset($currentPage) ? $currentPage : 'Dashboard'; ?></li>
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
