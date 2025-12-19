<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Manajemen Dataset';
$activePage = 'dataset';

// logika untuk simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dataset = $_POST['nama_dataset'];
    $deskripsi = $_POST['deskripsi'];
    $url = $_POST['url'];

    if (!empty($_POST['dataset_id'])) {
        // Mode Edit
        $id = $_POST['dataset_id'];
        $stmt = $pdo->prepare("UPDATE dataset SET nama_dataset=?, deskripsi=?, url=? WHERE id=?");
        $stmt->execute([$nama_dataset, $deskripsi, $url, $id]);
    } else {
        // Mode Tambah
        $stmt = $pdo->prepare("INSERT INTO dataset (nama_dataset, deskripsi, url) VALUES (?, ?, ?)");
        $stmt->execute([$nama_dataset, $deskripsi, $url]);
    }
    
    header('Location: dashboard_dataset.php');
    exit;
}

// logika untuk hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM dataset WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_dataset.php');
    exit;
}

// ambil data
$stmt = $pdo->query("SELECT * FROM dataset ORDER BY tanggal_ditambahkan DESC");
$list = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Dataset Penelitian</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Dataset</button>
        <button class="btn btn-secondary" id="btnCancelForm" style="display:none;">Tutup Form</button>
    </div>

    <div class="form-container" id="formContainer" style="display:none;">
        <h4 id="formTitle">Tambah Dataset Baru</h4>
        <form method="POST">
            <input type="hidden" name="dataset_id" id="dataset_id">
            
            <div class="form-group">
                <label for="nama_dataset">Nama Dataset:</label>
                <input type="text" name="nama_dataset" id="nama_dataset" class="form-control" placeholder="Contoh: Dataset Citra Daun Kopi" required>
            </div>

            <div class="form-group">
                <label for="url">Link Download / Sumber (URL):</label>
                <input type="url" name="url" id="url" class="form-control" placeholder="https://kaggle.com/..." required>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>Nama Dataset</th>
                    <th>Deskripsi</th>
                    <th>Link</th>
                    <th class="table-date-col">Tanggal</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr><td colspan="5" class="empty-table">Belum ada dataset tersedia.</td></tr>
                <?php endif; ?>

                <?php foreach ($list as $item): ?>
                <tr>
                    <td style="font-weight: 600; color: #0D4C7C;">
                        <?= htmlspecialchars($item['nama_dataset']) ?>
                    </td>
                    
                    <td style="color: #555; font-size: 0.9rem;">
                        <?= htmlspecialchars($item['deskripsi']) ?>
                    </td>

                    <td>
                        <a href="<?= htmlspecialchars($item['url']) ?>" target="_blank" class="badge bg-primary" style="text-decoration:none; padding: 5px 10px;">
                            <i class="fa-solid fa-link"></i> Buka Link
                        </a>
                    </td>

                    <td class="table-date-col">
                        <?= date('d M Y', strtotime($item['tanggal_ditambahkan'])) ?>
                    </td>
                    
                    <td class="table-aksi-col">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editDataset(
                               <?= $item['id'] ?>, 
                               <?= json_encode($item['nama_dataset']) ?>, 
                               <?= json_encode($item['deskripsi']) ?>, 
                               <?= json_encode($item['url']) ?>
                           ); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_dataset.php?delete=<?= $item['id'] ?>" class="btn-icon btn-delete" title="Hapus"
                           onclick="return confirm('Yakin ingin menghapus dataset ini?')">
                            <i class="fa-solid fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Script Toggle Form & Edit
    const formContainer = document.getElementById('formContainer');
    const btnShow = document.getElementById('btnShowForm');
    const btnCancel = document.getElementById('btnCancelForm');
    const formTitle = document.getElementById('formTitle');
    
    // Inputs
    const inputId = document.getElementById('dataset_id');
    const inputNama = document.getElementById('nama_dataset');
    const inputDeskripsi = document.getElementById('deskripsi');
    const inputUrl = document.getElementById('url');

    // form tambah
    btnShow.addEventListener('click', () => {
        inputId.value = '';
        inputNama.value = '';
        inputDeskripsi.value = '';
        inputUrl.value = '';
        formTitle.innerText = 'Tambah Dataset Baru';
        
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });

    // Sembunyikan Form
    btnCancel.addEventListener('click', () => {
        formContainer.style.display = 'none';
        btnShow.style.display = 'inline-block';
        btnCancel.style.display = 'none';
    });

    // form edit
    function editDataset(id, nama, deskripsi, url) {
        inputId.value = id;
        inputNama.value = nama;
        inputDeskripsi.value = deskripsi;
        inputUrl.value = url;

        formTitle.innerText = 'Edit Dataset';
        
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        formContainer.scrollIntoView({ behavior: 'smooth' });
    }
</script>
<?php include_once __DIR__ . '/../includes/table.php'; ?>