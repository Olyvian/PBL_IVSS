
<?php 
include "config/database.php";
include 'includes/header.php'; 

$has_featured = false;
$news_list = [];
$featured = [];

try {
    // 2. Query untuk Berita Utama (Featured) - Ambil 1 berita terbaru
    $query_featured = $pdo->prepare("SELECT * FROM berita ORDER BY created_at DESC LIMIT 1");
    $query_featured->execute();
    $row_featured = $query_featured->fetch();

    if ($row_featured) {
        $has_featured = true;

        // 3. Mapping Data Database -> Variabel HTML
        // Kita siapkan data agar mudah dipanggil di HTML bawah
        $featured = [
            'judul'      => $row_featured['judul'], 
            'created_at' => $row_featured['created_at'],
            // Potong isi berita jadi pendek (250 karakter) untuk isi
            'isi'  => substr(strip_tags($row_featured['isi']), 0, 250) . '...', 
            // Cek apakah ada gambar header
            'gambar'     => !empty($row_featured['gambar_header']) 
                            ? 'uploads/news/' . $row_featured['gambar_header'] 
                            : 'assets/img/placeholder.png', 
            // Link menuju halaman detail membawa ID
            'link'       => 'detail_berita.php?id=' . $row_featured['id'] 
        ];
        
        // 4. Query untuk Berita Lainnya (Grid) - Kecuali yang sudah jadi featured
        $query_list = $pdo->prepare("SELECT * FROM berita WHERE id != :id_featured ORDER BY created_at DESC");
        $query_list->execute(['id_featured' => $row_featured['id']]);
        $rows_list = $query_list->fetchAll();

        // Mapping data berita lainnya
        foreach($rows_list as $row) {
            $news_list[] = [
                'judul'      => $row['judul'],
                'created_at' => $row['created_at'],
                'isi'  => substr(strip_tags($row['isi']), 0, 150) . '...',
                'gambar'     => !empty($row['gambar_header']) 
                                ? 'uploads/news/' . $row['gambar_header'] 
                                : 'assets/img/placeholder.png',
                'link'       => 'detail_berita.php?id=' . $row['id']
            ];
        }

    } else {
        // Data Dummy jika Database Kosong
        $featured = [
            'gambar'     => '../assets/img/no-news.png', 
            'judul'      => 'Belum Ada Berita Terbaru',
            'created_at' => date('Y-m-d'),
            'isi'  => 'Saat ini belum ada berita atau pengumuman yang tersedia di database.',
            'link'       => '#'
        ];
    }
    
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita dan Pengumuman - Laboratorium IVSS</title>
    <link rel="stylesheet" href="assets/css/style_profil.css">
</head>
<body>

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
                <span class="date-meta"><?= date("d F Y", strtotime($featured['created_at'])) ?></span>
                <p><?= $featured['isi'] ?></p>
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
                    <span class="date-meta"><?= date("d F Y", strtotime($news['created_at'])) ?></span>
                    <p><?= $news['isi'] ?></p>
                    <a href="<?= $news['link'] ?>">Baca Selengkapnya →</a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>
</main>
    <script src="script.js"></script> 
</body>
</html>
<?php
include 'includes/footer.php'; 
?>