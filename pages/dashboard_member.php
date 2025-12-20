<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$pageTitle = 'Manajemen Member';
$activePage = 'member';

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap  = $_POST['nama_lengkap'];
    $bio           = $_POST['bio'];
    $posisi        = $_POST['posisi'];
    $nomor_telepon = $_POST['nomor_telepon']; 
    $status        = strtolower($_POST['status']); 
    $foto_profil   = null;
    
    // Handle Upload
    if (!empty($_FILES['foto_profil']['name'])) {
        $filename = uniqid() . '_' . basename($_FILES['foto_profil']['name']);
        $uploadPath = __DIR__ . '/../uploads/profile/' . $filename;
        
        if (!is_dir(__DIR__ . '/../uploads/profile/')) {
            mkdir(__DIR__ . '/../uploads/profile/', 0777, true);
        }

        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $uploadPath)) {
            $foto_profil = $filename;
        }
    }

    if (!empty($_POST['member_id'])) {
        // Mode: Update
        $id = $_POST['member_id'];
        if ($foto_profil === null) {
            $foto_profil = $_POST['existing_foto_profil'] ?? null;
        }

        $stmt = $pdo->prepare("UPDATE anggota_lab SET nama_lengkap=?, bio=?, posisi=?, nomor_telepon=?, status=?, foto_profil=? WHERE id=?");
        $stmt->execute([$nama_lengkap, $bio, $posisi, $nomor_telepon, $status, $foto_profil, $id]);

    } else {
    
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash('password', PASSWORD_DEFAULT);
    

        $stmt = $pdo->prepare("CALL tambah_anggota_lab(?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $username,
            $email,
            $password,
            $nama_lengkap,
            $posisi,
            $nomor_telepon
        ]);

    }
    
    header('Location: dashboard_member.php'); 
    exit;
}

// --- Handle Delete ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT foto_profil FROM anggota_lab WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();
    
    if ($old && $old['foto_profil']) {
        $filePath = __DIR__ . "/../uploads/profile/" . $old['foto_profil'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM anggota_lab WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: dashboard_member.php'); 
    exit;
}

// Fetch all members
$stmt = $pdo->query("SELECT * FROM anggota_lab ORDER BY nama_lengkap ASC");
$list = $stmt->fetchAll();

include_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <div class="tabs">
            <span class="tab-item active">Daftar Member</span>
        </div>
        <button class="btn btn-primary" id="btnShowForm">Tambah Anggota Lab</button>
        <button class="btn btn-secondary" id="btnCancelForm" style="display:none;">Tutup Form</button>
    </div>

    <div class="form-container" id="memberFormContainer">
        <h4 id="formTitle">Tambah Anggota Baru</h4>
        <form method="POST" enctype="multipart/form-data" id="memberForm">
            <input type="hidden" name="member_id" id="member_id">
            <input type="hidden" name="existing_foto_profil" id="existing_foto_profil">
            
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required placeholder="Nama Lengkap" >
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required placeholder="Usernmae">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" class="form-control" required placeholder="Email">
            </div>
            
            <div class="form-group">
                <label for="posisi">Posisi / Jabatan:</label>
                <input type="text" name="posisi" id="posisi" class="form-control" placeholder="Contoh: Ketua Lab, Programmer" required>
            </div>

            <div class="form-group">
                <label for="nomor_telepon">Nomor Telepon / WhatsApp:</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" placeholder="Contoh: 08123456789">
            </div>

            <div class="form-group">
                <label for="status">Status Keanggotaan:</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="aktif">Aktif</option>
                    <option value="alumni">Alumni</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bio">Bio Singkat:</label>
                <textarea name="bio" id="bio" class="form-control" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="image_input">Unggah Foto Profil:</label>
                <input type="file" name="foto_profil" id="image_input" class="form-control">
            </div>
            
            <div id="image_preview_container" style="display:none; margin-top:15px; text-align:center;">
                <p style="font-size:0.9rem; color:#666; margin-bottom:5px;">Preview Foto:</p>
                <img id="image_preview" src="" alt="Preview" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #eee;">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th class="table-thumb-col">Foto</th>
                    <th>Nama Lengkap</th>
                    <th>Status</th>
                    <th>Posisi</th>
                    <th>No. Telp</th> 
                    <th>Bio</th>
                    <th class="table-aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($list)): ?>
                    <tr><td colspan="7" class="empty-table">Belum ada anggota lab yang terdaftar.</td></tr>
                <?php endif; ?>

                <?php foreach ($list as $n): ?>
                <tr>
                    <td class="table-thumb-col">
                        <?php if (!empty($n['foto_profil'])): ?>
                            <img src="../uploads/profile/<?= htmlspecialchars($n['foto_profil']) ?>" class="table-thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <span class="table-thumbnail-placeholder" style="border-radius: 50%;">No Img</span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="font-weight: 500;"><?= htmlspecialchars($n['nama_lengkap']) ?></td>
                    
                    <td>
                        <?php if (strtolower($n['status']) == 'aktif'): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Alumni</span>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($n['posisi']) ?></td>
                    <td><?= htmlspecialchars($n['nomor_telepon'] ?? '-') ?></td>
                    <td style="font-size: 0.9rem; color: #666;">
                        <?php
                        $excerpt = strip_tags($n['bio']);
                        echo strlen($excerpt) > 30 ? htmlspecialchars(substr($excerpt, 0, 30)) . '...' : htmlspecialchars($excerpt);
                        ?>
                    </td>
                    
                    <td class="table-aksi-col">
                        <a href="#" class="btn-icon btn-edit" title="Edit"
                           onclick='editMember(
                               <?= $n['id'] ?>, 
                               <?= json_encode($n['nama_lengkap']) ?>, 
                               <?= json_encode($n['bio']) ?>, 
                               <?= json_encode($n['posisi']) ?>, 
                               <?= json_encode($n['nomor_telepon'] ?? '') ?>, 
                               <?= json_encode(strtolower($n['status'])) ?>, 
                               <?= json_encode($n['foto_profil'] ?? '') ?>
                           ); return false;'>
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        <a href="dashboard_member.php?delete=<?= $n['id'] ?>" class="btn-icon btn-delete" title="Hapus"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus member ini?')">
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
    const formContainer = document.getElementById('memberFormContainer');
    const btnShow = document.getElementById('btnShowForm');
    const btnCancel = document.getElementById('btnCancelForm');
    const formTitle = document.getElementById('formTitle');
    const form = document.getElementById('memberForm');

    // Input Fields
    const inputId = document.getElementById('member_id');
    const inputNama = document.getElementById('nama_lengkap');
    const inputPosisi = document.getElementById('posisi');
    const inputNoTelp = document.getElementById('nomor_telepon');
    const inputStatus = document.getElementById('status');
    const inputBio = document.getElementById('bio');
    const inputExistingFoto = document.getElementById('existing_foto_profil');
    
    // Image Preview Elements
    const inputImage = document.getElementById('image_input');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    btnShow.addEventListener('click', () => {
        form.reset(); 
        inputId.value = '';
        inputExistingFoto.value = '';
        inputNoTelp.value = ''; 
        if(inputStatus) inputStatus.value = 'aktif';

        formTitle.innerText = 'Tambah Anggota Baru';
        previewContainer.style.display = 'none';
        
        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';
        
        formContainer.scrollIntoView({ behavior: 'smooth' });
    });

    btnCancel.addEventListener('click', () => {
        formContainer.style.display = 'none';
        btnShow.style.display = 'inline-block';
        btnCancel.style.display = 'none';
        form.reset();
    });

    function editMember(id, nama, bio, posisi, no_telp, status, foto) {
        inputId.value = id;
        inputNama.value = nama;
        inputBio.value = bio;
        inputPosisi.value = posisi;
        inputNoTelp.value = no_telp;
        if(inputStatus) inputStatus.value = status;
        inputExistingFoto.value = foto;

        formTitle.innerText = 'Edit Data Anggota';
        
        if (foto) {
            previewImg.src = '../uploads/profile/' + foto;
            previewContainer.style.display = 'block';
        } else {
            previewContainer.style.display = 'none';
        }

        formContainer.style.display = 'block';
        btnCancel.style.display = 'inline-block';
        btnShow.style.display = 'none';

        formContainer.scrollIntoView({ behavior: 'smooth' });
    }

    inputImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            previewImg.src = URL.createObjectURL(file);
            previewContainer.style.display = 'block';
        }
    });
</script>

<?php include_once __DIR__ . '/../includes/table.php'; ?>