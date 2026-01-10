<?php
session_start();
require '../../db/db.php';
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
    <title>Produk - iBox Indonesia</title>
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
    </style>
</head>

<body>
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=mac" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_mac WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=ipad" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_ipad WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=iphone" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_iphone WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=watch" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_watch WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=music" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_music WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=airtag" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_airtag WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                        echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=aksesoris" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                        // Query untuk mengambil produk berdasarkan kategori
                                        $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_aksesoris WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                        $produk_result = mysqli_query($db, $produk_query);

                                        if (mysqli_num_rows($produk_result) > 0) {
                                            echo '<ul>';
                                            while ($produk = mysqli_fetch_assoc($produk_result)) {
                                                echo '<li><a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a></li>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=mac" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_mac WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=ipad" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_ipad WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=iphone" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_iphone WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=watch" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_watch WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=music" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_music WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=airtag" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_airtag WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
                                echo '<a href="category-products.php?category=' . urlencode($kategori) . '&type=aksesoris" class="dropdown-category" style="text-decoration: none; display: block;">' . htmlspecialchars($kategori) . '</a>';

                                // Query untuk mengambil produk berdasarkan kategori
                                $produk_query = "SELECT DISTINCT nama_produk FROM admin_produk_aksesoris WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "' ORDER BY nama_produk ASC";
                                $produk_result = mysqli_query($db, $produk_query);

                                if (mysqli_num_rows($produk_result) > 0) {
                                    while ($produk = mysqli_fetch_assoc($produk_result)) {
                                        echo '<a href="#">' . htmlspecialchars($produk['nama_produk']) . '</a>';
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
        <span class="breadcrumb-current">Produk</span>
    </div>

    <?php
    // Fetch all products from different tables
    $products = [];
    
    // Configuration for each product type with their specific variant tables
    $product_types = [
        'iphone' => [
            'table' => 'admin_produk_iphone', 
            'category' => 'iPhone',
            'image_table' => 'admin_produk_iphone_gambar',
            'variant_table' => 'admin_produk_iphone_kombinasi'
        ],
        'ipad' => [
            'table' => 'admin_produk_ipad', 
            'category' => 'iPad',
            'image_table' => 'admin_produk_ipad_gambar',
            'variant_table' => 'admin_produk_ipad_kombinasi'
        ],
        'mac' => [
            'table' => 'admin_produk_mac', 
            'category' => 'Mac',
            'image_table' => 'admin_produk_mac_gambar',
            'variant_table' => 'admin_produk_mac_kombinasi'
        ],
        'watch' => [
            'table' => 'admin_produk_watch', 
            'category' => 'Watch',
            'image_table' => 'admin_produk_watch_gambar',
            'variant_table' => 'admin_produk_watch_kombinasi'
        ],
        'airtag' => [
            'table' => 'admin_produk_airtag', 
            'category' => 'AirTag',
            'image_table' => 'admin_produk_airtag_gambar',
            'variant_table' => 'admin_produk_airtag_kombinasi'
        ],
        'music' => [
            'table' => 'admin_produk_music', 
            'category' => 'Music',
            'image_table' => 'admin_produk_music_gambar',
            'variant_table' => 'admin_produk_music_kombinasi'
        ],
        'aksesoris' => [
            'table' => 'admin_produk_aksesoris', 
            'category' => 'Aksesoris',
            'image_table' => 'admin_produk_aksesoris_gambar',
            'variant_table' => 'admin_produk_aksesoris_kombinasi'
        ]
    ];

    foreach ($product_types as $type => $config) {
        // Query tanpa filter status_produk karena kolom tidak ada
        $query = "SELECT * FROM {$config['table']} ORDER BY id DESC";
        $result = $db->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Get the first image for each product from specific image table
                $img_query = "SELECT foto_thumbnail FROM {$config['image_table']} WHERE produk_id = ? LIMIT 1";
                $img_stmt = $db->prepare($img_query);
                $img_stmt->bind_param("i", $row['id']);
                $img_stmt->execute();
                $img_result = $img_stmt->get_result();
                $image = $img_result->fetch_assoc();
                
                // Get min price from specific variant table
                $price_query = "SELECT 
                                    MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) as min_price,
                                    MIN(harga) as original_price,
                                    MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount,
                                    MIN(harga_diskon) as discount_price
                                FROM {$config['variant_table']} 
                                WHERE produk_id = ? AND jumlah_stok > 0";
                $price_stmt = $db->prepare($price_query);
                $price_stmt->bind_param("i", $row['id']);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_data = $price_result->fetch_assoc();
                
                // Only add products that have stock and price
                if ($price_data && $price_data['min_price'] > 0) {
                    $products[] = [
                        'id' => $row['id'],
                        'type' => $type,
                        'category' => $config['category'],
                        'name' => $row['nama_produk'],
                        'description' => $row['deskripsi_produk'] ?? '',
                        'image' => $image['foto_thumbnail'] ?? 'placeholder.jpg',
                        'price' => $price_data['min_price'],
                        'original_price' => $price_data['original_price'],
                        'has_discount' => $price_data['has_discount'] > 0 && $price_data['discount_price'] > 0
                    ];
                }
            }
        }
    }
    ?>

    <!-- Products Section -->
    <div class="products-container">
        <style>
            .products-container {
                margin: 0 auto;
                padding: 40px 5%;
            }

            .products-header {
                margin-bottom: 40px;
            }

            .products-title {
                font-size: 36px;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 10px;
                background: linear-gradient(135deg, #1d1d1f 0%, #4a4a4a 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .products-subtitle {
                font-size: 16px;
                color: #6e6e73;
                margin-bottom: 30px;
            }

            /* Main Layout Grid */
            .products-layout {
                display: grid;
                grid-template-columns: 280px 1fr;
                gap: 30px;
                align-items: start;
            }

            /* Filter Sidebar */
            .filter-sidebar {
                position: sticky;
                top: 100px;
                background: white;
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            }

            .filter-header {
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 2px solid #f5f5f7;
            }

            .filter-title {
                font-size: 20px;
                font-weight: 700;
                color: #1d1d1f;
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
            }

            .filter-title i {
                color: #007aff;
                font-size: 22px;
            }

            .filter-count {
                font-size: 13px;
                color: #6e6e73;
                font-weight: 500;
            }

            .filter-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .filter-btn {
                padding: 14px 20px;
                border: 2px solid #e5e5e7;
                background: white;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                color: #1d1d1f;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 12px;
                text-align: left;
                position: relative;
                overflow: hidden;
            }

            .filter-btn::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: #007aff;
                transform: scaleY(0);
                transition: transform 0.3s ease;
            }

            .filter-btn:hover {
                border-color: #007aff;
                color: #007aff;
                background: #f5f9ff;
                transform: translateX(5px);
            }

            .filter-btn:hover::before {
                transform: scaleY(1);
            }

            .filter-btn.active {
                background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
                border-color: #007aff;
                color: white;
                box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
                transform: translateX(5px);
            }

            .filter-btn.active::before {
                transform: scaleY(1);
                background: white;
            }

            .filter-btn i {
                font-size: 18px;
                min-width: 20px;
                text-align: center;
            }

            /* Products Content Area */
            .products-content {
                min-height: 400px;
            }

            .products-content-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                padding: 16px 24px;
                background: rgba(255, 255, 255, 0.65);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); 
            }

            .results-info {
                font-size: 15px;
                color: #6e6e73;
                font-weight: 500;
            }

            .results-count {
                font-weight: 700;
                color: #007aff;
            }

            /* Sort Styles - Glassmorphism */
            .sort-container {
                display: flex;
                align-items: center;
                gap: 12px;
                background: rgba(255, 255, 255, 0.4);
                padding: 4px 4px 4px 14px;
                border-radius: 14px;
                border: 1px solid rgba(255, 255, 255, 0.6);
                box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.02);
            }

            .sort-label {
                font-size: 13px;
                color: #555;
                font-weight: 600;
                letter-spacing: 0.2px;
            }

            .sort-select {
                border: 1px solid rgba(255, 255, 255, 0.6);
                border-radius: 10px;
                padding: 8px 32px 8px 14px;
                font-size: 13px;
                font-weight: 500;
                color: #333;
                background: rgba(255, 255, 255, 0.5);
                cursor: pointer;
                outline: none;
                transition: all 0.3s ease;
                appearance: none;
                backdrop-filter: blur(4px);
                background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007AFF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 10px auto;
            }

            .sort-select:hover {
                background-color: rgba(255, 255, 255, 0.9);
                border-color: rgba(0, 122, 255, 0.3);
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.1);
                transform: translateY(-1px);
            }

            .sort-select:focus {
                background-color: white;
                border-color: #007aff;
                box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
            }

            @media (max-width: 768px) {
                .products-content-header {
                     flex-direction: column;
                     gap: 15px;
                     align-items: stretch;
                }
                
                .sort-container {
                     width: 100%;
                     justify-content: space-between;
                }
            }

            /* Products Grid */
            .products-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
                margin-bottom: 40px;
            }

            .product-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: pointer;
                position: relative;
                display: flex;
                flex-direction: column;
                min-height: 400px;
            }

            .product-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            }

            .product-image-container {
                position: relative;
                padding-top: 100%;
                background: #f5f5f7;
                overflow: hidden;
            }

            .product-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.4s ease;
            }

            .product-card:hover .product-image {
                transform: scale(1.1);
            }

            .product-badge {
                position: absolute;
                top: 15px;
                left: 15px;
                background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
                color: white;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.4);
                z-index: 2;
            }

            .product-discount-badge {
                position: absolute;
                top: 15px;
                right: 15px;
                background: linear-gradient(135deg, #ff3b30 0%, #ff6b60 100%);
                color: white;
                padding: 6px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 700;
                box-shadow: 0 4px 12px rgba(255, 59, 48, 0.4);
                z-index: 2;
            }

            .product-info {
                padding: 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .product-category {
                font-size: 12px;
                color: #007aff;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 8px;
            }

            .product-name {
                font-size: 18px;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 8px;
                line-height: 1.3;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }



            .product-footer {
                display: flex;
                flex-direction: column;
                gap: 15px;
                padding-top: 15px;
                border-top: 1px solid #f5f5f7;
                margin-top: auto;
            }

            .product-price {
                font-size: 22px;
                font-weight: 700;
                color: #1d1d1f;
            }

            .product-price-original {
                font-size: 16px;
                font-weight: 500;
                color: #86868b;
                text-decoration: line-through;
                margin-bottom: 4px;
            }

            .product-price-discount {
                font-size: 24px;
                font-weight: 700;
                color: #ff3b30;
            }

            .price-container {
                display: flex;
                flex-direction: column;
                gap: 2px;
            }

            .product-price-label {
                font-size: 11px;
                color: #86868b;
                font-weight: 500;
                display: block;
                margin-bottom: 2px;
            }

            .product-cta {
                background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 100%;
            }

            .product-cta:hover {
                transform: scale(1.02);
                box-shadow: 0 6px 20px rgba(0, 122, 255, 0.4);
            }

            .product-cta-secondary {
                background: white;
                color: #007aff;
                padding: 12px 20px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                border: 1px solid #007aff;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 100%;
                margin-top: 8px;
            }

            .product-cta-secondary:hover {
                background: #f0f7ff;
                transform: scale(1.02);
            }

            .product-cta i, .product-cta-secondary i {
                font-size: 14px;
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 80px 20px;
                background: white;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            }

            .empty-state i {
                font-size: 64px;
                color: #d2d2d7;
                margin-bottom: 20px;
            }

            .empty-state h3 {
                font-size: 24px;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 10px;
            }

            .empty-state p {
                font-size: 16px;
                color: #6e6e73;
            }

            /* Responsive */
            @media (max-width: 1100px) {
                .products-layout {
                    display: block;
                }

                .filter-sidebar {
                    margin-bottom: 20px;
                    padding: 15px 20px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .filter-sidebar:hover {
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    transform: translateY(-2px);
                }

                .filter-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 0;
                    padding-bottom: 0;
                    border-bottom: none;
                }

                .filter-title {
                    font-size: 16px;
                }

                .filter-buttons {
                    display: none;
                    margin-top: 20px;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 10px;
                    padding-top: 20px;
                    border-top: 1px solid #f5f5f7;
                }

                .filter-buttons.show {
                    display: grid;
                }

                .products-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (max-width: 768px) {
                .products-title {
                    font-size: 28px;
                }

                .filter-buttons {
                    grid-template-columns: 1fr;
                }

                .filter-btn {
                    padding: 12px 16px;
                    font-size: 13px;
                }

                .products-grid {
                    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                    gap: 15px;
                }

                .product-name {
                    font-size: 16px;
                }

                .product-price {
                    font-size: 18px;
                }

                .products-content-header {
                    flex-direction: column;
                    gap: 15px;
                    align-items: flex-start;
                }
            }

            @media (min-width: 1400px) {
                .products-grid {
                    grid-template-columns: repeat(4, 1fr);
                }
            }

            @media (min-width: 1776px) {
                .products-grid {
                    grid-template-columns: repeat(5, 1fr);
                }
            }

            /* Modern Quantity Selector */
            .qty-selector {
                display: flex;
                align-items: center;
                background: #f5f5f7;
                border-radius: 25px;
                padding: 4px;
                width: fit-content;
                border: 1px solid #e5e5e5;
            }
            .qty-btn {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: none;
                background: white;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                color: #1d1d1f;
            }
            .qty-btn:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: scale(1.05);
                color: #007aff;
            }
            .qty-btn:active {
                transform: scale(0.95);
            }
            .qty-input {
                width: 40px;
                text-align: center;
                border: none;
                background: transparent;
                font-weight: 600;
                font-size: 16px;
                margin: 0 8px;
                color: #1d1d1f;
                padding: 0;
            }
            .qty-input:focus {
                outline: none;
            }
        </style>

        <div class="products-header">
            <h1 class="products-title">Semua Produk</h1>
            <p class="products-subtitle">Temukan produk Apple terbaik untuk kebutuhan Anda</p>
        </div>

        <!-- Products Layout -->
        <div class="products-layout">
            <!-- Filter Sidebar -->
            <aside class="filter-sidebar">
                <div class="filter-header">
                    <div class="filter-title">
                        <i class="fas fa-filter"></i>
                        Filter Kategori
                    </div>
                    <div class="filter-count">
                        <span id="productCount"><?php echo count($products); ?></span> Produk
                    </div>
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all" onclick="filterProducts('all')">
                        <i class="fas fa-th"></i>
                        Semua
                    </button>
                    <button class="filter-btn" data-category="iphone" onclick="filterProducts('iphone')">
                        <i class="fab fa-apple"></i>
                        iPhone
                    </button>
                    <button class="filter-btn" data-category="ipad" onclick="filterProducts('ipad')">
                        <i class="fas fa-tablet-alt"></i>
                        iPad
                    </button>
                    <button class="filter-btn" data-category="mac" onclick="filterProducts('mac')">
                        <i class="fas fa-laptop"></i>
                        Mac
                    </button>
                    <button class="filter-btn" data-category="watch" onclick="filterProducts('watch')">
                        <i class="fas fa-clock"></i>
                        Watch
                    </button>
                    <button class="filter-btn" data-category="airtag" onclick="filterProducts('airtag')">
                        <i class="fas fa-circle-notch"></i>
                        AirTag
                    </button>
                    <button class="filter-btn" data-category="music" onclick="filterProducts('music')">
                        <i class="fas fa-headphones"></i>
                        Music
                    </button>
                    <button class="filter-btn" data-category="aksesoris" onclick="filterProducts('aksesoris')">
                        <i class="fas fa-plug"></i>
                        Aksesoris
                    </button>
                </div>
            </aside>

            <!-- Products Content -->
            <div class="products-content">
                <div class="products-content-header">
                    <div class="results-info">
                        Menampilkan <span class="results-count" id="resultsCount"><?php echo count($products); ?></span> produk
                    </div>
                    <div class="sort-container">
                        <label for="sortProducts" class="sort-label">Urutkan:</label>
                        <select id="sortProducts" class="sort-select" onchange="sortProducts(this.value)">
                            <option value="default">Terbaru</option>
                            <option value="price_low">Harga Terendah</option>
                            <option value="price_high">Harga Tertinggi</option>
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" 
                         data-category="<?php echo $product['type']; ?>" 
                         data-price="<?php echo $product['price']; ?>"
                         data-name="<?php echo htmlspecialchars($product['name']); ?>"
                         data-id="<?php echo $product['id']; ?>"
                         onclick="window.location.href='../checkout/checkout.php?id=<?php echo $product['id']; ?>&tipe=<?php echo $product['type']; ?>'">
                        <div class="product-image-container">
                            <img src="../../admin/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="product-image"
                                 onerror="this.src='../../admin/uploads/placeholder.jpg'">
                            <div class="product-badge"><?php echo htmlspecialchars($product['category']); ?></div>
                            <?php if ($product['has_discount']): ?>
                                <div class="product-discount-badge">
                                    <i class="fas fa-tag"></i> DISKON
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>

                            <div class="product-footer">
                                <div>
                                    <span class="product-price-label">Mulai dari</span>
                                    <?php if ($product['has_discount']): ?>
                                        <div class="price-container">
                                            <div class="product-price-original">Rp <?php echo number_format($product['original_price'], 0, ',', '.'); ?></div>
                                            <div class="product-price-discount">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <div class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></div>
                                    <?php endif; ?>
                                </div>
                                <button class="product-cta" onclick="event.stopPropagation(); window.location.href='../checkout/checkout.php?id=<?php echo $product['id']; ?>&tipe=<?php echo $product['type']; ?>'">
                                    <i class="fas fa-shopping-bag"></i>
                                    Beli Sekarang
                                </button>
                                <button class="product-cta-secondary" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, '<?php echo $product['type']; ?>')">
                                    <i class="fas fa-shopping-cart"></i>
                                    Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="fas fa-box-open"></i>
                    <h3>Belum Ada Produk</h3>
                    <p>Produk akan segera tersedia</p>
                </div>
            <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function sortProducts(sortType) {
            const grid = document.getElementById('productsGrid');
            const cards = Array.from(grid.getElementsByClassName('product-card'));

            cards.sort((a, b) => {
                let valA, valB;

                switch(sortType) {
                    case 'price_low':
                        valA = parseFloat(a.dataset.price) || 0;
                        valB = parseFloat(b.dataset.price) || 0;
                        return valA - valB;
                    case 'price_high':
                        valA = parseFloat(a.dataset.price) || 0;
                        valB = parseFloat(b.dataset.price) || 0;
                        return valB - valA;
                    case 'name_asc':
                        valA = (a.dataset.name || '').toLowerCase();
                        valB = (b.dataset.name || '').toLowerCase();
                        return valA.localeCompare(valB);
                    case 'name_desc':
                        valA = (a.dataset.name || '').toLowerCase();
                        valB = (b.dataset.name || '').toLowerCase();
                        return valB.localeCompare(valA);
                    default: // default (newest / ID desc)
                        valA = parseInt(a.dataset.id) || 0;
                        valB = parseInt(b.dataset.id) || 0;
                        return valB - valA;
                }
            });

            // Re-append to grid
            cards.forEach(card => grid.appendChild(card));
        }

        function filterProducts(category) {
            const cards = document.querySelectorAll('.product-card');
            const buttons = document.querySelectorAll('.filter-btn');
            let visibleCount = 0;

            // Update active button
            buttons.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.category === category) {
                    btn.classList.add('active');
                }
            });

            // Update URL agar persist saat refresh
            if (category !== 'all') {
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('type', category);
                window.history.pushState({path: newUrl.toString()}, '', newUrl.toString());
            } else {
                 // Jika 'all', mungkin kita ingin menghapus parameter type atau membiarkannya
                 const newUrl = new URL(window.location.href);
                 newUrl.searchParams.delete('type');
                 window.history.pushState({path: newUrl.toString()}, '', newUrl.toString());
            }

            // Filter cards with animation
            cards.forEach((card, index) => {
                const cardCategory = card.dataset.category;
                
                if (category === 'all' || cardCategory === category) {
                    setTimeout(() => {
                        card.style.display = 'flex';
                        card.style.animation = 'fadeInUp 0.4s ease forwards';
                    }, index * 30);
                    visibleCount++;
                } else {
                    card.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });

            // Update count - gunakan resultsCount yang sesuai dengan HTML
            setTimeout(() => {
                // Update count di header (Menampilkan X produk)
                const resultsCountElement = document.getElementById('resultsCount');
                if (resultsCountElement) {
                    resultsCountElement.textContent = visibleCount;
                }
                
                // Update count di sidebar filter (X Produk)
                const productCountElement = document.getElementById('productCount');
                if (productCountElement) {
                    productCountElement.textContent = visibleCount;
                }
            }, 400);
        }

        // Initialize filter from URL param
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const typeParam = urlParams.get('type');
            if(typeParam) {
                // Map common types if necessary, or pass directly
                // valid types: iphone, ipad, mac, watch, aksesoris, airtag, music
                const validTypes = ['iphone', 'ipad', 'mac', 'watch', 'aksesoris', 'airtag', 'music'];
                if(validTypes.includes(typeParam)) {
                    filterProducts(typeParam);
                }
            }
        });

        // Initial cart add function
        function addToCart(productId, productType) {
            // Using fetch to send data to the server
            fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_type: productType,
                    quantity: 1
                })
            })
            .then(async response => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Server returned invalid JSON:', text);
                    throw new Error('Server Error');
                }
            })
            .then(data => {
                // Check if auth is required
                if (data.code === 'auth_required') {
                    showLoginRequiredModal();
                    return;
                }
                
                if (data.status === 'success') {
                    showModalNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                } else if (data.status === 'variant_required') {
                    openVariantModal(data);
                } else {
                    showModalNotification(data.message || 'Gagal menambahkan ke keranjang', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showModalNotification('Terjadi kesalahan saat menghubungi server.', 'error');
            });
        }
        
        // Function to show login required modal
        function showLoginRequiredModal() {
            const modalEl = document.getElementById('resultModal');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const msg = document.getElementById('resultMessage');
            
            title.textContent = 'Login Diperlukan';
            msg.textContent = 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.';
            icon.className = 'bi bi-lock-fill text-warning';
            
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        let currentVariants = [];
        let variantImages = {};

        function openVariantModal(data) {
            currentVariants = data.variants;
            variantImages = data.images || {};
            
            document.getElementById('modalProductId').value = data.product_id;
            document.getElementById('modalProductType').value = data.product_type;
            
            // Set Title (Best Effort)
            document.getElementById('variantModalTitle').textContent = 'Pilih Varian Produk';
            
            // Build Selectors
            const container = document.getElementById('variantSelectors');
            container.innerHTML = '';
            
            const potentialKeys = [
                {key: 'warna', label: 'Warna'},
                {key: 'warna_case', label: 'Warna Case'},
                {key: 'penyimpanan', label: 'Penyimpanan'},
                {key: 'ukuran', label: 'Ukuran'},
                {key: 'ukuran_case', label: 'Ukuran Case'},
                {key: 'processor', label: 'Processor'},
                {key: 'ram', label: 'RAM'},
                {key: 'konektivitas', label: 'Konektivitas'},
                {key: 'tipe_koneksi', label: 'Tipe Koneksi'},
                {key: 'tipe', label: 'Tipe'},
                {key: 'material', label: 'Material'}
            ];
            
            const sample = currentVariants[0];
            const activeKeys = potentialKeys.filter(k => sample.hasOwnProperty(k.key));
            
            activeKeys.forEach(k => {
                // Get unique values and trim them
                const values = [...new Set(currentVariants.map(v => {
                    const val = v[k.key];
                    return val ? val.toString().trim() : null;
                }))].filter(v => v);
                
                if(values.length > 0) {
                    const div = document.createElement('div');
                    div.className = 'mb-3';
                    const label = document.createElement('label');
                    label.className = 'form-label small text-muted';
                    label.textContent = k.label;
                    
                    const select = document.createElement('select');
                    select.className = 'form-select variant-select';
                    select.dataset.key = k.key;
                    select.onchange = checkSelection;
                    
                    select.add(new Option('Pilih ' + k.label, ''));
                    values.forEach(val => {
                        select.add(new Option(val, val));
                    });
                    
                    div.appendChild(label);
                    div.appendChild(select);
                    container.appendChild(div);
                }
            });
            
            checkSelection(); 
            
            const modal = new bootstrap.Modal(document.getElementById('variantModal'));
            modal.show();
        }

        function updateQuantity(change) {
            const qtyInput = document.getElementById('modalQuantity');
            const btn = document.getElementById('confirmVariantBtn');
            let maxStock = parseInt(btn.dataset.maxStock) || 999;
            
            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;
            
            if (newQty < 1) newQty = 1;
            if (newQty > maxStock) newQty = maxStock;
            
            qtyInput.value = newQty;
        }

        function checkSelection() {
            const selectors = document.querySelectorAll('.variant-select');
            const selectedCriteria = {};
            let isComplete = true;
            
            selectors.forEach(sel => {
                if(sel.value) {
                    // Trim the value to avoid trailing space issues
                    selectedCriteria[sel.dataset.key] = sel.value.trim();
                } else {
                    isComplete = false;
                }
            });
            
            const btn = document.getElementById('confirmVariantBtn');
            const priceEl = document.getElementById('variantModalPrice');
            const stokEl = document.getElementById('stockStatus');
            const imgEl = document.getElementById('variantModalImg');
            
            
            const colorKey = selectedCriteria['warna'] || selectedCriteria['warna_case'];
            if(colorKey && variantImages[colorKey]) {
                imgEl.src = '../../admin/uploads/' + variantImages[colorKey];
            } else if (Object.values(variantImages).length > 0) {
                 imgEl.src = '../../admin/uploads/' + Object.values(variantImages)[0];
            }

            if(!isComplete) {
                btn.disabled = true;
                priceEl.textContent = 'Rp -';
                stokEl.textContent = 'Stok: -';
                return;
            }
            
            // Match variant with trimmed comparison
            const match = currentVariants.find(v => {
                for(let key in selectedCriteria) {
                    const variantValue = (v[key] || '').toString().trim();
                    const selectedValue = selectedCriteria[key].toString().trim();
                    if(variantValue !== selectedValue) return false;
                }
                return true;
            });
            
            if(match) {
                priceEl.textContent = 'Rp ' + parseInt(match.harga).toLocaleString('id-ID');
                
                if(match.jumlah_stok > 0) {
                    stokEl.textContent = 'Stok: ' + match.jumlah_stok;
                    stokEl.className = 'badge bg-success me-2';
                    btn.disabled = false;
                    btn.dataset.combinationId = match.id;
                    btn.dataset.maxStock = match.jumlah_stok;
                    document.getElementById('modalQuantity').value = 1; // Reset quantity
                } else {
                    stokEl.textContent = 'Stok Habis';
                    stokEl.className = 'badge bg-danger me-2';
                    btn.disabled = true;
                    btn.dataset.maxStock = 0;
                }
            } else {
                priceEl.textContent = 'Tidak Tersedia';
                stokEl.textContent = 'Kombinasi tidak ditemukan';
                stokEl.className = 'badge bg-warning text-dark me-2';
                btn.disabled = true;
                btn.dataset.maxStock = 0;
            }
        }

        function submitVariant() {
            const btn = document.getElementById('confirmVariantBtn');
            const combId = btn.dataset.combinationId;
            const productId = document.getElementById('modalProductId').value;
            const productType = document.getElementById('modalProductType').value;
            
            if(!combId) return;

            const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
            
            // Add loading state
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menambahkan...';
            btn.disabled = true;
            
            fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    product_type: productType,
                    quantity: quantity,
                    combination_id: combId
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const modalEl = document.getElementById('variantModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    showModalNotification('Produk berhasil ditambahkan ke keranjang!', 'success');
                } else {
                    showModalNotification('Gagal: ' + data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showModalNotification('Terjadi kesalahan.', 'error');
            })
            .finally(() => {
                btn.innerHTML = 'Tambahkan ke Keranjang';
                btn.disabled = false;
            });
        }
    </script>

    <!-- Variant Selection Modal -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Pilih Varian Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex mb-4">
                        <div class="variant-img-wrapper me-3" style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f8f9fa;">
                            <img id="variantModalImg" src="" alt="Product" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <h6 id="variantModalTitle" class="fw-bold mb-1">Product Name</h6>
                            <p id="variantModalPrice" class="text-primary fw-bold mb-0">Rp 0</p>
                        </div>
                    </div>
                    
                    <form id="variantForm">
                        <input type="hidden" id="modalProductId">
                        <input type="hidden" id="modalProductType">
                        <div id="variantSelectors">
                            <!-- Dynamic Selectors will be injected here -->
                        </div>
                    </form>
                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <label class="form-label small text-muted mb-2 fw-bold">Jumlah</label>
                            <div class="qty-selector">
                                <button class="qty-btn" type="button" onclick="updateQuantity(-1)">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="text" id="modalQuantity" class="qty-input" value="1" readonly>
                                <button class="qty-btn" type="button" onclick="updateQuantity(1)">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <span id="stockStatus" class="badge bg-secondary">Stok: -</span>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary w-100 rounded-pill" id="confirmVariantBtn" disabled onclick="submitVariant()">
                        Tambahkan ke Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Modal -->
    <div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4" style="border-radius: 20px; border: none;">
                <div class="modal-body">
                    <div class="mb-3">
                        <i id="resultIcon" class="bi" style="font-size: 4rem;"></i>
                    </div>
                    <h4 id="resultTitle" class="fw-bold mb-2">Title</h4>
                    <p id="resultMessage" class="text-muted mb-4">Message</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary rounded-pill py-2 fw-bold" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showModalNotification(message, type = 'success') {
            const modalEl = document.getElementById('resultModal');
            const icon = document.getElementById('resultIcon');
            const title = document.getElementById('resultTitle');
            const msg = document.getElementById('resultMessage');
            
            msg.textContent = message;
            
            if (type === 'success') {
                title.textContent = 'Berhasil!';
                icon.className = 'bi bi-check-circle-fill text-success';
            } else if (type === 'error') {
                title.textContent = 'Gagal';
                icon.className = 'bi bi-x-circle-fill text-danger';
            } else {
                title.textContent = 'Info';
                icon.className = 'bi bi-info-circle-fill text-primary';
            }
            
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
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
                // products.php is in pages/products/
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
                            // products.php -> pages/products/ -> ../../assets/
                            imgPath = '../../' + item.image;
                        } else {
                             // products.php -> pages/products/ -> ../../admin/uploads/
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
                // products.php is in pages/products/
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
                            // products.php -> pages/products/ -> ../../assets/
                            imgPath = '../../' + item.image;
                        } else {
                             // products.php -> pages/products/ -> ../../admin/uploads/
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