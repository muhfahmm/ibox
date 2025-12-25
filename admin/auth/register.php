<?php
session_start();

// Jika sudah login, redirect ke index
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - iBox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 50px;
            color: #4a6cf7;
            margin-bottom: 15px;
        }

        .logo h2 {
            color: #1a1a2e;
            font-size: 24px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .register-form .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #555;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .input-with-icon input:focus {
            border-color: #4a6cf7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(74, 108, 247, 0.3);
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #4a6cf7;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <i class="fas fa-user-plus"></i>
            <h2>Register Admin</h2>
            <p>Buat akun admin baru</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message show" id="error-message">
                <i class="fas fa-exclamation-circle"></i> 
                <?php 
                if ($_GET['error'] == 'username_exists') {
                    echo 'Username sudah digunakan!';
                } elseif ($_GET['error'] == 'password_mismatch') {
                    echo 'Password tidak cocok!';
                } elseif ($_GET['error'] == 'empty') {
                    echo 'Semua field harus diisi!';
                } elseif ($_GET['error'] == 'short') {
                    echo 'Password minimal 6 karakter!';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="success-message show" id="success-message">
                <i class="fas fa-check-circle"></i> Registrasi berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <form class="register-form" action="api/proses-register.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="login.php">Login disini</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validasi panjang password
            if (password.length < 6) {
                alert('Password minimal 6 karakter!');
                return false;
            }
            
            // Validasi kesamaan password
            if (password !== confirmPassword) {
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }
            
            return true;
        }

        // Auto hide messages after 5 seconds
        setTimeout(() => {
            const errorMsg = document.getElementById('error-message');
            const successMsg = document.getElementById('success-message');
            
            if (errorMsg) errorMsg.style.display = 'none';
            if (successMsg) successMsg.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>