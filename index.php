<?php
include 'config/database.php';
include 'includes/header.php';

// Ambil Data Visi
try {
    $stmtVisi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'visi' ORDER BY konten_id ASC LIMIT 1");
    $stmtVisi->execute();
    $visi = $stmtVisi->fetchColumn() ?: "Visi belum tersedia di database."; 
} catch (Exception $e) {
    $visi = "Error mengambil visi.";
}

// Ambil Data Misi
try {
    $stmtMisi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'misi' ORDER BY konten_id ASC");
    $stmtMisi->execute();
    $misiList = $stmtMisi->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $misiList = [];
}

// Ambil Data Fasilitas
try {
    $stmtFasilitas = $pdo->prepare("SELECT judul, deskripsi, gambar FROM fasilitas_peralatan ORDER BY id");
    $stmtFasilitas->execute();
    $combinedList = $stmtFasilitas->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
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

    <section class="hero-section">
        <h1>Laboratorium Intelligent Vision and Smart Systems</h1>
    </section>

    <main class="main-content">

        <div class="logo-lab">
            <img src="assets/img/logo_ivss_tanpa_text.png" alt="Logo Laboratorium IVSS"> 
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
                <h4 class="card-title">VISI</h4>
                <p><?= htmlspecialchars($visi) ?></p>
            </div>

            <div class="card mission-card">
                <h4 class="card-title">MISI</h4>
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
            <h3 class="section-title">Fasilitas & Peralatan Laboratorium IVSS</h3>

            <div class="grid-container">
                
                <?php if (count($combinedList) > 0): ?>
                    <?php foreach ($combinedList as $item): ?>
                        <div class="info-card">
                            <?php 
                                $imgSrc = !empty($item['gambar']) 
                                          ? 'uploads/fasilitas/' . $item['gambar'] 
                                          : 'assets/img/placeholder_fasilitas.png';
                            ?>
                            
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($item['judul']) ?>" class="fasilitas-img">
                            
                            <h4 class="card-title"><?= htmlspecialchars($item['judul']) ?></h4>
                            <p><?= htmlspecialchars($item['deskripsi']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; color: var(--text-dark); padding: 20px;">
                        Data Fasilitas dan Peralatan belum tersedia di database.
                    </div>
                <?php endif; ?>
                
            </div>
        </section>
        
    </main>

    <script src="assets/js/script_main.js"></script> 
</body>
</html>
<?php
include 'includes/footer.php'; 
?>