<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
// redirectIfNotLoggedIn(['admin_lab']); dimatikan buat test desain

// Set judul dan halaman aktif
$pageTitle = 'Member';
$activePage = 'member';

// Panggil sidebar
include_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Konten Halaman -->
<div class="card">
    <div class="card-body" style="padding: 2rem;">
        <h4>Halaman Manajemen Member</h4>
        <p>Konten untuk mengelola member dan riset akan ada di sini.</p>
    </div>
</div>