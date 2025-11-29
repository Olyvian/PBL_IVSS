<?php
// Selalu mulai session di paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil info user dari session
$username = $_SESSION['username'] ?? 'User';
// Ambil role
$role_name = $_SESSION['role'] ?? 'viewer'; 

// Variabel $pageTitle dan $activePage di-set oleh halaman pemanggil
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - Lab IVSS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="sidebar-light">
        
        <div class="sidebar-brand-light" style="text-align: center; padding: 20px 0;">
            <img src="../assets/img/logo_ivss_tanpa_text.png" alt="Logo Lab IVSS" style="max-width: 100%; height: auto; max-height: 100px; object-fit: contain;">
        </div>
        <ul class="nav-menu-light">
            
            <?php if ($role_name === 'admin_lab' || $role_name === 'admin_berita'): ?>
            <li>
                <a href="dashboard.php" 
                   class="nav-link-light <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role_name === 'admin_lab'): ?>
            <li>
                <a href="dashboard_member.php" 
                   class="nav-link-light <?php echo ($activePage === 'member') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span>Member</span>
                </a>
            </li>
            <li>
                <a href="dashboard_riset.php" 
                   class="nav-link-light <?php echo ($activePage === 'riset') ? 'active' : ''; ?>">
                    <i class="bi bi-journal-check"></i>
                    <span>Riset</span>
                </a>
            </li>
            <li>
                <a href="dashboard_fasilitas.php" 
                   class="nav-link-light <?php echo ($activePage === 'fasilitas') ? 'active' : ''; ?>">
                    <i class="bi bi-tools"></i>
                    <span>Fasilitas</span>
                </a>
            </li>
            <li>
                <a href="dashboard_pendaftaran.php" 
                   class="nav-link-light <?php echo ($activePage === 'pendaftaran') ? 'active' : ''; ?>">
                    <i class="bi bi-person-check"></i>
                    <span>Pendaftaran</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role_name === 'admin_berita'): ?>
            <li>
                <a href="dashboard_berita.php" 
                   class="nav-link-light <?php echo ($activePage === 'berita') ? 'active' : ''; ?>">
                    <i class="bi bi-newspaper"></i>
                    <span>Berita</span>
                </a>
            </li>
            <?php endif; ?>

            </ul>
        <div class="sidebar-footer-light">
            <a href="../login/logout.php" class="nav-link-light logout">
                <i class="bi bi-box-arrow-left"></i>
                <span>Log Out</span>
            </a>
        </div>
    </div>

    <div class="main-content-light">
        <header class="top-bar-light">
            <h2 class="page-title-light"><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
            
            <div class="user-info-light">
                <div class="user-details-light">
                    <span class="user-name-light"><?php echo htmlspecialchars($username); ?></span>
                    <span class="user-role-light"><?php echo htmlspecialchars($role_name); ?></span>
                </div>
                <div class="user-avatar-light">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </header>

        <main class="container-fluid pt-4">