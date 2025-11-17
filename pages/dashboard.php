<?php
// 1. Panggil file config dan auth Anda
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Proteksi Halaman (sesuaikan role jika perlu)
// redirectIfNotLoggedIn(['admin_news', 'admin_lab']); dimatikan

// 3. (FLEKSIBEL) Set Judul Halaman dan Navigasi Aktif
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// 4. (FLEKSIBEL) Logika Halaman Ini: Hitung semua data dari DB baru
try {
    $totalBerita = $pdo->query("SELECT count(id) FROM berita")->fetchColumn();
    $totalMember = $pdo->query("SELECT count(id) FROM anggota_lab")->fetchColumn(); 
    $totalRiset = $pdo->query("SELECT count(id) FROM riset")->fetchColumn(); 
    $totalPendaftaran = $pdo->query("SELECT count(id) FROM pendaftaran_magang WHERE status = 'pending'")->fetchColumn(); 

} catch (PDOException $e) {
    // Jika tabel belum ada, anggap 0
    $totalBerita = $totalBerita ?? 0;
    $totalMember = $totalMember ?? 0;
    $totalRiset = $totalRiset ?? 0;
    $totalPendaftaran = $totalPendaftaran ?? 0;
}

// 5. Panggil Header
include_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- 6. (FLEKSIBEL) Konten Halaman Ini: Rangkuman Dashboard -->
<div class="dashboard-grid">
    
    <!-- Kartu Total Berita -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Berita</span>
            <span class="stat-number"><?php echo $totalBerita; ?></span>
        </div>
    </div>
    
    <!-- Kartu Total Member -->
    <div class="stat-card">
        <div class="stat-icon" style="color: #198754; background-color: #e8f3ee;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Member</span>
            <span class="stat-number"><?php echo $totalMember; ?></span>
        </div>
    </div>

    <!-- Kartu Total Riset -->
    <div class="stat-card">
        <div class="stat-icon" style="color: #ffc107; background-color: #fff8e6;">
            <i class="fa-solid fa-flask"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Riset</span>
            <span class="stat-number"><?php echo $totalRiset; ?></span>
        </div>
    </div>

    <!-- Kartu Pendaftaran Pending (BARU) -->
    <div class="stat-card">
        <div class="stat-icon" style="color: #6f42c1; background-color: #f1eef8;">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Pendaftaran Pending</span>
            <span class="stat-number"><?php echo $totalPendaftaran; ?></span>
        </div>
    </div>
</div>

<!-- Pesan Selamat Datang -->
<div class="card">
    <div class="card-body" style="padding: 2rem;">
        <h4>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</h4>
        <p>Anda login sebagai <?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?>. Gunakan menu di sebelah kiri untuk mengelola konten website.</p>
    </div>
</div>