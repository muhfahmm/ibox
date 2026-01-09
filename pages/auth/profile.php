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

// Cart Count
$cart_count = 0;
$uid = $_SESSION['user_id'];
$q_cart = $db->query("SELECT SUM(jumlah) as total FROM user_keranjang WHERE user_id = $uid");
if($q_cart && $row_cart = $q_cart->fetch_assoc()) {
    $cart_count = $row_cart['total'] ?? 0;
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        /* Navbar CSS from cart.php */
        .navbar-container {
            position: sticky;
            top: 0;
            z-index: 100;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .nav-top-container {
            padding: 0 5%;
            background-color: whitesmoke;
        }

        .navbar-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
            background-color: transparent;
        }

        .logo-hamburger-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Animasi Hamburger Button */
        .hamburger-menu {
            display: none;
            font-size: 24px;
            color: #333;
            cursor: pointer;
            background: none;
            border: none;
            padding: 8px;
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .hamburger-menu:hover {
            color: #007aff;
            background-color: rgba(0, 122, 255, 0.1);
            transform: rotate(180deg) scale(1.1);
        }

        .hamburger-menu:active {
            transform: rotate(180deg) scale(0.95);
        }

        .hamburger-menu i {
            transition: transform 0.3s ease;
        }

        .logo img {
            height: 50px;
            object-fit: contain;
        }

        .search-bar-menu {
            flex: 1;
            max-width: 500px;
            margin: 0 20px;
        }

        .search-bar-menu input[type="text"] {
            padding: 10px 20px;
            border: 1px solid #cccccc;
            border-radius: 20px;
            width: 100%;
            font-size: 14px;
            color: #333;
            transition: all 0.3s;
        }

        .search-bar-menu input[type="text"]:focus {
            outline: none;
            border-color: #007aff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
        }

        .nav-other-menu {
            display: flex;
            gap: 20px;
            font-size: 22px;
            color: #333;
            align-items: center;
        }

        .nav-other-menu i {
            cursor: pointer;
            transition: color 0.2s;
        }

        .nav-other-menu i:hover {
            color: #007aff;
        }
        
        .user-name-link {
            color: #333;
            transition: color 0.2s;
            text-decoration: none;
        }

        .user-name-link:hover {
            color: #007aff;
        }
        
        .user-icon {
            color: #333;
            font-size: 22px;
            transition: color 0.2s;
            text-decoration: none;
        }
        
        .user-icon:hover {
            color: #007aff;
        }

        /* Profile specific styles */
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
            .navbar-top {
                flex-wrap: wrap;
                padding-bottom: 10px;
            }

            .logo-hamburger-container {
                order: 1;
            }

            .search-bar-menu {
                order: 3;
                margin: 15px 0 0;
                max-width: 100%;
                flex: 0 0 100%;
            }

            .nav-other-menu {
                order: 2;
            }

            .nav-top-container {
                padding: 0 15px;
            }

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
            /* Cart Dropdown Styles - White Liquid Glass / Glassmorphism */
            .cart-dropdown-wrapper {
                position: relative;
            }

            .cart-dropdown {
                position: absolute;
                top: 100%;
                right: 0;
                width: 360px;
                
                /* White Glassmorphism Background */
                background: rgba(255, 255, 255, 0.7); 
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                
                border-radius: 16px; 
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                color: #1d1d1f; /* Dark text for white bg */
                overflow: hidden;
            }

            .cart-dropdown.active {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .cart-dropdown-header {
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                background-color: rgba(255, 255, 255, 0.1);
            }

            .cart-dropdown-title {
                font-size: 16px;
                font-weight: 600;
                color: #1d1d1f;
                text-shadow: none;
            }

            .cart-dropdown-link {
                font-size: 13px;
                color: #0071e3; /* Standard Apple Blue */
                text-decoration: none;
                font-weight: 500;
            }

            .cart-dropdown-link:hover {
                text-decoration: underline;
                color: #0056b3;
            }

            .cart-items-list {
                max-height: 350px;
                overflow-y: auto;
                padding: 0;
                list-style: none;
                margin: 0;
            }
            
            /* Custom Scrollbar for Glass */
            .cart-items-list::-webkit-scrollbar {
                width: 6px;
            }
            .cart-items-list::-webkit-scrollbar-track {
                background: transparent;
            }
            .cart-items-list::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 3px;
            }
            .cart-items-list::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.2);
            }

            .cart-item {
                display: flex;
                padding: 15px 20px;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05); 
                gap: 15px;
                transition: background 0.2s;
            }

            .cart-item:hover {
                background-color: rgba(0, 0, 0, 0.02); 
            }

            .cart-item-img {
                width: 60px;
                height: 60px;
                border-radius: 10px;
                background-color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                flex-shrink: 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                border: 1px solid rgba(0,0,0,0.05); /* Slight border for image */
            }

            .cart-item-img img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                padding: 5px;
            }

            .cart-item-details {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .cart-item-name {
                font-size: 14px;
                font-weight: 500;
                color: #1d1d1f;
                margin-bottom: 4px;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .cart-item-price-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: 4px;
            }

            .cart-item-qty {
                font-size: 13px;
                color: #86868b;
            }
            
            .cart-item-price {
                font-size: 14px;
                font-weight: 600;
                color: #1d1d1f;
                text-shadow: none;
            }

            .cart-empty-state {
                padding: 40px 20px;
                text-align: center;
                color: #86868b;
                font-size: 14px;
            }

            /* Triangle/Arrow - White Glass */
            .cart-dropdown::before {
                content: '';
                position: absolute;
                top: -6px;
                right: 20px;
                width: 12px;
                height: 12px;
                background: rgba(255, 255, 255, 0.7); 
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                transform: rotate(45deg);
                border-top: 1px solid rgba(255, 255, 255, 0.5);
                border-left: 1px solid rgba(255, 255, 255, 0.5);
                z-index: -1;
            }
    </style>
</head>
<body>
    <!-- Navbar from cart.php structure -->
    <nav class="navbar-container">
        <div class="nav-top-container">
            <div class="navbar-top">
                <div class="logo-hamburger-container">
                    <button class="hamburger-menu" id="hamburgerBtn" style="display: none;">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="logo">
                        <a href="../index.php">
                            <img src="../../assets/img/logo/logo.png" alt="iBox Logo">
                        </a>
                    </div>
                </div>
                <div class="search-bar-menu">
                    <form action="">
                        <input type="text" placeholder="Cari produk di iBox">
                    </form>
                </div>
                <div class="nav-other-menu">
                    <a href="profile.php" class="user-name-link" style="text-decoration: none; color: #333; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-person-circle" style="font-size: 20px;"></i>
                        <span><?php echo htmlspecialchars($user_initials); ?></span>
                    </a>
                    
                    <!-- Cart Icon with Dropdown Wrapper -->
                    <div class="position-relative cart-dropdown-wrapper">
                        <a href="../cart/cart.php" class="bag-icon position-relative text-dark text-decoration-none" id="cartDropdownTrigger">
                            <i class="bi bi-bag"></i>
                            <?php if (isset($cart_count) && $cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white" id="cartBadge" style="font-size: 10px; padding: 3px 6px;">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Dropdown Content -->
                        <div class="cart-dropdown" id="cartDropdown">
                            <div class="cart-dropdown-header">
                                <div class="cart-dropdown-title">
                                    Keranjang (<span id="cartDropdownCount">0</span>)
                                </div>
                                <a href="../cart/cart.php" class="cart-dropdown-link">Lihat</a>
                            </div>
                            <ul class="cart-items-list" id="cartItemsList">
                                <!-- Items will be populated via JS -->
                                <li class="cart-empty-state">Memuat keranjang...</li>
                            </ul>
                        </div>
                    </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Cart Dropdown Elements
            const cartTrigger = document.getElementById('cartDropdownTrigger');
            const cartDropdown = document.getElementById('cartDropdown');
            const cartList = document.getElementById('cartItemsList');
            const cartDropdownCount = document.getElementById('cartDropdownCount');
            const cartBadge = document.getElementById('cartBadge');

            // Handle Cart Dropdown
            let isCartOpen = false;

            if(cartTrigger) {
                cartTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    if(isCartOpen) {
                        closeCartDropdown();
                    } else {
                        openCartDropdown();
                    }
                });
            }

            function openCartDropdown() {
                if(!cartDropdown) return;
                cartDropdown.classList.add('active');
                isCartOpen = true;
                fetchCartData();
            }

            function closeCartDropdown() {
                if(!cartDropdown) return;
                cartDropdown.classList.remove('active');
                isCartOpen = false;
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if(isCartOpen && cartDropdown && !cartDropdown.contains(e.target) && !cartTrigger.contains(e.target)) {
                    closeCartDropdown();
                }
            });

            function fetchCartData() {
                // Adjust path based on where this file is included
                // profile.php is in pages/auth/
                fetch('../cart/get_cart_dropdown.php')
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            renderCartItems(data.items, data.count);
                        } else {
                            renderError('Gagal memuat data');
                        }
                    })
                    .catch(err => {
                        console.error('Cart fetch error:', err);
                         renderError('Gagal memuat keranjang');
                    });
            }

            function renderCartItems(items, count) {
                // Update counts
                if(cartDropdownCount) cartDropdownCount.textContent = count;
                
                if(items.length === 0) {
                    cartList.innerHTML = '<li class="cart-empty-state">Keranjang Anda kosong</li>';
                    return;
                }

                let html = '';
                items.forEach(item => {
                    let imgPath;
                    
                    if (item.image) {
                        if (item.image.startsWith('assets/')) {
                            // profile.php -> pages/auth/ -> ../../assets/
                            imgPath = '../../' + item.image;
                        } else {
                             // profile.php -> pages/auth/ -> ../../admin/uploads/
                            imgPath = '../../admin/uploads/' + item.image;
                        }
                    } else {
                         imgPath = '../../assets/img/logo/logo.png';
                    } 
                    
                    html += `
                        <li class="cart-item">
                            <div class="cart-item-img">
                                <img src="${imgPath}" alt="${item.name}" onerror="this.src='../../assets/img/logo/logo.png'">
                            </div>
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price-row">
                                    <div class="cart-item-qty">${item.qty} Barang</div>
                                    <div class="cart-item-price">${item.formatted_price}</div>
                                </div>
                            </div>
                        </li>
                    `;
                });
                cartList.innerHTML = html;
            }
            
            function renderError(msg) {
                cartList.innerHTML = `<li class="cart-empty-state text-danger">${msg}</li>`;
            }
        });
    </script>
</html>
