<?php
session_start();
$notification = isset($_SESSION['notification']) ? $_SESSION['notification'] : null;
unset($_SESSION['notification']); // Clear after reading
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar ID iBox - iBox Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f5f5f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #1d1d1f;
        }

        .container {
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            height: 40px;
            margin-bottom: 15px;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1d1d1f;
        }

        .auth-subtitle {
            font-size: 15px;
            color: #86868b;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1d1d1f;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d2d2d7;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #007aff;
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background-color: #007aff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #0071e3;
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #86868b;
        }

        .auth-link {
            color: #007aff;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-link:hover {
            text-decoration: underline;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #86868b;
            cursor: pointer;
        }

        .btn-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f5f5f7;
            border: none;
            color: #86868b;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-close:hover {
            background: #e8e8ed;
            color: #1d1d1f;
        }

        /* Notification Modal */
        .notification-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .notification-overlay.active {
            display: flex;
        }

        .notification-modal {
            background: white;
            max-width: 400px;
            width: 90%;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            padding: 40px 30px;
            animation: modalSlideUp 0.3s ease;
        }

        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .notification-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .notification-icon.success {
            background: #34c759;
        }

        .notification-icon.error {
            background: #ff3b30;
        }

        .notification-icon i {
            font-size: 35px;
            color: white;
        }

        .notification-title {
            font-size: 24px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
        }

        .notification-message {
            font-size: 15px;
            color: #6e6e73;
            margin-bottom: 30px;
        }

        .btn-notification {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border-radius: 12px;
            background: #007aff;
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-notification:hover {
            background: #0071e3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card" style="position: relative;">
            <button class="btn-close" onclick="window.history.back()" title="Kembali">
                <i class="fas fa-times"></i>
            </button>
            <div class="logo-container">
                <img src="../../assets/img/logo/logo.png" alt="iBox Logo">
                <h1 class="auth-title">Buat ID iBox</h1>
                <p class="auth-subtitle">Satu akun untuk semua layanan iBox.</p>
            </div>

            <form action="api/proses-register.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Depan</label>
                        <input type="text" name="firstname" class="form-input" required placeholder="John">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Belakang</label>
                        <input type="text" name="lastname" class="form-input" placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Handphone</label>
                    <input type="tel" name="no_hp" class="form-input" required placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required placeholder="nama@email.com">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-input" required placeholder="Masukkan password Anda">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="notification-overlay">
        <div class="notification-modal">
            <div class="notification-icon" id="notificationIcon">
                <i class="fas fa-check" id="notificationIconSymbol"></i>
            </div>
            <h3 class="notification-title" id="notificationTitle">Notifikasi</h3>
            <p class="notification-message" id="notificationMessage"></p>
            <button class="btn-notification" onclick="closeNotification()">OK</button>
        </div>
    </div>
                </div>

                <button type="submit" class="btn-submit">Buat Akun</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="login.php" class="auth-link">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function closeNotification() {
            document.getElementById('notificationModal').classList.remove('active');
        }

        // Show notification if exists
        <?php if ($notification): ?>
        window.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('notificationModal');
            const icon = document.getElementById('notificationIcon');
            const iconSymbol = document.getElementById('notificationIconSymbol');
            const title = document.getElementById('notificationTitle');
            const message = document.getElementById('notificationMessage');

            const type = '<?php echo $notification['type']; ?>';
            const msg = '<?php echo addslashes($notification['message']); ?>';

            if (type === 'success') {
                icon.classList.add('success');
                icon.classList.remove('error');
                iconSymbol.className = 'fas fa-check';
                title.textContent = 'Berhasil!';
            } else {
                icon.classList.add('error');
                icon.classList.remove('success');
                iconSymbol.className = 'fas fa-times';
                title.textContent = 'Gagal!';
            }

            message.textContent = msg;
            modal.classList.add('active');
        });
        <?php endif; ?>
    </script>
</body>
</html>
