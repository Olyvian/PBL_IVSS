<?php
// dashboard_fasilitas.php

// 1. Panggil file config dan auth
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. Proteksi Halaman: Hanya ADMIN LAB yang boleh akses
redirectIfNotLoggedIn(['admin_lab']);

// 3. Set Judul Halaman dan Navigasi Aktif
$pageTitle = 'Manajemen Fasilitas';
$activePage = 'fasilitas'; // Pastikan sidebar Anda menangani active state ini

// Inisialisasi pesan
$error_message = null;
$success_message = null;
$fasilitas_to_edit = null;
$show_fasilitas_form = false;

// Tangani pesan dari sesi
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// 4. Logika DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM fasilitas_peralatan WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Data Fasilitas/Peralatan berhasil dihapus.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus data (DB Error): " . $e->getMessage();
    }
    header("Location: dashboard_fasilitas.php");
    exit();
}

// 5. Logika EDIT (Mengambil data untuk diisi ke form)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT id, jenis, judul, deskripsi, ikon_fa FROM fasilitas_peralatan WHERE id = ?");
        $stmt->execute([$id]);
        $fasilitas_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fasilitas_to_edit) {
            $show_fasilitas_form = true;
        } else {
            $error_message = "Data fasilitas yang ingin diubah tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error saat mengambil data untuk diedit: " . $e->getMessage();
    }
}

// 6. Logika CREATE & UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_update = isset($_POST['update_fasilitas']);
    $fasilitas_id = $is_update ? (int)$_POST['fasilitas_id'] : null;

    $jenis = trim($_POST['jenis']);
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $ikon_fa = trim($_POST['ikon_fa']);

    if (!empty($jenis) && !empty($judul)) {
        try {
            if ($is_update) {
                // UPDATE
                $sql = "UPDATE fasilitas_peralatan SET jenis = ?, judul = ?, deskripsi = ?, ikon_fa = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$jenis, $judul, $deskripsi, $ikon_fa, $fasilitas_id]);
                $action_message = "diperbarui";
            } else {
                // CREATE
                $sql = "INSERT INTO fasilitas_peralatan (jenis, judul, deskripsi, ikon_fa) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$jenis, $judul, $deskripsi, $ikon_fa]);
                $action_message = "ditambahkan";
            }
            
            $_SESSION['success_message'] = "Data **Fasilitas/Peralatan** berhasil {$action_message}.";
            header("Location: dashboard_fasilitas.php");
            exit();

        } catch (PDOException $e) {
            $error_message = "Gagal {$action_message} data (DB Error): " . $e->getMessage();
            // Pertahankan input user jika gagal
            if ($is_update) {
                $fasilitas_to_edit = array_merge(['id' => $fasilitas_id], $_POST);
                $show_fasilitas_form = true;
            } else {
                // Jika tambah baru gagal, form tetap terbuka dengan data terisi
                $show_fasilitas_form = true; 
            }
        }
    } else {
        $error_message = "Jenis dan Judul wajib diisi.";
        $show_fasilitas_form = true;
    }
}

// 7. Ambil Data Fasilitas untuk Tabel
try {
    $fasilitasList = $pdo->query("SELECT id, jenis, judul, deskripsi, ikon_fa FROM fasilitas_peralatan ORDER BY jenis, id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $fasilitasList = [];
    $error_message = "Gagal memuat data: " . $e->getMessage();
}

// 8. Panggil Sidebar
include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Manajemen Fasilitas</span>
        </div>
        <button class="btn btn-primary" id="btnToggleFasilitasForm">
            <?php echo $show_fasilitas_form ? 'Tutup Form' : 'Tambah Fasilitas'; ?>
        </button>
    </div>

    <div class="form-container" id="fasilitasFormContainer" style="display: <?php echo $show_fasilitas_form ? 'block' : 'none'; ?>;">
        <h4 id="fasilitasFormTitle">
            <?php echo $show_fasilitas_form ? '✏️ Ubah Data Fasilitas: ' . htmlspecialchars($fasilitas_to_edit['judul'] ?? '') : '➕ Tambah Fasilitas Baru'; ?>
        </h4>
        
        <form method="POST" action="dashboard_fasilitas.php" enctype="multipart/form-data" id="formFasilitasActual">
            <?php if ($show_fasilitas_form && $fasilitas_to_edit): ?>
                <input type="hidden" name="update_fasilitas" value="1">
                <input type="hidden" name="fasilitas_id" value="<?= htmlspecialchars($fasilitas_to_edit['id']) ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="jenis_fasilitas">Jenis:</label>
                <input type="text" class="form-control" id="jenis_fasilitas" name="jenis" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['jenis'] ?? $_POST['jenis'] ?? '') ?>" 
                    placeholder="Contoh: Hardware, Software, Ruangan" required>
            </div>
            
            <div class="form-group">
                <label for="judul_fasilitas">Judul Fasilitas/Peralatan:</label>
                <input type="text" class="form-control" id="judul_fasilitas" name="judul" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['judul'] ?? $_POST['judul'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="ikon_fa_fasilitas">Ikon Font Awesome (Opsional):</label>
                <input type="text" class="form-control" id="ikon_fa_fasilitas" name="ikon_fa" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['ikon_fa'] ?? $_POST['ikon_fa'] ?? '') ?>" 
                    placeholder="Contoh: fa-laptop">
                <small class="form-text text-muted">Isi dengan nama ikon Font Awesome 6 (misal: **fa-laptop**).</small>
            </div>

            <div class="form-group">
                <label for="deskripsi_fasilitas">Deskripsi:</label>
                <textarea class="form-control" id="deskripsi_fasilitas" name="deskripsi" rows="3" required><?= htmlspecialchars($fasilitas_to_edit['deskripsi'] ?? $_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
                <button type="button" class="btn btn-secondary" id="btnCancelFasilitas">Batal</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if (isset($error_message) && !$show_fasilitas_form): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (empty($fasilitasList)): ?>
            <div class="alert alert-info">Belum ada Fasilitas atau Peralatan yang tercatat.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="table-tipe-col">Jenis</th>
                            <th>Judul & Deskripsi</th> 
                            <th class="table-date-col">Ikon</th>
                            <th class="table-aksi-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fasilitasList as $f): ?>
                            <tr>
                                <td class="table-tipe-col"><?= htmlspecialchars($f['jenis']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($f['judul']) ?></strong>
                                    <br><small class="text-muted"><?= substr(htmlspecialchars($f['deskripsi']), 0, 80) ?>...</small>
                                </td>
                                <td class="table-date-col">
                                    <?php if (!empty($f['ikon_fa'])): ?>
                                        <i class="fa-solid <?= htmlspecialchars($f['ikon_fa']) ?>"></i>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="table-aksi-col">
                                    <a href="dashboard_fasilitas.php?action=edit&id=<?= $f['id'] ?>" class="btn-icon btn-edit" title="Ubah">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="dashboard_fasilitas.php?action=delete&id=<?= $f['id'] ?>" class="btn-icon btn-delete" title="Hapus" 
                                        onclick="return confirm('Yakin ingin menghapus fasilitas: <?= htmlspecialchars($f['judul']) ?>?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.getElementById('fasilitasFormContainer');
        const btnToggle = document.getElementById('btnToggleFasilitasForm');
        const btnCancel = document.getElementById('btnCancelFasilitas');
        const formTitle = document.getElementById('fasilitasFormTitle');
        const actualForm = document.getElementById('formFasilitasActual');
        
        const isCurrentlyEdit = <?php echo $show_fasilitas_form ? 'true' : 'false'; ?>;

        btnToggle.addEventListener('click', () => {
            if (isCurrentlyEdit) {
                // Jika sedang edit, tombol berfungsi sebagai "Batal" (redirect bersih)
                window.location.href = 'dashboard_fasilitas.php';
                return;
            }

            if (formContainer.style.display === 'block') {
                formContainer.style.display = 'none';
                btnToggle.innerText = 'Tambah Fasilitas';
            } else {
                formContainer.style.display = 'block';
                btnToggle.innerText = 'Tutup Form';
                formTitle.innerText = '➕ Tambah Fasilitas/Peralatan Baru';
                actualForm.reset(); // Bersihkan form
                window.scrollTo(0, 0);
            }
        });

        if (btnCancel) {
            btnCancel.addEventListener('click', () => {
                window.location.href = 'dashboard_fasilitas.php';
            });
        }
    });
</script>