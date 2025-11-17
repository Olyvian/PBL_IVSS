<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/database.php'; 
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email']; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "❌ Konfirmasi password tidak cocok!";
    } else {
        
        // Gunakan variabel $pdo dari database.php
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $check_stmt->execute(['username' => $username, 'email' => $email]);
        
        if ($check_stmt->rowCount() > 0) {
            $message = "❌ Username atau email sudah digunakan!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Gunakan variabel $pdo dari database.php
            $insert_stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)" 
            );
            
            try {
                $insert_stmt->execute([
                    'username' => $username, 
                    'email' => $email, 
                    'password' => $hashed_password
                ]);
                
                $message = "✅ Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'violates check constraint') !== false) {
                     $message = "❌ Registrasi gagal. Pastikan nilai 'role' yang diizinkan di database adalah 'User'.";
                } else {
                     $message = "❌ Registrasi gagal. Error: " . htmlspecialchars($e->getMessage());
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PBL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
:root {
    --polinema-blue-dark: #072a52; 
    --polinema-orange: #f47f20; 
    --color-text-light: #ffffff;
    --color-text-dark: #333333;
}

body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex; 
    align-items: center; 
    justify-content: center; 
    color: var(--color-text-light);
    
    background: 
        linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.85)),
        /* Menggunakan latar belakang g4.png dari login.php, asumsi file ini tersedia */
        url('g4.png') center center no-repeat fixed; 
        
    background-size: cover; 
    background-color: #555; 
}

.card-login { /* Mengganti .card menjadi .card-login agar sesuai dengan style login */
    background-color: var(--polinema-blue-dark);
    color: var(--color-text-light);
    border-radius: 10px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);
    max-width: 450px;
    width: 100%;
}

.card-login h2 {
    color: var(--color-text-light);
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 5px;
}

.card-body-custom { /* Menambahkan class custom untuk padding */
    padding: 30px;
}

.greeting-text { /* Menambahkan teks sambutan untuk konsistensi */
    font-size: 0.9rem;
    margin-bottom: 20px;
    text-align: center;
    opacity: 0.9;
}

.form-control {
    background-color: rgba(255, 255, 255, 0.9);
    border: none;
    height: 50px;
    color: var(--color-text-dark);
    font-weight: 500;
}
.form-control:focus {
    background-color: var(--color-text-light);
    border-color: var(--polinema-orange);
    box-shadow: 0 0 5px rgba(244, 127, 32, 0.8);
}

.btn-custom {
    background-color: var(--polinema-orange);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 12px 0;
    box-shadow: 0 4px 15px rgba(244, 127, 32, 0.5);
    transition: background-color 0.3s ease;
}
.btn-custom:hover {
    background-color: #e0741c; 
    transform: translateY(-1px);
}
.alert-danger, .alert-success { /* Menyesuaikan alert agar sesuai dengan login, hanya menggunakan satu style */
    background-color: #ffc107;
    color: var(--color-text-dark);
    border: none;
    font-weight: 600;
    margin-bottom: 20px;
}

.card-login a, .container a { /* Menyesuaikan warna link */
    color: #ffc107;
    text-decoration: underline;
    font-weight: 500;
}
.card-login a:hover, .container a:hover {
    color: #ffd700;
}

/* Menghilangkan atau menyesuaikan style navbar yang ada di register.php */
.navbar {
    display: none; /* Menyembunyikan navbar agar sesuai dengan tampilan login.php */
}

/* Style tambahan untuk label di register.php agar terlihat di background gelap */
.form-label {
    color: var(--color-text-light);
    font-weight: 500;
    margin-bottom: 5px;
}

    </style>
</head>
<body>
    
    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card-login shadow-lg border-0"> 
                    <div class="card-body-custom">
                        <h2 class="text-center mb-4">Register</h2>
                        
                        <div class="greeting-text">
                            Silahkan isi data diri anda untuk membuat akun.
                        </div>
                        
                        <?php if (!empty($message)): ?>
                            <?php 
                            // Mengubah logika alert agar tetap menggunakan style kuning dari login.php
                            $alertClass = strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger';
                            ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-custom">Register</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>