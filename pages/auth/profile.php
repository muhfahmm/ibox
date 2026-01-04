<?php
session_start();
require '../../db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from database
$query = "SELECT firstname, lastname, no_hp, email FROM user_autentikasi WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Calculate initials
$firstname = $user['firstname'];
$lastname = $user['lastname'];
$first_initial = !empty($firstname) ? strtoupper(substr($firstname, 0, 1)) : '';
$last_initial = !empty($lastname) ? strtoupper(substr($lastname, 0, 1)) : '';
$user_initials = $first_initial . $last_initial;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - iBox Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f5f5f7;
            color: #1d1d1f;
        }

        /* Navbar Styles */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-logo img {
            height: 40px;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-initials-badge {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007aff, #0056cc);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        /* Profile Container */
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007aff, #0056cc);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(0, 122, 255, 0.3);
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-email {
            color: #86868b;
            font-size: 16px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .profile-section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1d1d1f;
        }

        .profile-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .profile-info-row:last-child {
            border-bottom: none;
        }

        .profile-info-label {
            font-size: 15px;
            color: #86868b;
            font-weight: 500;
        }

        .profile-info-value {
            font-size: 15px;
            color: #1d1d1f;
            font-weight: 600;
        }

        .password-value {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .password-dots {
            letter-spacing: 3px;
        }

        /* Buttons */
        .btn-logout {
            width: 100%;
            padding: 16px;
            background: #ff3b30;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-logout:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 59, 48, 0.3);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #007aff;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: gap 0.3s;
        }

        .btn-back:hover {
            gap: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                margin: 20px auto;
            }

            .profile-card {
                padding: 20px;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }

            .profile-name {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-logo">
                <a href="../index.php">
                    <img src="../../../assets/img/logo/logo.png" alt="iBox Logo">
                </a>
            </div>
            <div class="navbar-user">
                <div class="user-initials-badge">
                    <?php echo $user_initials; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Content -->
    <div class="profile-container">
        <a href="../index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Beranda
        </a>

        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo $user_initials; ?>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h1>
            <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <!-- Account Information -->
        <div class="profile-card">
            <h2 class="profile-section-title">Informasi Akun</h2>
            
            <div class="profile-info-row">
                <span class="profile-info-label">Nama Depan</span>
                <span class="profile-info-value"><?php echo htmlspecialchars($firstname); ?></span>
            </div>

            <div class="profile-info-row">
                <span class="profile-info-label">Nama Belakang</span>
                <span class="profile-info-value"><?php echo htmlspecialchars($lastname); ?></span>
            </div>

            <div class="profile-info-row">
                <span class="profile-info-label">Email</span>
                <span class="profile-info-value"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <div class="profile-info-row">
                <span class="profile-info-label">Nomor HP</span>
                <span class="profile-info-value"><?php echo htmlspecialchars($user['no_hp']); ?></span>
            </div>

            <div class="profile-info-row">
                <span class="profile-info-label">Password</span>
                <span class="profile-info-value password-value">
                    <span class="password-dots">••••••••</span>
                </span>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="profile-card">
            <button class="btn-logout" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i>
                Keluar dari Akun
            </button>
        </div>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari akun?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
