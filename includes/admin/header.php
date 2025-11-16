<?php
// Fungsi helper untuk memformat nama role
function formatRoleName($role) {
    switch ($role) {
        case 'admin_news': return 'Admin Berita';
        case 'admin_lab': return 'Admin Lab';
        case 'view': return 'Viewer';
        default: return ucfirst($role);
    }
}

// Ambil data dari session yang sudah disiapkan di 'dashboard_admin_news.php'
$username = $_SESSION['username'] ?? 'Guest';
$role_key = $_SESSION['role'] ?? 'guest';
$role_name = formatRoleName($role_key);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Berita</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="main-container">
        
        <?php include_once __DIR__ . '/sidebar.php'; ?>

        <main class="content">
            
            <header class="content-header">
                <div class="header-title">
                    <h2>Berita</h2>
                </div>
                <div class="user-info">
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($username) ?></span>
                        <span class="user-role"><?= htmlspecialchars($role_name) ?></span>
                    </div>
                    <div class="user-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>
                </div>
            </header>

            <section class="content-body"></section>