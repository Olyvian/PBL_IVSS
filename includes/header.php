<?php
// --- Bagian Logic Session ---
// Cek status session, jalankan jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menentukan Title Halaman secara dinamis
// Jika halaman induk tidak mendefinisikan $page_title, gunakan default
$title = isset($page_title) ? $page_title : 'Laboratorium IVSS - POLINEMA';

// Logic Username
$username_display = 'User';
if (isset($_SESSION['user_id'])) {
    // Menggunakan operator coalescing null (??) untuk keamanan
    $username_display = htmlspecialchars($_SESSION['username'] ?? 'User');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    
    <link rel="stylesheet" href="assets/css/style_profil.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* --- Bagian CSS Navbar & Header (Diambil dari beranda.php agar konsisten) --- */
        
        .header-institusi {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* Pastikan padding kanan konsisten */
            padding-right: 20px; 
        }
        .logout-container {
            display: flex;
            align-items: center;
        }
        .user-greeting {
            color: #fff; /* Diubah menjadi putih sesuai permintaan */
            margin-right: 15px;
            font-size: 0.9rem;
        }

        /* Navbar Centering */
        .navbar {
            display: flex;
            justify-content: center; 
            background-color: #0d364a; 
        }
        .navbar ul {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0; 
            list-style: none;
            margin: 0;
        }
        .navbar li {
            padding: 0 15px;
            position: relative;
        }
        
        .navbar li a {
            color: #fff;
            text-decoration: none;
            padding: 15px 0;
            display: block; 
            transition: border-bottom 0.2s;
        }

        .navbar li a:hover,
        /* Tambahkan style untuk kelas 'active' */

        .navbar li.has-dropdown > a {
            padding-right: 20px;
        }

        /* --- Bagian CSS Tambahan untuk Tombol Login/Logout Estetik --- */
        .logout-container a {
            padding: 8px 15px;
            border-radius: 5px; /* Sudut sedikit membulat */
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.1s ease;
            text-align: center;
            text-decoration: none;
        }

        /* Style untuk Tombol Logout (Warna Merah Kontras) */
        .logout-container .btn-danger {
            background-color: #dc3545; /* Merah cerah */
            border: 1px solid #dc3545;
        }
        .logout-container .btn-danger:hover {
            background-color: #c82333; /* Merah sedikit lebih gelap saat hover */
            border-color: #bd2130;
        }
        
        /* Style untuk Tombol Login (Warna Primary Biru Kontras) */
        .logout-container .service-btn.btn-filled {
            color: #fff; /* Teks putih */
            background-color: #007bff; /* Biru Primary */
            border: 1px solid #007bff;
        }
        .logout-container .service-btn.btn-filled:hover {
            background-color: #0056b3; /* Biru sedikit lebih gelap saat hover */
            border-color: #004085;
        }
        
        /* CSS Umum Tambahan */
        .main-content {
            padding-top: 0;
        }
    </style>
</head>
<body>

    <header class="header-institusi">
        <div class="logo-container">
            <img src="assets/img/logo_polinema.png" alt="Logo POLINEMA">
            <div class="text-identitas">
                <h3>Intelligent Vision and Smart Systems</h3>
                <h2>POLITEKNIK NEGERI MALANG</h2>
            </div>
        </div>
        <div class="logout-container" style="margin-left: auto;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                // Mengambil nama pengguna untuk ditampilkan
                $username = htmlspecialchars($_SESSION['username'] ?? 'User');
                ?>
                <span class="user-greeting">Haloo, <?php echo $username; ?></span>
                <a class="btn-danger" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="service-btn btn-filled" href="login.php" style="text-decoration: none;">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="navbar" id="main-navbar">
        <ul>
            <li class="active"><a href="index.php">Beranda</a></li>
            
            <li><a href="riset.php">Riset dan Penelitian</a></li>
            
            <li><a href="member.php">Member</a></li> 

            <li><a href="berita-pengumuman.php">Berita dan Pengumuman</a></li>
        </ul>
    </nav>