<?php
require_once __DIR__ . '/../config/database.php'; // PASTIkan file ini mendefinisikan $pdo (objek PDO)!
require_once __DIR__ . '/../includes/auth.php';

// Set judul dan halaman aktif
$pageTitle = 'Riset';
$activePage = 'riset';

// Inisialisasi pesan
$error_message = null;
$success_message = null;
$anggota_error = null;
$riset_data = [];
$anggota_list = [];
$riset_to_edit = null; // Variabel untuk menyimpan data riset saat mode EDIT

// Tangani pesan dari sesi (setelah redirect dari operasi CRUD)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// --- Tambahan: Query untuk mengambil daftar anggota (untuk formulir) ---
try {
    $stmt_anggota = $pdo->query("SELECT id, nama_lengkap FROM anggota_lab ORDER BY nama_lengkap ASC");
    $anggota_list = $stmt_anggota->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $anggota_error = "Gagal mengambil daftar anggota: " . $e->getMessage();
}

// --- Bagian Logika Server (CRUD) ---

// 1. Logika DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $riset_id = (int)$_GET['id'];
    try {
        $pdo->beginTransaction();

        $stmt_title = $pdo->prepare("SELECT judul_riset FROM riset WHERE id = ?");
        $stmt_title->execute([$riset_id]);
        $riset_title = $stmt_title->fetchColumn() ?: "ID: {$riset_id}";

        // 1a. Hapus entri di tabel 'riset_anggota'
        $pdo->prepare("DELETE FROM riset_anggota WHERE riset_id = ?")->execute([$riset_id]);

        // 1b. Hapus entri di tabel 'riset'
        $pdo->prepare("DELETE FROM riset WHERE id = ?")->execute([$riset_id]);

        $pdo->commit();
        $_SESSION['success_message'] = "Riset **\"{$riset_title}\"** berhasil dihapus.";
        header("Location: dashboard_riset.php");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Gagal menghapus riset (DB Error): " . $e->getMessage();
        header("Location: dashboard_riset.php");
        exit();
    }
}

// 2. Logika EDIT (Mengisi form saat action=edit)
$show_edit_form = false;
$riset_anggota_ids = [];
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $riset_id = (int)$_GET['id'];
    try {
        // Ambil detail Riset
        $stmt_riset = $pdo->prepare("SELECT * FROM riset WHERE id = ?");
        $stmt_riset->execute([$riset_id]);
        $riset_to_edit = $stmt_riset->fetch(PDO::FETCH_ASSOC);

        if ($riset_to_edit) {
            $show_edit_form = true;
            // Ambil anggota tim yang sudah dipilih
            $stmt_anggota_current = $pdo->prepare("SELECT anggota_id FROM riset_anggota WHERE riset_id = ?");
            $stmt_anggota_current->execute([$riset_id]);
            $riset_anggota_ids = $stmt_anggota_current->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $error_message = "Data riset yang ingin diubah tidak ditemukan.";
        }

    } catch (PDOException $e) {
        $error_message = "Error saat mengambil data riset untuk diedit: " . $e->getMessage();
    }
}


// 3. Logika CREATE (Menambah data riset) & UPDATE (Memperbarui data riset)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_update = isset($_POST['update_riset']);
    $riset_id = $is_update ? (int)$_POST['riset_id'] : null;

    $judul_riset = trim($_POST['judul_riset']);
    $deskripsi = trim($_POST['deskripsi']);
    // --- MODIFIKASI: Ambil link_riset dari POST ---
    $link_riset = empty($_POST['link_riset']) ? null : trim($_POST['link_riset']);
    // ---------------------------------------------
    $tanggal_mulai = empty($_POST['tanggal_mulai']) ? null : $_POST['tanggal_mulai'];
    $tanggal_selesai = empty($_POST['tanggal_selesai']) ? null : $_POST['tanggal_selesai'];
    $anggota_ids = $_POST['anggota_ids'] ?? [];

    if (!empty($judul_riset) && !empty($deskripsi)) {
        try {
            $pdo->beginTransaction();

            if ($is_update) {
                // UPDATE (Memperbarui)
                // --- MODIFIKASI SQL: Tambahkan link_riset ---
                $sql_riset = "UPDATE riset SET judul_riset = ?, deskripsi = ?, link_riset = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE id = ?";
                $stmt_riset = $pdo->prepare($sql_riset);
                $stmt_riset->execute([$judul_riset, $deskripsi, $link_riset, $tanggal_mulai, $tanggal_selesai, $riset_id]);
                // ---------------------------------------------
                
                // Update Anggota: Hapus lama, masukkan baru
                $pdo->prepare("DELETE FROM riset_anggota WHERE riset_id = ?")->execute([$riset_id]);
                $action_message = "diperbarui";
            } else {
                // CREATE (Menambah)
                // --- MODIFIKASI SQL: Tambahkan link_riset ---
                $sql_riset = "INSERT INTO riset (judul_riset, deskripsi, link_riset, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?, ?)";
                $stmt_riset = $pdo->prepare($sql_riset);
                $stmt_riset->execute([$judul_riset, $deskripsi, $link_riset, $tanggal_mulai, $tanggal_selesai]);
                // ---------------------------------------------
                $riset_id = $pdo->lastInsertId();
                $action_message = "ditambahkan";
            }

            // Insert Anggota
            if (!empty($anggota_ids) && $riset_id) {
                $sql_anggota = "INSERT INTO riset_anggota (riset_id, anggota_id) VALUES (?, ?)";
                $stmt_anggota = $pdo->prepare($sql_anggota);
                foreach ($anggota_ids as $anggota_id) {
                    if (is_numeric($anggota_id)) {
                        $stmt_anggota->execute([$riset_id, (int)$anggota_id]);
                    }
                }
            }

            $pdo->commit();
            $_SESSION['success_message'] = "Riset **\"{$judul_riset}\"** berhasil {$action_message}.";
            header("Location: dashboard_riset.php");
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Gagal {$action_message} riset (DB Error): " . $e->getMessage();
            // Jika gagal, pastikan data yang di-post tetap ada di form
            if ($is_update && $riset_to_edit) {
                 $riset_to_edit = array_merge($riset_to_edit, $_POST);
                 $riset_anggota_ids = $anggota_ids; // Pertahankan pilihan anggota
                 $show_edit_form = true;
            } else {
                 // Jika CREATE gagal, biarkan form Tambah terbuka
            }
        }
    } else {
        $error_message = "Judul dan Deskripsi riset wajib diisi.";
        // Jika CREATE gagal, biarkan form Tambah terbuka
    }
}


// 4. Logika READ (Mengambil data riset dengan anggota tim)
try {
    // Query untuk mengambil semua data riset dan anggota tim
    // --- MODIFIKASI SQL: Tambahkan r.link_riset ---
    $sql_read = "
        SELECT 
            r.id, r.judul_riset, r.deskripsi, r.link_riset, r.tanggal_mulai, r.tanggal_selesai,
            COALESCE(STRING_AGG(al.nama_lengkap, ', '), 'Tidak Ada Anggota') AS anggota_tim
        FROM riset r
        LEFT JOIN riset_anggota ra ON r.id = ra.riset_id
        LEFT JOIN anggota_lab al ON ra.anggota_id = al.id
        GROUP BY r.id, r.judul_riset, r.deskripsi, r.link_riset, r.tanggal_mulai, r.tanggal_selesai
        ORDER BY r.id DESC
    ";
    // ---------------------------------------------
    $stmt = $pdo->query($sql_read);
    $riset_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Tampilkan pesan error jika query READ gagal
    $error_message = "Error saat mengambil data riset: " . $e->getMessage();
}

// Hitung total riset
$totalRiset = count($riset_data);


// Panggil Header/Sidebar
include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-flask"></i> 
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Riset</span>
            <span class="stat-number"><?php echo $totalRiset; ?></span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Riset</span>
        </div>
        <button class="btn btn-primary" id="btnToggleRisetForm">
            <?php echo $show_edit_form ? 'Tutup Form' : 'Tambah Riset'; ?>
        </button>
    </div>

    <div class="form-container" id="risetFormContainer" style="display: <?php echo $show_edit_form ? 'block' : 'none'; ?>;">
        <h4 id="risetFormTitle">
            <?php echo $show_edit_form ? '✏️ Ubah Data Riset: ' . htmlspecialchars($riset_to_edit['judul_riset'] ?? 'Tidak Ditemukan') : '➕ Tambah Riset Baru'; ?>
        </h4>

        <?php if ($show_edit_form && $riset_to_edit): ?>
            <form method="POST" action="dashboard_riset.php">
                <input type="hidden" name="update_riset" value="1">
                <input type="hidden" name="riset_id" value="<?= htmlspecialchars($riset_to_edit['id']) ?>" id="edit_riset_id">
                
                <div class="form-group">
                    <label for="judul_riset_edit">Judul Riset:</label>
                    <input type="text" class="form-control" id="judul_riset_edit" name="judul_riset" 
                        value="<?= htmlspecialchars($riset_to_edit['judul_riset']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="link_riset_edit">Link Dokumen Riset (URL Opsional):</label>
                    <input type="url" class="form-control" id="link_riset_edit" name="link_riset" 
                        value="<?= htmlspecialchars($riset_to_edit['link_riset'] ?? '') ?>" placeholder="Masukkan URL dokumen riset">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi_edit">Deskripsi Riset:</label>
                    <textarea class="form-control" id="deskripsi_edit" name="deskripsi" rows="3" required><?= htmlspecialchars($riset_to_edit['deskripsi']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="tanggal_mulai_edit">Tanggal Mulai (Opsional):</label>
                        <input type="date" class="form-control" id="tanggal_mulai_edit" name="tanggal_mulai" 
                            value="<?= htmlspecialchars($riset_to_edit['tanggal_mulai']) ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="tanggal_selesai_edit">Tanggal Selesai (Opsional):</label>
                        <input type="date" class="form-control" id="tanggal_selesai_edit" name="tanggal_selesai" 
                            value="<?= htmlspecialchars($riset_to_edit['tanggal_selesai']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="anggota_ids_edit">Anggota Tim (Opsional - Multi-Select):</label>
                    <select class="form-select" id="anggota_ids_edit" name="anggota_ids[]" multiple>
                        <option value="">Pilih Anggota Tim...</option>
                        <?php if (isset($anggota_error)): ?>
                            <option disabled><?= htmlspecialchars($anggota_error) ?></option>
                        <?php else: ?>
                            <?php foreach ($anggota_list as $anggota): ?>
                                <?php 
                                    $selected = in_array($anggota['id'], array_map('strval', $riset_anggota_ids)) ? 'selected' : '';
                                ?>
                                <option value="<?= htmlspecialchars($anggota['id']) ?>" <?= $selected ?>>
                                    <?= htmlspecialchars($anggota['nama_lengkap']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted">Gunakan **Ctrl/Cmd** untuk memilih lebih dari satu.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" id="btnCancelEdit">Batal</button>
                </div>
            </form>
        <?php else: ?>
            <div class="collapse mt-3 
                <?php if (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_riset'])) echo 'show'; ?> 
            " id="formTambahRiset">
                <form method="POST" action="dashboard_riset.php" id="formTambahRisetActual">
                    <input type="hidden" name="tambah_riset" value="1">
                    
                    <div class="form-group">
                        <label for="judul_riset_tambah">Judul Riset:</label>
                        <input type="text" class="form-control" id="judul_riset_tambah" name="judul_riset" required value="<?= htmlspecialchars($_POST['judul_riset'] ?? '') ?>" placeholder="Masukkan judul riset">
                    </div>
                    
                    <div class="form-group">
                        <label for="link_riset_tambah">Link Dokumen Riset (URL Opsional):</label>
                        <input type="url" class="form-control" id="link_riset_tambah" name="link_riset" 
                            value="<?= htmlspecialchars($_POST['link_riset'] ?? '') ?>" placeholder="Masukkan URL dokumen riset">
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi_tambah">Deskripsi Riset:</label>
                        <textarea class="form-control" id="deskripsi_tambah" name="deskripsi" placeholder="Deskripsi riset" rows="3" 
                        required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="tanggal_mulai_tambah">Tanggal Mulai (Opsional):</label>
                            <input type="date" class="form-control" id="tanggal_mulai_tambah" name="tanggal_mulai" value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="tanggal_selesai_tambah">Tanggal Selesai (Opsional):</label>
                            <input type="date" class="form-control" id="tanggal_selesai_tambah" name="tanggal_selesai" value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="anggota_ids_tambah">Anggota Tim (Opsional - Multi-Select):</label>
                        <select class="form-select" id="anggota_ids_tambah" name="anggota_ids[]" multiple>
                            <option value="">Pilih Anggota Tim...</option>
                            <?php if (isset($anggota_error)): ?>
                                <option disabled><?= htmlspecialchars($anggota_error) ?></option>
                            <?php else: ?>
                                <?php foreach ($anggota_list as $anggota): ?>
                                    <option value="<?= htmlspecialchars($anggota['id']) ?>"><?= htmlspecialchars($anggota['nama_lengkap']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Gunakan **Ctrl/Cmd** untuk memilih lebih dari satu.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Simpan Riset</button>
                        <button type="button" class="btn btn-secondary" id="btnCancelTambah">Batal</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if (isset($error_message) && !$show_edit_form): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <?php if (empty($riset_data)): ?>
            <div class="alert alert-info">Belum ada data riset yang tercatat. Silakan tambahkan riset baru.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul & Deskripsi</th>
                            <th class="table-tipe-col">Link</th> <th>Anggota Tim</th>
                            <th class="table-date-col">Periode</th>
                            <th class="table-aksi-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riset_data as $riset): ?>
                            <tr>
                                <td><?= htmlspecialchars($riset['id']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($riset['judul_riset']) ?></strong>
                                    <br><small class="text-muted"><?= substr(htmlspecialchars($riset['deskripsi']), 0, 80) ?>...</small>
                                </td>
                                <td class="table-tipe-col">
                                    <?php if (!empty($riset['link_riset'])): ?>
                                        <a href="<?= htmlspecialchars($riset['link_riset']) ?>" target="_blank" class="btn-icon btn-edit" title="Lihat Dokumen">
                                            <i class="fa-solid fa-link"></i>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($riset['anggota_tim']) ?></td>
                                <td class="table-date-col">
                                    Mulai: <?= empty($riset['tanggal_mulai']) ? '-' : date('d/m/Y', strtotime($riset['tanggal_mulai'])) ?>
                                    <br>Selesai: <?= empty($riset['tanggal_selesai']) ? '-' : date('d/m/Y', strtotime($riset['tanggal_selesai'])) ?>
                                </td>
                                <td class="table-aksi-col">
                                    <a href="dashboard_riset.php?action=edit&id=<?= $riset['id'] ?>" class="btn-icon btn-edit" title="Ubah">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="dashboard_riset.php?action=delete&id=<?= $riset['id'] ?>" class="btn-icon btn-delete" title="Hapus" 
                                        onclick="return confirm('Yakin ingin menghapus riset: <?= htmlspecialchars($riset['judul_riset']) ?>?')">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.getElementById('risetFormContainer');
        const btnToggle = document.getElementById('btnToggleRisetForm');
        const formTambahCollapse = document.getElementById('formTambahRiset'); // Collapse div for Add Form
        const btnCancelTambah = document.getElementById('btnCancelTambah');
        const btnCancelEdit = document.getElementById('btnCancelEdit');

        const isCurrentlyEditMode = <?php echo $show_edit_form ? 'true' : 'false'; ?>;

        // Toggle visibility for the Add Form
        btnToggle.addEventListener('click', () => {
            if (isCurrentlyEditMode) {
                // If currently in Edit mode, clicking toggle is like 'Cancel'
                window.location.href = 'dashboard_riset.php';
                return;
            }

            if (formContainer.style.display === 'block') {
                // If currently open (in Add mode), close it
                formContainer.style.display = 'none';
                btnToggle.innerText = '➕ Tambah Riset';
                // Close the collapse (optional, Bootstrap handles it well)
                const bsCollapse = new bootstrap.Collapse(formTambahCollapse, { toggle: false });
                bsCollapse.hide();

            } else {
                // If closed, open the Add form
                formContainer.style.display = 'block';
                btnToggle.innerText = 'Tutup Form';
                // Open the collapse
                const bsCollapse = new bootstrap.Collapse(formTambahCollapse, { toggle: false });
                bsCollapse.show();
                
                // Reset form fields on open
                document.getElementById('formTambahRisetActual').reset();
                window.scrollTo(0, 0);
            }
        });

        // Handle Cancel button for Add Form
        if (btnCancelTambah) {
            btnCancelTambah.addEventListener('click', () => {
                formContainer.style.display = 'none';
                btnToggle.innerText = '➕ Tambah Riset';
                // Close the collapse
                const bsCollapse = new bootstrap.Collapse(formTambahCollapse, { toggle: false });
                bsCollapse.hide();
            });
        }
        
        // Handle Cancel button for Edit Form (only exists in Edit mode)
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener('click', () => {
                // Redirecting or simply hiding is an option. Redirecting clears the URL param.
                window.location.href = 'dashboard_riset.php';
            });
        }

        // If an error occurred in POST but it wasn't an update, open the ADD form on load
        <?php if (isset($error_message) && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_riset'])): ?>
            formContainer.style.display = 'block';
            btnToggle.innerText = 'Tutup Form';
        <?php endif; ?>
    });
</script>

<?php
// Penutup file, jika ada footer
// include_once __DIR__ . '/../includes/footer.php';
?>