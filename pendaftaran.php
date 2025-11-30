<?php
session_start();
require_once 'config/database.php';
include 'includes/header.php'; 

// 1. CEK LOGIN (Wajib Login karena tabel butuh user_id)
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Silakan login terlebih dahulu untuk mendaftar.'); window.location='login.php';</script>";
    exit;
}

$message = '';
$msgType = '';

// 2. LOGIKA PENYIMPANAN DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id      = $_SESSION['user_id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $motivasi     = trim($_POST['motivasi']);
    
    // Validasi & Upload CV
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $fileExt = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        
        // Hanya izinkan PDF
        if ($fileExt === 'pdf') {
            // Nama file unik: cv_USERID_TIMESTAMP.pdf
            $newFileName = 'cv_' . $user_id . '_' . time() . '.pdf';
            $uploadDir   = 'uploads/cv/';
            
            // Buat folder jika belum ada
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $uploadDir . $newFileName)) {
                // Simpan ke Database
                try {
                    $stmt = $pdo->prepare("INSERT INTO pendaftaran_magang (user_id, nama_lengkap, cv_file, motivasi) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $nama_lengkap, $newFileName, $motivasi]);
                    
                    $message = "Pendaftaran berhasil dikirim! Status: Pending.";
                    $msgType = "success";
                } catch (PDOException $e) {
                    $message = "Database Error: " . $e->getMessage();
                    $msgType = "danger";
                    unlink($uploadDir . $newFileName); // Hapus file jika DB gagal
                }
            } else {
                $message = "Gagal mengupload file ke server.";
                $msgType = "danger";
            }
        } else {
            $message = "Format file wajib PDF.";
            $msgType = "danger";
        }
    } else {
        $message = "Harap pilih file CV (PDF).";
        $msgType = "danger";
    }
}

// 3. INCLUDE HEADER UTAMA ANDA

?>

<style>
    /* Menggunakan variabel warna dari style_profil.css Anda */
    .form-wrapper {
        background: #fff; /* var(--card-bg) */
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* var(--shadow-content) */
        max-width: 800px;
        margin: 0 auto 50px auto;
        border: 1px solid #e0e0e0;
    }

    .form-title {
        color: #0D4C7C; /* var(--polinema-blue) */
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 20px;
        text-align: center;
        border-bottom: 3px solid #CC0000; /* var(--polinema-red) */
        display: inline-block;
        padding-bottom: 10px;
    }

    .form-group { margin-bottom: 20px; }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #34495E; /* var(--text-dark) */
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #0D4C7C;
        outline: none;
    }

    .btn-submit {
        background-color: #0D4C7C; /* var(--polinema-blue) */
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        width: 100%;
        font-size: 16px;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background-color: #CC0000; /* var(--polinema-red) */
    }

    /* Alert Styling */
    .msg-box { padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
    .msg-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .msg-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<section class="hero-section" style="height: 300px;">
    <h1>Pendaftaran Magang</h1>
</section>

<main class="main-content">
    
    <div style="text-align:center; margin-bottom:30px;">
        <h3 class="section-title">Formulir Pendaftaran</h3>
        <p>Bergabunglah bersama kami untuk riset dan inovasi masa depan.</p>
    </div>

    <div class="form-wrapper">
        
        <?php if ($message): ?>
            <div class="msg-box msg-<?= $msgType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" 
                       placeholder="Sesuai KTP / KTM" required 
                       value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Motivasi Bergabung</label>
                <textarea name="motivasi" class="form-control" rows="5" 
                          placeholder="Jelaskan skill Anda dan mengapa Anda ingin bergabung..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Upload CV (PDF)</label>
                <input type="file" name="cv_file" class="form-control" accept=".pdf" required>
                <small style="color:#666;">*Maksimal ukuran file disarankan di bawah 2MB.</small>
            </div>

            <button type="submit" class="btn-submit">Kirim Pendaftaran</button>

        </form>
    </div>

</main>

<?php 
// 4. INCLUDE FOOTER ANDA
include 'includes/footer.php'; 
?>