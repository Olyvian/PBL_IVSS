<?php
// 1. Koneksi Database
include "config/database.php";

// 2. Cek ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: berita-pengumuman.php");
    exit;
}

$id = $_GET['id'];

try {
    // 3. Ambil Data Berita berdasarkan ID
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    // Jika berita tidak ditemukan
    if (!$news) {
        die("<div style='text-align:center; padding:50px; font-family:sans-serif;'><h3>Berita tidak ditemukan.</h3><a href='berita-pengumuman.php'>Kembali</a></div>");
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['judul']) ?> - Laboratorium IVSS</title>
    
    <link rel="stylesheet" href="assets/css/style_profil.css">
    
    <style>
        /* Container Detail menyerupai Featured Card tapi vertikal */
        .detail-wrapper {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Shadow sama dengan featured card */
            text-align: left;
            margin-bottom: 30px;
        }

        .detail-header {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 20px;
        }

        .detail-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--polinema-blue);
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .detail-meta {
            font-size: 14px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-image-container {
            width: 100%;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .detail-image {
            width: 100%;
            height: auto;
            max-height: 500px; /* Batasi tinggi agar tidak memenuhi layar */
            object-fit: cover;
            display: block;
        }

        .detail-content {
            font-size: 16px;
            line-height: 1.8;
            color: var(--text-dark);
            text-align: justify; /* Teks rata kanan-kiri agar rapi */
        }
        
        /* Jarak antar paragraf */
        .detail-content p {
            margin-bottom: 20px; 
        }

        /* Tombol Kembali */
        .btn-back-container {
            margin-top: 40px;
            text-align: left;
        }

        .btn-back {
            display: inline-block;
            background-color: var(--polinema-blue);
            color: var(--text-light);
            padding: 12px 25px;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: var(--polinema-red);
            color: var(--text-light);
        }
    </style>
</head>
<body>

<header class="header-institusi">
    <div class="logo-container">
        <img src="assets/img/logo_polinema.png" alt="Logo POLINEMA">
        <div class="text-identitas">
            <h3>Intelligent Vision and Smart System</h3>
            <h2>POLITEKNIK NEGERI MALANG</h2>
        </div>
    </div>
    <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button>
</header>

<nav class="navbar" id="main-navbar">
    <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="#">Riset dan Penelitian</a></li>
        <li><a href="#">Member</a></li> 
        <li class="active"><a href="berita-pengumuman.php">Berita dan Pengumuman</a></li>
    </ul>
</nav>

<main class="main-content">
    
    <div class="news-list-container">
        
        <div class="detail-wrapper">
            
            <div class="detail-header">
                <h1 class="detail-title"><?= htmlspecialchars($news['judul']) ?></h1>
                <div class="detail-meta">
                    <span>Diposting pada <?= date("d F Y", strtotime($news['created_at'])) ?></span>
                </div>
            </div>

            <?php if (!empty($news['gambar_header'])): ?>
            <div class="detail-image-container">
                <img src="uploads/news/<?= htmlspecialchars($news['gambar_header']) ?>" alt="Gambar Berita" class="detail-image">
            </div>
            <?php endif; ?>

            <div class="detail-content">
                <?= nl2br(htmlspecialchars($news['isi'])) ?>
            </div>

            <div class="btn-back-container">
                <a href="berita-pengumuman.php" style="color : var(--polinema-red); font-weight: 600">← Kembali ke Daftar Berita</a>
            </div>

        </div> 
        </div>
</main>

<footer class="footer-polinema">
    <div class="footer-top">
        <div class="footer-identitas">
            <div class="logo-container-footer">
                <img src="assets/img/logo_polinema.png" alt="Logo POLINEMA">
                <div class="text-identitas-footer">
                    <h3>JURUSAN TEKNOLOGI INFORMASI</h3>
                    <h2>POLITEKNIK NEGERI MALANG</h2>
                    <h3>Jl. Soekarno Hatta No.9, Lowokwaru, Kota Malang</h3>
                </div>
            </div>
        </div>
        <div class="social-media">
            <a href="#"><img src="HalamanBeranda/yt.png" alt="YouTube Icon"></a>
            <a href="#"><img src="HalamanBeranda/ig.jpeg" alt="Instagram Icon"></a>
        </div>
    </div>
    <div class="footer-bottom-menu">
        <div class="menu-group">
            <h4>Tentang JTI</h4>
            <ul>
                <li><a href="#">Sejarah</a></li>
                <li><a href="#">Visi, Misi & Tujuan</a></li>
                <li><a href="#">Struktur Organisasi</a></li>
                <li><a href="#">Tenaga Pengajar</a></li>
                <li><a href="#">Tenaga Kependidikan</a></li>
                <li><a href="#">Sarana & Prasarana</a></li>
            </ul>
        </div>
        <div class="menu-group">
            <h4>Website Polinema Lainnya</h4>
            <ul>
                <li><a href="http://polinema.ac.id">Polinema.ac.id</a></li>
            </ul>
        </div>
    </div>
</footer>

<script src="script.js"></script> 
</body>
</html>