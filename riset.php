<?php
include 'config/database.php';

// Ambil semua data riset menggunakan PDO
$sql = "SELECT * FROM riset ORDER BY tanggal_mulai DESC";
$stmt = $pdo->query($sql);
$riset = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Riset - Laboratorium IVSS</title>
    <link rel="stylesheet" href="assets/css/style_profil.css">

    <style>
        .content-riset {
            padding: 30px;
            max-width: 1100px;
            margin: auto;
        }

        .judul-section {
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: bold;
            color: #003399;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .card-riset {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .card-riset h3 {
            margin-bottom: 10px;
            color: #003399;
        }

        .card-riset .deskripsi {
            margin-bottom: 15px;
            color: #444;
        }

        .card-riset .tanggal {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-link {
            display: inline-block;
            padding: 8px 14px;
            background: #003399;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-link:hover {
            background: #002277;
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
        <li class="active"><a href="riset.php">Riset dan Penelitian</a></li>
        <li><a href="#">Member</a></li>
        <li><a href="berita-pengumuman.php">Berita dan Pengumuman</a></li>
    </ul>
</nav>

<!-- ===================== MAIN CONTENT (DAFTAR RISET) ===================== -->

<main class="content-riset">
    <h2 class="judul-section">Daftar Riset Laboratorium</h2>

    <div class="card-container">
        <?php foreach ($riset as $row): ?>
            <div class="card-riset">
                <h3><?= $row['judul_riset']; ?></h3>

                <p class="deskripsi">
                    <?= $row['deskripsi']; ?>
                </p>

                <p class="tanggal">
                    <strong>Tanggal Mulai:</strong> 
                    <?= $row['tanggal_mulai'] ?: '–'; ?> <br>

                    <strong>Tanggal Selesai:</strong> 
                    <?= $row['tanggal_selesai'] ?: '–'; ?>
                </p>

                <?php if (!empty($row['link_riset'])): ?>
                    <a class="btn-link" href="<?= $row['link_riset']; ?>" target="_blank">
                        Lihat Detail Riset
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- ===================== FOOTER ===================== -->

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
            <a href="#"><img src="yt.png" alt="YouTube Icon"></a>
            <a href="#"><img src="ig.jpeg" alt="Instagram Icon"></a>
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
