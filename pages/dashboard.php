<?php
// 1. Panggil file config dan auth Anda
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Direktori tempat menyimpan file gambar fasilitas. Ini tidak lagi digunakan, tapi dipertahankan
// untuk menjaga struktur kode asli agar tidak terlalu banyak diubah, meskipun file upload-nya dihapus.
// Logika upload/manipulasi gambar akan dihapus/dilewati.
$uploadDir = __DIR__ . '/../uploads/fasilitas/';
if (!is_dir($uploadDir)) {
    // Buat direktori jika belum ada. Hati-hati dengan izin!
    @mkdir($uploadDir, 0777, true);
}


// 2. Proteksi Halaman (sesuaikan role jika perlu)
redirectIfNotLoggedIn(['admin_berita', 'admin_lab']);

// 3. (FLEKSIBEL) Set Judul Halaman dan Navigasi Aktif
$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Inisialisasi pesan
$error_message = null;
$success_message = null;

// Tangani pesan dari sesi (setelah redirect dari operasi CRUD)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// 4. Logika Fasilitas & Peralatan (CRUD)
$fasilitas_to_edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_fasilitas'])) {
    $is_update = isset($_POST['update_fasilitas']);
    $fasilitas_id = $is_update ? (int)$_POST['fasilitas_id'] : null;

    $jenis = trim($_POST['jenis']);
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $ikon_fa = trim($_POST['ikon_fa']);

    // --- LOGIKA TERKAIT GAMBAR DIHAPUS/DILEWATI ---
    // $current_gambar = null; 
    // $new_gambar_filename = null;
    // $upload_error = false;

    // if ($is_update) {
    //     try {
    //         $stmt_old_img = $pdo->prepare("SELECT gambar FROM fasilitas_peralatan WHERE id = ?");
    //         $stmt_old_img->execute([$fasilitas_id]);
    //         $current_gambar = $stmt_old_img->fetchColumn();
    //     } catch (PDOException $e) {
    //         $error_message = "Gagal mengambil gambar lama: " . $e->getMessage();
    //     }
    // }


    if (!empty($jenis) && !empty($judul)) {
        
        // --- Logika Upload Gambar DIHAPUS/DILEWATI ---
        // if (isset($_FILES['gambar_fasilitas']) && $_FILES['gambar_fasilitas']['error'] === UPLOAD_ERR_OK) {
        // ... seluruh blok upload file dihapus ...
        // }
        
        // Jika ada error upload, batalkan operasi CRUD
        // if ($upload_error) {
            // Pertahankan data yang diinput pengguna di form
            // if ($is_update) {
                // $fasilitas_to_edit = array_merge(['id' => $fasilitas_id, 'gambar' => $current_gambar ?? null], $_POST);
            // }
            // Tampilkan error_message di form container jika bukan redirect
        // } else { // <-- Lanjut ke DB Operation karena tidak ada error upload
            try {
                if ($is_update) {
                    // Tentukan nilai gambar yang akan disimpan DIHAPUS
                    // $new_gambar_value = $new_gambar_filename ?? $current_gambar;
                    
                    // UPDATE: Hapus kolom 'gambar' dari query dan parameter
                    $sql = "UPDATE fasilitas_peralatan SET jenis = ?, judul = ?, deskripsi = ?, ikon_fa = ? WHERE id = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$jenis, $judul, $deskripsi, $ikon_fa, $fasilitas_id]);
                    $action_message = "diperbarui";

                    // Logika hapus gambar lama DIHAPUS
                    // if (!empty($new_gambar_filename) && !empty($current_gambar) && $current_gambar !== $new_gambar_filename) {
                    // ...
                    // }

                } else {
                    // CREATE: Hapus kolom 'gambar' dari query dan parameter
                    $sql = "INSERT INTO fasilitas_peralatan (jenis, judul, deskripsi, ikon_fa) VALUES (?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$jenis, $judul, $deskripsi, $ikon_fa]);
                    $action_message = "ditambahkan";
                }
                
                $_SESSION['success_message'] = "Data **Fasilitas/Peralatan** berhasil {$action_message}.";
                header("Location: dashboard.php");
                exit();

            } catch (PDOException $e) {
                $error_message = "Gagal {$action_message} data (DB Error): " . $e->getMessage();
                // Logika hapus gambar baru jika DB gagal DIHAPUS
                // if (!empty($new_gambar_filename)) {
                // ...
                // }
                // Pertahankan data yang diinput pengguna di form (Hapus 'gambar' dari array merge)
                if ($is_update) {
                    $fasilitas_to_edit = array_merge(['id' => $fasilitas_id], $_POST);
                }
            }
        // } // <-- Penutup else untuk $upload_error
    } else {
        $error_message = "Jenis dan Judul wajib diisi.";
    }
}

// 4.1 Logika DELETE Fasilitas
if (isset($_GET['action']) && $_GET['action'] === 'delete_fasilitas' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Ambil nama gambar sebelum dihapus DIHAPUS
        // $stmt_img = $pdo->prepare("SELECT gambar FROM fasilitas_peralatan WHERE id = ?");
        // $stmt_img->execute([$id]);
        // $gambar_to_delete = $stmt_img->fetchColumn();
        
        // Hapus data dari DB
        $stmt = $pdo->prepare("DELETE FROM fasilitas_peralatan WHERE id = ?");
        $stmt->execute([$id]);
        
        // Hapus file gambar dari server DIHAPUS
        // if (!empty($gambar_to_delete)) {
        // ...
        // }
        
        $_SESSION['success_message'] = "Data Fasilitas/Peralatan berhasil dihapus.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus data Fasilitas/Peralatan (DB Error): " . $e->getMessage();
    }
    header("Location: dashboard.php");
    exit();
}

// 4.2 Logika EDIT (Mengisi form saat action=edit_fasilitas)
$show_fasilitas_form = false;
if (isset($_GET['action']) && $_GET['action'] === 'edit_fasilitas' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        // Pastikan kolom 'gambar' TIDAK diminta
        $stmt = $pdo->prepare("SELECT id, jenis, judul, deskripsi, ikon_fa FROM fasilitas_peralatan WHERE id = ?");
        $stmt->execute([$id]);
        $fasilitas_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fasilitas_to_edit) {
            $show_fasilitas_form = true;
        } else {
            $error_message = "Data fasilitas yang ingin diubah tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error saat mengambil data fasilitas untuk diedit: " . $e->getMessage();
    }
}


// 5. Logika Visi & Misi (CRUD)
// (Kode Visi & Misi TIDAK diubah karena tidak melibatkan kolom 'gambar')
$visi_misi_to_edit = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_visimisi'])) {
    $is_update = isset($_POST['update_visimisi']);
    $visimisi_id = $is_update ? (int)$_POST['visimisi_id'] : null;

    $tipe = trim($_POST['tipe']);
    $konten_id = (int)$_POST['konten_id'];
    $deskripsi = trim($_POST['deskripsi']);

    if (!empty($tipe) && !empty($deskripsi)) {
        try {
            if ($is_update) {
                // UPDATE
                $sql = "UPDATE visi_misi SET tipe = ?, konten_id = ?, deskripsi = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$tipe, $konten_id, $deskripsi, $visimisi_id]);
                $action_message = "diperbarui";
            } else {
                // CREATE
                $sql = "INSERT INTO visi_misi (tipe, konten_id, deskripsi) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$tipe, $konten_id, $deskripsi]);
                $action_message = "ditambahkan";
            }
            $_SESSION['success_message'] = "Data **Visi & Misi** berhasil {$action_message}.";
            header("Location: dashboard.php");
            exit();

        } catch (PDOException $e) {
            $error_message = "Gagal {$action_message} data (DB Error): " . $e->getMessage();
            if ($is_update) {
                $visi_misi_to_edit = array_merge(['id' => $visimisi_id], $_POST);
            }
        }
    } else {
        $error_message = "Tipe dan Deskripsi wajib diisi.";
    }
}

// 5.1 Logika DELETE Visi & Misi
if (isset($_GET['action']) && $_GET['action'] === 'delete_visimisi' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM visi_misi WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Data Visi & Misi berhasil dihapus.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Gagal menghapus data Visi & Misi (DB Error): " . $e->getMessage();
    }
    header("Location: dashboard.php");
    exit();
}

// 5.2 Logika EDIT (Mengisi form saat action=edit_visimisi)
$show_visimisi_form = false;
if (isset($_GET['action']) && $_GET['action'] === 'edit_visimisi' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM visi_misi WHERE id = ?");
        $stmt->execute([$id]);
        $visi_misi_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($visi_misi_to_edit) {
            $show_visimisi_form = true;
        } else {
            $error_message = "Data Visi & Misi yang ingin diubah tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error saat mengambil data Visi & Misi untuk diedit: " . $e->getMessage();
    }
}


// 6. Logika Halaman Ini: Hitung semua data dari DB
try {
    $totalBerita = $pdo->query("SELECT count(id) FROM berita")->fetchColumn();
    $totalMember = $pdo->query("SELECT count(id) FROM anggota_lab")->fetchColumn(); 
    $totalRiset = $pdo->query("SELECT count(id) FROM riset")->fetchColumn(); 
    $totalPendaftaran = $pdo->query("SELECT count(id) FROM pendaftaran_magang WHERE status = 'pending'")->fetchColumn(); 

    // Ambil data untuk CRUD Fasilitas dan Visi Misi
    // Hapus 'gambar' dari SELECT * untuk fasilitas
    $fasilitasList = $pdo->query("SELECT id, jenis, judul, deskripsi, ikon_fa FROM fasilitas_peralatan ORDER BY jenis, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $visiMisiList = $pdo->query("SELECT * FROM visi_misi ORDER BY tipe, konten_id ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika tabel belum ada, anggap 0
    $totalBerita = $totalBerita ?? 0;
    $totalMember = $totalMember ?? 0;
    $totalRiset = $totalRiset ?? 0;
    $totalPendaftaran = $totalPendaftaran ?? 0;
    $fasilitasList = [];
    $visiMisiList = [];
    $error_message = $error_message ?? "Gagal memuat data dashboard: " . $e->getMessage();
}


// 7. Panggil Header
include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Berita</span>
            <span class="stat-number"><?php echo $totalBerita; ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="color: #198754; background-color: #e8f3ee;">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Member</span>
            <span class="stat-number"><?php echo $totalMember; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #ffc107; background-color: #fff8e6;">
            <i class="fa-solid fa-flask"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Riset</span>
            <span class="stat-number"><?php echo $totalRiset; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="color: #6f42c1; background-color: #f1eef8;">
            <i class="fa-solid fa-file-signature"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Pendaftaran Pending</span>
            <span class="stat-number"><?php echo $totalPendaftaran; ?></span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 2rem;">
        <h4>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</h4>
        <p>Anda login sebagai <?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?>. Gunakan menu di sebelah kiri untuk mengelola konten website, atau kelola Visi, Misi, Fasilitas di bawah ini.</p>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>
<?php if (isset($error_message) && !$show_fasilitas_form && !$show_visimisi_form): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<hr style="margin: 40px 0;">

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Visi & Misi</span>
        </div>
        <button class="btn btn-primary" id="btnToggleVisiMisiForm">
            <?php echo $show_visimisi_form ? 'Tutup Form' : 'Tambah Visi & Misi'; ?>
        </button>
    </div>

    <div class="form-container" id="visiMisiFormContainer" style="display: <?php echo $show_visimisi_form ? 'block' : 'none'; ?>;">
        <h4 id="visiMisiFormTitle">
            <?php echo $show_visimisi_form ? '✏️ Ubah Data Visi & Misi' : '➕ Tambah Visi & Misi Baru'; ?>
        </h4>
        <?php if (isset($error_message) && $show_visimisi_form || (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_visimisi']))): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="dashboard.php">
            <input type="hidden" name="form_visimisi" value="1">
            <?php if ($show_visimisi_form): ?>
                <input type="hidden" name="update_visimisi" value="1">
                <input type="hidden" name="visimisi_id" value="<?= htmlspecialchars($visi_misi_to_edit['id']) ?>" id="edit_visimisi_id">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="tipe_visimisi">Tipe:</label>
                <select class="form-control" id="tipe_visimisi" name="tipe" required>
                    <option value="visi" <?php if (($visi_misi_to_edit['tipe'] ?? '') === 'visi') echo 'selected'; ?>>Visi</option>
                    <option value="misi" <?php if (($visi_misi_to_edit['tipe'] ?? '') === 'misi') echo 'selected'; ?>>Misi</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="konten_id_visimisi">ID Konten (Nomor Urut Visi / Misi):</label>
                <input type="number" class="form-control" id="konten_id_visimisi" name="konten_id" min="1" 
                    value="<?= htmlspecialchars($visi_misi_to_edit['konten_id'] ?? 1) ?>" required>
                <small class="form-text text-muted">Untuk urutan tampilan. Isi 1 untuk Visi, atau 1, 2, 3, dst untuk Misi.</small>
            </div>

            <div class="form-group">
                <label for="deskripsi_visimisi">Deskripsi/Poin:</label>
                <textarea class="form-control" id="deskripsi_visimisi" name="deskripsi" rows="3" required><?= htmlspecialchars($visi_misi_to_edit['deskripsi'] ?? '') ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $show_visimisi_form ? 'Simpan Perubahan' : 'Simpan'; ?></button>
                <button type="button" class="btn btn-secondary" id="btnCancelVisiMisi">Batal</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <?php if (empty($visiMisiList)): ?>
            <div class="alert alert-info">Belum ada Visi atau Misi yang tercatat.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="table-tipe-col">Tipe</th>
                            <th class="table-date-col">Urutan</th>
                            <th>Deskripsi</th>
                            <th class="table-aksi-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visiMisiList as $vm): ?>
                            <tr>
                                <td class="table-tipe-col">
                                    <span class="badge <?php echo $vm['tipe'] === 'visi' ? 'bg-info' : 'bg-primary'; ?>">
                                        <?= htmlspecialchars(ucfirst($vm['tipe'])) ?>
                                    </span>
                                </td>
                                <td class="table-date-col"><?= htmlspecialchars($vm['konten_id']) ?></td>
                                <td><?= htmlspecialchars($vm['deskripsi']) ?></td>
                                <td class="table-aksi-col">
                                    <a href="dashboard.php?action=edit_visimisi&id=<?= $vm['id'] ?>" class="btn-icon btn-edit" title="Ubah">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="dashboard.php?action=delete_visimisi&id=<?= $vm['id'] ?>" class="btn-icon btn-delete" title="Hapus" 
                                        onclick="return confirm('Yakin ingin menghapus poin <?= htmlspecialchars($vm['tipe']) ?> urutan <?= $vm['konten_id'] ?>?')">
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

<div class="card" style="margin-top: 20px;">
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
            <?php echo $show_fasilitas_form ? '✏️ Ubah Data Fasilitas/Peralatan: ' . htmlspecialchars($fasilitas_to_edit['judul'] ?? 'Tidak Ditemukan') : '➕ Tambah Fasilitas/Peralatan Baru'; ?>
        </h4>
        <?php if (isset($error_message) && $show_fasilitas_form || (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_fasilitas']))): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="dashboard.php" enctype="multipart/form-data">
            <input type="hidden" name="form_fasilitas" value="1">
            <?php if ($show_fasilitas_form): ?>
                <input type="hidden" name="update_fasilitas" value="1">
                <input type="hidden" name="fasilitas_id" value="<?= htmlspecialchars($fasilitas_to_edit['id']) ?>" id="edit_fasilitas_id">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="jenis_fasilitas">Jenis:</label>
                <input type="text" class="form-control" id="jenis_fasilitas" name="jenis" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['jenis'] ?? '') ?>" placeholder="Contoh: Hardware, Software, Ruangan" required>
            </div>
            
            <div class="form-group">
                <label for="judul_fasilitas">Judul Fasilitas/Peralatan:</label>
                <input type="text" class="form-control" id="judul_fasilitas" name="judul" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['judul'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="ikon_fa_fasilitas">Ikon Font Awesome (Opsional):</label>
                <input type="text" class="form-control" id="ikon_fa_fasilitas" name="ikon_fa" 
                    value="<?= htmlspecialchars($fasilitas_to_edit['ikon_fa'] ?? '') ?>" placeholder="Contoh: fa-laptop, fa-flask, fa-building">
                <small class="form-text text-muted">Isi dengan nama ikon Font Awesome 6, cth: **fa-solid fa-laptop** (hanya masukkan **fa-laptop**).</small>
            </div>

            <div class="form-group">
                <label for="deskripsi_fasilitas">Deskripsi:</label>
                <textarea class="form-control" id="deskripsi_fasilitas" name="deskripsi" rows="3" required><?= htmlspecialchars($fasilitas_to_edit['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo $show_fasilitas_form ? 'Simpan Perubahan' : 'Simpan Fasilitas'; ?></button>
                <button type="button" class="btn btn-secondary" id="btnCancelFasilitas">Batal</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <?php if (empty($fasilitasList)): ?>
            <div class="alert alert-info">Belum ada Fasilitas atau Peralatan yang tercatat.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="table-tipe-col">Jenis</th>
                            <th>Judul & Deskripsi</th> <th class="table-date-col">Ikon</th>
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
                                    <a href="dashboard.php?action=edit_fasilitas&id=<?= $f['id'] ?>" class="btn-icon btn-edit" title="Ubah">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="dashboard.php?action=delete_fasilitas&id=<?= $f['id'] ?>" class="btn-icon btn-delete" title="Hapus" 
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
        // --- Visi Misi Handlers ---
        const vmFormContainer = document.getElementById('visiMisiFormContainer');
        const btnToggleVM = document.getElementById('btnToggleVisiMisiForm');
        const btnCancelVM = document.getElementById('btnCancelVisiMisi');
        const vmFormTitle = document.getElementById('visiMisiFormTitle');

        const isCurrentlyEditVM = <?php echo $show_visimisi_form ? 'true' : 'false'; ?>;

        btnToggleVM.addEventListener('click', () => {
            if (isCurrentlyEditVM) {
                // If in Edit mode, clicking toggle redirects to clear URL params
                window.location.href = 'dashboard.php';
                return;
            }

            if (vmFormContainer.style.display === 'block') {
                vmFormContainer.style.display = 'none';
                btnToggleVM.innerText = '➕ Tambah Visi & Misi';
            } else {
                vmFormContainer.style.display = 'block';
                btnToggleVM.innerText = 'Tutup Form';
                vmFormTitle.innerText = '➕ Tambah Visi & Misi Baru';
                vmFormContainer.scrollIntoView({ behavior: 'smooth' });
                // Reset fields for fresh ADD
                vmFormContainer.querySelector('form').reset();
            }
        });

        btnCancelVM.addEventListener('click', () => {
            window.location.href = 'dashboard.php';
        });

        // --- Fasilitas Handlers ---
        const fFormContainer = document.getElementById('fasilitasFormContainer');
        const btnToggleF = document.getElementById('btnToggleFasilitasForm');
        const btnCancelF = document.getElementById('btnCancelFasilitas');
        const fFormTitle = document.getElementById('fasilitasFormTitle');
        
        const isCurrentlyEditF = <?php echo $show_fasilitas_form ? 'true' : 'false'; ?>;

        btnToggleF.addEventListener('click', () => {
            if (isCurrentlyEditF) {
                // If in Edit mode, clicking toggle redirects to clear URL params
                window.location.href = 'dashboard.php';
                return;
            }

            if (fFormContainer.style.display === 'block') {
                fFormContainer.style.display = 'none';
                btnToggleF.innerText = '➕ Tambah Fasilitas';
            } else {
                fFormContainer.style.display = 'block';
                btnToggleF.innerText = 'Tutup Form';
                fFormTitle.innerText = '➕ Tambah Fasilitas/Peralatan Baru';
                fFormContainer.scrollIntoView({ behavior: 'smooth' });
                 // Reset fields for fresh ADD
                fFormContainer.querySelector('form').reset();
            }
        });

        btnCancelF.addEventListener('click', () => {
            window.location.href = 'dashboard.php';
        });

        // Re-open form on POST error if it was a create operation
        <?php if (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_fasilitas']) && isset($_POST['form_fasilitas'])): ?>
            fFormContainer.style.display = 'block';
            btnToggleF.innerText = 'Tutup Form';
            fFormContainer.scrollIntoView({ behavior: 'smooth' });
        <?php endif; ?>

        <?php if (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_visimisi']) && isset($_POST['form_visimisi'])): ?>
            vmFormContainer.style.display = 'block';
            btnToggleVM.innerText = 'Tutup Form';
            vmFormContainer.scrollIntoView({ behavior: 'smooth' });
        <?php endif; ?>
    });
</script>