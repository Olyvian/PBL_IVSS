<?php
// Selalu mulai session di paling atas
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil info user dari session (diasumsikan di-set saat login oleh auth.php)
$username = $_SESSION['username'] ?? 'User';
$role_name = $_SESSION['role'] ?? 'Admin'; // Menggunakan 'role' dari kode Anda

// Variabel $pageTitle dan $activePage harus di-set OLEH HALAMAN
// yang memanggil header.php ini.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul halaman dinamis -->
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - Lab IVSS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons (Untuk Sidebar) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome (Untuk ikon stat card & ikon pensil/hapus di tabel) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS (style.css) -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Sidebar (Versi Cerah) -->
    <div class="sidebar-light">
        <h3 class="sidebar-brand-light">Lab IVSS</h3>
        <ul class="nav-menu-light">
            <!-- Link Dashboard Utama -->
            <li>
                <a href="dashboard.php" 
                   class="nav-link-light <?php echo ($activePage === 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Link Member -->
            <li>
                <a href="dashboard_member.php" 
                   class="nav-link-light <?php echo ($activePage === 'member') ? 'active' : ''; ?>">
                    <i class="bi bi-people"></i>
                    <span>Member</span>
                </a>
            </li>
            
            <!-- Link Riset -->
            <li>
                <a href="dashboard_riset.php" 
                   class="nav-link-light <?php echo ($activePage === 'riset') ? 'active' : ''; ?>">
                    <i class="bi bi-journal-check"></i> 
                    <span>Riset</span>
                </a>
            </li>

            <!-- Link Berita -->
            <li>
                <a href="dashboard_berita.php" 
                   class="nav-link-light <?php echo ($activePage === 'berita') ? 'active' : ''; ?>">
                    <i class="bi bi-newspaper"></i>
                    <span>Berita</span>
                </a>
            </li>
            <!-- Link Pendaftaran (BARU) -->
            <li>
                <a href="dashboard_pendaftaran.php" 
                   class="nav-link-light <?php echo ($activePage === 'pendaftaran') ? 'active' : ''; ?>">
                    <i class="bi bi-person-check"></i>
                    <span>Pendaftaran</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer-light">
            <!-- Link Logout -->
            <a href="../logout.php" class="nav-link-light logout">
                <i class="bi bi-box-arrow-left"></i>
                <span>Log Out</span>
            </a>
        </div>
    </div>

    <!-- Konten Utama -->
    <div class="main-content-light">
        <!-- Top Bar (Header) -->
        <header class="top-bar-light">
            <!-- Judul halaman dinamis -->
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

        <!-- Konten Halaman (dibuka di sini, ditutup di footer.php) -->
        <main class="container-fluid pt-4">