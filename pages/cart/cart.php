<?php
session_start();
require '../db.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_initials = '';
if ($is_logged_in) {
    $firstname = isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '';
    $lastname = isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '';
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
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Populer - iBox Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f7f7f7;
        }

        /* Breadcrumb CSS */
        .breadcrumb-container {
            padding: 15px 5%;
            background-color: #f7f7f7;
            font-size: 14px;
            color: #888;
            border-bottom: 1px solid #e0e0e0;
        }

        .breadcrumb-container a {
            color: #007aff;
            text-decoration: none;
        }

        .breadcrumb-container a:hover {
            text-decoration: underline;
        }

        .breadcrumb-separator {
            margin: 0 8px;
            color: #ccc;
        }

        .breadcrumb-current {
            color: #333;
            font-weight: 500;
        }

        /* New style for product links in dropdown */
        .dropdown-product-link {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            color: #2c3e50;
            border-left: 3px solid #e3e8ef;
            margin-bottom: 4px;
            text-decoration: none;
            display: block;
            padding: 8px 15px;
            font-size: 13px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dropdown-product-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(0, 122, 255, 0.05) 0%, rgba(0, 122, 255, 0.1) 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 0;
        }

        .dropdown-product-link:hover::before {
            width: 100%;
        }

        .dropdown-product-link:hover {
            background: linear-gradient(135deg, #f0f7ff 0%, #e3f2fd 100%);
            color: #007aff;
            border-left-color: #007aff;
            padding-left: 20px;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.15);
            transform: translateX(4px);
        }

        .dropdown-category {
            font-size: 11px;
            color: #1d1d1f;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 18px;
            margin-top: 10px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-left: 4px solid #007aff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.12), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .dropdown-category::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 100%);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <!-- navbar -->
    <nav class="navbar-container">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: #f9f9f9;
                color: #333;
                overflow-x: hidden;
            }

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
            }

            .nav-other-menu i {
                cursor: pointer;
                transition: color 0.2s;
            }

            .nav-other-menu i:hover {
                color: #007aff;
            }

            .header-top-container {
                background-color: white;
            }

            .header-top {
                padding: 10px 5%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            /* Menu Desktop dengan Dropdown */
            .list-menu {
                position: relative;
            }

            .list-menu ul {
                display: flex;
                justify-content: space-between;
                list-style: none;
                padding: 5px 0;
                margin: 0;
                position: relative;
                transition: all 0.3s ease;
            }

            .list-menu>ul>li {
                position: relative;
            }

            .list-menu>ul>li>a {
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                color: #333;
                padding: 12px 15px;
                transition: all 0.2s;
                display: block;
                border-bottom: 2px solid transparent;
                position: relative;
                white-space: nowrap;
                cursor: pointer;
            }

            .list-menu>ul>li>a:hover {
                color: #007aff;
                border-bottom-color: #007aff;
            }

            .list-menu>ul>li>a.active {
                color: #007aff;
                border-bottom-color: #007aff;
            }

            /* MODIFIKASI: Dropdown Styles dengan efek toggle dan scroll */
            .dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                background-color: white;
                min-width: 220px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                border-radius: 8px;
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                z-index: 100;
                padding: 15px 0;
                border: 1px solid #f0f0f0;
                max-height: 0;
                overflow: hidden;
                overflow-y: auto;
                /* Mengganti overflow: hidden dengan overflow-y: auto */
                scrollbar-width: thin;
                /* Untuk Firefox */
                scrollbar-color: rgba(0, 122, 255, 0.3) transparent;
                /* Untuk Firefox */
            }

            .dropdown.active {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                max-height: 70vh;
                /* Menggunakan viewport height untuk batas maksimal */
                overflow-y: auto;
                /* Pastikan bisa di-scroll */
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            /* MODIFIKASI: Custom scrollbar untuk dropdown (Webkit browsers) */
            .dropdown::-webkit-scrollbar {
                width: 6px;
            }

            .dropdown::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 3px;
            }

            .dropdown::-webkit-scrollbar-thumb {
                background: rgba(0, 122, 255, 0.3);
                border-radius: 3px;
            }

            .dropdown::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 122, 255, 0.5);
            }

            /* MODIFIKASI: Class untuk menyembunyikan menu lainnya saat dropdown aktif */
            .list-menu ul.focused-mode>li:not(.active-menu-item)>a {
                opacity: 0.3;
                pointer-events: none;
                transform: translateY(5px);
            }

            .list-menu ul.focused-mode>li:not(.active-menu-item) .dropdown {
                display: none;
            }

            .list-menu ul.focused-mode>li.active-menu-item>a {
                color: #007aff;
                border-bottom-color: #007aff;
                font-weight: 600;
                transform: translateY(0);
            }

            .dropdown::before {
                content: '';
                position: absolute;
                top: -8px;
                left: 25px;
                width: 16px;
                height: 16px;
                background-color: white;
                transform: rotate(45deg);
                border-top: 1px solid #f0f0f0;
                border-left: 1px solid #f0f0f0;
                z-index: -1;
            }

            .dropdown ul {
                display: block;
                padding: 0;
            }

            .dropdown li {
                display: block;
                margin: 0;
            }

            .dropdown a {
                text-decoration: none;
                color: #555;
                font-size: 13.5px;
                font-weight: 500;
                padding: 10px 20px;
                display: block;
                transition: all 0.2s;
                border-left: 3px solid transparent;
            }

            .dropdown a:hover {
                background-color: #f8faff;
                color: #007aff;
                padding-left: 25px;
                border-left-color: #007aff;
            }

            .dropdown-category {
                font-size: 11px;
                color: #999;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 8px 20px;
                margin-top: 5px;
                border-bottom: 1px solid #f0f0f0;
                margin-bottom: 5px;
                position: sticky;
                top: 0;
                z-index: 2;
                background-color: white;
            }

            .dropdown-category:first-child {
                margin-top: 0;
            }

            /* MODIFIKASI: Pastikan konten dropdown memiliki padding bottom */
            .dropdown>ul:last-child {
                padding-bottom: 15px;
            }

            /* Specific dropdown contents */
            .mac-dropdown {
                min-width: 300px;
            }

            .ipad-dropdown {
                min-width: 250px;
            }

            .iphone-dropdown {
                min-width: 280px;
            }

            .watch-dropdown {
                min-width: 250px;
            }

            .music-dropdown {
                min-width: 200px;
            }

            .airtag-dropdown {
                min-width: 220px;
            }

            .aksesori-dropdown {
                min-width: 280px;
            }

            .layanan-dropdown {
                min-width: 220px;
            }

            .event-dropdown {
                min-width: 200px;
            }

            .bisnis-dropdown {
                min-width: 200px;
            }

            .edukasi-dropdown {
                min-width: 200px;
            }

            /* MODIFIKASI: Close button untuk focused mode dengan sticky positioning */
            .dropdown-close-btn {
                position: sticky;
                /* Ubah dari absolute ke sticky */
                top: 10px;
                right: 10px;
                float: right;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background-color: #f8f9fa;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                color: #666;
                transition: all 0.2s;
                opacity: 0;
                visibility: hidden;
                transform: scale(0.8);
                z-index: 101;
                margin-bottom: 10px;
            }

            .dropdown.active .dropdown-close-btn {
                opacity: 1;
                visibility: visible;
                transform: scale(1);
            }

            .dropdown-close-btn:hover {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
            }

            /* MODIFIKASI: Pastikan dropdown tidak melebihi batas layar */
            @media (max-height: 700px) {
                .dropdown.active {
                    max-height: 60vh;
                }
            }

            @media (max-height: 500px) {
                .dropdown.active {
                    max-height: 50vh;
                }
            }

            /* Sidebar Styles */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -320px;
                width: 300px;
                height: 100%;
                background-color: white;
                z-index: 1000;
                box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
                transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
            }

            .sidebar.active {
                left: 0;
            }

            /* Header Sidebar */
            .sidebar-header {
                display: flex;
                align-items: center;
                padding: 20px 25px;
                border-bottom: 2px solid #f0f0f0;
                background-color: white;
                min-height: 80px;
                gap: 20px;
            }

            /* Animasi Close Button */
            .close-sidebar {
                font-size: 24px;
                color: #333;
                cursor: pointer;
                background: none;
                border: none;
                padding: 8px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                position: relative;
                order: 1;
            }

            .close-sidebar:hover {
                color: #ff3b30;
                background-color: rgba(255, 59, 48, 0.1);
                transform: rotate(90deg) scale(1.1);
            }

            .close-sidebar:active {
                transform: rotate(90deg) scale(0.95);
            }

            .close-sidebar i {
                transition: transform 0.3s ease;
            }

            .close-sidebar:hover i {
                transform: scale(1.1);
            }

            /* Logo di sidebar */
            .sidebar-logo {
                display: flex;
                align-items: center;
                order: 2;
            }

            .sidebar-logo img {
                height: 50px;
                object-fit: contain;
                transition: transform 0.3s ease;
            }

            .sidebar-logo:hover img {
                transform: scale(1.05);
            }

            /* Sidebar Menu dengan Sub-Dropdown iOS Style */
            .sidebar-menu {
                flex: 1;
                padding: 0;
                overflow-y: auto;
                position: relative;
                -webkit-overflow-scrolling: touch;
                /* Untuk scroll yang lebih smooth di mobile */
            }

            .sidebar-menu ul {
                list-style: none;
                transition: all 0.3s ease;
            }

            /* Menu Item Utama */
            .sidebar-menu>ul>li {
                border-bottom: 1px solid #f5f5f5;
                transition: all 0.3s ease;
            }

            .sidebar-menu>ul>li:last-child {
                border-bottom: none;
            }

            .sidebar-menu>ul>li>a {
                text-decoration: none;
                font-size: 16px;
                font-weight: 500;
                color: #333;
                padding: 18px 25px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                transition: all 0.3s ease;
                position: relative;
                background-color: white;
            }

            .sidebar-menu>ul>li>a .menu-arrow {
                font-size: 14px;
                color: #999;
                transition: transform 0.3s ease;
            }

            .sidebar-menu>ul>li>a:hover {
                background-color: #f8faff;
                color: #007aff;
            }

            .sidebar-menu>ul>li>a::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background-color: #007aff;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar-menu>ul>li>a:hover::before {
                transform: translateX(0);
            }

            /* MODIFIKASI: Sidebar focused mode */
            .sidebar.focused-mode .sidebar-menu>ul>li:not(.active-menu-item) {
                display: none;
            }

            .sidebar.focused-mode .sidebar-menu>ul>li.active-menu-item {
                border-bottom: none;
            }

            .sidebar.focused-mode .sidebar-header {
                display: none;
            }

            .sidebar.focused-mode .sidebar-bottom {
                display: none;
            }

            /* MODIFIKASI: Sidebar dropdown dengan scroll */
            .sidebar-dropdown {
                background-color: #f9f9f9;
                max-height: 0;
                overflow: hidden;
                overflow-y: auto;
                /* Tambahkan untuk scroll */
                transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                border-left: 3px solid transparent;
                scrollbar-width: thin;
                /* Untuk Firefox */
                scrollbar-color: rgba(0, 122, 255, 0.3) transparent;
                /* Untuk Firefox */
            }

            .sidebar-dropdown.active {
                max-height: 65vh;
                /* Menggunakan viewport height */
                overflow-y: auto;
            }

            /* MODIFIKASI: Custom scrollbar untuk sidebar dropdown (Webkit) */
            .sidebar-dropdown::-webkit-scrollbar {
                width: 4px;
            }

            .sidebar-dropdown::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 2px;
            }

            .sidebar-dropdown::-webkit-scrollbar-thumb {
                background: rgba(0, 122, 255, 0.3);
                border-radius: 2px;
            }

            @media (max-height: 700px) {
                .sidebar-dropdown.active {
                    max-height: 55vh;
                }
            }

            @media (max-height: 500px) {
                .sidebar-dropdown.active {
                    max-height: 45vh;
                }
            }

            /* Sub-Dropdown Items */
            .sidebar-dropdown a {
                display: flex;
                align-items: center;
                padding: 15px 25px 15px 35px;
                text-decoration: none;
                color: #555;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s ease;
                border-bottom: 1px solid #f0f0f0;
                position: relative;
            }

            .sidebar-dropdown a:last-child {
                border-bottom: none;
            }

            .sidebar-dropdown a:hover {
                background-color: #f0f7ff;
                color: #007aff;
                padding-left: 40px;
            }

            /* Category Titles in Dropdown */
            .sidebar-dropdown .dropdown-category {
                font-size: 12px;
                color: #999;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 12px 25px 8px 35px;
                margin-top: 5px;
                border-bottom: 1px solid #f0f0f0;
                background-color: #f9f9f9;
                position: sticky;
                top: 0;
                z-index: 2;
            }

            .sidebar-dropdown .dropdown-category:first-child {
                margin-top: 0;
            }

            /* MODIFIKASI: Back button untuk sidebar focused mode */
            .sidebar-back-btn {
                display: none;
                padding: 18px 25px;
                background-color: #f8f9fa;
                border-bottom: 2px solid #e0e0e0;
                cursor: pointer;
                font-size: 15px;
                font-weight: 600;
                color: #007aff;
                transition: all 0.3s ease;
                align-items: center;
                gap: 10px;
            }

            .sidebar.focused-mode .sidebar-back-btn {
                display: flex;
            }

            .sidebar-back-btn:hover {
                background-color: #e9ecef;
                color: #0056cc;
            }

            .sidebar-back-btn i {
                font-size: 16px;
            }

            /* Bagian bawah sidebar */
            .sidebar-bottom {
                padding: 25px;
                border-top: 2px solid #f0f0f0;
                background-color: #f9f9f9;
                transition: all 0.3s ease;
            }

            .sidebar-auth {
                display: flex;
                gap: 15px;
                margin-bottom: 20px;
            }

            .sidebar-auth-btn {
                flex: 1;
                padding: 14px 15px;
                border: none;
                border-radius: 10px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                text-align: center;
                letter-spacing: 0.5px;
            }

            .btn-login {
                background-color: #007aff;
                color: white;
                box-shadow: 0 2px 5px rgba(0, 122, 255, 0.2);
            }

            .btn-login:hover {
                background-color: #0056cc;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .btn-login:active {
                transform: translateY(-1px);
                box-shadow: 0 2px 5px rgba(0, 122, 255, 0.2);
            }

            .btn-register {
                background-color: white;
                color: #007aff;
                border: 2px solid #007aff;
                box-shadow: 0 2px 5px rgba(0, 122, 255, 0.1);
            }

            .btn-register:hover {
                background-color: #f0f7ff;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.2);
            }

            .btn-register:active {
                transform: translateY(-1px);
                box-shadow: 0 2px 5px rgba(0, 122, 255, 0.1);
            }

            /* Responsive Styles */
            @media (max-width: 1200px) {

                .nav-top-container,
                .header-top {
                    padding: 0 3%;
                }

                .search-bar-menu {
                    max-width: 400px;
                }

                .list-menu>ul>li>a {
                    padding: 12px 10px;
                    font-size: 13.5px;
                }
            }

            @media (max-width: 2030px) {
                .search-bar-menu input[type="text"] {
                    width: 100%;
                }

                .list-menu ul {
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 15px;
                }

                .dropdown {
                    min-width: 200px;
                }
            }

            @media (max-width: 1100px) {
                .hamburger-menu {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .list-menu {
                    display: none;
                }

                .search-bar-menu {
                    max-width: 300px;
                }

                .nav-other-menu {
                    gap: 15px;
                }
            }

            @media (max-width: 576px) {
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

                .nav-top-container,
                .header-top {
                    padding: 0 15px;
                }

                .sidebar {
                    width: 280px;
                    left: -280px;
                }

                .sidebar-header {
                    padding: 20px 20px;
                }

                .sidebar-menu>ul>li>a {
                    padding: 16px 20px;
                    font-size: 15px;
                }

                .sidebar-dropdown a {
                    padding: 14px 20px 14px 30px;
                    font-size: 13.5px;
                }

                .sidebar-dropdown .dropdown-category {
                    padding: 10px 20px 6px 30px;
                    font-size: 11px;
                }
            }
            
            /* Cart Dropdown Styles - White Liquid Glass / Glassmorphism */
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
                border-bottom: 1px solid rgba(0, 0, 0, 0.05); 
                transition: background 0.2s;
            }

            .cart-item:hover {
                background-color: rgba(0, 122, 255, 0.05); 
            }

            .cart-item-link {
                display: flex;
                padding: 15px 20px;
                gap: 15px;
                text-decoration: none;
                color: inherit;
                transition: all 0.2s;
                cursor: pointer;
            }

            .cart-item-link:hover {
                text-decoration: none;
                color: inherit;
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
                -webkit-line-clamp: 2;
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
        <div class="wrapper">
            <div class="nav-top-container">
                <div class="navbar-top">
                    <div class="logo-hamburger-container">
                        <button class="hamburger-menu" id="hamburgerBtn">
                            <i class="bi bi-list"></i>
                        </button>
                        <div class="logo">
                            <a href="../index.php">
                                <img src="../assets/img/logo/logo.png" alt="iBox Logo">
                            </a>
                        </div>
                    </div>
                    <div class="search-bar-menu">
                        <form action="">
                            <input type="text" placeholder="Cari produk di iBox">
                        </form>
                    </div>
                    <div class="nav-other-menu">
                        <?php if ($is_logged_in): ?>
                            <a href="../auth/profile.php" class="user-name-link" style="text-decoration: none; color: #333; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-person-circle" style="font-size: 20px;"></i>
                                <span><?php echo htmlspecialchars($user_initials); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="../auth/login.php" class="user-icon">
                                <i class="bi bi-person-fill"></i>
                            </a>
                        <?php endif; ?>
                        <!-- Cart Icon with Dropdown Wrapper -->
                        <div class="position-relative cart-dropdown-wrapper">
                            <a href="cart.php" class="bag-icon position-relative text-dark text-decoration-none" id="cartDropdownTrigger">
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
                                    <a href="cart.php" class="cart-dropdown-link">Lihat</a>
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

            <div class="header-top-container">
                <div class="header-top">
                    <div class="list-menu">
                        <?php
                        // Query untuk mengambil kategori produk dari database

                        // Mac
                        $mac_categories_query = "SELECT DISTINCT kategori FROM admin_produk_mac WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $mac_categories_result = mysqli_query($db, $mac_categories_query);
                        $mac_categories = [];
                        while ($row = mysqli_fetch_assoc($mac_categories_result)) {
                            $mac_categories[] = $row['kategori'];
                        }

                        // iPad
                        $ipad_categories_query = "SELECT DISTINCT kategori FROM admin_produk_ipad WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $ipad_categories_result = mysqli_query($db, $ipad_categories_query);
                        $ipad_categories = [];
                        while ($row = mysqli_fetch_assoc($ipad_categories_result)) {
                            $ipad_categories[] = $row['kategori'];
                        }

                        // iPhone
                        $iphone_categories_query = "SELECT DISTINCT kategori FROM admin_produk_iphone WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $iphone_categories_result = mysqli_query($db, $iphone_categories_query);
                        $iphone_categories = [];
                        while ($row = mysqli_fetch_assoc($iphone_categories_result)) {
                            $iphone_categories[] = $row['kategori'];
                        }

                        // Watch
                        $watch_categories_query = "SELECT DISTINCT kategori FROM admin_produk_watch WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $watch_categories_result = mysqli_query($db, $watch_categories_query);
                        $watch_categories = [];
                        while ($row = mysqli_fetch_assoc($watch_categories_result)) {
                            $watch_categories[] = $row['kategori'];
                        }

                        // Music
                        $music_categories_query = "SELECT DISTINCT kategori FROM admin_produk_music WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $music_categories_result = mysqli_query($db, $music_categories_query);
                        $music_categories = [];
                        while ($row = mysqli_fetch_assoc($music_categories_result)) {
                            $music_categories[] = $row['kategori'];
                        }

                        // AirTag
                        $airtag_categories_query = "SELECT DISTINCT kategori FROM admin_produk_airtag WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $airtag_categories_result = mysqli_query($db, $airtag_categories_query);
                        $airtag_categories = [];
                        while ($row = mysqli_fetch_assoc($airtag_categories_result)) {
                            $airtag_categories[] = $row['kategori'];
                        }

                        // Aksesori
                        $aksesori_categories_query = "SELECT DISTINCT kategori FROM admin_produk_aksesoris WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori ASC";
                        $aksesori_categories_result = mysqli_query($db, $aksesori_categories_query);
                        $aksesori_categories = [];
                        while ($row = mysqli_fetch_assoc($aksesori_categories_result)) {
                            $aksesori_categories[] = $row['kategori'];
                        }
                        ?>
                        <ul id="mainMenuList">
                            <li>
                                <a href="#" class="menu-trigger" data-target="mac">Mac</a>
                                <div class="dropdown mac-dropdown" id="mac-dropdown">
                                    <button class="dropdown-close-btn" data-close="mac">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori Mac dari database
                                    foreach ($mac_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=mac" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_mac WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=mac" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="ipad">iPad</a>
                                <div class="dropdown ipad-dropdown" id="ipad-dropdown">
                                    <button class="dropdown-close-btn" data-close="ipad">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori iPad dari database
                                    foreach ($ipad_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=ipad" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_ipad WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=ipad" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="iphone">iPhone</a>
                                <div class="dropdown iphone-dropdown" id="iphone-dropdown">
                                    <button class="dropdown-close-btn" data-close="iphone">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori iPhone dari database
                                    foreach ($iphone_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=iphone" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_iphone WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=iphone" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="watch">Watch</a>
                                <div class="dropdown watch-dropdown" id="watch-dropdown">
                                    <button class="dropdown-close-btn" data-close="watch">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori Watch dari database
                                    foreach ($watch_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=watch" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_watch WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=watch" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="music">Music</a>
                                <div class="dropdown music-dropdown" id="music-dropdown">
                                    <button class="dropdown-close-btn" data-close="music">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori Music dari database
                                    foreach ($music_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=music" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_music WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=music" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="airtag">AirTag</a>
                                <div class="dropdown airtag-dropdown" id="airtag-dropdown">
                                    <button class="dropdown-close-btn" data-close="airtag">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori AirTag dari database
                                    foreach ($airtag_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=airtag" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_airtag WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=airtag" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="aksesori">Aksesori</a>
                                <div class="dropdown aksesori-dropdown" id="aksesori-dropdown">
                                    <button class="dropdown-close-btn" data-close="aksesori">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <?php
                                    // Menampilkan kategori Aksesori dari database
                                    foreach ($aksesori_categories as $kategori) {
                                        echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=aksesoris" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_aksesoris WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=aksesoris" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
                                            }
                                            echo '</ul>';
                                        }
                                    }
                                    ?>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="layanan">Layanan</a>
                                <div class="dropdown layanan-dropdown" id="layanan-dropdown">
                                    <button class="dropdown-close-btn" data-close="layanan">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Apple Services</div>
                                    <ul>
                                        <li><a href="#">AppleCare+</a></li>
                                        <li><a href="#">iCloud+</a></li>
                                        <li><a href="#">Apple Music</a></li>
                                        <li><a href="#">Apple TV+</a></li>
                                    </ul>
                                    <div class="dropdown-category">Repair Services</div>
                                    <ul>
                                        <li><a href="#">Screen Replacement</a></li>
                                        <li><a href="#">Battery Replacement</a></li>
                                        <li><a href="#">Water Damage Repair</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="event">Event dan Promo</a>
                                <div class="dropdown event-dropdown" id="event-dropdown">
                                    <button class="dropdown-close-btn" data-close="event">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Promo Spesial</div>
                                    <ul>
                                        <li><a href="#">Diskon Akhir Tahun</a></li>
                                        <li><a href="#">Bundling Gratis</a></li>
                                        <li><a href="#">Trade-in Program</a></li>
                                    </ul>
                                    <div class="dropdown-category">Event Mendatang</div>
                                    <ul>
                                        <li><a href="#">Workshop iOS 17</a></li>
                                        <li><a href="#">Launch Event</a></li>
                                        <li><a href="#">Tech Seminar</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="bisnis">Bisnis</a>
                                <div class="dropdown bisnis-dropdown" id="bisnis-dropdown">
                                    <button class="dropdown-close-btn" data-close="bisnis">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Untuk Bisnis</div>
                                    <ul>
                                        <li><a href="#">Bulk Purchase</a></li>
                                        <li><a href="#">Corporate Discount</a></li>
                                        <li><a href="#">IT Support</a></li>
                                    </ul>
                                    <div class="dropdown-category">Edukasi</div>
                                    <ul>
                                        <li><a href="#">Apple School Manager</a></li>
                                        <li><a href="#">Classroom Apps</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="edukasi">Edukasi</a>
                                <div class="dropdown edukasi-dropdown" id="edukasi-dropdown">
                                    <button class="dropdown-close-btn" data-close="edukasi">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Untuk Pendidikan</div>
                                    <ul>
                                        <li><a href="#">Student Discount</a></li>
                                        <li><a href="#">Education Pricing</a></li>
                                        <li><a href="#">Teacher Program</a></li>
                                    </ul>
                                    <div class="dropdown-category">Learning Resources</div>
                                    <ul>
                                        <li><a href="#">Tutorial Videos</a></li>
                                        <li><a href="#">Online Courses</a></li>
                                        <li><a href="#">User Guides</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sidebar untuk menu pada layar kecil -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="sidebar" id="sidebar">
            <!-- Back button untuk sidebar focused mode -->
            <div class="sidebar-back-btn" id="sidebarBackBtn">
                <i class="bi bi-chevron-left"></i>
            </div>

            <div class="sidebar-header">
                <!-- Icon X di kiri -->
                <button class="close-sidebar" id="closeSidebar">
                    <i class="bi bi-x-lg"></i>
                </button>

                <!-- Logo di kanan -->
                <div class="sidebar-logo">
                    <img src="../../assets/img/logo/logo.png" alt="iBox Logo">
                </div>
            </div>

            <div class="sidebar-menu" id="sidebarMenu">
                <ul id="sidebarMenuList">
                    <!-- Menu Mac dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="mac">
                        <a href="#" class="sidebar-menu-trigger">
                            Mac
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori Mac dari database untuk sidebar
                            foreach ($mac_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=mac" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_mac WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=mac" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu iPad dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="ipad">
                        <a href="#" class="sidebar-menu-trigger">
                            iPad
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori iPad dari database untuk sidebar
                            foreach ($ipad_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=ipad" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_ipad WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=ipad" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu iPhone dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="iphone">
                        <a href="#" class="sidebar-menu-trigger">
                            iPhone
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori iPhone dari database untuk sidebar
                            foreach ($iphone_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=iphone" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_iphone WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=iphone" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu Watch dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="watch">
                        <a href="#" class="sidebar-menu-trigger">
                            Watch
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori Watch dari database untuk sidebar
                            foreach ($watch_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=watch" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_watch WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=watch" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu Music dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="music">
                        <a href="#" class="sidebar-menu-trigger">
                            Music
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori Music dari database untuk sidebar
                            foreach ($music_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=music" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_music WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=music" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu AirTag dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="airtag">
                        <a href="#" class="sidebar-menu-trigger">
                            AirTag
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori AirTag dari database untuk sidebar
                            foreach ($airtag_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=airtag" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_airtag WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=airtag" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu Aksesori dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="aksesori">
                        <a href="#" class="sidebar-menu-trigger">
                            Aksesori
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <?php
                            // Menampilkan kategori Aksesori dari database untuk sidebar
                            foreach ($aksesori_categories as $kategori) {
                                echo '<a href="../products/category-products.php?category=' . urlencode($kategori) . '&type=aksesoris" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT MIN(id) as id, nama_produk FROM admin_produk_aksesoris WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' GROUP BY nama_produk ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="../checkout/checkout.php?id=' . $produk['id'] . '&tipe=aksesoris" class="dropdown-product-link">' . htmlspecialchars($produk['nama_produk']) . '</a>';
                                    }
                                }
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Menu Layanan dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="layanan">
                        <a href="#" class="sidebar-menu-trigger">
                            Layanan
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <div class="dropdown-category">Apple Services</div>
                            <a href="#">AppleCare+</a>
                            <a href="#">iCloud+</a>
                            <a href="#">Apple Music</a>
                            <a href="#">Apple TV+</a>

                            <div class="dropdown-category">Repair Services</div>
                            <a href="#">Screen Replacement</a>
                            <a href="#">Battery Replacement</a>
                            <a href="#">Water Damage Repair</a>
                        </div>
                    </li>

                    <!-- Menu Event dan Promo dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="event">
                        <a href="#" class="sidebar-menu-trigger">
                            Event dan Promo
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <div class="dropdown-category">Promo Spesial</div>
                            <a href="#">Diskon Akhir Tahun</a>
                            <a href="#">Bundling Gratis</a>
                            <a href="#">Trade-in Program</a>

                            <div class="dropdown-category">Event Mendatang</div>
                            <a href="#">Workshop iOS 17</a>
                            <a href="#">Launch Event</a>
                            <a href="#">Tech Seminar</a>
                        </div>
                    </li>

                    <!-- Menu Bisnis dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="bisnis">
                        <a href="#" class="sidebar-menu-trigger">
                            Bisnis
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <div class="dropdown-category">Untuk Bisnis</div>
                            <a href="#">Bulk Purchase</a>
                            <a href="#">Corporate Discount</a>
                            <a href="#">IT Support</a>

                            <div class="dropdown-category">Edukasi</div>
                            <a href="#">Apple School Manager</a>
                            <a href="#">Classroom Apps</a>
                        </div>
                    </li>

                    <!-- Menu Edukasi dengan Sub-Dropdown -->
                    <li class="has-dropdown" data-menu="edukasi">
                        <a href="#" class="sidebar-menu-trigger">
                            Edukasi
                            <span class="menu-arrow">›</span>
                        </a>
                        <div class="sidebar-dropdown">
                            <div class="dropdown-category">Untuk Pendidikan</div>
                            <a href="#">Student Discount</a>
                            <a href="#">Education Pricing</a>
                            <a href="#">Teacher Program</a>

                            <div class="dropdown-category">Learning Resources</div>
                            <a href="#">Tutorial Videos</a>
                            <a href="#">Online Courses</a>
                            <a href="#">User Guides</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="sidebar-auth">
                    <button class="sidebar-auth-btn btn-login">Masuk</button>
                    <button class="sidebar-auth-btn btn-register">Daftar</button>
                </div>
            </div>
        </div>
        <script>
            // Mengambil elemen yang diperlukan
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const closeSidebar = document.getElementById('closeSidebar');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainMenuList = document.getElementById('mainMenuList');
            const sidebarMenu = document.getElementById('sidebarMenu');
            const sidebarMenuList = document.getElementById('sidebarMenuList');
            const sidebarBackBtn = document.getElementById('sidebarBackBtn');
            const sidebarMenuTriggers = document.querySelectorAll('.sidebar-menu-trigger');

            // Variabel untuk mengelola state sidebar
            let sidebarFocusedMode = false;
            let activeSidebarMenuItem = null;
            let activeDropdown = null;
            let activeMenuTrigger = null;
            let focusedMode = false;
            let focusedMenuItem = null;

            // MODIFIKASI: Fungsi untuk mengatur scroll dropdown
            function setupDropdownScroll() {
                const dropdowns = document.querySelectorAll('.dropdown');

                dropdowns.forEach(dropdown => {
                    dropdown.addEventListener('scroll', function() {
                        const closeBtn = this.querySelector('.dropdown-close-btn');
                        if (closeBtn) {
                            // Saat di-scroll, pastikan tombol close tetap terlihat
                            closeBtn.style.position = 'sticky';
                            closeBtn.style.top = '10px';
                        }
                    });
                });
            }

            // MODIFIKASI: Fungsi untuk reset dropdown scroll
            function resetDropdownScroll(dropdown) {
                if (dropdown) {
                    dropdown.scrollTop = 0;
                }
            }

            // MODIFIKASI: Fungsi untuk mengontrol scroll body
            function controlBodyScroll(disable) {
                if (disable) {
                    document.body.style.overflow = 'hidden';
                    document.body.style.height = '100vh';
                } else {
                    document.body.style.overflow = '';
                    document.body.style.height = '';
                }
            }

            // Fungsi untuk membuka sidebar
            function openSidebar() {
                sidebar.classList.add('active');
                sidebarOverlay.classList.add('active');

                // Animasi hamburger saat sidebar terbuka
                hamburgerBtn.style.transform = 'rotate(180deg)';
                hamburgerBtn.style.color = '#007aff';
                hamburgerBtn.querySelector('i').style.transform = 'rotate(10deg)';

                document.body.style.overflow = 'hidden';
            }

            // Fungsi untuk menutup sidebar
            function closeSidebarFunc() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');

                // Animasi hamburger saat sidebar tertutup
                hamburgerBtn.style.transform = 'rotate(0deg) scale(1)';
                hamburgerBtn.style.color = '#333';
                hamburgerBtn.querySelector('i').style.transform = 'rotate(0deg)';

                // Reset focused mode saat menutup sidebar
                exitSidebarFocusedMode();

                document.body.style.overflow = 'auto';
            }

            // Fungsi untuk toggle sidebar (buka/tutup)
            function toggleSidebar() {
                if (sidebar.classList.contains('active')) {
                    closeSidebarFunc();
                } else {
                    openSidebar();
                }
            }

            // MODIFIKASI: Fungsi untuk masuk ke focused mode di sidebar
            function enterSidebarFocusedMode(menuItem) {
                // Tambahkan kelas focused-mode ke sidebar
                sidebar.classList.add('focused-mode');

                // Tambahkan kelas active-menu-item ke menu item yang diklik
                menuItem.classList.add('active-menu-item');

                // Tampilkan dropdown dari menu yang aktif
                const dropdown = menuItem.querySelector('.sidebar-dropdown');
                if (dropdown) {
                    dropdown.classList.add('active');
                    // Reset scroll dropdown
                    resetDropdownScroll(dropdown);
                }

                // Simpan menu item yang aktif
                activeSidebarMenuItem = menuItem;
                sidebarFocusedMode = true;

                // Scroll ke atas
                sidebarMenu.scrollTop = 0;
            }

            // MODIFIKASI: Fungsi untuk keluar dari focused mode di sidebar
            function exitSidebarFocusedMode() {
                // Hapus kelas focused-mode dari sidebar
                sidebar.classList.remove('focused-mode');

                // Hapus kelas active-menu-item dari semua menu item
                document.querySelectorAll('.has-dropdown').forEach(item => {
                    item.classList.remove('active-menu-item');
                });

                // Tutup semua dropdown di sidebar dan reset scroll
                document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('active');
                    resetDropdownScroll(dropdown);
                });

                // Reset state
                activeSidebarMenuItem = null;
                sidebarFocusedMode = false;
            }

            // MODIFIKASI: Event listener untuk menu triggers di sidebar
            sidebarMenuTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const menuItem = this.parentElement;

                    // Jika belum dalam focused mode, masuk ke focused mode
                    if (!sidebarFocusedMode) {
                        enterSidebarFocusedMode(menuItem);
                    } else {
                        // Jika sudah dalam focused mode dan mengklik menu yang sama, keluar dari focused mode
                        if (activeSidebarMenuItem === menuItem) {
                            exitSidebarFocusedMode();
                        } else {
                            // Jika mengklik menu yang berbeda, ganti menu yang aktif
                            // Tutup dropdown yang aktif sebelumnya
                            if (activeSidebarMenuItem) {
                                const oldDropdown = activeSidebarMenuItem.querySelector('.sidebar-dropdown');
                                if (oldDropdown) {
                                    oldDropdown.classList.remove('active');
                                    resetDropdownScroll(oldDropdown);
                                }
                                activeSidebarMenuItem.classList.remove('active-menu-item');
                            }

                            // Buka dropdown menu yang baru diklik
                            const dropdown = menuItem.querySelector('.sidebar-dropdown');
                            if (dropdown) {
                                dropdown.classList.add('active');
                                resetDropdownScroll(dropdown);
                            }
                            menuItem.classList.add('active-menu-item');
                            activeSidebarMenuItem = menuItem;
                        }
                    }
                });
            });

            // MODIFIKASI: Event listener untuk tombol back di sidebar
            sidebarBackBtn.addEventListener('click', () => {
                exitSidebarFocusedMode();
            });

            // Event listener untuk link di dalam dropdown sidebar
            document.querySelectorAll('.sidebar-dropdown a').forEach(dropdownLink => {
                dropdownLink.addEventListener('click', function(e) {
                    // Jika link tidak memiliki href atau href="#", cegah default behavior
                    if (this.getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                    // Tutup sidebar setelah 200ms (memberikan waktu untuk animasi)
                    setTimeout(closeSidebarFunc, 200);
                });
            });

            // MODIFIKASI: Fungsi untuk masuk ke focused mode (Desktop)
            function enterFocusedMode(menuItem) {
                // Tambah kelas focused-mode untuk menyembunyikan menu lainnya
                mainMenuList.classList.add('focused-mode');

                // Tambah kelas active-menu-item pada menu yang aktif
                menuItem.classList.add('active-menu-item');

                // Di mobile, nonaktifkan scroll body
                if (window.innerWidth < 830) {
                    controlBodyScroll(true);
                }

                focusedMode = true;
                focusedMenuItem = menuItem;
            }

            // MODIFIKASI: Fungsi untuk keluar dari focused mode (Desktop)
            function exitFocusedMode() {
                // Hapus kelas focused-mode untuk menampilkan semua menu kembali
                mainMenuList.classList.remove('focused-mode');

                // Hapus kelas active-menu-item
                if (focusedMenuItem) {
                    focusedMenuItem.classList.remove('active-menu-item');
                }

                // Tutup semua dropdown dan reset scroll
                closeAllDropdowns();

                // Di mobile, aktifkan kembali scroll body
                if (window.innerWidth < 830) {
                    controlBodyScroll(false);
                }
                
                focusedMode = false;
                focusedMenuItem = null;
            }

            // MODIFIKASI: Fungsi untuk toggle dropdown desktop (klik menu)
            function toggleDesktopDropdown(trigger) {
                const targetId = trigger.getAttribute('data-target');
                const dropdown = document.getElementById(`${targetId}-dropdown`);
                const menuItem = trigger.parentElement;

                // Jika sudah dalam focused mode dan mengklik menu yang sama, keluar dari focused mode
                if (focusedMode && focusedMenuItem === menuItem) {
                    exitFocusedMode();
                    return;
                }

                // Jika sudah dalam focused mode dan mengklik menu berbeda, ganti menu yang aktif
                if (focusedMode && focusedMenuItem !== menuItem) {
                    // Tutup dropdown yang aktif
                    if (activeDropdown) {
                        activeDropdown.classList.remove('active');
                        resetDropdownScroll(activeDropdown);
                        if (activeMenuTrigger) {
                            activeMenuTrigger.classList.remove('active');
                        }
                    }

                    // Buka dropdown baru
                    dropdown.classList.add('active');
                    trigger.classList.add('active');
                    activeDropdown = dropdown;
                    activeMenuTrigger = trigger;

                    // Update focused menu item
                    focusedMenuItem.classList.remove('active-menu-item');
                    menuItem.classList.add('active-menu-item');
                    focusedMenuItem = menuItem;
                    return;
                }

                // Jika tidak dalam focused mode, masuk ke focused mode
                if (!focusedMode) {
                    // Buka dropdown
                    dropdown.classList.add('active');
                    trigger.classList.add('active');
                    activeDropdown = dropdown;
                    activeMenuTrigger = trigger;

                    // Reset scroll dropdown
                    resetDropdownScroll(dropdown);

                    // Masuk ke focused mode
                    enterFocusedMode(menuItem);
                }
            }

            // MODIFIKASI: Fungsi untuk menutup semua dropdown desktop
            function closeAllDropdowns() {
                const dropdowns = document.querySelectorAll('.dropdown');
                const menuTriggers = document.querySelectorAll('.menu-trigger');

                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                    resetDropdownScroll(dropdown);
                });

                menuTriggers.forEach(trigger => {
                    trigger.classList.remove('active');
                });

                activeDropdown = null;
                activeMenuTrigger = null;
            }

            // Animasi untuk hamburger button
            hamburgerBtn.addEventListener('mouseenter', () => {
                if (!sidebar.classList.contains('active')) {
                    hamburgerBtn.style.transform = 'rotate(180deg) scale(1.1)';
                    hamburgerBtn.querySelector('i').style.transform = 'rotate(10deg)';
                }
            });

            hamburgerBtn.addEventListener('mouseleave', () => {
                if (!sidebar.classList.contains('active')) {
                    hamburgerBtn.style.transform = 'rotate(0deg) scale(1)';
                    hamburgerBtn.querySelector('i').style.transform = 'rotate(0deg)';
                }
            });

            // Animasi untuk close button
            closeSidebar.addEventListener('mouseenter', () => {
                closeSidebar.style.transform = 'rotate(90deg) scale(1.1)';
            });

            closeSidebar.addEventListener('mouseleave', () => {
                closeSidebar.style.transform = 'rotate(0deg) scale(1)';
            });

            // Event listener untuk tombol hamburger (toggle)
            hamburgerBtn.addEventListener('click', toggleSidebar);

            // Event listener untuk tombol close di sidebar
            closeSidebar.addEventListener('click', closeSidebarFunc);

            // Event listener untuk overlay (klik di luar sidebar untuk menutup)
            sidebarOverlay.addEventListener('click', closeSidebarFunc);

            // Event listener untuk tombol login/register di sidebar
            document.querySelectorAll('.sidebar-auth-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Animasi klik
                    this.style.transform = 'translateY(-1px) scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-3px)';
                    }, 150);

                    setTimeout(() => {
                        alert('Fitur akan segera tersedia!');
                    }, 200);

                    // Tutup sidebar setelah 500ms
                    setTimeout(closeSidebarFunc, 500);
                });
            });

            // MODIFIKASI: Event listener untuk menu triggers desktop (klik)
            document.querySelectorAll('.menu-trigger').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Hanya di desktop (≥ 830px)
                    if (window.innerWidth >= 830) {
                        toggleDesktopDropdown(this);
                    }
                });
            });

            // MODIFIKASI: Event listener untuk tombol close di dropdown desktop
            document.querySelectorAll('.dropdown-close-btn').forEach(closeBtn => {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    exitFocusedMode();
                });
            });

            // Menutup sidebar saat menekan tombol Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    if (sidebar.classList.contains('active')) {
                        if (sidebarFocusedMode) {
                            exitSidebarFocusedMode();
                        } else {
                            closeSidebarFunc();
                        }
                    } else if (focusedMode) {
                        exitFocusedMode();
                    } else if (activeDropdown) {
                        closeAllDropdowns();
                    }
                }
            });

            // MODIFIKASI: Menutup dropdown desktop saat klik di luar dropdown (hanya di desktop)
            document.addEventListener('click', function(event) {
                if (window.innerWidth >= 830) {
                    const isClickInsideMenu = event.target.closest('.list-menu > ul > li');
                    const isClickInsideDropdown = event.target.closest('.dropdown');
                    const isClickOnCloseBtn = event.target.closest('.dropdown-close-btn');

                    if (!isClickInsideMenu && !isClickInsideDropdown && !isClickOnCloseBtn && focusedMode) {
                        exitFocusedMode();
                    }
                }
            });

            // Menyesuaikan lebar search bar berdasarkan ukuran layar
            function adjustSearchBar() {
                const searchInput = document.querySelector('.search-bar-menu input[type="text"]');
                const windowWidth = window.innerWidth;

                if (windowWidth <= 576) {
                    searchInput.placeholder = "Cari...";
                } else {
                    searchInput.placeholder = "Cari produk di iBox";
                }

                // Tutup dropdown desktop saat beralih ke mobile
                if (windowWidth < 830 && activeDropdown) {
                    closeAllDropdowns();
                    if (focusedMode) {
                        exitFocusedMode();
                    }
                }

                // Tutup sidebar jika terbuka di desktop (≥ 830px)
                if (windowWidth >= 830 && sidebar.classList.contains('active')) {
                    closeSidebarFunc();
                }

                // Reset focused mode jika berubah ukuran layar
                if (focusedMode) {
                    if (windowWidth < 830) {
                        // Di mobile, exit focused mode desktop
                        exitFocusedMode();
                    } else {
                        // Di desktop, exit focused mode sidebar
                        exitSidebarFocusedMode();
                    }
                }
            }

            // Setup dropdown scroll saat halaman dimuat
            window.addEventListener('load', function() {
                setupDropdownScroll();
                adjustSearchBar();
            });

            window.addEventListener('resize', adjustSearchBar);
        </script>
    </nav>

    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <a href="../index.php">Home</a>
        <span class="breadcrumb-separator">/</span>
        <a href="../products/products.php">Produk</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Keranjang</span>
    </div>

    <!-- Cart Content -->
    <style>
        .cart-content-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 5%;
        }

        .cart-header {
            margin-bottom: 30px;
        }

        .cart-title {
            font-size: 32px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
        }

        .cart-subtitle {
            font-size: 16px;
            color: #86868b;
        }

        .cart-products-list {
            display: grid;
            gap: 20px;
        }

        .cart-product-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            position: relative;
            border: 1px solid #e0e0e0;
        }

        .cart-product-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* Flex container for the whole row */
        .cart-product-content {
            display: flex;
            gap: 20px;
            align-items: flex-start; /* Align top so long descriptions don't center actions awkwardly */
        }

        /* LEFT SIDE: Image */
        .cart-product-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .cart-product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 5px;
        }

        /* MIDDLE SECTION: Product Info (Name, Variant) */
        .cart-product-info-wrapper {
            flex: 1; /* Takes up available space */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes price to top, actions to bottom */
            min-height: 100px; /* Match image height */
        }

        .cart-product-info {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .cart-product-name {
            font-size: 16px;
            font-weight: 600;
            color: #1d1d1f;
            line-height: 1.4;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cart-product-variant {
            font-size: 13px;
            color: #86868b;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        /* Direct Checkout Button (keeping per item as per original) */
        .btn-direct-checkout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px; 
            background: #007aff; 
            color: white; 
            text-decoration: none; 
            border-radius: 20px; 
            font-size: 13px; 
            font-weight: 500; 
            transition: all 0.2s;
            width: fit-content;
        }
        
        .btn-direct-checkout:hover {
            background: #0056cc;
            color: white;
            transform: none;
        }

        /* RIGHT SECTION: Price & Actions */
        .cart-product-right-section {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Pushes price to top, actions to bottom */
            align-items: flex-end; /* Align to right */
            min-width: 180px; /* Ensure enough space */
            min-height: 100px; /* Match image height */
        }

        /* Price at Top Right */
        .cart-product-price {
            font-size: 18px;
            font-weight: 700;
            color: #ff3b30; /* Red color as per image */
            letter-spacing: -0.5px;
            text-align: right;
            margin-bottom: auto; /* Pushes actions to bottom */
        }

        /* Bottom Right Actions Row */
        .cart-actions-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: auto; /* Pushes actions to bottom */
        }
        
        /* Action Icons (Heart/Trash) */
        .action-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px; /* Larger icons */
            color: #86868b;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }
        
        .action-icon-btn:hover {
            color: #1d1d1f;
        }
        
        .action-icon-btn.delete-btn:hover {
            color: #ff3b30;
        }

        /* Pill Shaped Quantity Control matching the image */
        .quantity-controls {
            display: flex;
            align-items: center;
            background: transparent;
            border: 1px solid #d2d2d7;
            border-radius: 20px; /* Pill shape */
            padding: 4px 8px;
            height: 36px;
            min-width: 100px;
            justify-content: space-between;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: transparent;
            color: #1d1d1f;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .qty-btn:hover:not(:disabled) {
            color: #007aff;
            background: transparent;
        }
        
        .qty-btn:disabled {
            color: #ccc;
            cursor: default;
        }

        .qty-display {
            font-size: 14px;
            font-weight: 600;
            color: #1d1d1f;
            min-width: 20px;
            text-align: center;
        }

        /* Empty State */
        .cart-empty {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .cart-empty-icon {
            font-size: 64px;
            color: #d2d2d7;
            margin-bottom: 20px;
        }

        .cart-empty-title {
            font-size: 24px;
            font-weight: 600;
            color: #1d1d1f;
            margin-bottom: 12px;
        }

        .cart-empty-text {
            font-size: 16px;
            color: #86868b;
            margin-bottom: 30px;
        }

        .btn-browse-products {
            display: inline-block;
            padding: 14px 32px;
            background: #007aff;
            color: white;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        .btn-browse-products:hover {
            background: #0056cc;
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.3);
        }
        
        .cart-checkout-hint {
            margin-top: 16px;
            padding: 16px;
            background: #f0f7ff;
            border-radius: 12px;
            border-left: 4px solid #007aff;
        }

        .cart-checkout-hint-text {
            font-size: 14px;
            color: #1d1d1f;
            margin: 0;
        }

        .cart-checkout-hint-text i {
            color: #007aff;
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .cart-product-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .cart-product-info-wrapper {
                min-height: auto;
            }

            .cart-product-right-section {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                min-height: auto;
                padding-top: 15px;
                border-top: 1px solid #f0f0f0;
            }
            
            .cart-product-price {
                order: 1; /* Price comes first on mobile */
                margin-bottom: 0;
            }
            .cart-actions-row {
                order: 2; /* Actions come second on mobile */
                margin-top: 0;
            }
            
            .cart-product-image {
                width: 80px;
                height: 80px;
            }
        }

    </style>

    <div class="cart-content-wrapper">
        <div class="cart-header">
            <h1 class="cart-title">Keranjang Belanja</h1>
            <p class="cart-subtitle">Klik pada produk untuk melanjutkan ke checkout</p>
        </div>

        <div class="cart-checkout-hint">
            <p class="cart-checkout-hint-text">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Cara Checkout:</strong> Klik pada produk yang ingin Anda beli untuk melanjutkan ke halaman checkout.
            </p>
        </div>

        <div class="cart-products-list" id="cartProductsList">
            <?php
            if ($is_logged_in) {
                $uid = $_SESSION['user_id'];
                $stmt = $db->prepare("SELECT * FROM user_keranjang WHERE user_id = ? ORDER BY id DESC");
                $stmt->bind_param("i", $uid);
                $stmt->execute();
                $result_cart = $stmt->get_result();
                
                if ($result_cart->num_rows > 0) {
                    while ($row = $result_cart->fetch_assoc()) {
                        $cart_id = $row['id'];
                        $product_id = $row['product_id'];
                        $tipe = strtolower($row['tipe_produk']);
                        $qty = $row['jumlah'];
                        $kombinasi_id = isset($row['kombinasi_id']) ? $row['kombinasi_id'] : null;
                        $cart_image = $row['foto_thumbnail'] ?? '';
                        
                        // Determine table
                        $table = "";
                        if (in_array($tipe, ['mac', 'iphone', 'ipad', 'watch', 'music', 'airtag'])) {
                            $table = "admin_produk_" . $tipe;
                        } elseif ($tipe == 'aksesori' || $tipe == 'aksesoris') {
                            $table = "admin_produk_aksesoris";
                        }
                        
                        if (empty($table)) continue;
                        
                        // Fetch product details
                        $q_prod = $db->query("SELECT * FROM $table WHERE id = $product_id");
                        if (!$q_prod || $q_prod->num_rows == 0) continue;
                        $prod = $q_prod->fetch_assoc();
                        
                        $name = $prod['nama_produk'];
                        $price = $prod['harga'] ?? 0;
                        
                        // Variant Logic - Adjusted to match SQL Schema
                        $variant_str = "";
                        if ($kombinasi_id) {
                            $table_comb = $table . "_kombinasi";
                            $q_comb = $db->query("SELECT * FROM $table_comb WHERE id = $kombinasi_id");
                            if ($q_comb && $comb = $q_comb->fetch_assoc()) {
                                if (isset($comb['harga']) && $comb['harga'] > 0) $price = $comb['harga'];
                                
                                $variants = [];
                                
                                // 1. Warna (All types usually have color)
                                if (!empty($comb['warna'])) $variants[] = $comb['warna'];
                                if (!empty($comb['warna_case'])) $variants[] = $comb['warna_case']; // Watch specific
                                
                                // 2. Specific Attributes based on Type and SQL Schema
                                if ($tipe == 'mac') {
                                    if (!empty($comb['processor'])) $variants[] = $comb['processor'];
                                    if (!empty($comb['ram'])) $variants[] = $comb['ram'];
                                    if (!empty($comb['penyimpanan'])) $variants[] = $comb['penyimpanan'];
                                } elseif ($tipe == 'iphone' || $tipe == 'ipad') {
                                    if (!empty($comb['penyimpanan'])) $variants[] = $comb['penyimpanan'];
                                    if (!empty($comb['konektivitas'])) $variants[] = $comb['konektivitas'];
                                } elseif ($tipe == 'watch') {
                                    if (!empty($comb['ukuran_case'])) $variants[] = $comb['ukuran_case'];
                                    if (!empty($comb['material'])) $variants[] = $comb['material'];
                                    if (!empty($comb['tipe_koneksi'])) $variants[] = $comb['tipe_koneksi'];
                                } elseif ($tipe == 'airtag') {
                                    if (!empty($comb['pack'])) $variants[] = $comb['pack'];
                                    if (!empty($comb['aksesoris'])) $variants[] = $comb['aksesoris'];
                                } elseif ($tipe == 'music') {
                                    if (!empty($comb['tipe'])) $variants[] = $comb['tipe'];
                                    if (!empty($comb['konektivitas'])) $variants[] = $comb['konektivitas'];
                                } elseif ($tipe == 'aksesori' || $tipe == 'aksesoris') {
                                    if (!empty($comb['tipe'])) $variants[] = $comb['tipe'];
                                    if (!empty($comb['ukuran'])) $variants[] = $comb['ukuran'];
                                }
                                
                                $variant_str = implode(" • ", $variants);
                            }
                        } else {
                            // Fallback for price if variant not selected
                            if ($price <= 0) {
                                $table_comb = $table . "_kombinasi";
                                $q_min = $db->query("SELECT MIN(harga) as min_h FROM $table_comb WHERE produk_id = $product_id AND status_stok = 'tersedia'");
                                if ($q_min && $r_min = $q_min->fetch_assoc()) $price = $r_min['min_h'];
                            }
                        }
                        
                        // Image Path
                        $img_src = !empty($cart_image) ? $cart_image : 'default.png';
                        if (strpos($img_src, 'assets/') === 0) {
                            $img_final = '../../' . $img_src;
                        } else {
                            $img_final = '../../admin/uploads/' . $img_src;
                        }
                        
                        $formatted_price = "Rp " . number_format($price, 0, ',', '.');
                        ?>
                        <div class="cart-product-card" data-cart-id="<?php echo $cart_id; ?>">
                            <div class="cart-product-content">
                                <!-- LEFT: Image -->
                                <div class="cart-product-image">
                                    <img src="<?php echo $img_final; ?>" alt="<?php echo htmlspecialchars($name); ?>" onerror="this.src='../../assets/img/logo/logo.png'">
                                </div>
                                
                                <!-- MIDDLE: Info -->
                                <div class="cart-product-info-wrapper">
                                    <div class="cart-product-info">
                                        <h3 class="cart-product-name"><?php echo htmlspecialchars($name); ?></h3>
                                        <?php if (!empty($variant_str)): ?>
                                        <div class="cart-product-variant">
                                            <i class="bi bi-tag" style="color: #86868b;"></i>
                                            <span><?php echo htmlspecialchars($variant_str); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <a href="../checkout/checkout.php?id=<?php echo $product_id; ?>&tipe=<?php echo $tipe; ?>" class="btn-direct-checkout">
                                            <i class="bi bi-cart-check"></i> Checkout
                                        </a>
                                    </div>
                                </div>

                                <!-- RIGHT: Price (Top) & Actions (Bottom) -->
                                <div class="cart-product-right-section">
                                    <span class="cart-product-price"><?php echo $formatted_price; ?></span>
                                    
                                    <div class="cart-actions-row">
                                        <!-- Wishlist (UI Only) -->
                                        <button class="action-icon-btn" title="Simpan ke Wishlist">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                        
                                        <!-- Delete -->
                                        <button class="action-icon-btn delete-btn" onclick="deleteCartItem(<?php echo $cart_id; ?>)" title="Hapus dari keranjang">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Quantity Control Pill -->
                                        <div class="quantity-controls">
                                            <button class="qty-btn qty-decrease" onclick="updateQuantity(<?php echo $cart_id; ?>, 'decrease')" <?php echo $qty <= 1 ? 'disabled' : ''; ?>>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <span class="qty-display"><?php echo $qty; ?></span>
                                            <button class="qty-btn qty-increase" onclick="updateQuantity(<?php echo $cart_id; ?>, 'increase')">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    // Empty Cart HTML
                    echo '
                    <div class="cart-empty">
                        <div class="cart-empty-icon">
                            <i class="bi bi-cart-x"></i>
                        </div>
                        <h2 class="cart-empty-title">Keranjang Anda Kosong</h2>
                        <p class="cart-empty-text">Belum ada produk di keranjang Anda. Mulai belanja sekarang!</p>
                        <a href="../products/products.php" class="btn-browse-products">
                            <i class="bi bi-shop"></i> Jelajahi Produk
                        </a>
                    </div>';
                }
            } else {
                 echo '
                    <div class="cart-empty">
                        <div class="cart-empty-icon">
                            <i class="bi bi-person-lock"></i>
                        </div>
                        <h2 class="cart-empty-title">Silakan Login</h2>
                        <p class="cart-empty-text">Anda perlu login untuk melihat keranjang belanja.</p>
                        <a href="../auth/login.php" class="btn-browse-products">
                            <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
                        </a>
                    </div>';
            }
            ?>
        </div>
    </div>

    <!-- Glassmorphism Delete Confirmation Modal -->
    <div class="glass-modal-overlay" id="deleteModal" style="display: none;">
        <div class="glass-modal-content">
            <div class="glass-modal-icon">
                <i class="bi bi-trash-fill"></i>
            </div>
            <h3 class="glass-modal-title">Hapus Produk?</h3>
            <p class="glass-modal-text">Produk ini akan dihapus dari keranjang belanja Anda. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="glass-modal-actions">
                <button class="glass-btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button class="glass-btn-confirm" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>

    <style>
        /* Premium iBox/Apple Style Modal */
        .glass-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3); /* Darker, simpler dimming */
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 10001;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .glass-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .glass-modal-content {
            background: #ffffff; /* Solid white for crisp look */
            border-radius: 14px; /* classic iOS radius */
            width: 90%;
            max-width: 320px; /* Compact */
            padding: 0;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            transform: scale(0.95);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .glass-modal-overlay.active .glass-modal-content {
            transform: scale(1);
        }

        .glass-modal-body {
            padding: 24px 24px 20px;
        }

        .glass-modal-icon {
            font-size: 42px;
            color: #ff3b30;
            margin-bottom: 12px;
            display: block;
        }
        
        /* Remove the circle background for a cleaner look */
        .glass-modal-icon i {
            display: inline-block;
        }

        .glass-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1d1d1f;
            margin: 0 0 8px;
        }

        .glass-modal-text {
            font-size: 14px;
            color: #424245;
            margin: 0;
            line-height: 1.4;
        }

        .glass-modal-actions {
            display: flex;
            border-top: 1px solid #e5e5e5;
        }

        .glass-btn-cancel, .glass-btn-confirm {
            padding: 14px 0;
            font-size: 16px;
            font-weight: 400;
            cursor: pointer;
            border: none;
            flex: 1;
            background: white;
            transition: background 0.2s;
            margin: 0;
            border-radius: 0;
        }
        
        .glass-btn-cancel {
            color: #007aff; /* Apple Blue for Cancel/Action */
            border-right: 1px solid #e5e5e5;
            font-weight: 400;
        }

        .glass-btn-confirm {
            color: #ff3b30; /* Red for destructive */
            font-weight: 600; /* Bold for destructive/primary */
        }

        .glass-btn-cancel:hover, .glass-btn-confirm:hover {
            background: #f5f5f7;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>

    <script>
        let itemToDelete = null;
        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        function openDeleteModal(cartId) {
            itemToDelete = cartId;
            deleteModal.style.display = 'flex';
            // Force reflow
            deleteModal.offsetHeight;
            deleteModal.classList.add('active');
        }

        function closeDeleteModal() {
            deleteModal.classList.remove('active');
            setTimeout(() => {
                deleteModal.style.display = 'none';
                itemToDelete = null;
            }, 200);
        }
        
        confirmDeleteBtn.addEventListener('click', function() {
            if (itemToDelete) {
                performDelete(itemToDelete);
                closeDeleteModal();
            }
        });

        // Update cart quantity
        function updateQuantity(cartId, action) {
            const card = document.querySelector(`[data-cart-id="${cartId}"]`);
            if (!card) return;

            card.classList.add('updating');

            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                card.classList.remove('updating');
                
                if (data.success) {
                    if (data.deleted) {
                        // Remove card with animation
                        card.style.transition = 'all 0.3s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            location.reload(); // Reload page to reflect changes
                        }, 300);
                    } else {
                        // Update quantity display
                        const qtyDisplay = card.querySelector('.qty-display');
                        const decreaseBtn = card.querySelector('.qty-decrease');
                        
                        if (qtyDisplay) {
                            qtyDisplay.textContent = data.new_quantity;
                        }
                        
                        // Disable decrease button if quantity is 1
                        if (decreaseBtn) {
                            decreaseBtn.disabled = data.new_quantity <= 1;
                        }

                        // Show success feedback
                        showToast('Jumlah diperbarui', 'success');
                    }
                } else {
                    showToast(data.message || 'Gagal memperbarui jumlah', 'error');
                }
            })
            .catch(err => {
                card.classList.remove('updating');
                console.error('Error updating quantity:', err);
                showToast('Terjadi kesalahan', 'error');
            });
        }

        // Trigger Delete Modal
        function deleteCartItem(cartId) {
            openDeleteModal(cartId);
        }

        // Actual Delete Logic
        function performDelete(cartId) {
            const card = document.querySelector(`[data-cart-id="${cartId}"]`);
            if (!card) return;

            card.classList.add('updating');

            fetch('update_cart_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    action: 'set',
                    quantity: 0
                })
            })
            .then(response => response.json())
            .then(data => {
                card.classList.remove('updating');
                
                if (data.success) {
                    // Remove card with animation
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        location.reload(); 
                    }, 300);
                    showToast('Produk dihapus', 'success');
                } else {
                    showToast(data.message || 'Gagal menghapus produk', 'error');
                }
            })
            .catch(err => {
                card.classList.remove('updating');
                console.error('Error deleting item:', err);
                showToast('Terjadi kesalahan', 'error');
            });
        }

        // Modern iBox Style Toast
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            
            // Icon selection
            let icon = 'bi-info-circle-fill';
            let iconColor = '#007aff';
            if (type === 'success') { icon = 'bi-check-circle-fill'; iconColor = '#34c759'; }
            if (type === 'error') { icon = 'bi-exclamation-circle-fill'; iconColor = '#ff3b30'; }

            toast.innerHTML = `<i class="bi ${icon}" style="color: ${iconColor}; font-size: 18px; margin-right: 10px;"></i> <span>${message}</span>`;
            
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%); /* Start centered */
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                color: #1d1d1f;
                padding: 12px 24px;
                border-radius: 50px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                z-index: 10002;
                font-weight: 500;
                font-size: 14px;
                display: flex;
                align-items: center;
                border: 1px solid rgba(0,0,0,0.05);
                opacity: 0;
                margin-top: -20px;
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            `;
            
            document.body.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.marginTop = '0';
            });

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.marginTop = '-20px';
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        // Add CSS for toast if needed
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    
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
                // cart.php is in pages/cart/
                fetch('get_cart_dropdown.php')
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
                            imgPath = '../../' + item.image;
                        } else {
                            imgPath = '../../admin/uploads/' + item.image;
                        }
                    } else {
                         imgPath = '../../assets/img/logo/logo.png';
                    } 
                    
                    // Build variant display for dropdown
                    let variantHtml = '';
                    if (item.variant && item.variant.trim() !== '') {
                        variantHtml = `<div style="font-size: 12px; color: #86868b; margin-top: 4px;"><i class="bi bi-tag" style="font-size: 11px;"></i> ${item.variant}</div>`;
                    }
                    
                    html += `
                        <li class="cart-item">
                            <a href="${item.checkout_url}" class="cart-item-link">
                                <div class="cart-item-img">
                                    <img src="${imgPath}" alt="${item.name}" onerror="this.src='../../assets/img/logo/logo.png'">
                                </div>
                                <div class="cart-item-details">
                                    <div class="cart-item-name">${item.name}</div>
                                    ${variantHtml}
                                    <div class="cart-item-price-row">
                                        <div class="cart-item-qty">${item.qty} Barang</div>
                                        <div class="cart-item-price">${item.formatted_price}</div>
                                    </div>
                                </div>
                            </a>
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