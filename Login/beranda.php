<?php
$page_title = 'Laboratorium IVSS - POLINEMA';

require_once '../includes/header.php';
require_once '../config/database.php';

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

// Inisialisasi variabel untuk data dari DB
$visi = "Visi belum tersedia di database."; // Default value jika data tidak ditemukan
$misiList = []; 
$combinedList = []; 

// TAMBAHKAN: Logika Pengambilan Data dari Database
try {
    // Ambil Visi (tipe = 'visi', ambil yang pertama)
    $stmt_visi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'visi' ORDER BY konten_id ASC LIMIT 1");
    $stmt_visi->execute();
    // Jika Visi ditemukan, timpa nilai default
    if ($fetched_visi = $stmt_visi->fetchColumn()) {
        $visi = $fetched_visi;
    }

    // Ambil Misi (tipe = 'misi', diurutkan berdasarkan konten_id)
    $stmt_misi = $pdo->prepare("SELECT deskripsi FROM visi_misi WHERE tipe = 'misi' ORDER BY konten_id ASC");
    $stmt_misi->execute();
    $misiList = $stmt_misi->fetchAll(PDO::FETCH_ASSOC);

    // Ambil Fasilitas & Peralatan
    $stmt_fasilitas = $pdo->prepare("SELECT jenis, judul, deskripsi, ikon_fa FROM fasilitas_peralatan ORDER BY jenis, id ASC");
    $stmt_fasilitas->execute();
    $combinedList = $stmt_fasilitas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Opsional: Log error ke server, dan tampilkan pesan umum ke pengguna jika perlu
    // error_log("Database error in beranda.php: " . $e->getMessage());
    // Biarkan variabel tetap pada nilai default
}
?>

    <?php if (!empty($success_message)): ?>
        <div class="container">
            <div class="alert alert-success text-center mt-4" role="alert">
                <?php echo $success_message; ?>
            </div>
        </div>
    <?php endif; ?>

    <section class="hero-section">
        <h1>Intelligent Vision and Smart Systems Laboratory</h1>
    </section>

    <main class="main-content"> <div class="logo-lab">
            <img src="../assets/img/logo_ivss_tanpa_text.png" alt="Logo Laboratorium IVSS"> 
        </div>

        <div class="content-area">
            <h3>Selamat Datang di Laboratorium IVSS</h3>
            <p>
                Laboratorium Intelligent Vision and Intelligent Systems (IVSS) berfokus pada pengembangan sistem cerdas 
                yang melibatkan pengolahan citra, pengenalan pola, dan aplikasi visi komputer untuk berbagai solusi industri dan pendidikan. 
                Kami mendukung penelitian mahasiswa dan dosen dalam bidang Artificial Intelligence dan Smart Systems.
            </p>
            <p>
                Silakan jelajahi menu di atas untuk mengetahui lebih lanjut tentang fasilitas, proyek penelitian, dan layanan kami.
            </p>
        </div>

        <section class="info-section service-section">
            <div class="service-header">
                <div class="service-buttons">
                    <a href="#" class="service-btn btn-outline">SOP Praktikum</a>
                    <a href="#" class="service-btn btn-filled">Form Permohonan Layanan</a>
                </div>
            </div>
        </section>

        <div class="content-area">
            <h3>Laboratorium Visi Cerdas dan Sistem Cerdas</h3>
            <p>
                Laboratorium Visi Cerdas dan Sistem Cerdas merupakan pusat riset dan pengembangan di bawah Jurusan Teknologi Informasi Politeknik Negeri Malang yang berfokus pada bidang intelligent vision, dan smart system. Laboratorium ini menjadi wadah bagi dosen dan mahasiswa untuk melakukan penelitian, pembelajaran, serta pelatihan dalam pengembangan sistem cerdas berbasis pengolahan citra dan kecerdasan buatan.
            </p>
            <p>
                Penelitian di laboratorium ini mengintegrasikan computer vision, AI, dan IoT untuk menciptakan solusi inovatif yang mampu mengenali, menganalisis, serta merespon lingkungan secara mandiri.
            </p>
        </div>

        <section class="vision-mission-section">
            <div class="card vision-card">
                <h4 class="card-title">VISI</h4>
                <p><?= htmlspecialchars($visi) ?></p>
                <?php /* Hapus placeholder lama */ ?>
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
<?php require_once '../includes/footer.php'; ?>