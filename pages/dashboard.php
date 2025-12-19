<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../controllers/DashboardController.php';

redirectIfNotLoggedIn(['admin_berita', 'admin_lab']);

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

// Init messages
$error_message = null;
$success_message = null;

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$isAdminLab = ($_SESSION['role'] ?? '') === 'admin_lab';

$visi_misi_to_edit = null;
$show_visimisi_form = false;

if ($isAdminLab) {
    // Handle Save/Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_visimisi'])) {
        $is_update = isset($_POST['update_visimisi']);
        $visimisi_id = $is_update ? (int) $_POST['visimisi_id'] : null;

        $tipe = trim($_POST['tipe']);
        $konten_id = (int) $_POST['konten_id'];
        $deskripsi = trim($_POST['deskripsi']);

        if (!empty($tipe) && !empty($deskripsi)) {
            try {
                if ($is_update) {
                    $stmt = $pdo->prepare("UPDATE visi_misi SET tipe = ?, konten_id = ?, deskripsi = ? WHERE id = ?");
                    $stmt->execute([$tipe, $konten_id, $deskripsi, $visimisi_id]);
                    $action_message = "diperbarui";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO visi_misi (tipe, konten_id, deskripsi) VALUES (?, ?, ?)");
                    $stmt->execute([$tipe, $konten_id, $deskripsi]);
                    $action_message = "ditambahkan";
                }
                $_SESSION['success_message'] = "Data **Visi & Misi** berhasil {$action_message}.";
                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                $error_message = "Database Error: " . $e->getMessage();
                if ($is_update) {
                    $visi_misi_to_edit = array_merge(['id' => $visimisi_id], $_POST);
                    $show_visimisi_form = true;
                }
            }
        } else {
            $error_message = "Tipe dan Deskripsi wajib diisi.";
            $show_visimisi_form = true;
        }
    }

    // Handle Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete_visimisi' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM visi_misi WHERE id = ?");
            $stmt->execute([(int) $_GET['id']]);
            $_SESSION['success_message'] = "Data Visi & Misi berhasil dihapus.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Gagal menghapus data: " . $e->getMessage();
        }
        header("Location: dashboard.php");
        exit();
    }

    // Prepare Edit Form
    if (isset($_GET['action']) && $_GET['action'] === 'edit_visimisi' && isset($_GET['id'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM visi_misi WHERE id = ?");
            $stmt->execute([(int) $_GET['id']]);
            $visi_misi_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($visi_misi_to_edit)
                $show_visimisi_form = true;
        } catch (PDOException $e) {
            $error_message = "Error fetching data: " . $e->getMessage();
        }
    }
}

try {
    // 1. Panggil Controller Statistik
    $dashboardController = new DashboardController($pdo);
    $stats = $dashboardController->getStats();

    // 2. Fetch Visi Misi (Legacy Query)
    $visiMisiList = $pdo->query("SELECT * FROM visi_misi ORDER BY tipe, konten_id ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Fallback values
    $stats = [
        'totalBerita' => 0,
        'totalMember' => 0,
        'totalRiset' => 0,
        'totalPendaftaran' => 0,
        'totalFasilitas' => 0
    ];
    $visiMisiList = [];
    $error_message = $error_message ?? "Gagal memuat data: " . $e->getMessage();
}

include_once __DIR__ . '/../includes/sidebar.php';
?>

<?php include __DIR__ . '/../views/dashboard_stats.php'; ?>

<div class="card">
    <div class="card-body" style="padding: 2rem;">
        <h4>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</h4>
        <p>Anda login sebagai <?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?>. Gunakan menu di sebelah
            kiri untuk mengelola konten website.</p>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success" style="margin-top: 20px;"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>
<?php if (isset($error_message) && !$show_visimisi_form): ?>
    <div class="alert alert-danger" style="margin-top: 20px;"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<hr style="margin: 40px 0;">

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Visi & Misi</span>
        </div>
        <?php if ($isAdminLab): ?>
            <button class="btn btn-primary" id="btnToggleVisiMisiForm">
                <?php echo $show_visimisi_form ? 'Tutup Form' : 'Tambah Visi & Misi'; ?>
            </button>
        <?php endif; ?>
    </div>

    <?php if ($isAdminLab): ?>
        <div class="form-container" id="visiMisiFormContainer"
            style="display: <?php echo $show_visimisi_form ? 'block' : 'none'; ?>;">
            <h4 id="visiMisiFormTitle">
                <?php echo $show_visimisi_form ? 'Ubah Data Visi & Misi' : 'Tambah Visi & Misi Baru'; ?>
            </h4>

            <form method="POST" action="dashboard.php">
                <input type="hidden" name="form_visimisi" value="1">
                <?php if ($show_visimisi_form): ?>
                    <input type="hidden" name="update_visimisi" value="1">
                    <input type="hidden" name="visimisi_id" value="<?= htmlspecialchars($visi_misi_to_edit['id']) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="tipe_visimisi">Tipe:</label>
                    <select class="form-control" id="tipe_visimisi" name="tipe" required>
                        <option value="visi" <?php if (($visi_misi_to_edit['tipe'] ?? '') === 'visi')
                            echo 'selected'; ?>>Visi
                        </option>
                        <option value="misi" <?php if (($visi_misi_to_edit['tipe'] ?? '') === 'misi')
                            echo 'selected'; ?>>Misi
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="konten_id_visimisi">Urutan:</label>
                    <input type="number" class="form-control" id="konten_id_visimisi" name="konten_id" min="1"
                        value="<?= htmlspecialchars($visi_misi_to_edit['konten_id'] ?? 1) ?>" required>
                </div>

                <div class="form-group">
                    <label for="deskripsi_visimisi">Deskripsi/Poin:</label>
                    <textarea class="form-control" id="deskripsi_visimisi" name="deskripsi" rows="3"
                        required><?= htmlspecialchars($visi_misi_to_edit['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit"
                        class="btn btn-primary"><?php echo $show_visimisi_form ? 'Simpan Perubahan' : 'Simpan'; ?></button>
                    <button type="button" class="btn btn-secondary" id="btnCancelVisiMisi">Batal</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

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
                            <?php if ($isAdminLab): ?>
                                <th class="table-aksi-col">Aksi</th>
                            <?php endif; ?>
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
                                <?php if ($isAdminLab): ?>
                                    <td class="table-aksi-col">
                                        <a href="dashboard.php?action=edit_visimisi&id=<?= $vm['id'] ?>" class="btn-icon btn-edit"
                                            title="Ubah">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <a href="dashboard.php?action=delete_visimisi&id=<?= $vm['id'] ?>"
                                            class="btn-icon btn-delete" title="Hapus"
                                            onclick="return confirm('Yakin hapus poin ini?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
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
        const vmFormContainer = document.getElementById('visiMisiFormContainer');
        const btnToggleVM = document.getElementById('btnToggleVisiMisiForm');
        const btnCancelVM = document.getElementById('btnCancelVisiMisi');
        const vmFormTitle = document.getElementById('visiMisiFormTitle');

        const isCurrentlyEditVM = <?php echo $show_visimisi_form ? 'true' : 'false'; ?>;
        const isAdminLabUser = <?php echo $isAdminLab ? 'true' : 'false'; ?>;

        if (isAdminLabUser && btnToggleVM) {
            btnToggleVM.addEventListener('click', () => {
                if (isCurrentlyEditVM) {
                    window.location.href = 'dashboard.php';
                    return;
                }

                if (vmFormContainer.style.display === 'block') {
                    vmFormContainer.style.display = 'none';
                    btnToggleVM.innerText = 'Tambah Visi & Misi';
                } else {
                    vmFormContainer.style.display = 'block';
                    btnToggleVM.innerText = 'Tutup Form';
                    vmFormTitle.innerText = 'Tambah Visi & Misi Baru';
                    vmFormContainer.scrollIntoView({ behavior: 'smooth' });
                    vmFormContainer.querySelector('form').reset();
                }
            });

            btnCancelVM.addEventListener('click', () => {
                window.location.href = 'dashboard.php';
            });
        }
    });
</script>

<?php include_once __DIR__ . '/../includes/table.php'; ?>