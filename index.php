<?php
// Hubungkan ke database PostgreSQL via PDO
// Pastikan file db_config.php sudah tersedia dan berisi koneksi PDO
include 'config/database.php';

// ================== LOAD DATA DARI DATABASE ===================

// VISI
try {
    // Ambil baris dengan tipe 'Visi'
    $stmtVisi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'Visi' LIMIT 1");
    $stmtVisi->execute();
    $visiRow = $stmtVisi->fetch(PDO::FETCH_ASSOC);
    // Tambahkan filter 'Visi' agar lebih spesifik
    $visi = $visiRow ? $visiRow['deskripsi'] : "Visi belum tersedia di database."; 
} catch (Exception $e) {
    // Pesan error aman, tapi di sini tetap menampilkan pesan error asli untuk debugging sementara
    $visi = "Error mengambil visi: " . $e->getMessage();
}

// MISI
try {
    // Ambil baris dengan tipe 'Misi'
    $stmtMisi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'Misi' ORDER BY id");
    $stmtMisi->execute();
    $misiList = $stmtMisi->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $misiList = [];
}

// ================== FASILITAS & PERALATAN (DINAMIS) ===================
// Logika untuk mengambil data Fasilitas & Peralatan dari tabel 'fasilitas_peralatan'
try {
    $stmtFasilitas = $pdo->prepare("SELECT judul, deskripsi, ikon_fa FROM fasilitas_peralatan ORDER BY id");
    $stmtFasilitas->execute();
    $combinedList = $stmtFasilitas->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    // Jika ada error database (seperti tabel tidak ditemukan), $combinedList akan kosong
    $combinedList = [];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorium IVSS - POLINEMA</title>
    <link rel="stylesheet" href="assets/css/style_profil.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button> 
        <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button> 
    </header>

    <nav class="navbar" id="main-navbar">
        <ul>
            <li class="active"><a href="index.php">Beranda</a></li>
            
            <li><a href="#">Riset dan Penelitian</a></li>
            
            <li><a href="member.php">Member</a></li> 

            <li><a href="berita-pengumuman.php">Berita dan Pengumuman</a></li>
        </ul>
    </nav>

    <section class="hero-section">
        <h1>Intelligent Vision and Smart Systems Laboratory</h1>
    </section>

    <main class="main-content">

        <div class="logo-lab">
            <img src="assets/img/Logo-lab-IVSS-300x118.png" alt="Logo Laboratorium IVSS"> 
        </div>

        <div class="content-area">
            <h3>Selamat Datang di Laboratorium IVSS</h3>
            <p>
                Laboratorium Visi Cerdas dan Sistem Cerdas merupakan pusat riset dan pengembangan di bawah Jurusan Teknologi Informasi Politeknik Negeri Malang yang berfokus pada bidang intelligent vision, dan smart system. Laboratorium ini menjadi wadah bagi dosen dan mahasiswa untuk melakukan penelitian, pembelajaran, serta pelatihan dalam pengembangan sistem cerdas berbasis pengolahan citra dan kecerdasan buatan.

Penelitian di laboratorium ini mengintegrasikan computer vision, AI, dan IoT untuk menciptakan solusi inovatif yang mampu mengenali, menganalisis, serta merespon lingkungan secara mandiri.
            </p>
        </div>

        <section class="vision-mission-section">
            <div class="card vision-card">
                <h4 class="card-title">VISION</h4>
                <p><?= htmlspecialchars($visi) ?></p>
            </div>

            <div class="card mission-card">
                <h4 class="card-title">MISSION</h4>
                <ul>
                    <?php if (count($misiList) > 0): ?>
                        <?php foreach ($misiList as $m): ?>
                            <li><?= htmlspecialchars($m['deskripsi']) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Misi belum tersedia di database.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>

        <section class="info-section">
            <h3 class="section-title">Fasilitas & Peralatan Laboratorium IVIS</h3>

            <div class="grid-container">
                
                <?php if (count($combinedList) > 0): ?>
                    <?php foreach ($combinedList as $item): ?>
                        <div class="info-card">
                            <i class="<?= htmlspecialchars($item['ikon_fa'] ?? 'fa-solid fa-circle-info') ?> card-icon"></i>
                            <h4 class="card-title"><?= htmlspecialchars($item['judul']) ?></h4>
                            <p><?= htmlspecialchars($item['deskripsi']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-dark);">
                        Data Fasilitas dan Peralatan belum tersedia di database.
                    </div>
                <?php endif; ?>
                
            </div>
        </section>
        </main>

    <footer class="footer-polinema">
        <div class="footer-top">
            <div class="footer-identitas">
                <div class="logo-container-footer">
                    <img src="assets/img/logo_polinema.png" alt="Logo POLINEMA">
                    <div class="text-identitas-footer">
                        <h3>JURUSAN TEKNOLOGI INFORMASI</h3>
                        <h2>POLITEKNIK NEGERI MALANG</h2>
                    </div>
                </div>
                <div class="alamat-info">
                    <p>BLU POLITEKNIK NEGERI MALANG</p>
                    <p>Jl. Soekarno Hatta No.9, Lowokwaru, Kota Malang</p>
                </div>
            </div>

            <div class="social-media">
                <a href="#"><img src="assets/img/yt.png" alt="YouTube"></a>
                <a href="#"><img src="assets/img/ig.jpeg" alt="Instagram"></a>
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

    <script src="assets/js/script_main.js"></script> 
</body>
</html>