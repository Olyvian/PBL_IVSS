<?php
include 'koneksi.php';
$message = '';

if (isset($_SESSION['user_id'])) {
    header("Location: galeri.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email']; 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "❌ Konfirmasi password tidak cocok!";
    } else {
        $query = pg_query_params($koneksi, "SELECT id FROM users WHERE username=$1 OR email=$2", array($username, $email));
        
        if (pg_num_rows($query) > 0) {
            $message = "❌ Username atau email sudah digunakan!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = pg_query_params(
                $koneksi, 
                "INSERT INTO users (username, email, password) VALUES ($1, $2, $3)", 
                array($username, $email, $hashed_password)
            );
            
            if ($insert_query) {
                $message = "✅ Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error_detail = pg_last_error($koneksi);
                $message = "❌ Registrasi gagal. Error: " . htmlspecialchars($error_detail);
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
    <style>
        body {
            background: linear-gradient(to right, #F5A623, #6ED7B1);
            color: #333333;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        
        .navbar {
            background: transparent !important;
            border-bottom: none;
            box-shadow: none;
            position: absolute;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding-top: 20px;
        }
        .navbar-brand span { color: #ffffff !important; text-shadow: 1px 1px 3px rgba(0,0,0,0.3); } 
        .nav-link { color: #ffffff !important; text-shadow: 1px 1px 3px rgba(0,0,0,0.3); }
        .nav-link.active, .nav-link:hover { color: #f0f0f0 !important; text-shadow: 0 0 5px rgba(255, 255, 255, 0.7); }

        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            color: #4CAF50; 
            font-weight: 700;
        }

        .btn-custom {
            background: linear-gradient(90deg, #F5A623, #6ED7B1);
            border: none;
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            background-color: #f8f9fa;
            color: #333333;
            border: 1px solid #ced4da;
        }
        .form-control:focus {
            background-color: #ffffff;
            color: #333333;
            border-color: #6ED7B1;
            box-shadow: 0 0 5px rgba(110, 215, 177, 0.5);
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php" style="font-weight: 600;"><span>PBL</span></a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link active" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Buat Akun</h2>
                        
                        <?php if (!empty($message)): ?>
                            <?php 
                            $alertClass = strpos($message, '✅') !== false ? 'alert-success' : 'alert-danger';
                            ?>
                            <div class="alert <?php echo $alertClass; ?> text-center" role="alert">
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
                                <button type="submit" class="btn btn-custom btn-lg">Register</button>
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