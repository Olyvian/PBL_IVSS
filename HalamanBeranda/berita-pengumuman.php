<?php 
include "db_config.php";

$has_featured = false;
$news_list = [];

try {
    // Ambil berita terbaru sebagai featured
    $query_featured = $pdo->prepare("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 1");
    $query_featured->execute();
    $featured = $query_featured->fetch();

    if ($featured) {
        $has_featured = true;
        
        // Ambil berita lainnya
        $query_list = $pdo->prepare("SELECT * FROM berita WHERE id != :id_featured ORDER BY tanggal DESC");
        $query_list->execute(['id_featured' => $featured['id']]);
        $news_list = $query_list->fetchAll();
    } else {
        // Placeholder jika tidak ada berita (untuk mencegah 'Undefined Index' error di HTML)
        $featured = [
            'gambar' => 'image_793324.png', // Ganti dengan gambar placeholder Anda
            'judul' => 'Belum Ada Berita Terbaru',
            'tanggal' => date('Y-m-d'),
            'deskripsi' => 'Saat ini belum ada berita atau pengumuman yang tersedia di database.',
            'link' => '#'
        ];
    }
    
} catch (PDOException $e) {
    // Tangani error database yang fatal
    die("Error Database: Gagal memuat data berita. Mohon periksa koneksi atau skema tabel berita. Pesan error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita dan Pengumuman - Laboratorium IVSS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header-institusi">
    <div class="logo-container">
        <img src="logo_polinema.png" alt="Logo POLINEMA">
        <div class="text-identitas">
            <h3>Intelligent Vision and Smart System</h3>
            <h2>POLITEKNIK NEGERI MALANG</h2>
        </div>
    </div>
    <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button>
</header>

<nav class="navbar" id="main-navbar">
        <ul>
            <li class="active"><a href="index.php">Beranda</a></li>
            
            <li><a href="#">Riset dan Penelitian</a></li>
            
            <li><a href="#">Member</a></li> 

            <li><a href="berita-pengumuman.php">Berita dan Pengumuman</a></li>
        </ul>
    </nav>

<section class="hero-section">
    <h1>IVSS Laboratory News and Announcements</h1>
</section>

<main class="main-content">
    <h3 class="section-title">Berita dan Pengumuman - Laboratorium IVSS</h3>

    <div class="news-list-container">

        <div class="featured-news-card">
            <img src="<?= $featured['gambar'] ?>" alt="Gambar Berita Utama">
            <div class="featured-news-content">
                <h4><?= $featured['judul'] ?></h4>
                <span class="date-meta"><?= date("d F Y", strtotime($featured['tanggal'])) ?></span>
                <p><?= $featured['deskripsi'] ?></p>
                <a href="<?= $featured['link'] ?>">Baca Selengkapnya →</a>
            </div>
        </div>

        <?php if ($has_featured || !empty($news_list)):?>
        <hr class="section-divider">
        <?php endif; ?>

        <div class="news-grid">
        <?php if (empty($news_list) && $has_featured): ?>
            <p>Tidak ada berita lain selain berita utama.</p>
        <?php elseif (empty($news_list) && !$has_featured): ?>
            <p>Tidak ada berita atau pengumuman lain yang tersedia saat ini.</p>
        <?php endif; ?>

        <?php foreach ($news_list as $news): ?>
            <div class="news-item-grid">
                <img src="<?= $news['gambar'] ?>" alt="Thumbnail Berita">
                <div class="news-item-content">
                    <h4><?= $news['judul'] ?></h4>
                    <span class="date-meta"><?= date("d F Y", strtotime($news['tanggal'])) ?></span>
                    <p><?= $news['deskripsi'] ?></p>
                    <a href="<?= $news['link'] ?>">Baca Selengkapnya →</a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>
</main>

<footer class="footer-polinema">
        <div class="footer-top">
            <div class="footer-identitas">
                <div class="logo-container-footer">
                    <img src="logo_polinema.png" alt="Logo POLINEMA">
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