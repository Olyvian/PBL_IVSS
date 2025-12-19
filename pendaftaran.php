<?php
session_start();
require_once 'config/database.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu untuk mendaftar.'); window.location='login.php';</script>";
    exit;
}

$message = '';
$msgType = '';

// Logika Penyimpanan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id      = $_SESSION['user_id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $motivasi     = trim($_POST['motivasi']);
    
    // Validasi & Upload CV
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $fileExt = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        
        if ($fileExt === 'pdf') {
            $newFileName = 'cv_' . $user_id . '_' . time() . '.pdf';
            $uploadDir   = 'uploads/cv/';
            
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $uploadDir . $newFileName)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO pendaftaran_magang (user_id, nama_lengkap, cv_file, motivasi) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $nama_lengkap, $newFileName, $motivasi]);
                    
                    $message = "✅ Pendaftaran berhasil dikirim! Status: Pending.";
                    $msgType = "success";
                } catch (PDOException $e) {
                    $message = "❌ Database Error: " . htmlspecialchars($e->getMessage());
                    $msgType = "danger";
                    unlink($uploadDir . $newFileName); // Hapus file jika DB gagal
                }
            } else {
                $message = "❌ Gagal mengupload file ke server.";
                $msgType = "danger";
            }
        } else {
            $message = "❌ Format file wajib PDF.";
            $msgType = "danger";
        }
    } else {
        $message = "❌ Harap pilih file CV (PDF).";
        $msgType = "danger";
    }
}

include 'includes/header.php'; 
?>

<style>
    :root {
        --polinema-blue: #072a52;
        --polinema-orange: #f47f20;
    }

    .hero-section {
        background: linear-gradient(rgba(7, 42, 82, 0.9), rgba(7, 42, 82, 0.9)), url('assets/img/g4.png');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    .form-wrapper {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 0 auto 50px auto;
        border-top: 5px solid var(--polinema-orange);
    }

    .form-label {
        font-weight: 600;
        color: var(--polinema-blue);
    }

    .btn-custom {
        background-color: var(--polinema-blue);
        color: white;
        padding: 12px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-custom:hover {
        background-color: var(--polinema-orange);
        color: white;
    }
</style>

<section class="hero-section">
    <div class="container">
        <h1>Pendaftaran Magang</h1>
        <p class="mb-0">Bergabunglah bersama kami untuk riset dan inovasi masa depan.</p>
    </div>
</section>

<main class="container">
    <div class="form-wrapper">
        <h3 class="text-center mb-4" style="color: var(--polinema-blue); font-weight:700;">Formulir Pendaftaran</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?> text-center" role="alert">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" 
                       placeholder="Sesuai KTP / KTM" required 
                       value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Motivasi Bergabung</label>
                <textarea name="motivasi" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Upload CV (PDF)</label>
                <input type="file" name="cv_file" class="form-control" accept=".pdf" required>
                <div class="form-text text-muted">*Format wajib PDF, maksimal ukuran file disarankan di bawah 2MB.</div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-custom">Kirim Pendaftaran</button>
            </div>

        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>