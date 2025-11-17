<?php
include 'config/database.php';

$message = '';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_identifier = trim(htmlspecialchars($_POST['username'])); 
    $password = $_POST['password'];

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = :login_id OR email = :login_id"
    );
    $stmt->execute(['login_id' => $login_identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        $_SESSION['success_message'] = "Anda berhasil login"; 
        
        header("Location: index.php"); 
        exit;
    } else {
        try {
            $stmt = $koneksi->prepare(
                "SELECT id, username, password, role FROM users WHERE username = :login_id OR email = :login_id"
            );
            $stmt->execute(['login_id' => $login_identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success_message'] = "✅ Anda berhasil login!"; 
                
                header("Location: index.php"); 
                exit;
            } else {
                $message = "❌ NIM/Email atau password salah!";
            }
        } catch (PDOException $e) {
            $message = "❌ Terjadi kesalahan database. Mohon coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login POLINEMA</title>
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
        url('g4.png') center center no-repeat fixed;
        
    background-size: cover; 
    background-color: #555; 
}

.card-login {
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

.card-body-custom {
    padding: 30px;
}

.greeting-text {
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
.alert-danger {
    background-color: #ffc107;
    color: var(--color-text-dark);
    border: none;
    font-weight: 600;
    margin-bottom: 20px;
}
.card-login a {
    color: #ffc107;
    text-decoration: underline;
    font-weight: 500;
}
.card-login a:hover {
    color: #ffd700;
}
    </style>
</head>
<body>

    <div class="polinema-header">
        </div>
        <div class="container">
        <div class="d-flex justify-content-center w-100">
            <div class="">
                <div class="card-login shadow-lg border-0">
                    <div class="card-body-custom">
                        <h2 class="text-center">LOGIN</h2>
                        
                        <div class="greeting-text">
                            Selamat datang, **Silahkan masukkan username/NIM dan password** anda.
                        </div>
                        
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username / NIM" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-custom">LOGIN</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
