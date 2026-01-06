<?php
session_start();
require '../db/db.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_initials = '';
if ($is_logged_in) {
    // Get firstname and lastname from session
    $firstname = isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '';
    $lastname = isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '';

    // Create initials (first letter of firstname + first letter of lastname)
    $first_initial = !empty($firstname) ? strtoupper(substr($firstname, 0, 1)) : '';
    $last_initial = !empty($lastname) ? strtoupper(substr($lastname, 0, 1)) : '';
    $user_initials = $first_initial . $last_initial;

    // Cart Count
    $cart_count = 0;
    $uid = $_SESSION['user_id'];
    $q_cart = $db->query("SELECT SUM(jumlah) as total FROM user_keranjang WHERE user_id = $uid");
    if ($q_cart && $row_cart = $q_cart->fetch_assoc()) {
        $cart_count = $row_cart['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox Indonesia (Stabilized Menu)</title>
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
                color: #1d1d1f;
                /* Dark text for white bg */
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
                color: #0071e3;
                /* Standard Apple Blue */
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
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 0, 0, 0.05);
                /* Slight border for image */
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
                            <img src="assets/img/logo/logo.png" alt="iBox Logo">
                        </div>
                    </div>
                    <div class="search-bar-menu">
                        <form action="">
                            <input type="text" placeholder="Cari produk di iBox">
                        </form>
                    </div>
                    <div class="nav-other-menu">
                        <?php if ($is_logged_in): ?>
                            <a href="auth/profile.php" class="user-name-link" style="text-decoration: none; color: #333; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-person-circle" style="font-size: 20px;"></i>
                                <span><?php echo htmlspecialchars($user_initials); ?></span>
                            </a>
                        <?php else: ?>
                            <a href="auth/login.php" class="user-icon">
                                <i class="bi bi-person-fill"></i>
                            </a>
                        <?php endif; ?>
                        <!-- Cart Icon with Dropdown Wrapper -->
                        <div class="position-relative cart-dropdown-wrapper">
                            <a href="cart/cart.php" class="bag-icon position-relative text-dark text-decoration-none" id="cartDropdownTrigger">
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
                                    <a href="cart/cart.php" class="cart-dropdown-link">Lihat</a>
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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                        echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                    <img src="assets/img/logo/logo.png" alt="iBox Logo">
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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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
                                echo '<div class="dropdown-category">' . htmlspecialchars($kategori) . '</div>';

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

    <!-- image-slider -->
    <div class="image-slider-container">
        <?php
        // Query untuk mengambil data slider dari database
        $query = "SELECT * FROM home_image_slider ORDER BY id ASC";
        $result = mysqli_query($db, $query);

        // Cek apakah query berhasil
        if (!$result) {
            die("Query error: " . mysqli_error($db));
        }

        // Ambil semua data slide
        $slides = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $slides[] = $row;
        }

        // Jika tidak ada data, tampilkan slide default
        $hasSlides = !empty($slides);
        ?>

        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: #f5f5f7;
            }

            .image-slider-container {
                position: relative;
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                overflow: hidden;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                height: 611px;
            }

            .slider {
                display: flex;
                transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                height: 100%;
                width: 100%;
            }

            .slide {
                min-width: 100%;
                height: 100%;
                position: relative;
                flex-shrink: 0;
            }

            .slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .slide-content {
                position: absolute;
                bottom: 60px;
                left: 60px;
                background: rgba(255, 255, 255, 0.95);
                padding: 30px;
                border-radius: 12px;
                max-width: 500px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .slide-content h3 {
                color: #1d1d1f;
                font-size: 32px;
                margin-bottom: 12px;
                font-weight: 700;
                line-height: 1.2;
            }

            .slide-content p {
                color: #515154;
                font-size: 17px;
                margin-bottom: 25px;
                line-height: 1.5;
            }

            .slide-btn {
                background-color: #007aff;
                color: white;
                border: none;
                padding: 14px 30px;
                border-radius: 28px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 15px;
                letter-spacing: 0.3px;
                box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
                text-decoration: none;
            }

            .slide-btn:hover {
                background-color: #0056cc;
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 122, 255, 0.4);
            }

            .slide-btn:active {
                transform: translateY(-1px);
            }

            .slider-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding: 0 25px;
                z-index: 10;
                pointer-events: none;
            }

            .nav-btn {
                background-color: rgba(255, 255, 255, 0.95);
                border: none;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-size: 22px;
                color: #1d1d1f;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                opacity: 1;
                pointer-events: auto;
            }

            .nav-btn:hover {
                background-color: #ffffff;
                transform: scale(1.15);
                color: #007aff;
                box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            }

            .nav-btn:active {
                transform: scale(1.05);
            }

            /* Status indicator (simple dots) */
            .slider-status {
                position: absolute;
                bottom: 25px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 8px;
                z-index: 10;
            }

            .status-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.4);
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .status-dot.active {
                background-color: #ffffff;
                transform: scale(1.3);
            }

            .status-dot:hover {
                background-color: rgba(255, 255, 255, 0.8);
            }

            /* Mobile-first responsive styles */
            @media (max-width: 1400px) {
                .image-slider-container {
                    max-width: 1200px;
                    height: 450px;
                }
            }

            @media (max-width: 1200px) {
                .image-slider-container {
                    max-width: 1000px;
                    height: 400px;
                }

                .slide-content {
                    bottom: 50px;
                    left: 50px;
                    max-width: 420px;
                    padding: 25px;
                }

                .slide-content h3 {
                    font-size: 28px;
                }

                .slide-content p {
                    font-size: 16px;
                }
            }

            @media (max-width: 992px) {
                .image-slider-container {
                    height: 350px;
                    border-radius: 10px;
                }

                .slide-content {
                    bottom: 40px;
                    left: 40px;
                    max-width: 380px;
                    padding: 22px;
                }

                .slide-content h3 {
                    font-size: 24px;
                }

                .slide-content p {
                    font-size: 15px;
                    margin-bottom: 20px;
                }

                .slide-btn {
                    padding: 12px 25px;
                    font-size: 14px;
                }

                .nav-btn {
                    width: 48px;
                    height: 48px;
                    font-size: 20px;
                }
            }

            @media (max-width: 768px) {
                .image-slider-container {
                    height: 320px;
                    border-radius: 8px;
                }

                .slide-content {
                    bottom: 30px;
                    left: 30px;
                    max-width: 320px;
                    padding: 20px;
                }

                .slide-content h3 {
                    font-size: 22px;
                }

                .slide-content p {
                    font-size: 14px;
                    margin-bottom: 18px;
                }

                .slide-btn {
                    padding: 11px 22px;
                    font-size: 13.5px;
                }

                .nav-btn {
                    width: 44px;
                    height: 44px;
                    font-size: 18px;
                    padding: 0 15px;
                }

                .slider-nav {
                    padding: 0 15px;
                }

                .slider-status {
                    bottom: 15px;
                }
            }

            @media (max-width: 576px) {
                body {
                    padding: 10px;
                }

                .image-slider-container {
                    height: 280px;
                    border-radius: 6px;
                }

                .slide-content {
                    position: relative;
                    background: white;
                    max-width: 100%;
                    left: 0;
                    bottom: 0;
                    border-radius: 0;
                    padding: 20px 25px;
                    margin-top: -5px;
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                }

                .slide-content h3 {
                    font-size: 20px;
                }

                .slide-content p {
                    font-size: 13.5px;
                }

                .slider-status {
                    bottom: 10px;
                }

                .nav-btn {
                    opacity: 1;
                    background-color: rgba(255, 255, 255, 0.9);
                    width: 40px;
                    height: 40px;
                    font-size: 16px;
                }
            }

            @media (max-width: 480px) {
                .image-slider-container {
                    height: 250px;
                }

                .slide-content {
                    padding: 18px 20px;
                }

                .slide-content h3 {
                    font-size: 18px;
                }

                .slide-content p {
                    font-size: 13px;
                }

                .slide-btn {
                    padding: 10px 20px;
                    font-size: 13px;
                    border-radius: 24px;
                }

                .nav-btn {
                    width: 36px;
                    height: 36px;
                    font-size: 15px;
                    padding: 0 12px;
                }

                .slider-status {
                    bottom: 8px;
                }
            }

            @media (max-width: 400px) {
                .image-slider-container {
                    height: 220px;
                }

                .slide-content {
                    padding: 15px 18px;
                }

                .slide-content h3 {
                    font-size: 17px;
                }

                .slide-content p {
                    font-size: 12.5px;
                    margin-bottom: 15px;
                }

                .slide-btn {
                    padding: 9px 18px;
                    font-size: 12.5px;
                }

                .slider-status {
                    bottom: 6px;
                }
            }

            /* Animation for slide transitions */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .slide-content {
                animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.3s both;
            }
        </style>

        <div class="slider" id="ibox-slider">
            <?php if ($hasSlides): ?>
                <?php foreach ($slides as $slide): ?>
                    <?php
                    // Path gambar relatif dari halaman pages/index.php
                    $gambar_path = '../admin/uploads/slider/' . htmlspecialchars($slide['gambar_produk']);

                    // Link produk jika ada produk_id dan tipe_produk
                    $produk_link = '#';
                    if (!empty($slide['produk_id']) && !empty($slide['tipe_produk'])) {
                        $produk_link = 'products/products.php?tipe=' . $slide['tipe_produk'] . '&id=' . $slide['produk_id'];
                    }
                    ?>
                    <div class="slide">
                        <img src="<?php echo $gambar_path; ?>"
                            alt="<?php echo htmlspecialchars($slide['nama_produk']); ?>"
                            onerror="this.src='https://via.placeholder.com/1400x500/007AFF/FFFFFF?text=<?php echo urlencode($slide['nama_produk']); ?>'">
                        <div class="slide-content">
                            <h3><?php echo htmlspecialchars($slide['nama_produk']); ?></h3>
                            <p><?php echo htmlspecialchars($slide['deskripsi_produk']); ?></p>
                            <a href="<?php echo $produk_link; ?>" class="slide-btn">
                                <i class="bi bi-bag"></i> Beli Sekarang
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback slides jika tidak ada data di database -->
                <!-- Slide 1: iPhone -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="iPhone 15 Pro">
                    <div class="slide-content">
                        <h3>iPhone 15 Pro</h3>
                        <p>Titanium. So strong. So light. So Pro. Rasakan kekuatan chip A17 Pro yang revolusioner.</p>
                        <a href="#" class="slide-btn">
                            <i class="bi bi-bag"></i> Beli Sekarang
                        </a>
                    </div>
                </div>

                <!-- Slide 2: MacBook -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="MacBook Pro">
                    <div class="slide-content">
                        <h3>MacBook Pro</h3>
                        <p>Ditenagai chip M3 yang luar biasa. Untuk pengembang, desainer, dan profesional kreatif.</p>
                        <a href="#" class="slide-btn">
                            <i class="bi bi-laptop"></i> Jelajahi Mac
                        </a>
                    </div>
                </div>

                <!-- Slide 3: Apple Watch -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="Apple Watch">
                    <div class="slide-content">
                        <h3>Apple Watch Series 9</h3>
                        <p>Lebih cerdas, lebih cerah, lebih kuat. Pantau kesehatan dan tingkatkan produktivitas Anda.</p>
                        <a href="#" class="slide-btn">
                            <i class="bi bi-watch"></i> Lihat Watch
                        </a>
                    </div>
                </div>

                <!-- Slide 4: iPad -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80" alt="iPad Pro">
                    <div class="slide-content">
                        <h3>iPad Pro</h3>
                        <p>Chip M2 yang super cepat. Layar Liquid Retina XDR yang menakjubkan. Sangat Pro.</p>
                        <a href="#" class="slide-btn">
                            <i class="bi bi-tablet"></i> Beli iPad
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation buttons -->
        <div class="slider-nav">
            <button class="nav-btn prev-btn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="nav-btn next-btn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

        <!-- Simple status dots -->
        <div class="slider-status" id="sliderStatus">
            <!-- Dots will be generated by JavaScript -->
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.getElementById('ibox-slider');
                const slides = document.querySelectorAll('.slide');
                const prevBtn = document.querySelector('.prev-btn');
                const nextBtn = document.querySelector('.next-btn');
                const sliderStatus = document.getElementById('sliderStatus');

                let currentSlide = 0;
                let slideInterval;
                let isForwardDirection = true;
                let autoPlaySpeed = 3000;

                // Create status dots
                function createStatusDots() {
                    sliderStatus.innerHTML = '';
                    for (let i = 0; i < slides.length; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('status-dot');
                        if (i === 0) dot.classList.add('active');
                        dot.addEventListener('click', () => {
                            clearInterval(slideInterval);
                            goToSlide(i);
                            startAutoPlay();
                        });
                        sliderStatus.appendChild(dot);
                    }
                }

                // Go to specific slide
                function goToSlide(slideIndex) {
                    currentSlide = slideIndex;
                    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                    updateStatusDots();
                }

                // Next slide
                function nextSlide() {
                    let newSlide = currentSlide + 1;
                    if (newSlide >= slides.length) newSlide = 0;
                    goToSlide(newSlide);
                }

                // Previous slide
                function prevSlide() {
                    let newSlide = currentSlide - 1;
                    if (newSlide < 0) newSlide = slides.length - 1;
                    goToSlide(newSlide);
                }

                // Auto slide function
                function autoSlide() {
                    let newSlide;

                    if (isForwardDirection) {
                        newSlide = currentSlide + 1;
                        if (newSlide >= slides.length) {
                            isForwardDirection = false;
                            newSlide = currentSlide - 1;
                        }
                    } else {
                        newSlide = currentSlide - 1;
                        if (newSlide < 0) {
                            isForwardDirection = true;
                            newSlide = currentSlide + 1;
                        }
                    }

                    goToSlide(newSlide);
                }

                // Update status dots
                function updateStatusDots() {
                    const dots = document.querySelectorAll('.status-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlide);
                    });
                }

                // Start autoplay
                function startAutoPlay() {
                    clearInterval(slideInterval);
                    slideInterval = setInterval(autoSlide, autoPlaySpeed);
                }

                // Stop autoplay
                function stopAutoPlay() {
                    clearInterval(slideInterval);
                }

                // Initialize slider
                function initSlider() {
                    createStatusDots();
                    startAutoPlay();

                    // Event listeners for navigation buttons
                    prevBtn.addEventListener('click', () => {
                        prevSlide();
                        resetAutoPlay();
                    });

                    nextBtn.addEventListener('click', () => {
                        nextSlide();
                        resetAutoPlay();
                    });

                    // Reset autoplay after manual navigation
                    function resetAutoPlay() {
                        stopAutoPlay();
                        startAutoPlay();
                    }

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') {
                            prevSlide();
                            resetAutoPlay();
                        } else if (e.key === 'ArrowRight') {
                            nextSlide();
                            resetAutoPlay();
                        }
                    });

                    // Pause on hover
                    slider.addEventListener('mouseenter', stopAutoPlay);
                    slider.addEventListener('mouseleave', startAutoPlay);

                    // Touch/swipe support for mobile
                    let touchStartX = 0;
                    let touchEndX = 0;
                    let touchStartY = 0;
                    let touchEndY = 0;

                    slider.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                        touchStartY = e.changedTouches[0].screenY;
                        stopAutoPlay();
                    });

                    slider.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        touchEndY = e.changedTouches[0].screenY;

                        const diffX = touchStartX - touchEndX;
                        const diffY = touchStartY - touchEndY;

                        if (Math.abs(diffX) > Math.abs(diffY)) {
                            handleSwipe(diffX);
                        }

                        setTimeout(startAutoPlay, 1000);
                    });

                    function handleSwipe(diff) {
                        const swipeThreshold = 50;

                        if (Math.abs(diff) > swipeThreshold) {
                            if (diff > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                    }

                    // Handle window resize
                    let resizeTimeout;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimeout);
                        resizeTimeout = setTimeout(() => {
                            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        }, 250);
                    });
                }

                // Initialize the slider
                initSlider();
            });
        </script>
    </div>

    <!-- kategori produk tab -->
    <div class="category-products-container">
        <style>
            body {
                background-color: #f7f7f7;
                color: #333;
            }

            .category-products-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 30px 0 20px;
            }

            .category-products-wrapper {
                padding: 40px 0;
            }

            h3 {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 30px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            h3::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            /* CATEGORY PRODUCTS SLIDER STYLES */
            .category-products-wrapper {
                position: relative;
                margin-bottom: 30px;
                overflow: hidden;
                padding: 10px 0;
            }

            .category-products {
                display: flex;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 15px;
                transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                width: max-content;
                cursor: grab;
                user-select: none;
                padding: 5px 20px;
            }

            .category-products.grabbing {
                cursor: grabbing;
                transition: none;
            }

            .product-items {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                width: 110px;
                padding: 15px;
                border-radius: 12px;
                transition: all 0.3s ease;
                cursor: pointer;
                flex-shrink: 0;
                background-color: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .product-items:hover {
                background-color: #f5f5f7;
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .product-items.active {
                background-color: #f0f7ff;
                border: 2px solid #007aff;
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.15);
            }

            .product-items img {
                height: 50px;
                margin-bottom: 15px;
                object-fit: contain;
            }

            .product-items-title {
                width: 100%;
            }

            .product-items-name {
                font-weight: 700;
                font-size: 16px;
                margin-bottom: 5px;
                color: #1d1d1f;
            }

            .product-items-price {
                font-weight: 600;
                font-size: 13px;
                color: #86868b;
            }

            /* SWIPE INDICATOR FOR MOBILE */
            .swipe-hint {
                display: block;
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: #86868b;
                animation: pulse 2s infinite;
            }

            .swipe-hint i {
                margin-right: 5px;
                font-size: 14px;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 0.5;
                }

                50% {
                    opacity: 1;
                }
            }

            /* Responsif untuk layar lebih kecil */
            @media (max-width: 768px) {
                .category-products {
                    gap: 10px;
                    padding: 5px 15px;
                }

                .product-items {
                    width: 100px;
                    padding: 10px;
                }

                .product-items img {
                    height: 40px;
                    margin-bottom: 10px;
                }

                .product-items-name {
                    font-size: 14px;
                }

                .product-items-price {
                    font-size: 11px;
                }
            }

            @media (max-width: 480px) {
                .category-products {
                    gap: 8px;
                    padding: 5px 10px;
                }

                .product-items {
                    width: 90px;
                    padding: 8px;
                }

                .product-items img {
                    height: 35px;
                }
            }

            @media (min-width: 1040px) {
                .category-products-wrapper {
                    overflow: visible;
                    padding: 0;
                }

                .category-products {
                    flex-wrap: wrap;
                    justify-content: center;
                    width: 100%;
                    gap: 20px;
                    padding: 0;
                    cursor: default;
                }

                .product-items {
                    width: 120px;
                }

                .swipe-hint {
                    display: none;
                }
            }
        </style>
        <div class="category-products-wrapper">
            <div class="category-products" id="categoryProducts">
                <div class="product-items" data-category="mac" data-category-name="Mac">
                    <img src="https://cdnpro.eraspace.com/media/wysiwyg/banner/IMG_3966.png">
                    <div class="product-items-title">
                        <p class="product-items-name">Mac</p>
                        <p class="product-items-price">Mulai dari Rp11 juta</p>
                    </div>
                </div>
                <div class="product-items" data-category="iphone" data-category-name="iPhone">
                    <img src="https://cdnpro.eraspace.com/media/wysiwyg/store_card_13_iphone_nav_202509_f4cfa37f_36ba_4094_9b94_e110f8a4e707.png">
                    <div class="product-items-title">
                        <p class="product-items-name">iPhone</p>
                        <p class="product-items-price">Mulai dari Rp8 juta</p>
                    </div>
                </div>
                <div class="product-items" data-category="ipad" data-category-name="iPad">
                    <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/iPad_-_Cek_yang_terbaru.webp?rand=1720254832">
                    <div class="product-items-title">
                        <p class="product-items-name">iPad</p>
                        <p class="product-items-price">Mulai dari Rp4 juta</p>
                    </div>
                </div>
                <div class="product-items" data-category="watch" data-category-name="Apple Watch">
                    <img src="https://cdnpro.eraspace.com/media/wysiwyg/image-watch.png">
                    <div class="product-items-title">
                        <p class="product-items-name">WATCH</p>
                        <p class="product-items-price">Mulai dari Rp3juta</p>
                    </div>
                </div>
                <div class="product-items" data-category="music" data-category-name="Music">
                    <img src="https://cdnpro.eraspace.com/media/wysiwyg/url_upload_691a6fad14870.tmp-691a6fad3c537.png">
                    <div class="product-items-title">
                        <p class="product-items-name">Music</p>
                        <p class="product-items-price">Mulai dari Rp2 juta</p>
                    </div>
                </div>
                <div class="product-items" data-category="aksesoris" data-category-name="Aksesori">
                    <img src="https://bim4s4kti.eraspace.com/media/wysiwyg/store-card-13-accessories-nav-202509.png">
                    <div class="product-items-title">
                        <p class="product-items-name">Aksesori</p>
                        <p class="product-items-price">Mulai dari Rp200 ribu</p>
                    </div>
                </div>
                <div class="product-items" data-category="airtag" data-category-name="AirTag">
                    <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/berbagai-produk/airtag.png">
                    <div class="product-items-title">
                        <p class="product-items-name">AirTag</p>
                        <p class="product-items-price">Mulai dari Rp400 ribu</p>
                    </div>
                </div>
            </div>

            <!-- SWIPE HINT UNTUK MOBILE -->
            <div class="swipe-hint" id="swipeHint">
                <i class="bi bi-arrow-left-right"></i> Geser untuk melihat lebih banyak
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryProducts = document.getElementById('categoryProducts');
                const swipeHint = document.getElementById('swipeHint');
                const categoryItems = document.querySelectorAll('.product-items');

                // ===== FUNGSI UNTUK KATEGORI PRODUK SLIDER =====
                let isCategoryDragging = false;
                let categoryStartX = 0;
                let categoryCurrentTranslate = 0;
                let categoryPrevTranslate = 0;

                // Inisialisasi Category Products Slider
                function initCategorySlider() {
                    // Hitung total lebar konten
                    const containerWidth = categoryProducts.parentElement.clientWidth;
                    const items = categoryProducts.children;
                    let totalWidth = 0;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Reset posisi slider jika konten lebih kecil dari container
                    if (totalWidth <= containerWidth) {
                        categoryProducts.style.transform = 'translateX(0)';
                        categoryCurrentTranslate = 0;
                        categoryPrevTranslate = 0;
                    }

                    setupCategoryDragEvents();
                }

                function setupCategoryDragEvents() {
                    categoryProducts.addEventListener('touchstart', categoryTouchStart);
                    categoryProducts.addEventListener('touchmove', categoryTouchMove);
                    categoryProducts.addEventListener('touchend', categoryTouchEnd);

                    categoryProducts.addEventListener('mousedown', categoryMouseDown);
                    categoryProducts.addEventListener('mousemove', categoryMouseMove);
                    categoryProducts.addEventListener('mouseup', categoryMouseUp);
                    categoryProducts.addEventListener('mouseleave', categoryMouseLeave);

                    categoryProducts.style.cursor = 'grab';
                }

                // Event handlers untuk Category Products Slider
                function categoryTouchStart(e) {
                    isCategoryDragging = true;
                    categoryStartX = e.touches[0].clientX;
                    categoryPrevTranslate = categoryCurrentTranslate;
                    categoryProducts.classList.add('grabbing');
                }

                function categoryTouchMove(e) {
                    if (!isCategoryDragging) return;

                    const currentX = e.touches[0].clientX;
                    const diffX = currentX - categoryStartX;
                    const newTranslate = categoryPrevTranslate + diffX;

                    // Batasi pergerakan slider
                    setCategorySliderPosition(newTranslate);
                }

                function categoryTouchEnd() {
                    isCategoryDragging = false;
                    categoryProducts.classList.remove('grabbing');

                    // Snap ke posisi terdekat
                    snapCategorySlider();
                }

                function categoryMouseDown(e) {
                    isCategoryDragging = true;
                    categoryStartX = e.clientX;
                    categoryPrevTranslate = categoryCurrentTranslate;
                    categoryProducts.classList.add('grabbing');
                    e.preventDefault();
                }

                function categoryMouseMove(e) {
                    if (!isCategoryDragging) return;

                    const currentX = e.clientX;
                    const diffX = currentX - categoryStartX;
                    const newTranslate = categoryPrevTranslate + diffX;

                    setCategorySliderPosition(newTranslate);
                }

                function categoryMouseUp() {
                    isCategoryDragging = false;
                    categoryProducts.classList.remove('grabbing');
                    snapCategorySlider();
                }

                function categoryMouseLeave() {
                    if (isCategoryDragging) {
                        isCategoryDragging = false;
                        categoryProducts.classList.remove('grabbing');
                        snapCategorySlider();
                    }
                }

                // Fungsi untuk mengatur posisi slider dengan batasan
                function setCategorySliderPosition(position) {
                    const container = categoryProducts.parentElement;
                    const containerWidth = container.clientWidth;
                    const items = categoryProducts.children;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;
                    let totalWidth = 0;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Batasi pergerakan slider
                    // Posisi maksimal (ke kiri): 0 (tidak bergerak dari posisi awal)
                    // Posisi minimal (ke kanan): containerWidth - totalWidth
                    // Jika totalWidth <= containerWidth, maka posisi harus 0
                    if (totalWidth <= containerWidth) {
                        position = 0;
                    } else {
                        const minTranslate = Math.min(0, containerWidth - totalWidth - 20); // 20px padding untuk estetika
                        const maxTranslate = 0;
                        position = Math.max(minTranslate, Math.min(maxTranslate, position));
                    }

                    categoryProducts.style.transform = `translateX(${position}px)`;
                    categoryCurrentTranslate = position;
                }

                // Fungsi untuk snap slider ke posisi yang tepat
                function snapCategorySlider() {
                    const container = categoryProducts.parentElement;
                    const containerWidth = container.clientWidth;
                    const items = categoryProducts.children;

                    // Hitung total lebar semua item termasuk gap
                    const style = window.getComputedStyle(categoryProducts);
                    const gap = parseFloat(style.gap) || 15;
                    let totalWidth = 0;

                    for (let i = 0; i < items.length; i++) {
                        totalWidth += items[i].offsetWidth;
                        if (i < items.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Jika totalWidth <= containerWidth, snap ke 0
                    if (totalWidth <= containerWidth) {
                        animateCategorySlider(0);
                        return;
                    }

                    // Snap ke posisi terdekat yang valid
                    const minTranslate = Math.min(0, containerWidth - totalWidth - 20);
                    const maxTranslate = 0;

                    let snapPosition = categoryCurrentTranslate;

                    // Jika posisi saat ini dekat dengan batas kiri (0), snap ke 0
                    if (snapPosition > -50) {
                        snapPosition = 0;
                    }
                    // Jika posisi saat ini dekat dengan batas kanan, snap ke batas kanan
                    else if (snapPosition < minTranslate + 50) {
                        snapPosition = minTranslate;
                    }

                    animateCategorySlider(snapPosition);
                }

                // Fungsi animasi untuk slider kategori
                function animateCategorySlider(targetPosition) {
                    const startPosition = categoryCurrentTranslate;
                    const duration = 300;
                    const startTime = performance.now();

                    function animate(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        // Easing function (easeOutCubic)
                        const ease = 1 - Math.pow(1 - progress, 3);

                        const currentPosition = startPosition + (targetPosition - startPosition) * ease;

                        categoryProducts.style.transform = `translateX(${currentPosition}px)`;
                        categoryCurrentTranslate = currentPosition;

                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    }

                    requestAnimationFrame(animate);
                }

                // ===== EVENT LISTENERS =====
                window.addEventListener('resize', debounce(initCategorySlider, 250));

                categoryItems.forEach(item => {
                    item.addEventListener('click', function() {
                        const category = this.dataset.category;
                        const categoryName = this.dataset.categoryName;

                        // Redirect ke halaman products dengan parameter type
                        window.location.href = `products/products.php?type=${category}`;
                    });
                });

                categoryProducts.addEventListener('touchstart', function() {
                    swipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        swipeHint.style.display = 'none';
                    }, 300);
                });

                categoryProducts.addEventListener('mousedown', function() {
                    swipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        swipeHint.style.display = 'none';
                    }, 300);
                });

                // ===== UTILITY FUNCTIONS =====
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // ===== INISIALISASI =====
                initCategorySlider();
            });
        </script>
    </div>

    <!-- all-products -->
    <div class="all-products-container">
        <?php
        // Fungsi untuk mengambil produk populer dari tabel home_produk_populer
        function getPopularProducts($db, $limit = 12)
        {
            $products = [];

            // 1. Ambil data dasar dari home_produk_populer
            $hpp_query = "SELECT produk_id, tipe_produk, label FROM home_produk_populer 
                          ORDER BY urutan ASC, created_at DESC LIMIT $limit";
            $hpp_result = mysqli_query($db, $hpp_query);

            if (!$hpp_result) return [];

            $items = [];
            $ids_by_type = [];
            while ($row = mysqli_fetch_assoc($hpp_result)) {
                $items[] = $row;
                $ids_by_type[$row['tipe_produk']][] = $row['produk_id'];
            }

            // 2. Ambil detail untuk setiap tipe produk yang ada
            $details = [];
            foreach ($ids_by_type as $type => $ids) {
                if (empty($ids)) continue;
                $id_list = implode(',', array_map('intval', $ids));

                // Gunakan tabel yang sesuai berdasarkan tipe
                $table_main = "admin_produk_" . $type;
                $table_gambar = "admin_produk_" . $type . "_gambar";
                $table_kombi = "admin_produk_" . $type . "_kombinasi";

                $detail_query = "SELECT p.id, p.nama_produk, pg.foto_thumbnail, 
                                 MIN(pk.harga) as harga_asli,
                                 MIN(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN pk.harga_diskon ELSE pk.harga END) as harga_terendah,
                                 MAX(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount
                                 FROM $table_main p
                                 LEFT JOIN $table_gambar pg ON p.id = pg.produk_id
                                 LEFT JOIN $table_kombi pk ON p.id = pk.produk_id
                                 WHERE p.id IN ($id_list)
                                 GROUP BY p.id";

                $detail_result = mysqli_query($db, $detail_query);
                if ($detail_result) {
                    while ($d = mysqli_fetch_assoc($detail_result)) {
                        $details[$type][$d['id']] = $d;
                    }
                }
            }

            // 3. Susun kembali sesuai urutan original
            foreach ($items as $item) {
                $t = $item['tipe_produk'];
                $pid = $item['produk_id'];

                if (isset($details[$t][$pid])) {
                    $d = $details[$t][$pid];
                    $products[] = [
                        'id' => $pid,
                        'name' => $d['nama_produk'],
                        'category' => $t,
                        'price' => 'Rp ' . number_format($d['harga_terendah'] ?? 0, 0, ',', '.'),
                        'harga_asli' => (float)($d['harga_asli'] ?? 0),
                        'harga_terendah' => (float)($d['harga_terendah'] ?? 0),
                        'has_discount' => (int)($d['has_discount'] ?? 0),
                        'image' => $d['foto_thumbnail'] ? '../admin/uploads/' . $d['foto_thumbnail'] : 'https://via.placeholder.com/200x180?text=No+Image',
                        'rating' => 4.5,
                        'badge' => ['text' => $item['label'] ?? 'Populer', 'type' => 'hot']
                    ];
                }
            }

            return $products;
        }

        // Fungsi untuk mengambil produk terbaru dari tabel home_produk_terbaru (STRUKTUR BARU)
        function getLatestProducts($db, $limit = 12)
        {
            $products = [];

            // 1. Ambil data dasar dari home_produk_terbaru
            $hpt_query = "SELECT produk_id, tipe_produk FROM home_produk_terbaru 
                          ORDER BY urutan ASC, created_at DESC LIMIT $limit";
            $hpt_result = mysqli_query($db, $hpt_query);

            if (!$hpt_result) return [];

            $items = [];
            $ids_by_type = [];
            while ($row = mysqli_fetch_assoc($hpt_result)) {
                $items[] = $row;
                $ids_by_type[$row['tipe_produk']][] = $row['produk_id'];
            }

            // 2. Ambil detail untuk setiap tipe produk
            $details = [];
            foreach ($ids_by_type as $type => $ids) {
                if (empty($ids)) continue;
                $id_list = implode(',', array_map('intval', $ids));

                $table_main = "admin_produk_" . $type;
                $table_gambar = "admin_produk_" . $type . "_gambar";
                $table_kombi = "admin_produk_" . $type . "_kombinasi";

                $detail_query = "SELECT p.id, p.nama_produk, pg.foto_thumbnail, 
                                 MIN(pk.harga) as harga_asli,
                                 MIN(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN pk.harga_diskon ELSE pk.harga END) as harga_terendah,
                                 MAX(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount
                                 FROM $table_main p
                                 LEFT JOIN $table_gambar pg ON p.id = pg.produk_id
                                 LEFT JOIN $table_kombi pk ON p.id = pk.produk_id
                                 WHERE p.id IN ($id_list)
                                 GROUP BY p.id";

                $detail_result = mysqli_query($db, $detail_query);
                if ($detail_result) {
                    while ($d = mysqli_fetch_assoc($detail_result)) {
                        $details[$type][$d['id']] = $d;
                    }
                }
            }

            // 3. Susun kembali
            foreach ($items as $item) {
                $t = $item['tipe_produk'];
                $pid = $item['produk_id'];

                if (isset($details[$t][$pid])) {
                    $d = $details[$t][$pid];
                    $products[] = [
                        'id' => $pid,
                        'name' => $d['nama_produk'],
                        'category' => $t,
                        'price' => 'Rp ' . number_format($d['harga_terendah'] ?? 0, 0, ',', '.'),
                        'harga_asli' => (float)($d['harga_asli'] ?? 0),
                        'harga_terendah' => (float)($d['harga_terendah'] ?? 0),
                        'has_discount' => (int)($d['has_discount'] ?? 0),
                        'image' => $d['foto_thumbnail'] ? '../admin/uploads/' . $d['foto_thumbnail'] : 'https://via.placeholder.com/200x180?text=No+Image',
                        'rating' => 4.5,
                        'badge' => ['text' => 'Terbaru', 'type' => 'new']
                    ];
                }
            }

            return $products;
        }

        // Fungsi untuk mengambil semua produk dari semua kategori (untuk filter kategori)
        function getAllProductsForFilter($db)
        {
            $allProducts = [];

            // Ambil produk dari semua kategori untuk filter (dengan LIMIT untuk performa)
            $categories = [
                'mac' => "SELECT m.id, m.nama_produk as name, 'mac' as category, 
                         COALESCE(MIN(mk.harga), 0) as price, mg.foto_thumbnail as image
                  FROM admin_produk_mac m
                  LEFT JOIN admin_produk_mac_kombinasi mk ON m.id = mk.produk_id
                  LEFT JOIN admin_produk_mac_gambar mg ON m.id = mg.produk_id
                  GROUP BY m.id, mg.foto_thumbnail LIMIT 16",

                'iphone' => "SELECT p.id, p.nama_produk as name, 'iphone' as category, 
                             COALESCE(MIN(pk.harga), 0) as price, pg.foto_thumbnail as image
                      FROM admin_produk_iphone p
                      LEFT JOIN admin_produk_iphone_kombinasi pk ON p.id = pk.produk_id
                      LEFT JOIN admin_produk_iphone_gambar pg ON p.id = pg.produk_id
                      GROUP BY p.id, pg.foto_thumbnail LIMIT 16",

                'ipad' => "SELECT p.id, p.nama_produk as name, 'ipad' as category, 
                           COALESCE(MIN(pk.harga), 0) as price, pg.foto_thumbnail as image
                    FROM admin_produk_ipad p
                    LEFT JOIN admin_produk_ipad_kombinasi pk ON p.id = pk.produk_id
                    LEFT JOIN admin_produk_ipad_gambar pg ON p.id = pg.produk_id
                    GROUP BY p.id, pg.foto_thumbnail LIMIT 16",

                'watch' => "SELECT w.id, w.nama_produk as name, 'watch' as category, 
                            COALESCE(MIN(wk.harga), 0) as price, wg.foto_thumbnail as image
                     FROM admin_produk_watch w
                     LEFT JOIN admin_produk_watch_kombinasi wk ON w.id = wk.produk_id
                     LEFT JOIN admin_produk_watch_gambar wg ON w.id = wg.produk_id
                     GROUP BY w.id, wg.foto_thumbnail LIMIT 16",

                'aksesori' => "SELECT a.id, a.nama_produk as name, 'aksesori' as category, 
                               COALESCE(MIN(ak.harga), 0) as price, ag.foto_thumbnail as image
                        FROM admin_produk_aksesoris a
                        LEFT JOIN admin_produk_aksesoris_kombinasi ak ON a.id = ak.produk_id
                        LEFT JOIN admin_produk_aksesoris_gambar ag ON a.id = ag.produk_id
                        GROUP BY a.id, ag.foto_thumbnail LIMIT 16",

                'music' => "SELECT m.id, m.nama_produk as name, 'music' as category, 
                            COALESCE(MIN(mk.harga), 0) as price, mg.foto_thumbnail as image
                     FROM admin_produk_music m
                     LEFT JOIN admin_produk_music_kombinasi mk ON m.id = mk.produk_id
                     LEFT JOIN admin_produk_music_gambar mg ON m.id = mg.produk_id
                     GROUP BY m.id, mg.foto_thumbnail LIMIT 16",

                'airtag' => "SELECT a.id, a.nama_produk as name, 'airtag' as category, 
                             COALESCE(MIN(ak.harga), 0) as price, ag.foto_thumbnail as image
                      FROM admin_produk_airtag a
                      LEFT JOIN admin_produk_airtag_kombinasi ak ON a.id = ak.produk_id
                      LEFT JOIN admin_produk_airtag_gambar ag ON a.id = ag.produk_id
                      GROUP BY a.id, ag.foto_thumbnail LIMIT 16"
            ];

            foreach ($categories as $category => $query) {
                $result = mysqli_query($db, $query);
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $row['price'] = 'Rp ' . number_format($row['price'], 0, ',', '.');
                        $row['image'] = $row['image'] ? '../admin/uploads/' . $row['image'] : 'https://via.placeholder.com/200x180?text=No+Image';
                        $row['rating'] = 4.5;
                        $row['badge'] = ['text' => 'Terlaris', 'type' => 'hot'];
                        $allProducts[] = $row;
                    }
                }
            }

            return $allProducts;
        }

        // Ambil data dari database
        $popularProducts = getPopularProducts($db, 16);
        $latestProducts = getLatestProducts($db, 12);
        $allProductsForFilter = getAllProductsForFilter($db);

        // Konversi ke JSON untuk JavaScript
        $popularProductsJSON = json_encode($popularProducts);
        $latestProductsJSON = json_encode($latestProducts);
        $allProductsJSON = json_encode($allProductsForFilter);
        ?>
        <style>
            body {
                background-color: #f7f7f7;
                color: #333;
            }

            .all-products-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .products-section-title {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 30px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            .products-section-title::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            /* ========================= */
            /* CATEGORY TABS SLIDER STYLES */
            /* ========================= */
            .category-tabs-wrapper {
                position: relative;
                margin-bottom: 40px;
                overflow: hidden;
                padding: 10px 0;
            }

            .category-tabs-container {
                display: flex;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 12px;
                transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                cursor: grab;
                user-select: none;
                width: max-content;
                padding: 5px 20px;
            }

            .category-tabs-container.grabbing {
                cursor: grabbing;
                transition: none;
            }

            .category-tab {
                padding: 12px 25px;
                background-color: white;
                border: 2px solid #e0e0e0;
                border-radius: 30px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #666;
                flex-shrink: 0;
                white-space: nowrap;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            }

            .category-tab.active {
                background-color: #007aff;
                color: white;
                border-color: #007aff;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.25);
            }

            .category-tab:hover:not(.active) {
                border-color: #007aff;
                color: #007aff;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 122, 255, 0.15);
            }

            /* Tabs swipe hint */
            .tabs-swipe-hint {
                display: block;
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: #86868b;
                animation: pulse 2s infinite;
            }

            .tabs-swipe-hint i {
                margin-right: 5px;
                font-size: 14px;
            }

            @keyframes pulse {
                0% {
                    opacity: 0.5;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    opacity: 0.5;
                }
            }

            /* Responsif untuk tabs slider */
            @media (max-width: 992px) {
                .category-tabs-wrapper {
                    overflow: hidden;
                }

                .category-tabs-container {
                    gap: 12px;
                    padding: 5px 20px;
                }

                .category-tab {
                    padding: 10px 20px;
                    font-size: 14px;
                }
            }

            @media (max-width: 768px) {
                .category-tabs-container {
                    gap: 10px;
                    padding: 5px 15px;
                }

                .category-tab {
                    padding: 8px 18px;
                    font-size: 13px;
                }
            }

            @media (max-width: 480px) {
                .category-tabs-container {
                    gap: 8px;
                    padding: 5px 10px;
                }

                .category-tab {
                    padding: 8px 15px;
                    font-size: 12px;
                }
            }

            @media (min-width: 993px) {
                .category-tabs-wrapper {
                    overflow: visible;
                    padding: 0;
                }

                .category-tabs-container {
                    flex-wrap: wrap;
                    justify-content: center;
                    width: 100%;
                    gap: 15px;
                    padding: 0;
                    cursor: default;
                }

                .category-tab {
                    padding: 12px 25px;
                }

                .tabs-swipe-hint {
                    display: none;
                }
            }

            /* SLIDER CONTAINER UNTUK SEMUA PRODUK */
            .all-products-image-slider-container {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                padding: 30px 20px 60px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                margin-bottom: 40px;
            }

            .all-products-image-slider-container.single-slide {
                padding-bottom: 30px;
            }

            .all-products-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 0;
            }

            .all-products-slide {
                min-width: 100%;
                padding: 10px;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                flex-shrink: 0;
            }

            .all-products-slide-inner {
                display: flex;
                gap: 20px;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            /* PRODUCT CARD STYLES UNTUK SEMUA */
            .product-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                height: 100%;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                width: calc(25% - 20px);
                min-height: 380px;
            }

            .product-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 30px rgba(0, 122, 255, 0.1);
                border-color: #007aff;
            }

            .product-image {
                width: 100%;
                max-width: 200px;
                height: 180px;
                object-fit: contain;
                margin-bottom: 15px;
                border-radius: 8px;
                background-color: #f8f9fa;
                padding: 15px;
            }

            .product-badge {
                background-color: #ff3b30;
                color: white;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                margin-bottom: 10px;
                display: inline-block;
            }

            .product-badge.new {
                background-color: #007aff;
            }

            .product-badge.hot {
                background-color: #ff3b30;
            }

            .product-name {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .product-rating {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
                margin-bottom: 10px;
                color: #ffc107;
            }

            .product-rating span {
                color: #666;
                font-size: 13px;
                margin-left: 5px;
            }

            .product-price {
                font-size: 16px;
                color: #007aff;
                font-weight: 500;
                margin-bottom: 15px;
            }

            .product-price-original {
                font-size: 13px;
                color: #86868b;
                font-weight: 500;
                text-decoration: line-through;
                margin-bottom: 4px;
            }

            .product-price-wrapper {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 15px;
            }

            .product-price-discount {
                color: #ff3b30 !important;
                font-size: 17px !important;
                font-weight: 700 !important;
                margin-bottom: 0 !important;
            }

            .discount-badge {
                background: linear-gradient(135deg, #ff3b30 0%, #ff6b60 100%);
                color: white;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 8px rgba(255, 59, 48, 0.3);
                white-space: nowrap;
            }

            .product-btn {
                background-color: #007aff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 25px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-size: 14px;
                margin-top: auto;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .product-btn:hover {
                background-color: var(--apple-blue-hover);
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
            }

            /* Efek ripple pada button */
            .product-btn::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .product-btn:hover::after {
                width: 200px;
                height: 200px;
            }

            /* Efek ikon pada button saat hover */
            .product-btn i {
                transition: transform 0.3s ease, opacity 0.3s ease;
                opacity: 0;
                transform: translateX(-8px);
                font-size: 12px;
            }

            .product-btn:hover i {
                opacity: 1;
                transform: translateX(0);
            }

            /* Navigation Buttons untuk semua slider */
            .all-products-slider-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: calc(100% - 40px);
                left: 20px;
                display: flex;
                justify-content: space-between;
                pointer-events: none;
                z-index: 20;
            }

            .all-products-slider-nav.hidden {
                display: none;
            }

            .all-products-nav-btn {
                background-color: white;
                border: none;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 20px;
                color: #333;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                pointer-events: auto;
                opacity: 1;
                visibility: visible;
            }

            .all-products-nav-btn.hidden {
                display: none;
            }

            .all-products-nav-btn:hover:not(:disabled) {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .all-products-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                background-color: #f0f0f0;
            }

            .all-products-nav-btn:disabled:hover {
                transform: none;
                background-color: #f0f0f0;
                color: #333;
            }

            /* Dots Navigation untuk semua slider */
            .all-products-slider-dots {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 15px;
                position: absolute;
                bottom: 25px;
                left: 0;
                right: 0;
                z-index: 10;
            }

            .all-products-slider-dots.hidden {
                display: none;
            }

            .all-products-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background-color: rgba(0, 122, 255, 0.2);
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .all-products-dot.active {
                background-color: #007aff !important;
                transform: scale(1.2);
                border-color: white !important;
            }

            .all-products-dot:hover {
                background-color: rgba(0, 122, 255, 0.4);
            }

            /* Responsive untuk semua slider */
            @media (min-width: 1200px) {
                .all-products-slide-inner .product-card {
                    width: calc(25% - 20px);
                    max-width: calc(25% - 20px);
                    min-width: 0;
                }
            }

            @media (min-width: 900px) and (max-width: 1199px) {
                .all-products-slide-inner .product-card {
                    width: calc(33.333% - 20px);
                    max-width: calc(33.333% - 20px);
                    min-width: 0;
                }

                .product-image {
                    max-width: 180px;
                    height: 160px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }
            }

            @media (min-width: 576px) and (max-width: 899px) {
                .all-products-slide-inner .product-card {
                    width: calc(50% - 20px);
                    max-width: calc(50% - 20px);
                    min-width: 0;
                }

                .product-image {
                    max-width: 160px;
                    height: 140px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }

                .all-products-image-slider-container {
                    padding: 25px 15px 50px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 30px);
                    left: 15px;
                }
            }

            @media (max-width: 575px) {
                .all-products-slide-inner .product-card {
                    width: calc(100% - 20px);
                    max-width: 300px;
                    min-width: 0;
                }

                .product-image {
                    max-width: 140px;
                    height: 120px;
                }

                .product-name {
                    font-size: 15px;
                    height: 36px;
                }

                .product-price {
                    font-size: 15px;
                }

                .all-products-image-slider-container {
                    padding: 20px 10px 50px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 20px);
                    left: 10px;
                }

                .all-products-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }
            }

            @media (max-width: 300px) {
                .all-products-slide-inner .product-card {
                    width: 100%;
                    max-width: 100%;
                    padding: 15px;
                }

                .product-image {
                    max-width: 120px;
                    height: 100px;
                    padding: 10px;
                }

                .product-name {
                    font-size: 14px;
                    height: 32px;
                }

                .product-price {
                    font-size: 14px;
                }

                .product-btn {
                    padding: 8px 16px;
                    font-size: 13px;
                }

                .all-products-image-slider-container {
                    padding: 15px 5px 40px;
                }

                .all-products-slider-nav {
                    width: calc(100% - 10px);
                    left: 5px;
                }

                .all-products-nav-btn {
                    width: 35px;
                    height: 35px;
                    font-size: 16px;
                }

                .all-products-slider-dots {
                    bottom: 20px;
                }

                .all-products-dot {
                    width: 10px;
                    height: 10px;
                }
            }

            /* Container untuk produk terbaru */
            #latest-products-container {
                margin-top: 40px;
            }

            @media (max-width: 768px) {
                .products-section-title {
                    font-size: 22px;
                }
            }

            @media (max-width: 480px) {
                .products-section-title {
                    font-size: 20px;
                }
            }

            /* Loading indicator */
            .loading {
                text-align: center;
                padding: 40px;
                font-size: 18px;
                color: #666;
            }

            .loading i {
                margin-right: 10px;
                color: #007aff;
            }

            /* Empty state */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #666;
            }

            .empty-state i {
                font-size: 50px;
                margin-bottom: 15px;
                color: #ddd;
            }

            .empty-state .empty-state-title {
                font-size: 20px;
                margin-bottom: 10px;
                color: #333;
            }

            .empty-state p {
                font-size: 16px;
                margin-bottom: 20px;
            }
        </style>
        <div class="all-products-wrapper">

            <h3 class="products-section-title">Produk Apple Terpopuler</h3>

            <!-- Category Tabs Slider untuk Produk Populer -->
            <div class="category-tabs-wrapper">
                <div class="category-tabs-container" id="categoryTabsSlider">
                    <button class="category-tab active" data-category="all">Semua Produk</button>
                    <button class="category-tab" data-category="mac">Mac</button>
                    <button class="category-tab" data-category="iphone">iPhone</button>
                    <button class="category-tab" data-category="ipad">iPad</button>
                    <button class="category-tab" data-category="watch">Apple Watch</button>
                    <button class="category-tab" data-category="aksesori">Aksesori</button>
                    <button class="category-tab" data-category="music">Music</button>
                    <button class="category-tab" data-category="airtag">Airtag</button>
                </div>

                <!-- SWIPE HINT UNTUK MOBILE -->
                <div class="tabs-swipe-hint" id="tabsSwipeHint">
                    <i class="bi bi-arrow-left-right"></i> Geser untuk melihat lebih banyak
                </div>
            </div>

            <!-- Container untuk slider produk populer -->
            <div id="popular-products-container">
                <?php if (empty($popularProducts)): ?>
                    <div class="empty-state">
                        <i class="bi bi-fire"></i>
                        <h4 class="empty-state-title">Belum ada produk populer</h4>
                        <p>Produk populer akan ditampilkan di sini</p>
                    </div>
                <?php else: ?>
                    <div class="loading">
                        <i class="bi bi-arrow-clockwise"></i> Memuat produk populer...
                    </div>
                <?php endif; ?>

            </div>

            <h3 class="products-section-title">Produk Terbaru</h3>

            <!-- Container untuk slider produk terbaru -->
            <div id="latest-products-container">
                <?php if (empty($latestProducts)): ?>
                    <div class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        <h4 class="empty-state-title">Belum ada produk terbaru</h4>
                        <p>Produk terbaru akan ditampilkan di sini</p>
                    </div>
                <?php else: ?>
                    <div class="loading">
                        <i class="bi bi-arrow-clockwise"></i> Memuat produk terbaru...
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data produk dari PHP (dikonversi ke JavaScript)
                const popularProductsFromDB = <?php echo $popularProductsJSON; ?>;
                const latestProductsFromDB = <?php echo $latestProductsJSON; ?>;
                const allProductsFromDB = <?php echo $allProductsJSON; ?>;

                const popularProductsContainer = document.getElementById('popular-products-container');
                const latestProductsContainer = document.getElementById('latest-products-container');
                const categoryTabs = document.querySelectorAll('#categoryTabsSlider .category-tab');
                const categoryTabsSlider = document.getElementById('categoryTabsSlider');
                const tabsSwipeHint = document.getElementById('tabsSwipeHint');

                // ===== FUNGSI UNTUK TABS KATEGORI SLIDER =====
                let isTabsDragging = false;
                let tabsStartX = 0;
                let tabsCurrentTranslate = 0;
                let tabsPrevTranslate = 0;

                // Inisialisasi Category Tabs Slider
                function initTabsSlider() {
                    // Hitung total lebar konten
                    const containerWidth = categoryTabsSlider.parentElement.clientWidth;
                    const tabs = categoryTabsSlider.children;
                    let totalWidth = 0;

                    // Hitung total lebar semua tab termasuk gap
                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Reset posisi slider jika konten lebih kecil dari container
                    if (totalWidth <= containerWidth) {
                        categoryTabsSlider.style.transform = 'translateX(0)';
                        tabsCurrentTranslate = 0;
                        tabsPrevTranslate = 0;
                    }

                    setupTabsDragEvents();
                }

                function setupTabsDragEvents() {
                    categoryTabsSlider.addEventListener('touchstart', tabsTouchStart);
                    categoryTabsSlider.addEventListener('touchmove', tabsTouchMove);
                    categoryTabsSlider.addEventListener('touchend', tabsTouchEnd);

                    categoryTabsSlider.addEventListener('mousedown', tabsMouseDown);
                    categoryTabsSlider.addEventListener('mousemove', tabsMouseMove);
                    categoryTabsSlider.addEventListener('mouseup', tabsMouseUp);
                    categoryTabsSlider.addEventListener('mouseleave', tabsMouseLeave);

                    categoryTabsSlider.style.cursor = 'grab';
                }

                // Event handlers untuk Category Tabs Slider
                function tabsTouchStart(e) {
                    isTabsDragging = true;
                    tabsStartX = e.touches[0].clientX;
                    tabsPrevTranslate = tabsCurrentTranslate;
                    categoryTabsSlider.classList.add('grabbing');
                }

                function tabsTouchMove(e) {
                    if (!isTabsDragging) return;

                    const currentX = e.touches[0].clientX;
                    const diffX = currentX - tabsStartX;
                    const newTranslate = tabsPrevTranslate + diffX;

                    setTabsSliderPosition(newTranslate);
                }

                function tabsTouchEnd() {
                    isTabsDragging = false;
                    categoryTabsSlider.classList.remove('grabbing');
                    snapTabsSlider();
                }

                function tabsMouseDown(e) {
                    isTabsDragging = true;
                    tabsStartX = e.clientX;
                    tabsPrevTranslate = tabsCurrentTranslate;
                    categoryTabsSlider.classList.add('grabbing');
                    e.preventDefault();
                }

                function tabsMouseMove(e) {
                    if (!isTabsDragging) return;

                    const currentX = e.clientX;
                    const diffX = currentX - tabsStartX;
                    const newTranslate = tabsPrevTranslate + diffX;

                    setTabsSliderPosition(newTranslate);
                }

                function tabsMouseUp() {
                    isTabsDragging = false;
                    categoryTabsSlider.classList.remove('grabbing');
                    snapTabsSlider();
                }

                function tabsMouseLeave() {
                    if (isTabsDragging) {
                        isTabsDragging = false;
                        categoryTabsSlider.classList.remove('grabbing');
                        snapTabsSlider();
                    }
                }

                // Fungsi untuk mengatur posisi tabs slider dengan batasan
                function setTabsSliderPosition(position) {
                    const container = categoryTabsSlider.parentElement;
                    const containerWidth = container.clientWidth;
                    const tabs = categoryTabsSlider.children;

                    // Hitung total lebar semua tab termasuk gap
                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;
                    let totalWidth = 0;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    // Batasi pergerakan slider
                    if (totalWidth <= containerWidth) {
                        position = 0;
                    } else {
                        const minTranslate = Math.min(0, containerWidth - totalWidth - 20);
                        const maxTranslate = 0;
                        position = Math.max(minTranslate, Math.min(maxTranslate, position));
                    }

                    categoryTabsSlider.style.transform = `translateX(${position}px)`;
                    tabsCurrentTranslate = position;
                }

                // Fungsi untuk snap tabs slider ke posisi yang tepat
                function snapTabsSlider() {
                    const container = categoryTabsSlider.parentElement;
                    const containerWidth = container.clientWidth;
                    const tabs = categoryTabsSlider.children;

                    const style = window.getComputedStyle(categoryTabsSlider);
                    const gap = parseFloat(style.gap) || 12;
                    let totalWidth = 0;

                    for (let i = 0; i < tabs.length; i++) {
                        totalWidth += tabs[i].offsetWidth;
                        if (i < tabs.length - 1) {
                            totalWidth += gap;
                        }
                    }

                    if (totalWidth <= containerWidth) {
                        animateTabsSlider(0);
                        return;
                    }

                    const minTranslate = Math.min(0, containerWidth - totalWidth - 20);
                    const maxTranslate = 0;

                    let snapPosition = tabsCurrentTranslate;

                    if (snapPosition > -50) {
                        snapPosition = 0;
                    } else if (snapPosition < minTranslate + 50) {
                        snapPosition = minTranslate;
                    }

                    animateTabsSlider(snapPosition);
                }

                // Fungsi animasi untuk tabs slider
                function animateTabsSlider(targetPosition) {
                    const startPosition = tabsCurrentTranslate;
                    const duration = 300;
                    const startTime = performance.now();

                    function animate(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);

                        const ease = 1 - Math.pow(1 - progress, 3);
                        const currentPosition = startPosition + (targetPosition - startPosition) * ease;

                        categoryTabsSlider.style.transform = `translateX(${currentPosition}px)`;
                        tabsCurrentTranslate = currentPosition;

                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    }

                    requestAnimationFrame(animate);
                }

                // ===== FUNGSI UNTUK PRODUK SLIDER =====
                function renderProductsSlider(products, containerId, title = '', viewAllLink = null) {

                    // Create render list with optional View All card
                    const renderList = [...products];
                    if (viewAllLink) {
                        renderList.push({
                            isViewAll: true,
                            link: viewAllLink
                        });
                    }

                    if (renderList.length === 0) {
                        document.getElementById(containerId).innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h4>Tidak ada produk untuk ditampilkan</h4>
                            <p>Belum ada produk dalam kategori ini</p>
                        </div>
                    `;
                        return;
                    }

                    // Hitung jumlah produk per slide berdasarkan lebar layar
                    const productsPerSlide = getProductsPerSlide();
                    const slideCount = Math.ceil(renderList.length / productsPerSlide);
                    const isSingleSlide = slideCount <= 1;

                    let html = `
                <div class="all-products-image-slider-container ${isSingleSlide ? 'single-slide' : ''}">
                    <div class="all-products-slider-nav ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-nav">
                        <button class="all-products-nav-btn all-products-prev-btn ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-prev-btn">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="all-products-nav-btn all-products-next-btn ${isSingleSlide ? 'hidden' : ''}" id="${containerId}-next-btn">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    <div class="all-products-slider" id="${containerId}-slider">
                `;

                    // Buat slide sesuai jumlah yang dibutuhkan
                    for (let slideIndex = 0; slideIndex < slideCount; slideIndex++) {
                        html += `<div class="all-products-slide">`;
                        html += `<div class="all-products-slide-inner" id="${containerId}-slide-inner-${slideIndex}">`;

                        // Tambahkan produk ke slide ini
                        const startIndex = slideIndex * productsPerSlide;
                        const endIndex = Math.min(startIndex + productsPerSlide, renderList.length);

                        for (let i = startIndex; i < endIndex; i++) {
                            const item = renderList[i];
                            if (item.isViewAll) {
                                html += createViewAllCard(item.link);
                            } else {
                                html += createProductCard(item);
                            }
                        }

                        html += `</div></div>`;
                    }

                    html += `
                    </div>
                    ${!isSingleSlide ? `<div class="all-products-slider-dots" id="${containerId}-dots"></div>` : ''}
                </div>
                `;

                    const container = document.getElementById(containerId);
                    if (container) {
                        container.innerHTML = html;
                        initAllProductsSlider(productsPerSlide, slideCount, containerId);
                    }
                }

                // Fungsi untuk menentukan jumlah produk per slide berdasarkan lebar layar
                function getProductsPerSlide() {
                    const screenWidth = window.innerWidth;

                    if (screenWidth >= 1200) return 4;
                    if (screenWidth >= 900) return 3;
                    if (screenWidth >= 576) return 2;
                    return 1;
                }

                function createProductCard(product) {
                    const badgeClass = product.badge ? `product-badge ${product.badge.type}` : '';
                    const badgeHTML = product.badge ?
                        `<div class="${badgeClass}">${product.badge.text}</div>` : '';

                    const stars = getProductStarRating(product.rating);

                    // Format harga dengan atau tanpa diskon
                    let priceHTML = '';
                    if (product.has_discount && product.harga_asli) {
                        // Hitung persentase diskon
                        const discountPercent = Math.round(((product.harga_asli - product.harga_terendah) / product.harga_asli) * 100);

                        priceHTML = `
                            <div class="product-price-original">Rp ${new Intl.NumberFormat('id-ID').format(product.harga_asli)}</div>
                            <div class="product-price-wrapper">
                                <div class="product-price product-price-discount">Rp ${new Intl.NumberFormat('id-ID').format(product.harga_terendah)}</div>
                                <span class="discount-badge">${discountPercent}% OFF</span>
                            </div>
                        `;
                    } else {
                        priceHTML = `<div class="product-price">${product.price}</div>`;
                    }

                    return `
                <div class="product-card" data-category="${product.category}" data-product-id="${product.id}" data-product-category="${product.category}" style="cursor: pointer;">
                    <img src="${product.image}" alt="${product.name}" class="product-image" 
                         onerror="this.src='https://via.placeholder.com/200x180?text=No+Image'">
                    ${badgeHTML}
                    <div class="product-name">${product.name}</div>
                    <div class="product-rating">
                        ${stars}
                        <span>(${product.rating})</span>
                    </div>
                    ${priceHTML}
                    <button class="product-btn" data-product-id="${product.id}" data-product-category="${product.category}">
                        <i class="bi bi-bag"></i> Beli Sekarang
                    </button>
                </div>
                `;
                }

                function createViewAllCard(link) {
                    return `
                    <div class="product-card content-products" onclick="location.href='${link}'" style="cursor: pointer; background: #f5f5f7; height: 100%;">
                        <div class="header-card-products" style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px;">
                            <div style="width: 80px; height: 80px; background: #007aff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 10px 20px rgba(0, 122, 255, 0.2);">
                                <i class="bi bi-arrow-right" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            <h3 style="font-size: 1.5rem; font-weight: 700; color: #1d1d1f; margin-bottom: 5px;">Lihat Semua</h3>
                            <p style="color: #86868b; text-align: center; font-size: 0.95rem;">Jelajahi berbagai pilihan produk lainnya</p>
                        </div>
                    </div>
                    `;
                }

                function getProductStarRating(rating) {
                    let stars = '';
                    const fullStars = Math.floor(rating);
                    const hasHalfStar = rating % 1 >= 0.5;

                    for (let i = 0; i < fullStars; i++) {
                        stars += '<i class="bi bi-star-fill"></i>';
                    }

                    if (hasHalfStar) {
                        stars += '<i class="bi bi-star-half"></i>';
                    }

                    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
                    for (let i = 0; i < emptyStars; i++) {
                        stars += '<i class="bi bi-star"></i>';
                    }

                    return stars;
                }

                function initAllProductsSlider(productsPerSlide, slideCount, containerId) {
                    const slider = document.getElementById(`${containerId}-slider`);
                    const prevBtn = document.getElementById(`${containerId}-prev-btn`);
                    const nextBtn = document.getElementById(`${containerId}-next-btn`);
                    const dotsContainer = document.getElementById(`${containerId}-dots`);
                    const sliderContainer = document.querySelector(`#${containerId} .all-products-image-slider-container`);
                    const navContainer = document.getElementById(`${containerId}-nav`);

                    if (slideCount <= 1) {
                        if (sliderContainer) {
                            sliderContainer.classList.add('single-slide');
                        }
                        if (navContainer) {
                            navContainer.classList.add('hidden');
                        }
                        if (dotsContainer) {
                            dotsContainer.classList.add('hidden');
                        }
                        return;
                    } else {
                        if (sliderContainer) {
                            sliderContainer.classList.remove('single-slide');
                        }
                        if (navContainer) {
                            navContainer.classList.remove('hidden');
                        }
                    }

                    let currentSlide = 0;

                    if (slideCount > 1 && dotsContainer) {
                        dotsContainer.classList.remove('hidden');
                        dotsContainer.innerHTML = '';
                        for (let i = 0; i < slideCount; i++) {
                            const dot = document.createElement('div');
                            dot.classList.add('all-products-dot');
                            if (i === 0) dot.classList.add('active');
                            dot.addEventListener('click', () => goToSlide(i));
                            dotsContainer.appendChild(dot);
                        }
                    } else if (dotsContainer) {
                        dotsContainer.classList.add('hidden');
                    }

                    function updateNavButtons() {
                        if (prevBtn) {
                            prevBtn.disabled = currentSlide === 0;
                        }
                        if (nextBtn) {
                            nextBtn.disabled = currentSlide === slideCount - 1;
                        }
                    }

                    function goToSlide(slideIndex) {
                        if (slideIndex < 0 || slideIndex >= slideCount) return;

                        currentSlide = slideIndex;
                        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        updateDots();
                        updateNavButtons();
                    }

                    function updateDots() {
                        if (slideCount <= 1 || !dotsContainer) return;

                        const dots = document.querySelectorAll(`#${containerId}-dots .all-products-dot`);
                        dots.forEach((dot, index) => {
                            dot.classList.toggle('active', index === currentSlide);
                        });
                    }

                    function nextSlide() {
                        if (currentSlide < slideCount - 1) {
                            goToSlide(currentSlide + 1);
                        }
                    }

                    function prevSlide() {
                        if (currentSlide > 0) {
                            goToSlide(currentSlide - 1);
                        }
                    }

                    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
                    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (slideCount <= 1) return;

                        if (e.ctrlKey && e.key === 'ArrowLeft') {
                            prevSlide();
                        } else if (e.ctrlKey && e.key === 'ArrowRight') {
                            nextSlide();
                        }
                    });

                    // Swipe untuk mobile
                    let touchStartX = 0;
                    let touchEndX = 0;

                    if (slider && slideCount > 1) {
                        slider.addEventListener('touchstart', (e) => {
                            touchStartX = e.changedTouches[0].screenX;
                        });

                        slider.addEventListener('touchend', (e) => {
                            touchEndX = e.changedTouches[0].screenX;
                            handleSwipe();
                        });
                    }

                    function handleSwipe() {
                        const swipeThreshold = 50;
                        const diff = touchStartX - touchEndX;

                        if (Math.abs(diff) > swipeThreshold) {
                            if (diff > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                    }

                    updateNavButtons();
                }

                // ===== FILTER PRODUK POPULER =====
                function filterPopularProducts(category) {
                    let filteredProducts;

                    if (category === 'all') {
                        // Untuk filter "Semua Produk", gunakan semua produk populer yang sudah ada
                        filteredProducts = popularProductsFromDB;
                    } else {
                        // Filter berdasarkan kategori produk populer yang sudah ada
                        filteredProducts = popularProductsFromDB.filter(product => product.category === category);
                    }

                    renderProductsSlider(filteredProducts, 'popular-products-container', 'Produk Populer', 'products/produk populer/produk-populer.php');
                }

                // ===== UPDATE TAB KATEGORI =====
                function updateCategoryTab(category) {
                    categoryTabs.forEach(tab => {
                        if (tab.dataset.category === category) {
                            tab.classList.add('active');
                        } else {
                            tab.classList.remove('active');
                        }
                    });
                }

                // ===== FUNGSI UNTUK RESPONSIF =====
                function handleResize() {
                    initTabsSlider();

                    const activeTab = document.querySelector('#categoryTabsSlider .category-tab.active');
                    if (activeTab) {
                        filterPopularProducts(activeTab.dataset.category);
                    }

                    // Render ulang produk terbaru
                    renderProductsSlider(latestProductsFromDB, 'latest-products-container', 'Produk Terbaru', 'products/produk-terbaru/produk-terbaru.php');
                }

                // ===== EVENT LISTENERS =====
                window.addEventListener('resize', debounce(handleResize, 250));

                categoryTabsSlider.addEventListener('touchstart', function() {
                    tabsSwipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        tabsSwipeHint.style.display = 'none';
                    }, 300);
                });

                categoryTabsSlider.addEventListener('mousedown', function() {
                    tabsSwipeHint.style.opacity = '0.5';
                    setTimeout(() => {
                        tabsSwipeHint.style.display = 'none';
                    }, 300);
                });

                categoryTabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        const category = tab.dataset.category;
                        updateCategoryTab(category);
                        filterPopularProducts(category);
                    });
                });

                // Add click event to product cards
                document.addEventListener('click', function(e) {
                    const productCard = e.target.closest('.product-card');

                    // If clicked on product card (or its children)
                    if (productCard) {
                        const productId = productCard.getAttribute('data-product-id');
                        const productCategory = productCard.getAttribute('data-product-category');

                        // Redirect ke halaman checkout
                        if (productId && productCategory) {
                            window.location.href = `checkout/checkout.php?id=${productId}&type=${productCategory}`;
                        }
                    }
                });

                // ===== UTILITY FUNCTIONS =====
                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // ===== INISIALISASI =====
                initTabsSlider();

                // Render produk populer (dari tabel home_produk_populer)
                if (popularProductsFromDB.length > 0) {
                    renderProductsSlider(popularProductsFromDB, 'popular-products-container', 'Produk Populer', 'products/produk populer/produk-populer.php');
                }

                // Render produk terbaru (dari tabel home_produk_terbaru yang baru)
                if (latestProductsFromDB.length > 0) {
                    renderProductsSlider(latestProductsFromDB, 'latest-products-container', 'Produk Terbaru', 'products/produk-terbaru/produk-terbaru.php');
                }
            });
        </script>
    </div>

    <!-- produk grid -->
    <div class="container-grid-products">
        <?php
        // Fungsi untuk mengambil data grid dari tabel home_grid
        function getGridProducts($db, $limit = 3)
        {
            $gridItems = [];
            $query = "SELECT * FROM home_grid ORDER BY urutan ASC, created_at DESC LIMIT $limit";
            $result = mysqli_query($db, $query);

            if (!$result) return [];

            while ($item = mysqli_fetch_assoc($result)) {
                $tipe = $item['tipe_produk'];
                $produk_id = $item['produk_id'];

                $table_main = "admin_produk_" . $tipe;
                $table_gambar = "admin_produk_" . $tipe . "_gambar";
                $table_kombi = "admin_produk_" . $tipe . "_kombinasi";

                if ($tipe == 'aksesoris' || $tipe == 'aksesori') {
                    $table_main = "admin_produk_aksesoris";
                    $table_gambar = "admin_produk_aksesoris_gambar";
                    $table_kombi = "admin_produk_aksesoris_kombinasi";
                }

                $detail_query = "SELECT p.nama_produk, p.deskripsi_produk, pg.foto_thumbnail, 
                                 MIN(pk.harga) as harga_asli,
                                 MIN(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN pk.harga_diskon ELSE pk.harga END) as harga_terendah,
                                 MAX(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount
                                 FROM $table_main p
                                 LEFT JOIN $table_gambar pg ON p.id = pg.produk_id
                                 LEFT JOIN $table_kombi pk ON p.id = pk.produk_id
                                 WHERE p.id = '$produk_id'
                                 GROUP BY p.id";

                $detail_result = mysqli_query($db, $detail_query);
                if ($detail_result && $d = mysqli_fetch_assoc($detail_result)) {
                    $gridItems[] = [
                        'name' => $d['nama_produk'],
                        'description' => $d['deskripsi_produk'] ?? '',
                        'price' => 'Rp ' . number_format($d['harga_terendah'] ?? 0, 0, ',', '.'),
                        'price_raw' => $d['harga_terendah'] ?? 0,
                        'original_price' => $d['harga_asli'] ?? 0,
                        'has_discount' => $d['has_discount'] == 1,
                        'image' => $d['foto_thumbnail'] ? '../admin/uploads/' . $d['foto_thumbnail'] : 'https://via.placeholder.com/800x800?text=No+Image',
                        'label' => $item['label'] ?? 'NEW',
                        'tipe' => $tipe,
                        'id' => $produk_id
                    ];
                }
            }
            return $gridItems;
        }

        $gridProducts = getGridProducts($db, 3);

        // Fungsi untuk mengambil data trade-in
        function getTradeInProducts($db, $limit = 12)
        {
            $tradeItems = [];
            $query = "SELECT * FROM home_trade_in ORDER BY urutan ASC, created_at DESC LIMIT $limit";
            $result = mysqli_query($db, $query);

            if (!$result) return [];

            while ($item = mysqli_fetch_assoc($result)) {
                $tipe = $item['tipe_produk'];
                $produk_id = $item['produk_id'];

                $table_main = "admin_produk_" . $tipe;
                $table_gambar = "admin_produk_" . $tipe . "_gambar";
                $table_kombi = "admin_produk_" . $tipe . "_kombinasi";

                if ($tipe == 'aksesoris' || $tipe == 'aksesori') {
                    $table_main = "admin_produk_aksesoris";
                    $table_gambar = "admin_produk_aksesoris_gambar";
                    $table_kombi = "admin_produk_aksesoris_kombinasi";
                }

                $detail_query = "SELECT p.id, p.nama_produk, pg.foto_thumbnail, 
                                 MIN(pk.harga) as harga_asli,
                                 MIN(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN pk.harga_diskon ELSE pk.harga END) as harga_terendah,
                                 MAX(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount
                                 FROM $table_main p
                                 LEFT JOIN $table_gambar pg ON p.id = pg.produk_id
                                 LEFT JOIN $table_kombi pk ON p.id = pk.produk_id
                                 WHERE p.id = '$produk_id'
                                 GROUP BY p.id";

                $detail_result = mysqli_query($db, $detail_query);
                if ($detail_result && $d = mysqli_fetch_assoc($detail_result)) {
                    $base_price = (float)($d['harga_terendah'] ?? 0);
                    $promo_value = $item['label_promo'] ?: "0";

                    // Ambil hanya angka/desimal dari label (cth: "10%" atau "10" menjadi 10)
                    $percentage = (float) preg_replace('/[^0-9.]/', '', $promo_value);
                    $discount_amount = $base_price * ($percentage / 100);

                    $trade_in_price = $base_price - $discount_amount;
                    if ($trade_in_price < 0) $trade_in_price = 0;

                    $tradeItems[] = [
                        'id' => $produk_id,
                        'tipe' => $tipe,
                        'name' => $d['nama_produk'],
                        'oldPrice' => 'Rp ' . number_format($base_price, 0, ',', '.'),
                        'newPrice' => 'Rp ' . number_format($trade_in_price, 0, ',', '.'),
                        'discount' => $percentage, // Kirim angka saja ke frontend
                        'image' => $d['foto_thumbnail'] ? '../admin/uploads/' . $d['foto_thumbnail'] : 'https://via.placeholder.com/200x180?text=No+Image'
                    ];
                }
            }
            return $tradeItems;
        }

        $tradeInProductsFromDB = getTradeInProducts($db);

        ?>
        <style>
            /* Variabel Warna Apple */
            :root {
                --apple-blue: #0071e3;
                --apple-blue-hover: #0077ed;
                --apple-gray-bg: #f5f5f7;
                --apple-gray-light: #f5f5f7;
                --apple-gray-text: #86868b;
                --apple-dark-text: #1d1d1f;
                --apple-new-badge: #bf4800;
                --apple-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                --apple-card-shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
                --apple-border-color: rgba(0, 0, 0, 0.1);
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: var(--apple-gray-bg);
                color: var(--apple-dark-text);
                line-height: 1.5;
            }

            .container-grid-products {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px
            }

            .grid-main {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            .grid-side {
                display: grid;
                grid-template-rows: 1fr 1fr;
                gap: 24px;
            }

            /* Styling Kartu */
            .card {
                background-color: #ffffff;
                border-radius: 18px;
                padding: 24px;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
                position: relative;
                overflow: hidden;
                border: 1px solid var(--apple-border-color);
                box-shadow: var(--apple-card-shadow);
            }

            /* Efek hover default pada kartu */
            .card:hover {
                transform: translateY(-6px);
                box-shadow: var(--apple-card-shadow-hover);
            }

            /* Efek khusus saat hover button */
            .button-hover-active {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 12px 40px rgba(0, 113, 227, 0.18);
                border-color: var(--apple-blue);
            }

            .card-large {
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center; /* Ubah dari space-between ke center */
                align-items: center;
                gap: 20px; /* Gunakan gap yang fixed */
            }

            .card-horizontal {
                display: flex;
                flex-direction: row; /* Paksa row */
                align-items: center;
                justify-content: flex-start; /* Jangan space-between */
                gap: 20px;
                height: 100%; /* Pastikan mengisi tinggi grid row */
            }

            /* Badge */
            .badge {
                color: var(--apple-new-badge);
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.3px;
                display: block;
                margin-bottom: 2px;
                text-transform: uppercase;
            }

            /* Judul */
            .title {
                font-weight: 700;
                line-height: 1.1;
                margin: 2px 0;
            }

            .card-large .title {
                font-size: 24px;
            }

            .card-horizontal .title {
                font-size: 20px;
            }

            /* Teks */
            .text {
                color: var(--apple-dark-text);
                margin: 2px 0 4px 0;
            }

            .card-large .text {
                font-size: 16px;
                font-weight: 500;
                line-height: 1.3;
            }

            .card-horizontal .text {
                font-size: 14px;
            }

            /* Harga dalam teks */
            .price-text {
                font-size: 17px;
                color: var(--apple-dark-text);
                margin: 4px 0 8px 0;
            }

            .price-start {
                color: var(--apple-gray-text);
                font-weight: 400;
            }

            /* BUTTON KECIL SAMA UNTUK SEMUA */
            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                /* DIPERKECIL */
                background-color: var(--apple-blue);
                color: #ffffff;
                padding: 8px 16px;
                /* DIPERKECIL: dari 10px 22px */
                border-radius: 980px;
                text-decoration: none;
                font-size: 14px;
                /* DIPERKECIL: dari 15px */
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                position: relative;
                overflow: hidden;
                z-index: 2;
                margin-top: 4px;
                /* Tambahkan ini agar button tidak terlalu panjang */
                width: auto;
                min-width: 120px;
                /* Lebar minimum yang sama untuk semua */
            }

            .button:hover {
                background-color: var(--apple-blue-hover);
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
                /* DIPERKECIL */
            }

            /* Efek ripple pada button */
            .button::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .button:hover::after {
                width: 200px;
                /* DIPERKECIL: dari 300px */
                height: 200px;
                /* DIPERKECIL: dari 300px */
            }

            /* Gambar */
            .image-large {
                margin-top: 10px;
                height: 180px; /* Perkecil lagi */
                width: auto;
                max-width: 100%;
                border-radius: 12px;
                transition: transform 0.5s ease;
                object-fit: contain;
            }

            .card:hover .image-large {
                transform: scale(1.05);
            }

            .image-small {
                width: 100px; /* Perkecil lagi agar muat */
                height: 100px;
                object-fit: contain; /* Ganti cover ke contain agar gambar produk utuh */
                background: #fff;
                border-radius: 10px;
                transition: transform 0.5s ease;
                flex-shrink: 0;
            }

            .card-horizontal:hover .image-small {
                transform: scale(1.08);
            }

            .content-right {
                text-align: left; /* Paksa rata kiri untuk horizontal card */
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                align-items: flex-start; /* Rata kiri */
                justify-content: center;
            }

            /* Untuk button di card-large (iPhone) */
            .card-large .button-container {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            /* Efek ikon pada button saat hover */
            .button i {
                transition: transform 0.3s ease;
                opacity: 0;
                transform: translateX(-8px);
                /* DIPERKECIL */
                font-size: 12px;
                /* DIPERKECIL */
            }

            .button:hover i {
                opacity: 1;
                transform: translateX(0);
            }

            /* Responsif */
            @media (max-width: 900px) {
                .grid-main {
                    grid-template-columns: 1fr;
                }

                .grid-side {
                    grid-template-rows: auto;
                }

                .card-horizontal {
                    flex-direction: column;
                    text-align: center;
                }

                .content-right {
                    text-align: center;
                    max-width: 100%;
                    align-items: center;
                    /* Di mobile, button di tengah */
                }

                .card-large .title {
                    font-size: 24px;
                }

                .card-horizontal .title {
                    font-size: 20px;
                }

                .image-large {
                    height: 250px;
                }

                .button {
                    min-width: 110px;
                    /* Sedikit lebih kecil di mobile */
                }
            }

            /* Animasi saat halaman dimuat */
            @keyframes cardAppear {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card {
                animation: cardAppear 0.6s ease-out forwards;
                opacity: 0;
            }

            .card-large {
                animation-delay: 0.1s;
            }

            .grid-side .card:first-child {
                animation-delay: 0.2s;
            }

            .grid-side .card:last-child {
                animation-delay: 0.3s;
            }
        </style>
        <div class="grid-main">
            <?php if (isset($gridProducts[0])): $p = $gridProducts[0]; ?>
                <!-- Main Card (Large) -->
                <div class="card card-large">
                    <?php if (!empty($p['label'])): ?>
                        <span class="badge"><?php echo htmlspecialchars($p['label']); ?></span>
                    <?php endif; ?>
                    <h2 class="title"><?php echo htmlspecialchars($p['name']); ?></h2>
                    <p class="text"><?php echo htmlspecialchars($p['description']); ?></p>
                    <p class="price-text">
                        <?php if (isset($p['has_discount']) && $p['has_discount']): ?>
                            <span class="price-start" style="text-decoration: line-through; color: #86868b; margin-right: 5px; font-size: 0.9em;">Rp <?php echo number_format($p['original_price'], 0, ',', '.'); ?></span>
                            <span style="color: #1d1d1f; font-weight: 600;">Mulai <?php echo $p['price']; ?></span>
                        <?php else: ?>
                            <span class="price-start">Mulai </span><?php echo $p['price']; ?>
                        <?php endif; ?>
                    </p>
                    <div class="button-container">
                        <a href="#" class="button" data-id="<?php echo $p['id']; ?>" data-type="<?php echo $p['tipe']; ?>">
                            <i class="bi bi-bag-fill"></i>
                            Beli sekarang
                        </a>
                    </div>
                    <img src="<?php echo $p['image']; ?>" class="image-large" alt="<?php echo htmlspecialchars($p['name']); ?>">
                </div>
            <?php endif; ?>

            <div class="grid-side">
                <?php if (isset($gridProducts[1])): $p = $gridProducts[1]; ?>
                    <!-- Second Card (Horizontal) -->
                    <div class="card card-horizontal">
                        <img src="<?php echo $p['image']; ?>" class="image-small" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <div class="content-right">
                            <?php if (!empty($p['label'])): ?>
                                <span class="badge"><?php echo htmlspecialchars($p['label']); ?></span>
                            <?php endif; ?>
                            <h3 class="title"><?php echo htmlspecialchars($p['name']); ?></h3>
                            <p class="text"><?php echo htmlspecialchars($p['description']); ?></p>
                            <p class="price-text">
                                <?php if (isset($p['has_discount']) && $p['has_discount']): ?>
                                    <span class="price-start" style="text-decoration: line-through; color: #86868b; margin-right: 5px; font-size: 0.9em;">Rp <?php echo number_format($p['original_price'], 0, ',', '.'); ?></span>
                                    <span style="color: #1d1d1f; font-weight: 600;">Mulai <?php echo $p['price']; ?></span>
                                <?php else: ?>
                                    <span class="price-start">Mulai </span><?php echo $p['price']; ?>
                                <?php endif; ?>
                            </p>
                            <a href="#" class="button" data-id="<?php echo $p['id']; ?>" data-type="<?php echo $p['tipe']; ?>">
                                <i class="bi bi-bag-fill"></i>
                                Beli sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($gridProducts[2])): $p = $gridProducts[2]; ?>
                    <!-- Third Card (Horizontal) -->
                    <div class="card card-horizontal">
                        <img src="<?php echo $p['image']; ?>" class="image-small" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        <div class="content-right">
                            <?php if (!empty($p['label'])): ?>
                                <span class="badge"><?php echo htmlspecialchars($p['label']); ?></span>
                            <?php endif; ?>
                            <h3 class="title"><?php echo htmlspecialchars($p['name']); ?></h3>
                            <p class="text"><?php echo htmlspecialchars($p['description']); ?></p>
                            <p class="price-text">
                                <?php if (isset($p['has_discount']) && $p['has_discount']): ?>
                                    <span class="price-start" style="text-decoration: line-through; color: #86868b; margin-right: 5px; font-size: 0.9em;">Rp <?php echo number_format($p['original_price'], 0, ',', '.'); ?></span>
                                    <span style="color: #1d1d1f; font-weight: 600;">Mulai <?php echo $p['price']; ?></span>
                                <?php else: ?>
                                    <span class="price-start">Mulai </span><?php echo $p['price']; ?>
                                <?php endif; ?>
                            </p>
                            <a href="#" class="button" data-id="<?php echo $p['id']; ?>" data-type="<?php echo $p['tipe']; ?>">
                                <i class="bi bi-bag-fill"></i>
                                Beli sekarang
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script>
            // Menambahkan efek hover yang mempengaruhi seluruh grid
            document.querySelectorAll('.button').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    // Tambah kelas aktif ke semua kartu
                    document.querySelectorAll('.card').forEach(card => {
                        card.classList.add('button-hover-active');

                        // Tambah efek glow khusus
                        card.style.boxShadow = '0 12px 40px rgba(0, 113, 227, 0.18)';
                        card.style.borderColor = 'rgba(0, 113, 227, 0.3)';
                    });
                });

                button.addEventListener('mouseleave', function() {
                    // Hapus kelas aktif dari semua kartu
                    document.querySelectorAll('.card').forEach(card => {
                        card.classList.remove('button-hover-active');

                        // Kembalikan ke semula
                        card.style.boxShadow = '';
                        card.style.borderColor = '';
                    });
                });

                // Efek klik pada tombol
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const productId = this.getAttribute('data-id');
                    const productType = this.getAttribute('data-type');

                    // Animasi klik
                    this.style.transform = 'scale(0.95)';
                    this.style.backgroundColor = '#0056b3';

                    // Efek ripple
                    const ripple = document.createElement('span');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.width = '80px';
                    ripple.style.height = '80px';
                    ripple.style.top = '50%';
                    ripple.style.left = '50%';
                    ripple.style.marginTop = '-40px';
                    ripple.style.marginLeft = '-40px';

                    this.appendChild(ripple);

                    // Hapus ripple setelah animasi selesai
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);

                    // Redirect ke halaman checkout setelah sedikit delay animasi
                    setTimeout(() => {
                        window.location.href = `checkout/checkout.php?id=${productId}&type=${productType}`;
                    }, 400);
                });
            });

            // Tambah style untuk animasi ripple
            const style = document.createElement('style');
            style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
            document.head.appendChild(style);
        </script>
    </div>

    <!-- trade in -->
    <div class="tradein-container">
        <style>
            body {
                background-color: #f7f7f7;
            }

            .tradein-container {
                padding: 40px 20px;
                max-width: 1400px;
                margin: 0 auto;
            }

            /* HEADING STYLES */
            .tradein-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .tradein-header h1 {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            .tradein-header h1::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            /* SLIDER WRAPPER */
            .tradein-slider-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                padding: 30px 20px 60px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                margin-bottom: 40px;
            }

            .tradein-slider-wrapper.single-slide {
                padding-bottom: 30px;
            }

            /* SLIDER */
            .tradein-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 0;
            }

            /* SLIDE */
            .tradein-slide {
                min-width: 100%;
                padding: 10px;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                flex-shrink: 0;
            }

            /* SLIDE INNER - Flex layout untuk produk */
            .tradein-slide-inner {
                display: flex;
                gap: 20px;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            /* PRODUCT CARD */
            .tradein-product {
                background: white;
                border-radius: 12px;
                padding: 20px;
                height: 100%;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .tradein-product:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 30px rgba(0, 122, 255, 0.1);
                border-color: #007aff;
            }

            .tradein-image {
                width: 100%;
                max-width: 200px;
                height: 180px;
                object-fit: contain;
                margin-bottom: 15px;
                border-radius: 8px;
                background-color: #f8f9fa;
                padding: 15px;
            }

            /* NAMA PRODUK */
            .tradein-name {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin-bottom: 20px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                line-height: 1.3;
            }

            /* HARGA - Layout sejajar */
            .tradein-prices {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-bottom: 25px;
                width: 100%;
                text-align: left;
            }

            .tradein-price-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 5px 0;
            }

            .tradein-price-label {
                font-size: 14px;
                color: #666;
                font-weight: 500;
            }

            .tradein-price-value {
                font-size: 15px;
                font-weight: 600;
            }

            .tradein-old-price {
                color: #999;
                text-decoration: line-through;
            }

            .tradein-new-price {
                color: #007aff;
            }

            .tradein-discount {
                color: #34c759;
            }

            /* BUTTON */
            .tradein-btn {
                background-color: #007aff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 25px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-size: 14px;
                margin-top: auto;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .tradein-btn:hover {
                background-color: var(--apple-blue-hover);
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
            }

            /* Efek ripple pada button */
            .tradein-btn::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .tradein-btn:hover::after {
                width: 200px;
                height: 200px;
            }

            /* NAVIGATION BUTTONS */
            .tradein-controls {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: calc(100% - 40px);
                left: 20px;
                display: flex;
                justify-content: space-between;
                pointer-events: none;
                z-index: 20;
            }

            .tradein-controls.hidden {
                display: none;
            }

            .tradein-nav-btn {
                background-color: white;
                border: none;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 20px;
                color: #333;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                pointer-events: auto;
                opacity: 1;
                visibility: visible;
            }

            .tradein-nav-btn.hidden {
                display: none;
            }

            .tradein-nav-btn:hover:not(:disabled) {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .tradein-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                background-color: #f0f0f0;
            }

            .tradein-nav-btn:disabled:hover {
                transform: none;
                background-color: #f0f0f0;
                color: #333;
            }

            /* DOTS NAVIGATION */
            .tradein-dots {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 15px;
                position: absolute;
                bottom: 25px;
                left: 0;
                right: 0;
                z-index: 10;
            }

            .tradein-dots.hidden {
                display: none;
            }

            .tradein-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background-color: rgba(0, 122, 255, 0.2);
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
            }

            .tradein-dot.active {
                background-color: #007aff !important;
                transform: scale(1.2);
                border-color: white !important;
            }

            .tradein-dot:hover {
                background-color: rgba(0, 122, 255, 0.4);
            }

            .empty-state i {
                display: block;
                margin-bottom: 15px;
            }

            .empty-state h4 {
                font-size: 18px;
                font-weight: 600;
            }

            /* RESPONSIVE STYLES - MODIFIKASI UTAMA */
            /* Desktop besar: 4 produk per slide */
            @media (min-width: 1200px) {
                .tradein-slide-inner .tradein-product {
                    width: calc(25% - 20px);
                    max-width: calc(25% - 20px);
                    min-width: 0;
                }
            }

            /* Tablet landscape: 3 produk per slide */
            @media (min-width: 900px) and (max-width: 1199px) {
                .tradein-slide-inner .tradein-product {
                    width: calc(33.333% - 20px);
                    max-width: calc(33.333% - 20px);
                    min-width: 0;
                }

                .tradein-image {
                    max-width: 180px;
                    height: 160px;
                }

                .tradein-name {
                    font-size: 15px;
                    height: 36px;
                }
            }

            /* Tablet portrait: 2 produk per slide */
            @media (min-width: 576px) and (max-width: 899px) {
                .tradein-slide-inner .tradein-product {
                    width: calc(50% - 20px);
                    max-width: calc(50% - 20px);
                    min-width: 0;
                }

                .tradein-image {
                    max-width: 160px;
                    height: 140px;
                }

                .tradein-name {
                    font-size: 15px;
                    height: 36px;
                }

                .tradein-slider-wrapper {
                    padding: 25px 15px 50px;
                }

                .tradein-controls {
                    width: calc(100% - 30px);
                    left: 15px;
                }
            }

            /* Mobile kecil: 1 produk per slide */
            @media (max-width: 575px) {
                .tradein-slide-inner .tradein-product {
                    width: calc(100% - 20px);
                    max-width: 300px;
                    min-width: 0;
                }

                .tradein-image {
                    max-width: 140px;
                    height: 120px;
                }

                .tradein-name {
                    font-size: 15px;
                    height: 36px;
                }

                .tradein-price-label {
                    font-size: 14px;
                }

                .tradein-price-value {
                    font-size: 14px;
                }

                .tradein-slider-wrapper {
                    padding: 20px 10px 50px;
                }

                .tradein-controls {
                    width: calc(100% - 20px);
                    left: 10px;
                }

                .tradein-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }
            }

            /* Mobile sangat kecil */
            @media (max-width: 300px) {
                .tradein-slide-inner .tradein-product {
                    width: 100%;
                    max-width: 100%;
                    padding: 15px;
                }

                .tradein-image {
                    max-width: 120px;
                    height: 100px;
                    padding: 10px;
                }

                .tradein-name {
                    font-size: 14px;
                    height: 32px;
                }

                .tradein-price-label {
                    font-size: 12px;
                }

                .tradein-price-value {
                    font-size: 13px;
                }

                .tradein-btn {
                    padding: 8px 16px;
                    font-size: 13px;
                }

                .tradein-slider-wrapper {
                    padding: 15px 5px 40px;
                }

                .tradein-controls {
                    width: calc(100% - 10px);
                    left: 5px;
                }

                .tradein-nav-btn {
                    width: 35px;
                    height: 35px;
                    font-size: 16px;
                }

                .tradein-dots {
                    bottom: 20px;
                }

                .tradein-dot {
                    width: 10px;
                    height: 10px;
                }
            }

            /* Responsif heading */
            @media (max-width: 768px) {
                .tradein-header h1 {
                    font-size: 24px;
                }
            }

            @media (max-width: 480px) {
                .tradein-header h1 {
                    font-size: 22px;
                }

                .tradein-container {
                    padding: 30px 15px;
                }
            }
        </style>
        <!-- HEADING -->
        <div class="tradein-header">
            <h1>Trade-in produkmu dengan produk terbaru.</h1>
        </div>

        <!-- SLIDER -->
        <div class="tradein-slider-wrapper" id="tradeinSliderWrapper">
            <?php if (empty($tradeInProductsFromDB)): ?>
                <div class="empty-state" style="text-align: center; padding: 40px;">
                    <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                    <h4 style="margin-top: 15px; color: #333;">Tidak ada produk untuk ditampilkan</h4>
                    <p style="color: #666;">Belum ada produk dalam kategori ini</p>
                </div>
            <?php else: ?>
                <div class="tradein-controls" id="tradeinControls">
                    <button class="tradein-nav-btn tradein-prev" id="tradeinPrev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="tradein-nav-btn tradein-next" id="tradeinNext">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                <div class="tradein-slider" id="tradeinSlider">
                    <!-- Slides akan di-generate oleh JavaScript -->
                </div>

                <div class="tradein-dots" id="tradeinDots"></div>
            <?php endif; ?>
        </div>
        <script>
            // Data produk dengan harga trade-in (diambil dari database)
            const tradeinProducts = <?php echo json_encode($tradeInProductsFromDB); ?>;

            // ===== FUNGSI UTAMA =====
            function renderTradeinSlider() {
                const sliderWrapper = document.getElementById('tradeinSliderWrapper');
                const slider = document.getElementById('tradeinSlider');

                if (!slider) return; // Exit jika empty state ditampilkan

                // Periksa jika hanya ada 1 slide
                const productsPerSlide = getProductsPerSlide();
                const slideCount = Math.ceil(tradeinProducts.length / productsPerSlide);
                const isSingleSlide = slideCount <= 1;

                let html = '';

                for (let i = 0; i < slideCount; i++) {
                    html += `<div class="tradein-slide">`;
                    html += `<div class="tradein-slide-inner" id="tradein-slide-inner-${i}">`;

                    for (let j = 0; j < productsPerSlide; j++) {
                        const index = i * productsPerSlide + j;
                        if (index < tradeinProducts.length) {
                            html += createTradeinCard(tradeinProducts[index]);
                        }
                    }

                    html += `</div></div>`;
                }

                slider.innerHTML = html;

                // Hide controls and dots if single slide
                const controls = document.getElementById('tradeinControls');
                const dots = document.getElementById('tradeinDots');
                if (isSingleSlide) {
                    if (controls) controls.classList.add('hidden');
                    if (dots) dots.classList.add('hidden');
                    sliderWrapper.classList.add('single-slide');
                } else {
                    if (controls) controls.classList.remove('hidden');
                    if (dots) dots.classList.remove('hidden');
                    sliderWrapper.classList.remove('single-slide');
                }

                initTradeinSlider(productsPerSlide, slideCount);
            }

            // Fungsi untuk menentukan jumlah produk per slide berdasarkan lebar layar
            function getProductsPerSlide() {
                const screenWidth = window.innerWidth;

                if (screenWidth >= 1200) return 4; // 4 produk per slide
                if (screenWidth >= 900) return 3; // 3 produk per slide
                if (screenWidth >= 576) return 2; // 2 produk per slide
                return 1; // 1 produk per slide
            }

            function createTradeinCard(product) {
                return `
                <div class="tradein-product" data-product-id="${product.id}" data-product-type="${product.tipe}" style="cursor: pointer;">
                    <img src="${product.image}" alt="${product.name}" class="tradein-image" onerror="this.src='https://via.placeholder.com/200x180?text=No+Image'">
                    <div class="tradein-name">${product.name}</div>
                    
                    <div class="tradein-prices">
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Harga Normal:</span>
                            <span class="tradein-price-value tradein-old-price">${product.oldPrice}</span>
                        </div>
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Setelah Trade-in:</span>
                            <span class="tradein-price-value tradein-new-price">${product.newPrice}</span>
                        </div>
                        <div class="tradein-price-row">
                            <span class="tradein-price-label">Promo:</span>
                            <span class="tradein-price-value tradein-discount">${product.discount}%</span>
                        </div>
                    </div>
                    
                    <button class="tradein-btn">
                        <i class="bi bi-bag"></i> Beli Sekarang
                    </button>
                </div>
            `;
            }

            function initTradeinSlider(productsPerSlide, slideCount) {
                const slider = document.getElementById('tradeinSlider');
                const prevBtn = document.getElementById('tradeinPrev');
                const nextBtn = document.getElementById('tradeinNext');
                const dotsContainer = document.getElementById('tradeinDots');
                const sliderWrapper = document.getElementById('tradeinSliderWrapper');

                if (!slider || slideCount <= 1) return;

                let currentSlide = 0;

                // Buat dots navigation
                if (dotsContainer) {
                    dotsContainer.innerHTML = '';
                    for (let i = 0; i < slideCount; i++) {
                        const dot = document.createElement('div');
                        dot.classList.add('tradein-dot');
                        if (i === 0) dot.classList.add('active');
                        dot.addEventListener('click', () => goToSlide(i));
                        dotsContainer.appendChild(dot);
                    }
                }

                function updateNavButtons() {
                    if (prevBtn) {
                        prevBtn.disabled = currentSlide === 0;
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentSlide === slideCount - 1;
                    }
                }

                function goToSlide(slideIndex) {
                    if (slideIndex < 0 || slideIndex >= slideCount) return;

                    currentSlide = slideIndex;
                    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                    updateDots();
                    updateNavButtons();
                }

                function updateDots() {
                    if (!dotsContainer) return;
                    const dots = dotsContainer.querySelectorAll('.tradein-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlide);
                    });
                }

                function nextSlide() {
                    if (currentSlide < slideCount - 1) {
                        goToSlide(currentSlide + 1);
                    }
                }

                function prevSlide() {
                    if (currentSlide > 0) {
                        goToSlide(currentSlide - 1);
                    }
                }

                if (prevBtn) prevBtn.addEventListener('click', prevSlide);
                if (nextBtn) nextBtn.addEventListener('click', nextSlide);

                updateNavButtons();
            }

            // ===== INISIALISASI =====
            document.addEventListener('DOMContentLoaded', function() {
                if (tradeinProducts && tradeinProducts.length > 0) {
                    renderTradeinSlider();

                    // Re-render on resize
                    window.addEventListener('resize', debounce(function() {
                        renderTradeinSlider();
                    }, 250));
                }
            });

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // ADDED: Event listener for trade-in product clicks
            document.addEventListener('click', function(e) {
                const tradeinProduct = e.target.closest('.tradein-product');

                if (tradeinProduct) {
                    const productId = tradeinProduct.getAttribute('data-product-id');
                    const productType = tradeinProduct.getAttribute('data-product-type');

                    if (productId && productType) {
                        window.location.href = `checkout/checkout.php?id=${productId}&type=${productType}`;
                    }
                }
            });
        </script>
    </div>

    <!-- Layanan & Penawaran Eksklusif -->
    <div class="simple-services-container">
        <style>
            .simple-services-container * {
                box-sizing: border-box;
                font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            body {
                background-color: #f5f7fa;
                color: #1d1d1f;
            }

            .simple-services-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            .page-header {
                text-align: center;
                margin-bottom: 40px;
            }

            .page-header h1 {
                font-size: 2rem;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 10px;
            }

            /* SIMPLE SLIDER STYLES */
            .simple-services-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
                margin: 0 auto;
                border: 1px solid #e0e0e0;
            }

            .simple-slider {
                display: flex;
                transition: transform 0.5s ease;
                width: 100%;
            }

            .simple-slide {
                min-width: 100%;
                display: flex;
                gap: 20px;
                padding: 25px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .simple-slide.active {
                opacity: 1;
            }

            /* CARD STYLES - LEBIH KECIL DAN SEDERHANA */
            .simple-card {
                flex: 1;
                min-width: 0;
                background: white;
                border-radius: 10px;
                padding: 20px;
                border: 1px solid #e0e0e0;
                display: flex;
                flex-direction: column;
                position: relative;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .simple-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            /* Warna border top untuk setiap card */
            .simple-card[data-service-type="pickup"] {
                border-top: 4px solid #0071bc;
            }

            .simple-card[data-service-type="eraxpress"] {
                border-top: 4px solid #ff3b30;
            }

            .simple-card[data-service-type="financing"] {
                border-top: 4px solid #0071bc;
            }

            .simple-card[data-service-type="experience"] {
                border-top: 4px solid #5856d6;
            }

            .simple-card[data-service-type="sale"] {
                border-top: 4px solid #008900;
            }

            .simple-icon {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .simple-card[data-service-type="pickup"] .simple-icon {
                color: #0071bc;
            }

            .simple-card[data-service-type="eraxpress"] .simple-icon {
                color: #ff3b30;
            }

            .simple-card[data-service-type="financing"] .simple-icon {
                color: #0071bc;
            }

            .simple-card[data-service-type="experience"] .simple-icon {
                color: #5856d6;
            }

            .simple-card[data-service-type="sale"] .simple-icon {
                color: #008900;
            }

            .simple-category {
                font-size: 0.8rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 10px;
            }

            .simple-card[data-service-type="pickup"] .simple-category {
                color: #0071bc;
            }

            .simple-card[data-service-type="eraxpress"] .simple-category {
                color: #ff3b30;
            }

            .simple-card[data-service-type="financing"] .simple-category {
                color: #0071bc;
            }

            .simple-card[data-service-type="experience"] .simple-category {
                color: #5856d6;
            }

            .simple-card[data-service-type="sale"] .simple-category {
                color: #008900;
            }

            .simple-title {
                font-size: 1rem;
                font-weight: 600;
                line-height: 1.3;
                color: #1d1d1f;
                margin-bottom: 15px;
                min-height: 40px;
            }

            .simple-footer {
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #f0f0f5;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: auto;
            }

            .simple-badge {
                background-color: #f5f5f7;
                color: #86868b;
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 0.75rem;
                font-weight: 600;
            }

            .simple-cta {
                background-color: #0071bc;
                color: white;
                padding: 8px 15px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.8rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .simple-cta:hover {
                background-color: #005a9c;
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 188, 0.25);
            }

            /* Efek ripple pada button */
            .simple-cta::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .simple-cta:hover::after {
                width: 150px;
                height: 150px;
            }

            .simple-cta i {
                margin-left: 5px;
                font-size: 0.8rem;
            }

            /* Navigation Buttons */
            .simple-controls {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding: 0 15px;
                z-index: 10;
            }

            .simple-nav-btn {
                background: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 18px;
                color: #333;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .simple-nav-btn:hover {
                background: #0071bc;
                color: white;
            }

            .simple-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
            }

            .simple-nav-btn:disabled:hover {
                background: white;
                color: #333;
            }

            /* Dots Navigation */
            .simple-dots {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 5px;
                margin-bottom: 20px;
            }

            .simple-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #ddd;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .simple-dot.active {
                background: #0071bc;
            }

            /* RESPONSIVE STYLES - MODIFIKASI UTAMA */
            /* Desktop: 4 cards per slide (default) */
            @media (min-width: 993px) {
                .simple-slide .simple-card {
                    flex: 0 0 calc(25% - 15px);
                    /* 4 cards per slide */
                    min-width: calc(25% - 15px);
                }
            }

            /* Tablet: 1 card per slide */
            @media (max-width: 992px) and (min-width: 577px) {
                .simple-slide {
                    padding: 20px;
                    gap: 15px;
                    justify-content: center;
                }

                .simple-slide .simple-card {
                    flex: 0 0 100%;
                    /* 1 card per slide */
                    max-width: 400px;
                    /* Maksimal lebar card */
                    margin: 0 auto;
                }

                .simple-title {
                    min-height: 35px;
                }

                .page-header h1 {
                    font-size: 1.8rem;
                }
            }

            /* Mobile: 1 card per slide */
            @media (max-width: 576px) {
                .simple-slide {
                    padding: 15px;
                    gap: 10px;
                    justify-content: center;
                }

                .simple-slide .simple-card {
                    flex: 0 0 100%;
                    /* 1 card per slide */
                    padding: 15px;
                }

                .simple-title {
                    min-height: 30px;
                    font-size: 0.95rem;
                }

                .simple-cta {
                    padding: 6px 12px;
                    font-size: 0.75rem;
                }

                .page-header h1 {
                    font-size: 1.6rem;
                }

                .simple-services-wrapper {
                    border-radius: 10px;
                }

                .simple-controls {
                    padding: 0 10px;
                }

                .simple-nav-btn {
                    width: 35px;
                    height: 35px;
                    font-size: 16px;
                }

                .simple-dots {
                    gap: 8px;
                }
            }

            /* Ukuran layar sangat kecil */
            @media (max-width: 360px) {
                .simple-slide {
                    padding: 10px;
                }

                .simple-card {
                    padding: 12px;
                }

                .simple-icon {
                    font-size: 1.3rem;
                    margin-bottom: 10px;
                }

                .simple-category {
                    font-size: 0.75rem;
                }

                .simple-title {
                    font-size: 0.9rem;
                    min-height: 28px;
                }

                .simple-footer {
                    flex-direction: column;
                    gap: 10px;
                    align-items: flex-start;
                }

                .simple-badge {
                    align-self: flex-start;
                }

                .simple-cta {
                    width: 100%;
                    justify-content: center;
                }
            }
        </style>
        <div class="page-header">
            <h1>Layanan & Penawaran Eksklusif</h1>
        </div>

        <!-- SIMPLE SLIDER -->
        <div class="simple-services-wrapper">
            <div class="simple-controls">
                <button class="simple-nav-btn simple-prev" id="simplePrev" disabled>
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="simple-nav-btn simple-next" id="simpleNext">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="simple-slider" id="simpleSlider">
                <!-- Slides akan di-generate oleh JavaScript -->
            </div>

            <div class="simple-dots" id="simpleDots"></div>
        </div>
        <script>
            // Data layanan
            const simpleServices = [{
                    id: 1,
                    type: "pickup",
                    category: "PICKUP",
                    title: "Kirim dan Ambil. Belanja online dan bebas biaya kirim.",
                    icon: "bi bi-bag-check-fill",
                    badge: "Gratis Ongkir",
                    ctaText: "Pelajari"
                },
                {
                    id: 2,
                    type: "eraxpress",
                    category: "eraXpress",
                    title: "Kirim cepat, tanpa takut telat.",
                    icon: "bi bi-truck",
                    badge: "Same Day Delivery",
                    ctaText: "Cek Jadwal"
                },
                {
                    id: 3,
                    type: "financing",
                    category: "FINANCING",
                    title: "Dapatkan harga spesial dan cicilan 0% untuk produk Apple.",
                    icon: "bi bi-credit-card-fill",
                    badge: "0% Bunga",
                    ctaText: "Ajukan Sekarang"
                },
                {
                    id: 4,
                    type: "experience",
                    category: "IBOX EXPERIENCE DAYS",
                    title: "Memaksimalkan penggunaan produk Apple anda bersama Apple expert",
                    icon: "bi bi-mortarboard-fill",
                    badge: "Expert Session",
                    ctaText: "Daftar Kelas"
                },
                {
                    id: 5,
                    type: "sale",
                    category: "SALE",
                    title: "Penawaran terbaik hari ini untuk Belanja Online dan Click & PickUp",
                    icon: "bi bi-tags-fill",
                    badge: "Limited Time",
                    ctaText: "Lihat Promo"
                }
            ];

            // Variabel slider
            let currentSimpleSlide = 0;
            let totalSimpleSlides = 0;
            let cardsPerSlide = 4; // Default untuk desktop

            // Fungsi untuk menentukan jumlah card per slide berdasarkan lebar layar
            function getCardsPerSlide() {
                if (window.innerWidth <= 576) { // Mobile
                    return 1;
                } else if (window.innerWidth <= 992) { // Tablet
                    return 1;
                } else { // Desktop
                    return 4;
                }
            }

            // Render slider
            function renderSimpleSlider() {
                const slider = document.getElementById('simpleSlider');
                const dotsContainer = document.getElementById('simpleDots');

                // Reset
                slider.innerHTML = '';
                dotsContainer.innerHTML = '';

                // Update cardsPerSlide berdasarkan lebar layar
                cardsPerSlide = getCardsPerSlide();

                // Hitung jumlah slide
                totalSimpleSlides = Math.ceil(simpleServices.length / cardsPerSlide);

                // Buat slides
                for (let i = 0; i < totalSimpleSlides; i++) {
                    const slide = document.createElement('div');
                    slide.className = i === 0 ? 'simple-slide active' : 'simple-slide';

                    // Tambahkan layanan ke slide ini
                    for (let j = 0; j < cardsPerSlide; j++) {
                        const index = i * cardsPerSlide + j;
                        if (index < simpleServices.length) {
                            slide.appendChild(createSimpleCard(simpleServices[index]));
                        }
                    }

                    slider.appendChild(slide);
                }

                // Buat dots navigation
                for (let i = 0; i < totalSimpleSlides; i++) {
                    const dot = document.createElement('div');
                    dot.className = i === 0 ? 'simple-dot active' : 'simple-dot';
                    dot.addEventListener('click', () => goToSimpleSlide(i));
                    dotsContainer.appendChild(dot);
                }

                // Update tombol navigasi
                updateSimpleNavButtons();
            }

            // Buat card sederhana
            function createSimpleCard(service) {
                const card = document.createElement('div');
                card.className = 'simple-card';
                card.setAttribute('data-service-type', service.type);

                card.innerHTML = `
                <div class="simple-icon">
                    <i class="${service.icon}"></i>
                </div>
                <div class="simple-category">${service.category}</div>
                <div class="simple-title">${service.title}</div>
                <div class="simple-footer">
                    <div class="simple-badge">${service.badge}</div>
                    <a href="#" class="simple-cta" onclick="simpleServiceAction(${service.id})">
                        ${service.ctaText} <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            `;
                return card;
            }

            // Fungsi pindah slide
            function goToSimpleSlide(index) {
                if (index < 0 || index >= totalSimpleSlides) return;

                // Update slide aktif
                const slides = document.querySelectorAll('.simple-slide');
                const dots = document.querySelectorAll('.simple-dot');

                // Sembunyikan slide lama
                slides[currentSimpleSlide].classList.remove('active');

                // Tampilkan slide baru
                slides[index].classList.add('active');
                dots[currentSimpleSlide].classList.remove('active');
                dots[index].classList.add('active');

                // Update slider position
                const slider = document.getElementById('simpleSlider');
                slider.style.transform = `translateX(-${index * 100}%)`;

                // Update current slide
                currentSimpleSlide = index;

                // Update tombol navigasi
                updateSimpleNavButtons();
            }

            // Next slide
            function nextSimpleSlide() {
                if (currentSimpleSlide < totalSimpleSlides - 1) {
                    goToSimpleSlide(currentSimpleSlide + 1);
                }
            }

            // Prev slide
            function prevSimpleSlide() {
                if (currentSimpleSlide > 0) {
                    goToSimpleSlide(currentSimpleSlide - 1);
                }
            }

            // Update tombol navigasi
            function updateSimpleNavButtons() {
                const prevBtn = document.getElementById('simplePrev');
                const nextBtn = document.getElementById('simpleNext');

                prevBtn.disabled = currentSimpleSlide === 0;
                nextBtn.disabled = currentSimpleSlide === totalSimpleSlides - 1;
            }

            // Fungsi aksi layanan
            function simpleServiceAction(id) {
                const service = simpleServices.find(s => s.id === id);
                if (service) {
                    alert(`Anda memilih layanan: ${service.category}\n${service.title}`);
                }
            }

            // Fungsi untuk menangani perubahan ukuran layar
            function handleResize() {
                const newCardsPerSlide = getCardsPerSlide();

                // Jika jumlah card per slide berubah, render ulang slider
                if (newCardsPerSlide !== cardsPerSlide) {
                    // Simpan slide aktif saat ini berdasarkan data yang ditampilkan
                    const currentFirstIndex = currentSimpleSlide * cardsPerSlide;

                    // Render ulang slider
                    renderSimpleSlider();

                    // Hitung slide baru yang harus aktif
                    const newSlideIndex = Math.floor(currentFirstIndex / newCardsPerSlide);

                    // Pindah ke slide yang sesuai
                    goToSimpleSlide(Math.min(newSlideIndex, totalSimpleSlides - 1));
                }
            }

            // Initialize
            document.addEventListener('DOMContentLoaded', function() {
                renderSimpleSlider();

                // Event listeners untuk tombol navigasi
                document.getElementById('simplePrev').addEventListener('click', prevSimpleSlide);
                document.getElementById('simpleNext').addEventListener('click', nextSimpleSlide);

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowLeft') prevSimpleSlide();
                    if (e.key === 'ArrowRight') nextSimpleSlide();
                });

                // Event listener untuk resize window
                window.addEventListener('resize', handleResize);
            });
        </script>
    </div>

    <!-- produk aksesori -->
    <div class="container-aksesori">
        <?php
        // Fungsi untuk mengambil data aksesori unggulan
        if (!function_exists('getHomeAksesori')) {
            function getHomeAksesori($db, $limit = 12)
            {
                $aksesoriItems = [];
                $query = "SELECT * FROM home_aksesori ORDER BY urutan ASC, created_at DESC LIMIT $limit";
                $result = mysqli_query($db, $query);

                if (!$result) return [];

                while ($item = mysqli_fetch_assoc($result)) {
                    $tipe = $item['tipe_produk'];
                    $produk_id = $item['produk_id'];

                    $table_main = "admin_produk_" . $tipe;
                    $table_gambar = "admin_produk_" . $tipe . "_gambar";
                    $table_kombi = "admin_produk_" . $tipe . "_kombinasi";

                    if ($tipe == 'aksesoris' || $tipe == 'aksesori') {
                        $table_main = "admin_produk_aksesoris";
                        $table_gambar = "admin_produk_aksesoris_gambar";
                        $table_kombi = "admin_produk_aksesoris_kombinasi";
                    }

                    $detail_query = "SELECT p.id, p.nama_produk, p.deskripsi_produk, pg.foto_thumbnail, 
                                 MIN(pk.harga) as harga_asli,
                                 MIN(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN pk.harga_diskon ELSE pk.harga END) as harga_terendah,
                                 MAX(CASE WHEN pk.harga_diskon > 0 AND pk.harga_diskon IS NOT NULL THEN 1 ELSE 0 END) as has_discount
                                 FROM $table_main p
                                 LEFT JOIN $table_gambar pg ON p.id = pg.produk_id
                                 LEFT JOIN $table_kombi pk ON p.id = pk.produk_id
                                 WHERE p.id = '$produk_id'
                                 GROUP BY p.id";

                    $detail_result = mysqli_query($db, $detail_query);
                    if ($detail_result && $d = mysqli_fetch_assoc($detail_result)) {
                        $aksesoriItems[] = [
                            'id' => $produk_id,
                            'tipe' => $tipe,
                            'name' => $d['nama_produk'],
                            'description' => $d['deskripsi_produk'],
                            'price' => (float)($d['harga_terendah'] ?? 0),
                            'harga_asli' => (float)($d['harga_asli'] ?? 0),
                            'has_discount' => (int)($d['has_discount'] ?? 0),
                            'label' => $item['label'] ?: ucfirst($tipe),
                            'image' => $d['foto_thumbnail'] ? '../admin/uploads/' . $d['foto_thumbnail'] : 'https://via.placeholder.com/400x400?text=No+Image'
                        ];
                    }
                }
                return $aksesoriItems;
            }
        }

        $homeAksesoriFromDB = getHomeAksesori($db);
        ?>
        <style>
            body {
                background-color: #f8f9fa;
                color: #1d1d1f;
                line-height: 1.6;
                overflow-x: hidden;
            }

            /* CONTAINER UTAMA */
            .container-aksesori {
                width: 100%;
                max-width: 1400px;
                margin: 50px auto 30px;
                padding: 0 20px;
                position: relative;
            }

            /* HEADING STYLE */
            .section-heading {
                text-align: center;
                position: relative;
                margin-bottom: 30px;
            }

            .section-heading .section-main-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1d1d1f;
                display: inline-block;
                position: relative;
                margin-bottom: 15px;
                letter-spacing: -0.5px;
            }

            .section-heading .section-main-title::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            .section-heading p {
                font-size: 1.1rem;
                color: #86868b;
                max-width: 600px;
                margin: 25px auto 0;
                font-weight: 400;
            }

            /* SLIDER WRAPPER */
            .aksesori-slider-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 24px;
                padding: 30px 0 50px;
                margin: 0;
                background: #ffffff;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            }

            /* SLIDER TRACK */
            .aksesori-slider-track {
                display: flex;
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                gap: 25px;
                padding: 0 60px;
            }

            /* CONTENT CARD - SEMUA CARD SAMA TINGGI */
            .content-aksesori {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.05);
                display: flex;
                flex-direction: column;
                height: 100%;
                position: relative;
                flex-shrink: 0;
            }

            .content-aksesori:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 35px rgba(0, 122, 255, 0.1);
                border-color: rgba(0, 122, 255, 0.15);
            }

            /* HEADER CARD - SEMUA GAMBAR SAMA TINGGI */
            .header-card-aksesori {
                width: 100%;
                height: 220px;
                overflow: hidden;
                background: linear-gradient(135deg, #f5f5f7 0%, #ffffff 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
            }

            .header-card-aksesori::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
            }

            .header-card-aksesori img {
                width: auto;
                height: 160px;
                max-width: 100%;
                object-fit: contain;
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .content-aksesori:hover .header-card-aksesori img {
                transform: scale(1.08) translateY(-3px);
                filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
            }

            /* FOOTER CARD - KONTEN YANG FLEKSIBEL */
            .footer-card-aksesori {
                padding: 25px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .badge-category {
                display: inline-block;
                background: rgba(0, 122, 255, 0.1);
                color: #007aff;
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 600;
                letter-spacing: 0.5px;
                margin-bottom: 15px;
                align-self: flex-start;
            }

            .nama-aksesori {
                font-size: 1.4rem;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 12px;
                line-height: 1.3;
                letter-spacing: -0.2px;
                min-height: 60px;
            }

            .deskripsi-aksesori {
                font-size: 0.95rem;
                color: #515154;
                margin-bottom: 20px;
                line-height: 1.6;
                flex-grow: 1;
            }

            /* HARGA DAN TOMBEL - VERTIKAL LAYOUT */
            .harga-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-top: auto;
            }

            .harga-aksesori {
                font-size: 1.2rem;
                font-weight: 700;
                color: #007aff;
                background: rgba(0, 122, 255, 0.08);
                padding: 12px 20px;
                border-radius: 12px;
                text-align: center;
                width: 100%;
                display: block;
                box-sizing: border-box;
            }

            .harga-asli-coret {
                font-size: 0.95rem;
                font-weight: 500;
                color: #86868b;
                text-decoration: line-through;
                text-align: center;
                display: block;
                margin-bottom: 6px;
            }

            .harga-diskon {
                color: #ff3b30 !important;
                background: rgba(255, 59, 48, 0.08) !important;
                font-size: 1.25rem !important;
                font-weight: 700 !important;
            }

            .btn-beli {
                background: #007aff;
                color: white;
                border: none;
                padding: 14px 24px;
                border-radius: 12px;
                font-size: 0.95rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
                box-sizing: border-box;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .btn-beli:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0, 113, 227, 0.25);
                background: var(--apple-blue-hover);
            }

            /* Efek ripple pada button */
            .btn-beli::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
                z-index: -1;
            }

            .btn-beli:hover::after {
                width: 200px;
                height: 200px;
            }

            .btn-beli i {
                font-size: 0.95rem;
            }

            /* NAVIGATION BUTTONS */
            .aksesori-nav-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: white;
                border: none;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-size: 1.5rem;
                color: #1d1d1f;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                z-index: 20;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.8);
            }

            .aksesori-nav-btn:hover:not(:disabled) {
                background: #007aff;
                color: white;
                transform: translateY(-50%) scale(1.1);
                box-shadow: 0 10px 25px rgba(0, 122, 255, 0.3);
            }

            .aksesori-nav-btn:disabled {
                opacity: 0.4;
                cursor: not-allowed;
                background: #f5f5f7;
                color: #86868b;
                transform: translateY(-50%);
            }

            .aksesori-nav-btn:disabled:hover {
                background: #f5f5f7;
                color: #86868b;
                transform: translateY(-50%);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            }

            .aksesori-prev {
                left: 20px;
            }

            .aksesori-next {
                right: 20px;
            }

            /* DOTS INDICATOR */
            .aksesori-dots {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 14px;
                margin-top: 30px;
            }

            .aksesori-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.1);
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                border: none;
                padding: 0;
            }

            .aksesori-dot:hover {
                background: rgba(0, 122, 255, 0.6);
                transform: scale(1.2);
            }

            .aksesori-dot.active {
                background: #007aff;
                transform: scale(1.3);
                box-shadow: 0 3px 8px rgba(0, 122, 255, 0.3);
            }

            /* RESPONSIVE DESIGN - SISTEM GRID RESPONSIF */
            /* Desktop Besar: 3 produk per slide (3-3-3) */
            @media (min-width: 1200px) {
                .aksesori-slider-track {
                    gap: 30px;
                    padding: 0 70px;
                }

                .content-aksesori {
                    width: calc((100% - 60px) / 3);
                }
            }

            /* Desktop Sedang: 3 produk per slide */
            @media (min-width: 992px) and (max-width: 1199px) {
                .aksesori-slider-track {
                    gap: 25px;
                    padding: 0 60px;
                }

                .content-aksesori {
                    width: calc((100% - 50px) / 3);
                }

                .header-card-aksesori {
                    height: 200px;
                }

                .header-card-aksesori img {
                    height: 140px;
                }

                .nama-aksesori {
                    font-size: 1.3rem;
                    min-height: 55px;
                }

                .deskripsi-aksesori {
                    font-size: 0.9rem;
                }
            }

            /* Tablet Landscape: 2 produk per slide (2-2-2-2) */
            @media (min-width: 768px) and (max-width: 991px) {
                .aksesori-slider-track {
                    gap: 25px;
                    padding: 0 60px;
                }

                .content-aksesori {
                    width: calc((100% - 25px) / 2);
                }

                .section-heading h1 {
                    font-size: 2.2rem;
                }

                .header-card-aksesori {
                    height: 200px;
                }

                .header-card-aksesori img {
                    height: 140px;
                }

                .footer-card-aksesori {
                    padding: 22px;
                }

                .nama-aksesori {
                    font-size: 1.3rem;
                    min-height: 55px;
                }

                .deskripsi-aksesori {
                    font-size: 0.9rem;
                }

                .harga-aksesori {
                    font-size: 1.15rem;
                    padding: 11px 18px;
                }

                .btn-beli {
                    padding: 12px 22px;
                    font-size: 0.9rem;
                }
            }

            /* Tablet Portrait: 2 produk per slide */
            @media (min-width: 576px) and (max-width: 767px) {
                .aksesori-slider-track {
                    gap: 20px;
                    padding: 0 60px;
                }

                .content-aksesori {
                    width: calc((100% - 20px) / 2);
                }

                .section-heading h1 {
                    font-size: 2rem;
                }

                .header-card-aksesori {
                    height: 180px;
                }

                .header-card-aksesori img {
                    height: 120px;
                }

                .footer-card-aksesori {
                    padding: 20px;
                }

                .nama-aksesori {
                    font-size: 1.2rem;
                    min-height: 50px;
                }

                .deskripsi-aksesori {
                    font-size: 0.88rem;
                }

                .harga-aksesori {
                    font-size: 1.1rem;
                    padding: 10px 16px;
                }

                .btn-beli {
                    padding: 11px 20px;
                    font-size: 0.88rem;
                }

                .aksesori-nav-btn {
                    width: 48px;
                    height: 48px;
                    font-size: 1.3rem;
                }

                .aksesori-prev {
                    left: 15px;
                }

                .aksesori-next {
                    right: 15px;
                }
            }

            /* Mobile: 1 produk per slide (1-1-1-1) */
            @media (max-width: 575px) {
                .container-aksesori {
                    margin: 30px auto;
                    padding: 0 15px;
                }

                .section-heading h1 {
                    font-size: 1.8rem;
                }

                .section-heading p {
                    font-size: 1rem;
                    padding: 0;
                }

                .aksesori-slider-wrapper {
                    padding: 20px 0 40px;
                    border-radius: 18px;
                }

                .aksesori-slider-track {
                    gap: 0;
                    padding: 0 50px;
                }

                .content-aksesori {
                    width: 100%;
                    margin: 0 5px;
                    border-radius: 16px;
                }

                .header-card-aksesori {
                    height: 180px;
                    padding: 15px;
                }

                .header-card-aksesori img {
                    height: 120px;
                }

                .footer-card-aksesori {
                    padding: 18px;
                }

                .badge-category {
                    font-size: 0.8rem;
                    padding: 5px 14px;
                }

                .nama-aksesori {
                    font-size: 1.2rem;
                    min-height: 45px;
                }

                .deskripsi-aksesori {
                    font-size: 0.88rem;
                }

                .harga-aksesori {
                    font-size: 1.1rem;
                    padding: 10px 16px;
                }

                .btn-beli {
                    padding: 12px 20px;
                    font-size: 0.9rem;
                }

                .aksesori-nav-btn {
                    width: 44px;
                    height: 44px;
                    font-size: 1.2rem;
                }

                .aksesori-prev {
                    left: 10px;
                }

                .aksesori-next {
                    right: 10px;
                }

                .aksesori-dots {
                    gap: 12px;
                    margin-top: 25px;
                }

                .aksesori-dot {
                    width: 8px;
                    height: 8px;
                }
            }

            /* Mobile Sangat Kecil */
            @media (max-width: 400px) {
                .container-aksesori {
                    padding: 0 10px;
                }

                .section-heading .section-main-title {
                    font-size: 1.5rem;
                }

                .aksesori-slider-track {
                    padding: 0 40px;
                }

                .header-card-aksesori {
                    height: 160px;
                }

                .header-card-aksesori img {
                    height: 110px;
                }

                .footer-card-aksesori {
                    padding: 16px;
                }

                .nama-aksesori {
                    font-size: 1.1rem;
                    min-height: 40px;
                }

                .deskripsi-aksesori {
                    font-size: 0.85rem;
                }

                .harga-aksesori {
                    font-size: 1rem;
                    padding: 9px 14px;
                }

                .btn-beli {
                    padding: 10px 18px;
                    font-size: 0.85rem;
                }

                .aksesori-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 1.1rem;
                }

                .aksesori-prev {
                    left: 5px;
                }

                .aksesori-next {
                    right: 5px;
                }
            }

            /* Auto-hide dots jika hanya 1 slide */
            .aksesori-dots.hidden {
                display: none;
            }

            /* Hide navigation jika hanya 1 slide */
            .aksesori-nav-btn.hidden {
                display: none;
            }

            /* Animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .content-aksesori {
                animation: fadeIn 0.6s ease forwards;
            }

            /* Equal Height for Cards */
            .equal-height {
                display: flex;
                flex-wrap: wrap;
            }
        </style>
        <!-- HEADING SECTION -->
        <div class="section-heading">
            <h1 class="section-main-title">Aksesori Unggulan Apple</h1>
        </div>

        <!-- SLIDER WRAPPER -->
        <div class="aksesori-slider-wrapper">
            <!-- Navigation Buttons -->
            <button class="aksesori-nav-btn aksesori-prev" id="aksesoriPrevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="aksesori-nav-btn aksesori-next" id="aksesoriNextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- SLIDER TRACK -->
            <div class="aksesori-slider-track" id="aksesoriSliderTrack">
                <?php if (empty($homeAksesoriFromDB)): ?>
                    <!-- Empty State -->
                    <div class="content-aksesori" style="width: 100%; min-width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background: white; border-radius: 24px; padding: 60px;">
                        <div style="width: 100px; height: 100px; background: #f5f5f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #d2d2d7;"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #1d1d1f; margin-bottom: 12px; text-align: center;">Belum Ada Aksesori Pilihan</h3>
                        <p style="color: #86868b; text-align: center; max-width: 400px; font-size: 1rem;">Kami sedang menyiapkan aksesori terbaik untuk melengkapi perangkat Apple Anda. Cek kembali segera!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($homeAksesoriFromDB as $aksesori): ?>
                        <!-- Card Dynamic -->
                        <div class="content-aksesori">
                            <div class="header-card-aksesori">
                                <img src="<?= $aksesori['image'] ?>" alt="<?= htmlspecialchars($aksesori['name']) ?>" onerror="this.src='https://via.placeholder.com/400?text=No+Image'">
                            </div>
                            <div class="footer-card-aksesori">
                                <span class="badge-category"><?= htmlspecialchars($aksesori['label']) ?></span>
                                <h3 class="nama-aksesori"><?= htmlspecialchars($aksesori['name']) ?></h3>
                                <p class="deskripsi-aksesori"><?= htmlspecialchars(mb_strimwidth($aksesori['description'], 0, 100, "...")) ?></p>
                                <div class="harga-container">
                                    <?php if (isset($aksesori['has_discount']) && $aksesori['has_discount']): ?>
                                        <span style="text-decoration: line-through; color: #86868b; margin-right: 5px; font-size: 0.9em;">Rp <?= number_format($aksesori['harga_asli'], 0, ',', '.') ?></span>
                                        <span class="harga-aksesori" style="color: #1d1d1f; font-weight: 600; font-size: 1rem;">Mulai Rp <?= number_format($aksesori['price'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="harga-aksesori">Mulai Rp <?= number_format($aksesori['price'], 0, ',', '.') ?></span>
                                    <?php endif; ?>
                                    <button class="btn-beli" onclick="location.href='checkout/checkout.php?id=<?= $aksesori['id'] ?>&type=<?= $aksesori['tipe'] ?>'">
                                        <i class="bi bi-cart-plus"></i> Beli Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Card Lihat Semua -->
                <div class="content-aksesori" onclick="location.href='products/products.php'" style="cursor: pointer; background: #f5f5f7;">
                    <div class="header-card-aksesori" style="height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 40px;">
                        <div style="width: 80px; height: 80px; background: #007aff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 10px 20px rgba(0, 122, 255, 0.2);">
                            <i class="bi bi-arrow-right" style="font-size: 2.5rem; color: white;"></i>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #1d1d1f; margin-bottom: 5px;">Lihat Semua</h3>
                        <p style="color: #86868b; text-align: center; font-size: 0.95rem;">Jelajahi berbagai pilihan aksesori lainnya</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOTS INDICATOR -->
        <div class="aksesori-dots" id="aksesoriDots"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Elemen DOM
                const sliderTrack = document.getElementById('aksesoriSliderTrack');
                const prevBtn = document.getElementById('aksesoriPrevBtn');
                const nextBtn = document.getElementById('aksesoriNextBtn');
                const dotsContainer = document.getElementById('aksesoriDots');

                // Variabel slider
                const cards = document.querySelectorAll('.content-aksesori');
                const totalCards = cards.length;
                let currentSlide = 0;
                let cardsPerView = 3; // Default 3 cards per slide untuk desktop

                // Fungsi untuk menghitung cards per view berdasarkan lebar layar
                function calculateCardsPerView() {
                    const screenWidth = window.innerWidth;

                    if (screenWidth >= 1200) return 3; // Desktop besar: 3-3-3
                    if (screenWidth >= 768) return 2; // Tablet: 2-2-2-2
                    return 1; // Mobile: 1-1-1-1
                }

                // Fungsi untuk menghitung total slides
                function calculateTotalSlides() {
                    cardsPerView = calculateCardsPerView();
                    return Math.ceil(totalCards / cardsPerView);
                }

                let totalSlides = calculateTotalSlides();

                // Fungsi untuk membuat dots indicator
                function createDots() {
                    dotsContainer.innerHTML = '';
                    totalSlides = calculateTotalSlides();

                    if (totalSlides <= 1) {
                        dotsContainer.classList.add('hidden');
                    } else {
                        dotsContainer.classList.remove('hidden');

                        for (let i = 0; i < totalSlides; i++) {
                            const dot = document.createElement('div');
                            dot.classList.add('aksesori-dot');
                            if (i === currentSlide) dot.classList.add('active');
                            dot.setAttribute('data-slide', i);
                            dot.addEventListener('click', () => goToSlide(i));
                            dotsContainer.appendChild(dot);
                        }
                    }
                }

                // Fungsi untuk update slider position
                function updateSliderPosition() {
                    // Hitung lebar satu card termasuk gap
                    const cardStyle = window.getComputedStyle(cards[0]);
                    const cardWidth = cards[0].offsetWidth;
                    const gap = parseFloat(window.getComputedStyle(sliderTrack).gap) || 25;

                    const translateX = currentSlide * cardsPerView * (cardWidth + gap);
                    sliderTrack.style.transform = `translateX(-${translateX}px)`;

                    // Update dots
                    const dots = document.querySelectorAll('.aksesori-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlide);
                    });

                    // Update navigation buttons
                    updateNavigationButtons();
                }

                // Fungsi untuk update navigation buttons
                function updateNavigationButtons() {
                    if (totalSlides <= 1) {
                        prevBtn.classList.add('hidden');
                        nextBtn.classList.add('hidden');
                        return;
                    }

                    prevBtn.classList.remove('hidden');
                    nextBtn.classList.remove('hidden');

                    prevBtn.disabled = currentSlide === 0;
                    nextBtn.disabled = currentSlide === totalSlides - 1;
                }

                // Fungsi untuk pindah slide
                function goToSlide(slideIndex) {
                    if (slideIndex < 0 || slideIndex >= totalSlides) return;

                    // Animasi untuk transisi slide
                    sliderTrack.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';

                    currentSlide = slideIndex;
                    updateSliderPosition();
                }

                // Fungsi untuk next slide
                function nextSlide() {
                    if (currentSlide < totalSlides - 1) {
                        currentSlide++;
                        updateSliderPosition();
                    }
                }

                // Fungsi untuk prev slide
                function prevSlide() {
                    if (currentSlide > 0) {
                        currentSlide--;
                        updateSliderPosition();
                    }
                }

                // Event listeners untuk navigation buttons
                prevBtn.addEventListener('click', prevSlide);
                nextBtn.addEventListener('click', nextSlide);

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        prevSlide();
                    } else if (e.key === 'ArrowRight') {
                        nextSlide();
                    }
                });

                // Touch/swipe untuk mobile
                let touchStartX = 0;
                let touchEndX = 0;

                sliderTrack.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].clientX;
                });

                sliderTrack.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].clientX;
                    const swipeThreshold = 50;
                    const diff = touchStartX - touchEndX;

                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0) {
                            nextSlide();
                        } else {
                            prevSlide();
                        }
                    }
                });

                // Handle window resize dengan debounce
                let resizeTimeout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        cardsPerView = calculateCardsPerView();
                        totalSlides = calculateTotalSlides();

                        // Reset to first slide jika current slide tidak valid setelah resize
                        if (currentSlide >= totalSlides) {
                            currentSlide = totalSlides - 1;
                        }

                        createDots();
                        updateSliderPosition();
                        updateNavigationButtons();
                    }, 250);
                });

                // Fungsi untuk menyamakan tinggi semua card secara global
                function equalizeCardHeights() {
                    if (cards.length === 0) return;

                    // Reset semua tinggi ke auto terlebih dahulu
                    cards.forEach(card => {
                        card.style.height = 'auto';
                    });

                    // Cari tinggi maksimum dari SEMUA card
                    let maxHeight = 0;
                    cards.forEach(card => {
                        const cardHeight = card.offsetHeight;
                        if (cardHeight > maxHeight) maxHeight = cardHeight;
                    });

                    // Terapkan tinggi maksimum ke semua card
                    cards.forEach(card => {
                        card.style.height = `${maxHeight}px`;
                    });
                }

                // Inisialisasi
                cardsPerView = calculateCardsPerView();
                totalSlides = calculateTotalSlides();
                createDots();
                updateNavigationButtons();
                updateSliderPosition();

                // Samakan tinggi card setelah semua gambar dimuat
                const images = document.querySelectorAll('.header-card-aksesori img');
                let loadedImages = 0;

                function checkAllImagesLoaded() {
                    loadedImages++;
                    if (loadedImages === images.length) {
                        // Tunggu sebentar agar layout stabil
                        setTimeout(() => {
                            equalizeCardHeights();
                        }, 100);
                    }
                }

                images.forEach(img => {
                    if (img.complete) {
                        checkAllImagesLoaded();
                    } else {
                        img.addEventListener('load', checkAllImagesLoaded);
                        img.addEventListener('error', checkAllImagesLoaded);
                    }
                });

                // Jika tidak ada gambar, tetap jalankan equalize
                if (images.length === 0) {
                    setTimeout(equalizeCardHeights, 100);
                }

                // Tombol beli menggunakan onclick inline untuk redirect ke checkout/checkout.php
            });
        </script>
    </div>

    <!-- layanan ibox untuk anda -->
    <div class="kelas-layanan-container">
        <style>
            body {
                background-color: #f7f7f7;
            }

            .kelas-layanan-container {
                padding: 40px 20px;
                max-width: 1400px;
                margin: 0 auto;
            }

            /* HEADING STYLES */
            .kelas-layanan-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .kelas-layanan-header h1 {
                font-size: 28px;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
                text-align: center;
                position: relative;
                padding-bottom: 15px;
            }

            .kelas-layanan-header h1::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: #007aff;
            }

            /* SLIDER WRAPPER */
            .kelas-layanan-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: white;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                margin-bottom: 40px;
            }

            .kelas-layanan-wrapper.single-slide {
                padding-bottom: 30px;
            }

            /* SLIDER */
            .kelas-layanan-slider {
                display: flex;
                transition: transform 0.5s ease-in-out;
                gap: 0;
            }

            /* SLIDE */
            .kelas-layanan-slide {
                min-width: 100%;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                flex-shrink: 0;
            }

            /* SLIDE INNER - Flex layout untuk konten */
            .kelas-layanan-slide-inner {
                display: flex;
                gap: 20px;
                width: 100%;
                padding: 10px 5px;
                justify-content: center;
            }

            /* CONTENT CARD */
            .kelas-layanan-content-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                height: 100%;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                border: 1px solid #f0f0f0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                flex: 1;
                min-width: 0;
            }

            .kelas-layanan-content-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 20px rgba(0, 122, 255, 0.1);
                border-color: #007aff;
            }

            /* IMAGE STYLES */
            .kelas-layanan-content-img {
                width: 100%;
                margin-bottom: 15px;
                display: flex;
                justify-content: center;
            }

            .kelas-layanan-content-img img {
                width: 160px;
                height: 160px;
                object-fit: contain;
                border-radius: 8px;
                background-color: #f8f9fa;
                padding: 12px;
            }

            /* TITLE STYLES */
            .kelas-layanan-title {
                font-size: 11px;
                font-weight: 600;
                color: #707070;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 8px;
            }

            /* TEXT STYLES */
            .kelas-layanan-text {
                width: 100%;
                text-align: center;
            }

            .kelas-layanan-text-heading {
                font-size: 18px;
                font-weight: 800;
                color: #333;
                margin-bottom: 12px;
                line-height: 1.3;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 50px;
            }

            .kelas-layanan-first-paragraph,
            .kelas-layanan-second-paragraph {
                font-size: 13px;
                font-weight: 600;
                color: #666;
                margin-bottom: 8px;
                line-height: 1.4;
            }

            .kelas-layanan-second-paragraph {
                font-weight: 400;
            }

            /* Line break styling for long text */
            .line-break {
                display: block;
                margin-top: 3px;
            }

            /* NAVIGATION BUTTONS */
            .kelas-layanan-controls {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: calc(100% - 40px);
                left: 20px;
                display: flex;
                justify-content: space-between;
                pointer-events: none;
                z-index: 20;
            }

            .kelas-layanan-controls.hidden {
                display: none;
            }

            .kelas-layanan-nav-btn {
                background-color: white;
                border: none;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 20px;
                color: #333;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
                pointer-events: auto;
                opacity: 1;
                visibility: visible;
            }

            .kelas-layanan-nav-btn.hidden {
                display: none;
            }

            .kelas-layanan-nav-btn:hover {
                background-color: #007aff;
                color: white;
                transform: scale(1.1);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            }

            .kelas-layanan-nav-btn:disabled {
                opacity: 0.3;
                cursor: not-allowed;
                background-color: #f0f0f0;
            }

            .kelas-layanan-nav-btn:disabled:hover {
                transform: none;
                background-color: #f0f0f0;
                color: #333;
            }

            /* DOTS INDICATOR */
            .kelas-layanan-dots {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 15px 0 25px;
                gap: 8px;
            }

            .kelas-layanan-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #ddd;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0;
            }

            .kelas-layanan-dot:hover {
                background-color: #aaa;
            }

            .kelas-layanan-dot.active {
                border-radius: 10px;
                background-color: #007aff;
            }

            .kelas-layanan-dots.hidden {
                display: none;
            }

            /* RESPONSIVE STYLES */
            /* Desktop & Tablet: 2 card per slide (≥769px) */
            @media (min-width: 769px) {
                .kelas-layanan-content-card {
                    flex: 0 0 calc(50% - 15px);
                    min-width: calc(50% - 15px);
                    max-width: none;
                }

                .kelas-layanan-slide-inner {
                    gap: 30px;
                    padding: 10px 15px;
                    justify-content: center;
                }

                .kelas-layanan-content-img img {
                    width: 180px;
                    height: 180px;
                    padding: 15px;
                }

                .kelas-layanan-text-heading {
                    font-size: 20px;
                    min-height: 55px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 14px;
                }

                .kelas-layanan-wrapper {
                    padding: 25px 0px 20px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 30px);
                    left: 15px;
                }

                .kelas-layanan-dots {
                    padding: 20px 0 15px;
                }
            }

            /* Mobile: 1 card per slide (≤768px) */
            @media (max-width: 768px) {
                .kelas-layanan-content-card {
                    flex: 0 0 100%;
                    min-width: 100%;
                    max-width: 100%;
                    padding: 25px;
                }

                .kelas-layanan-slide-inner {
                    gap: 0;
                    padding: 10px 20px;
                    justify-content: center;
                }

                .kelas-layanan-content-img img {
                    width: 180px;
                    height: 180px;
                    padding: 15px;
                }

                .kelas-layanan-text-heading {
                    font-size: 20px;
                    min-height: 55px;
                    margin-bottom: 15px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 15px;
                    margin-bottom: 10px;
                }

                .kelas-layanan-second-paragraph {
                    margin-bottom: 0;
                }

                .kelas-layanan-wrapper {
                    padding: 20px 0px 15px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 30px);
                    left: 15px;
                }

                .kelas-layanan-nav-btn {
                    width: 45px;
                    height: 45px;
                    font-size: 20px;
                }

                .kelas-layanan-header h1 {
                    font-size: 24px;
                }

                .kelas-layanan-dots {
                    padding: 15px 0 10px;
                }
            }

            /* Mobile Kecil: 1 card per slide dengan penyesuaian */
            @media (max-width: 576px) {
                .kelas-layanan-content-card {
                    padding: 20px;
                }

                .kelas-layanan-content-img img {
                    width: 150px;
                    height: 150px;
                    padding: 12px;
                }

                .kelas-layanan-text-heading {
                    font-size: 18px;
                    min-height: 50px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 14px;
                }

                .kelas-layanan-wrapper {
                    padding: 15px 0px 10px;
                }

                .kelas-layanan-controls {
                    width: calc(100% - 20px);
                    left: 10px;
                }

                .kelas-layanan-nav-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 18px;
                }

                .kelas-layanan-dots {
                    padding: 12px 0 8px;
                }
            }

            @media (max-width: 480px) {
                .kelas-layanan-container {
                    padding: 30px 15px;
                }

                .kelas-layanan-header h1 {
                    font-size: 22px;
                }

                .kelas-layanan-content-card {
                    padding: 18px;
                }

                .kelas-layanan-content-img img {
                    width: 130px;
                    height: 130px;
                    padding: 10px;
                }

                .kelas-layanan-text-heading {
                    font-size: 17px;
                    min-height: 48px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 13px;
                }
            }

            @media (max-width: 360px) {
                .kelas-layanan-content-card {
                    padding: 15px;
                }

                .kelas-layanan-content-img img {
                    width: 110px;
                    height: 110px;
                    padding: 8px;
                }

                .kelas-layanan-text-heading {
                    font-size: 16px;
                    min-height: 45px;
                }

                .kelas-layanan-first-paragraph,
                .kelas-layanan-second-paragraph {
                    font-size: 12px;
                }
            }
        </style>
        <!-- HEADING -->
        <div class="kelas-layanan-header">
            <h1>Layanan iBox untuk Anda</h1>
        </div>

        <!-- SLIDER -->
        <div class="kelas-layanan-wrapper" id="kelasLayananWrapper">
            <!-- Konten akan di-generate oleh JavaScript -->
        </div>
        <script>
            // Data layanan dengan teks yang sudah diformat
            const layananData = [{
                    id: 1,
                    title: "IN STORE",
                    heading: "Kirim atau ambil",
                    firstParagraph: "Bebas biaya kirim dan ambil sendiri",
                    secondParagraph: "Ambil pesananmu di toko iBox terdekat",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_kirim_atau_ambil_-_desktop.png"
                },
                {
                    id: 2,
                    title: "IN STORE",
                    heading: "Cari toko terdekat",
                    firstParagraph: "Belanja produk terbaru Apple",
                    secondParagraph: "Temukan cabang iBox di terdekatmu",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_toko_-_desktop.png"
                },
                {
                    id: 3,
                    title: "IN STORE",
                    heading: "Ikuti kelas",
                    firstParagraph: "Gratis setiap sesi kelasnya",
                    secondParagraph: "Sesi kreativitas gratis untukmu dalam bidang seni",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_ikuti_kelas_-_desktop.png"
                },
                {
                    id: 4,
                    title: "IN STORE",
                    heading: "Bantuan untukmu",
                    firstParagraph: "iBox selalu ada solusi untukmu",
                    secondParagraph: "Mulai dari mengatur perangkat hingga memulihkan ID Apple",
                    image: "https://cdnpro.eraspace.com/media/wysiwyg/mobileapps/foto_bantuan_untukmu_-_desktop.png"
                }
            ];

            // Fungsi untuk memformat teks dengan pemisahan kalimat
            function formatText(text, maxWords = 5) {
                // Pisahkan teks menjadi kata-kata
                const words = text.split(' ');

                // Jika kata kurang dari atau sama dengan maxWords, kembalikan teks asli
                if (words.length <= maxWords) {
                    return text;
                }

                // Hitung titik tengah
                const midPoint = Math.floor(words.length / 2);

                // Gabungkan kata-kata menjadi dua baris
                const firstLine = words.slice(0, midPoint).join(' ');
                const secondLine = words.slice(midPoint).join(' ');

                // Kembalikan teks dengan line break
                return `${firstLine}<span class="line-break">${secondLine}</span>`;
            }

            // ===== FUNGSI UNTUK KELAS LAYANAN SLIDER =====
            function renderKelasLayananSlider() {
                const wrapper = document.getElementById('kelasLayananWrapper');

                // Tentukan jumlah item per slide berdasarkan lebar layar
                const itemsPerSlide = getItemsPerSlide();
                const slideCount = Math.ceil(layananData.length / itemsPerSlide);
                const isSingleSlide = slideCount <= 1;

                console.log(`Items per slide: ${itemsPerSlide}, Slide count: ${slideCount}, Single slide: ${isSingleSlide}`);

                let html = `
                <div class="kelas-layanan-controls ${isSingleSlide ? 'hidden' : ''}">
                    <button class="kelas-layanan-nav-btn kelas-layanan-prev ${isSingleSlide ? 'hidden' : ''}" data-action="prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="kelas-layanan-nav-btn kelas-layanan-next ${isSingleSlide ? 'hidden' : ''}" data-action="next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="kelas-layanan-slider">
            `;

                // Buat slide berdasarkan jumlah item per slide
                for (let slideIndex = 0; slideIndex < slideCount; slideIndex++) {
                    html += `<div class="kelas-layanan-slide">`;
                    html += `<div class="kelas-layanan-slide-inner" data-slide-index="${slideIndex}">`;

                    // Tambahkan item ke slide ini
                    for (let itemIndex = 0; itemIndex < itemsPerSlide; itemIndex++) {
                        const dataIndex = slideIndex * itemsPerSlide + itemIndex;
                        if (dataIndex < layananData.length) {
                            html += createLayananCard(layananData[dataIndex]);
                        }
                    }

                    html += `</div></div>`;
                }

                html += `</div>`;

                // Tambahkan dots indikator
                html += `<div class="kelas-layanan-dots ${isSingleSlide ? 'hidden' : ''}" id="kelasLayananDots">`;

                for (let i = 0; i < slideCount; i++) {
                    html += `<button class="kelas-layanan-dot ${i === 0 ? 'active' : ''}" data-slide="${i}"></button>`;
                }

                html += `</div>`;

                wrapper.innerHTML = html;
                initKelasLayananSlider(itemsPerSlide, slideCount);
            }

            function getItemsPerSlide() {
                const screenWidth = window.innerWidth;

                // Mobile (≤ 768px): 1 card per slide
                if (screenWidth <= 768) {
                    return 1;
                }
                // Tablet & Desktop (> 768px): 2 cards per slide
                else {
                    return 2;
                }
            }

            function createLayananCard(item) {
                // Format teks dengan pemisahan kalimat
                const formattedHeading = formatText(item.heading, 5);
                const formattedFirstParagraph = formatText(item.firstParagraph, 5);
                const formattedSecondParagraph = formatText(item.secondParagraph, 5);

                return `
                <div class="kelas-layanan-content-card">
                    <div class="kelas-layanan-content-img">
                        <img src="${item.image}" alt="${item.heading}">
                    </div>
                    <p class="kelas-layanan-title">${item.title}</p>
                    <div class="kelas-layanan-text">
                        <h4 class="kelas-layanan-text-heading">${formattedHeading}</h4>
                        <p class="kelas-layanan-first-paragraph">${formattedFirstParagraph}</p>
                        <p class="kelas-layanan-second-paragraph">${formattedSecondParagraph}</p>
                    </div>
                </div>
            `;
            }

            function initKelasLayananSlider(itemsPerSlide, slideCount) {
                const slider = document.querySelector('.kelas-layanan-slider');
                const prevBtn = document.querySelector('.kelas-layanan-prev');
                const nextBtn = document.querySelector('.kelas-layanan-next');
                const wrapper = document.querySelector('.kelas-layanan-wrapper');
                const controls = document.querySelector('.kelas-layanan-controls');
                const dotsContainer = document.getElementById('kelasLayananDots');
                const dots = document.querySelectorAll('.kelas-layanan-dot');

                console.log(`Init slider - Slide count: ${slideCount}, Items per slide: ${itemsPerSlide}`);

                // Jika hanya ada 1 slide, sembunyikan controls dan dots
                if (slideCount <= 1) {
                    if (wrapper) {
                        wrapper.classList.add('single-slide');
                    }
                    if (controls) {
                        controls.classList.add('hidden');
                    }
                    if (dotsContainer) {
                        dotsContainer.classList.add('hidden');
                    }
                    console.log('Single slide - hiding controls and dots');
                    return;
                } else {
                    if (wrapper) {
                        wrapper.classList.remove('single-slide');
                    }
                    if (controls) {
                        controls.classList.remove('hidden');
                    }
                    if (dotsContainer) {
                        dotsContainer.classList.remove('hidden');
                    }
                    console.log('Multiple slides - showing controls and dots');
                }

                let currentSlide = 0;

                // Fungsi untuk update dots indikator
                function updateDots() {
                    dots.forEach((dot, index) => {
                        if (index === currentSlide) {
                            dot.classList.add('active');
                        } else {
                            dot.classList.remove('active');
                        }
                    });
                }

                function updateNavButtons() {
                    if (prevBtn) {
                        prevBtn.disabled = currentSlide === 0;
                        console.log(`Prev button disabled: ${prevBtn.disabled}`);
                    }
                    if (nextBtn) {
                        nextBtn.disabled = currentSlide === slideCount - 1;
                        console.log(`Next button disabled: ${nextBtn.disabled}`);
                    }
                }

                function goToSlide(slideIndex) {
                    if (slideIndex < 0 || slideIndex >= slideCount) return;

                    currentSlide = slideIndex;
                    if (slider) {
                        slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                        console.log(`Moving to slide: ${slideIndex}`);
                    }
                    updateNavButtons();
                    updateDots();
                }

                function nextSlide() {
                    if (currentSlide < slideCount - 1) {
                        goToSlide(currentSlide + 1);
                    }
                }

                function prevSlide() {
                    if (currentSlide > 0) {
                        goToSlide(currentSlide - 1);
                    }
                }

                // Event listeners untuk tombol navigasi
                if (prevBtn) {
                    prevBtn.addEventListener('click', prevSlide);
                    console.log('Prev button event listener added');
                }
                if (nextBtn) {
                    nextBtn.addEventListener('click', nextSlide);
                    console.log('Next button event listener added');
                }

                // Event listeners untuk dots
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-slide'));
                        goToSlide(slideIndex);
                    });
                });

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (slideCount <= 1) return;

                    if (e.ctrlKey && e.key === 'ArrowLeft') {
                        prevSlide();
                    } else if (e.ctrlKey && e.key === 'ArrowRight') {
                        nextSlide();
                    }
                });

                // Swipe untuk mobile
                let touchStartX = 0;
                let touchEndX = 0;

                if (slider && slideCount > 1) {
                    slider.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    });

                    slider.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        const swipeThreshold = 50;
                        const diff = touchStartX - touchEndX;

                        if (Math.abs(diff) > swipeThreshold) {
                            if (diff > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                    });
                }

                // Update tombol navigasi dan dots pertama kali
                updateNavButtons();
                updateDots();

                // Set posisi awal ke slide 0
                if (slider) {
                    slider.style.transform = `translateX(0)`;
                }
            }

            // ===== INISIALISASI =====
            document.addEventListener('DOMContentLoaded', function() {
                renderKelasLayananSlider();

                // Handle resize - re-render slider saat ukuran layar berubah
                let resizeTimeout;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        renderKelasLayananSlider();
                    }, 250);
                });
            });
        </script>
    </div>

    <!-- layanan lengkap -->
    <div class="layanan-lengkap-untuk-anda-container">
        <style>
            body {
                background-color: #f8f9fa;
                color: #333;
                line-height: 1.6;
            }

            /* CONTAINER UTAMA */
            .layanan-lengkap-untuk-anda-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 0 20px;
            }

            /* SERVICES SECTION - GRID untuk desktop, FLEX untuk mobile */
            .layanan-lengkap-untuk-anda-services {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                align-items: stretch;
                /* Pastikan semua card sama tinggi */
            }

            /* SERVICE CARD */
            .layanan-lengkap-untuk-anda-card {
                background-color: white;
                border-radius: 16px;
                padding: 40px 35px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                border: 1px solid #eaeaea;
                display: flex;
                flex-direction: column;
                height: 100%;
                /* Pastikan semua card sama tinggi */
            }

            .layanan-lengkap-untuk-anda-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0, 122, 255, 0.15);
                border-color: #007aff;
            }

            /* Title Styles - SAMA untuk semua card */
            .layanan-lengkap-untuk-anda-card h2 {
                font-size: 28px;
                font-weight: 700;
                color: #1a1a1a;
                margin-bottom: 20px;
                line-height: 1.3;
                min-height: 68px;
                /* Pastikan semua judul sama tinggi */
                display: flex;
                align-items: center;
            }

            /* Content Styles */
            .layanan-lengkap-untuk-anda-card p {
                font-size: 16.5px;
                color: #555;
                margin-bottom: 30px;
                line-height: 1.7;
                flex-grow: 1;
                /* Konten tumbuh sama rata */
            }

            /* Link Styles */
            .layanan-lengkap-untuk-anda-link {
                display: inline-flex;
                align-items: center;
                color: #007aff;
                text-decoration: none;
                font-weight: 600;
                font-size: 16.5px;
                transition: all 0.2s ease;
                padding: 10px 0;
                border-top: 1px solid #eee;
                margin-top: auto;
                /* Dorong link ke bawah */
                padding-top: 20px;
            }

            .layanan-lengkap-untuk-anda-link:hover {
                color: #0056cc;
            }

            .layanan-lengkap-untuk-anda-link::after {
                content: "→";
                margin-left: 8px;
                font-weight: 700;
                font-size: 18px;
                transition: transform 0.2s ease;
            }

            .layanan-lengkap-untuk-anda-link:hover::after {
                transform: translateX(5px);
            }

            /* HR Styles */
            .layanan-lengkap-untuk-anda-card hr {
                border: none;
                height: 2px;
                background: linear-gradient(90deg, #007aff, transparent);
                margin: 25px 0;
                opacity: 0.3;
            }

            /* RESPONSIVE DESIGN */
            /* Desktop Sedang */
            @media (max-width: 1200px) {
                .layanan-lengkap-untuk-anda-services {
                    gap: 25px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 35px 30px;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 26px;
                    min-height: 62px;
                }
            }

            /* Tablet (1023px) - 2 Kolom (Atas 2, Bawah 1) */
            @media (max-width: 1023px) {

                .layanan-lengkap-untuk-anda-container {
                    max-width: 100%;
                }

                .layanan-lengkap-untuk-anda-services {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 20px;
                }

                /* Card ke-3 akan otomatis mengikuti grid layout (di kiri, lebar 50%) */

                .layanan-lengkap-untuk-anda-card {
                    padding: 30px 25px;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 24px;
                    min-height: 58px;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 16px;
                }
            }

            /* Tablet Kecil - Tetap 2 Kolom atau adjustment padding */
            @media (max-width: 768px) {

                .layanan-lengkap-untuk-anda-services {
                    gap: 15px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 25px 20px;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 22px;
                    min-height: 52px;
                    margin-bottom: 15px;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 15.5px;
                    margin-bottom: 20px;
                }

                .layanan-lengkap-untuk-anda-link {
                    font-size: 16px;
                    padding-top: 15px;
                }
            }

            /* MOBILE - BERUBAH MENJADI COLUMN (768px ke bawah) */
            @media (max-width: 767px) {
                .layanan-lengkap-untuk-anda-services {
                    display: flex !important;
                    flex-direction: column;
                    gap: 20px;
                    grid-template-columns: 1fr !important;
                }

                .layanan-lengkap-untuk-anda-card {
                    width: 100% !important;
                    flex: 1 1 auto !important;
                    min-height: auto !important;
                    padding: 30px 25px !important;
                    margin-bottom: 0 !important;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 24px !important;
                    min-height: auto !important;
                    margin-bottom: 15px !important;
                    align-items: flex-start !important;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 16px !important;
                    margin-bottom: 20px !important;
                }

                .layanan-lengkap-untuk-anda-link {
                    font-size: 16.5px !important;
                    padding-top: 20px !important;
                }

                .layanan-lengkap-untuk-anda-card hr {
                    margin: 20px 0 !important;
                }
            }

            /* Mobile Sedang */
            @media (max-width: 576px) {
                .layanan-lengkap-untuk-anda-services {
                    gap: 18px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 25px 20px !important;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 22px !important;
                    margin-bottom: 12px !important;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 15.5px !important;
                    margin-bottom: 18px !important;
                }

                .layanan-lengkap-untuk-anda-link {
                    font-size: 16px !important;
                    padding-top: 18px !important;
                }

                .layanan-lengkap-untuk-anda-card hr {
                    margin: 18px 0 !important;
                }
            }

            /* Mobile Kecil */
            @media (max-width: 480px) {
                .layanan-lengkap-untuk-anda-services {
                    gap: 15px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 22px 18px !important;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 20px !important;
                    margin-bottom: 10px !important;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 15px !important;
                    margin-bottom: 15px !important;
                }

                .layanan-lengkap-untuk-anda-link {
                    font-size: 15.5px !important;
                    padding-top: 15px !important;
                }

                .layanan-lengkap-untuk-anda-card hr {
                    margin: 15px 0 !important;
                }
            }

            /* Mobile Sangat Kecil */
            @media (max-width: 360px) {

                .layanan-lengkap-untuk-anda-services {
                    gap: 12px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 20px 16px !important;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 19px !important;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 14.5px !important;
                }

                .layanan-lengkap-untuk-anda-link {
                    font-size: 15px !important;
                }
            }

            /* Desktop Lebar */
            @media (min-width: 1400px) {
                .layanan-lengkap-untuk-anda-services {
                    gap: 40px;
                }

                .layanan-lengkap-untuk-anda-card {
                    padding: 45px 40px;
                }

                .layanan-lengkap-untuk-anda-card h2 {
                    font-size: 30px;
                    min-height: 72px;
                }

                .layanan-lengkap-untuk-anda-card p {
                    font-size: 17px;
                }
            }
        </style>
        <!-- SERVICES - DESKTOP: 3 CARDS SEJAJAR, MOBILE: COLUMN -->
        <div class="layanan-lengkap-untuk-anda-services">
            <!-- Service 1 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Dapatkan servis dan bantuan profesional</h2>
                <p>Mulai dari pengaturan device terbaru-mu hingga servis, dapatkan pengalaman terbaiknya dengan tim ahli kami yang siap membantu.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>

            <!-- Service 2 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Simulasi kredit dan cicilan</h2>
                <p>Berbagai pilihan pembayaran kredit dan cicilan yang fleksibel. Temukan semua pilihanmu dengan kemudahan dan transparansi.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>

            <!-- Service 3 -->
            <article class="layanan-lengkap-untuk-anda-card">
                <h2>Beli online, ambil di toko</h2>
                <p>Belanja online dengan mudah dan bebas biaya kirim. Pesan sekarang dan ambil produkmu di toko iBox terdekat kapan saja.</p>
                <hr>
                <a href="#" class="layanan-lengkap-untuk-anda-link">Lebih lanjut</a>
            </article>
        </div>
        <script>
            // Efek hover untuk kartu layanan
            document.addEventListener('DOMContentLoaded', function() {
                const serviceCards = document.querySelectorAll('.layanan-lengkap-untuk-anda-card');
                const serviceLinks = document.querySelectorAll('.layanan-lengkap-untuk-anda-link');

                // Efek hover card (hanya di desktop)
                serviceCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        if (window.innerWidth > 767) {
                            this.style.transform = 'translateY(-10px)';
                        }
                    });

                    card.addEventListener('mouseleave', function() {
                        if (window.innerWidth > 767) {
                            this.style.transform = 'translateY(0)';
                        }
                    });
                });

                // Efek klik pada link "Lebih lanjut"
                serviceLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const cardTitle = this.closest('.layanan-lengkap-untuk-anda-card').querySelector('h2').textContent;
                        alert(`Anda mengklik "${cardTitle}"\nFitur ini akan membawa Anda ke halaman detail layanan.`);
                    });
                });

                // Fungsi untuk mereset height card (hanya di desktop)
                function resetCardHeights() {
                    const cards = document.querySelectorAll('.layanan-lengkap-untuk-anda-card');

                    if (window.innerWidth > 767) {
                        // Desktop: semua card sama tinggi
                        let maxHeight = 0;

                        // Reset heights dulu
                        cards.forEach(card => {
                            card.style.height = 'auto';
                        });

                        // Cari height tertinggi
                        cards.forEach(card => {
                            const height = card.offsetHeight;
                            if (height > maxHeight) {
                                maxHeight = height;
                            }
                        });

                        // Terapkan height yang sama
                        cards.forEach(card => {
                            card.style.height = maxHeight + 'px';
                        });
                    } else {
                        // Mobile: height auto (natural height)
                        cards.forEach(card => {
                            card.style.height = 'auto';
                        });
                    }
                }

                // Jalankan saat load dan resize
                window.addEventListener('load', resetCardHeights);
                window.addEventListener('resize', resetCardHeights);
            });
        </script>
    </div>

    <!-- apple care -->
    <div class="container-about-apple-care">
        <style>
            body {
                background: #f8f9fa;
                color: #1d1d1f;
            }

            /* CONTAINER UTAMA */
            .container-about-apple-care {
                max-width: 1400px;
                margin: 80px auto 60px;
                padding: 0 20px;
            }

            /* JUDUL YANG LEBIH RAPI */
            .apple-care-header {
                text-align: center;
                padding-bottom: 25px;
                position: relative;
            }

            .apple-care-header::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 120px;
                height: 4px;
                background: linear-gradient(90deg, #007aff, #0056d6);
                border-radius: 2px;
            }

            .apple-care-main-title {
                font-size: 2.8rem;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 15px;
                letter-spacing: -0.5px;
                line-height: 1.2;
            }

            .apple-care-subtitle {
                font-size: 1.2rem;
                color: #86868b;
                max-width: 600px;
                margin: 0 auto;
                font-weight: 400;
                line-height: 1.6;
            }

            /* GRID CARDS */
            .cards-apple-care {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 30px;
                padding: 20px 0;
            }

            /* CARD STYLE */
            .card-apple-care {
                background: white;
                border-radius: 16px;
                padding: 25px;
                box-shadow:
                    0 8px 30px rgba(0, 0, 0, 0.08),
                    0 2px 8px rgba(0, 0, 0, 0.03);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.05);
                position: relative;
                overflow: hidden;
            }

            .card-apple-care:hover {
                transform: translateY(-8px);
                box-shadow:
                    0 15px 35px rgba(0, 122, 255, 0.1),
                    0 5px 15px rgba(0, 0, 0, 0.05);
                border-color: rgba(0, 122, 255, 0.15);
            }

            /* EFEK TOP BORDER PADA HOVER */
            .card-apple-care::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, #007aff, #0056d6);
                transform: scaleX(0);
                transition: transform 0.3s ease;
                transform-origin: left;
            }

            .card-apple-care:hover::before {
                transform: scaleX(1);
            }

            /* GAMBAR CARD */
            .card-apple-care img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                border-radius: 12px;
                margin-bottom: 20px;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-apple-care:hover img {
                transform: scale(1.03);
            }

            /* JUDUL CARD */
            .card-apple-care .apple-care-card-title {
                color: #1d1d1f;
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 15px;
                line-height: 1.3;
            }

            /* FITUR CARD */
            .applecare-feature {
                color: #515154;
                font-size: 0.95rem;
                margin: 12px 0;
                padding-left: 28px;
                position: relative;
                line-height: 1.5;
            }

            .applecare-feature::before {
                content: "✓";
                position: absolute;
                left: 0;
                top: 0;
                color: #007aff;
                font-weight: bold;
                background: rgba(0, 122, 255, 0.1);
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
            }

            /* BADGE HARGA */
            .applecare-price {
                display: inline-block;
                background: rgba(0, 122, 255, 0.1);
                color: #007aff;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                margin-top: 20px;
                letter-spacing: 0.5px;
                border: 1px solid rgba(0, 122, 255, 0.2);
            }

            /* RESPONSIVE DESIGN */
            /* Desktop Lebar */
            @media (min-width: 1400px) {
                .apple-care-main-title {
                    font-size: 1.5rem;
                }

                .apple-care-subtitle {
                    font-size: 1.25rem;
                }

                .card-apple-care {
                    padding: 28px;
                }

                .card-apple-care img {
                    height: 220px;
                }

                .card-apple-care .apple-care-card-title {
                    font-size: 1.6rem;
                }
            }

            /* Tablet */
            @media (max-width: 1024px) {
                .cards-apple-care {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 25px;
                }

                .container-about-apple-care {
                    margin: 60px auto 40px;
                }

                .apple-care-header {
                    padding-bottom: 22px;
                }

                .apple-care-main-title {
                    font-size: 2.3rem;
                }

                .apple-care-subtitle {
                    font-size: 1.1rem;
                    padding: 0 20px;
                }

                .card-apple-care {
                    padding: 22px;
                }

                .card-apple-care img {
                    height: 180px;
                }

                .card-apple-care .apple-care-card-title {
                    font-size: 1.4rem;
                }
            }

            /* Mobile */
            @media (max-width: 768px) {
                .cards-apple-care {
                    grid-template-columns: 1fr;
                    gap: 25px;
                    margin: 0 auto;
                }

                .container-about-apple-care {
                    margin: 50px auto 30px;
                    padding: 0 15px;
                }

                .apple-care-header {
                    padding-bottom: 20px;
                }

                .apple-care-main-title {
                    font-size: 2rem;
                    margin-bottom: 12px;
                }

                .apple-care-subtitle {
                    font-size: 1rem;
                    padding: 0 10px;
                }

                .apple-care-header::after {
                    width: 100px;
                    height: 3px;
                }

                .card-apple-care {
                    padding: 20px;
                    border-radius: 14px;
                }

                .card-apple-care img {
                    height: 160px;
                }

                .card-apple-care .apple-care-card-title {
                    font-size: 1.3rem;
                }

                .applecare-feature {
                    font-size: 0.9rem;
                }
            }

            /* Small Mobile */
            @media (max-width: 480px) {
                .container-about-apple-care {
                    margin: 40px auto 20px;
                    padding: 0 10px;
                }

                .apple-care-main-title {
                    font-size: 1.7rem;
                }

                .apple-care-subtitle {
                    font-size: 0.95rem;
                }

                .card-apple-care {
                    padding: 18px;
                }

                .card-apple-care img {
                    height: 150px;
                }

                .card-apple-care .apple-care-card-title {
                    font-size: 1.2rem;
                }

                .applecare-feature {
                    font-size: 0.85rem;
                }
            }

            /* Animasi untuk cards */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card-apple-care {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 0;
            }

            .card-apple-care:nth-child(1) {
                animation-delay: 0.1s;
            }

            .card-apple-care:nth-child(2) {
                animation-delay: 0.2s;
            }

            .card-apple-care:nth-child(3) {
                animation-delay: 0.3s;
            }
        </style>
        <!-- HEADER SECTION YANG RAPI -->
        <div class="apple-care-header">
            <h1 class="apple-care-main-title">AppleCare+</h1>
        </div>

        <!-- GRID CARDS -->
        <div class="cards-apple-care">
            <!-- Card 1 - MacBook Air -->
            <a href="other/apple care/apple-care.php">
                <div class="card-apple-care">
                <img src="https://esmeralda.cygnuss-district8.com/media/wysiwyg/ibox-v4/images/applecare/applecare-macbook-air.png" alt="MacBook Air">
                <h3 class="apple-care-card-title">MacBook Air</h3>
                <p class="applecare-feature">Perlindungan hingga 500,000 kerusakan</p>
                <p class="applecare-feature">Garansi 3 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">Dukungan teknis prioritas</p>
                <span class="applecare-price">Mulai Rp 1.499.000/tahun</span>
            </div>
            </a>

            <!-- Card 2 - MacBook Pro -->
            <a href="other/apple care/apple-care.php">
                <div class="card-apple-care">
                <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="MacBook Pro">
                <h3 class="apple-care-card-title">MacBook Pro</h3>
                <p class="applecare-feature">Perlindungan hingga 600,000 kerusakan</p>
                <p class="applecare-feature">Garansi 4 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">Battery service coverage</p>
                <span class="applecare-price">Mulai Rp 1.999.000/tahun</span>
            </div>
            </a>

            <!-- Card 3 - iMac -->
            <a href="other/apple care/apple-care.php">
                <div class="card-apple-care">
                <img src="https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="iMac">
                <h3 class="apple-care-card-title">iMac</h3>
                <p class="applecare-feature">Perlindungan hingga 2,000,000 kerusakan</p>
                <p class="applecare-feature">Garansi 2 tahun untuk hardware</p>
                <p class="applecare-feature">Coverage untuk accidental damage</p>
                <p class="applecare-feature">On-site service available</p>
                <span class="applecare-price">Mulai Rp 2.499.000/tahun</span>
            </div>
            </a>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animasi untuk header saat scroll
                const header = document.querySelector('.apple-care-header');

                // Intersection Observer untuk animasi header
                const observerOptions = {
                    threshold: 0.2,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            header.style.opacity = '1';
                            header.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Set initial state
                header.style.opacity = '0';
                header.style.transform = 'translateY(-20px)';
                header.style.transition = 'opacity 0.8s ease, transform 0.8s ease';

                observer.observe(header);

                // Efek hover dan klik pada cards
                const cards = document.querySelectorAll('.card-apple-care');

                cards.forEach(card => {
                    // Efek hover
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                    });

                    card.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('clicked')) {
                            this.style.transform = 'translateY(0)';
                        }
                    });

                    // Efek klik
                    card.addEventListener('click', function() {
                        const productName = this.querySelector('.apple-care-card-title').textContent;
                        const productPrice = this.querySelector('.applecare-price').textContent;

                        // Animasi klik
                        this.style.transform = 'scale(0.98)';
                        this.style.transition = 'transform 0.2s ease';

                        setTimeout(() => {
                            this.style.transform = 'translateY(-8px)';

                            // Simulasi modal atau alert
                            showAppleCareModal(productName, productPrice);
                        }, 200);
                    });
                });

                // Fungsi untuk menampilkan modal AppleCare
                function showAppleCareModal(productName, price) {
                    // Buat modal
                    const modal = document.createElement('div');
                    modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;

                    const modalContent = document.createElement('div');
                    modalContent.style.cssText = `
                    background: white;
                    padding: 30px;
                    border-radius: 16px;
                    max-width: 400px;
                    width: 90%;
                    text-align: center;
                    transform: translateY(-20px);
                    transition: transform 0.3s ease;
                `;

                    modalContent.innerHTML = `
                    <h3 style="color: #1d1d1f; margin-bottom: 15px;">AppleCare+ untuk ${productName}</h3>
                    <p style="color: #515154; margin-bottom: 20px; line-height: 1.6;">
                        ${price}<br>
                        Perlindungan komprehensif termasuk accidental damage, dukungan teknis 24/7, dan garansi hardware.
                    </p>
                    <button class="modal-close-btn" style="
                        background: #007aff;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                    ">Tutup</button>
                `;

                    modal.appendChild(modalContent);
                    document.body.appendChild(modal);

                    // Animasi masuk
                    setTimeout(() => {
                        modal.style.opacity = '1';
                        modalContent.style.transform = 'translateY(0)';
                    }, 10);

                    // Fungsi tutup modal
                    const closeModal = () => {
                        modal.style.opacity = '0';
                        modalContent.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            document.body.removeChild(modal);
                        }, 300);
                    };

                    // Event listener untuk tombol tutup
                    modalContent.querySelector('.modal-close-btn').addEventListener('click', closeModal);

                    // Tutup modal saat klik di luar
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            closeModal();
                        }
                    });

                    // Tutup modal dengan ESC
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            closeModal();
                        }
                    });
                }

                // Animasi cards saat scroll
                const cardObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                cards.forEach(card => {
                    cardObserver.observe(card);
                });
            });
        </script>
    </div>

    <!-- checkout produk -->
    <div class="container-checkout">
        <?php
        // Query untuk mengambil data checkout dari database
        $checkout_query = "SELECT 
            hc.*,
            CASE 
                WHEN hc.tipe_produk = 'iphone' THEN iphone.nama_produk
                WHEN hc.tipe_produk = 'ipad' THEN ipad.nama_produk
                WHEN hc.tipe_produk = 'mac' THEN mac.nama_produk
                WHEN hc.tipe_produk = 'music' THEN music.nama_produk
                WHEN hc.tipe_produk = 'watch' THEN watch.nama_produk
                WHEN hc.tipe_produk = 'aksesoris' THEN aksesoris.nama_produk
                WHEN hc.tipe_produk = 'airtag' THEN airtag.nama_produk
            END as nama_produk,
            CASE 
                WHEN hc.tipe_produk = 'iphone' THEN iphone_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'ipad' THEN ipad_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'mac' THEN mac_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'music' THEN music_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'watch' THEN watch_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'aksesoris' THEN aksesoris_gambar.foto_thumbnail
                WHEN hc.tipe_produk = 'airtag' THEN airtag_gambar.foto_thumbnail
            END as foto_thumbnail,
            CASE 
                WHEN hc.deskripsi_produk IS NOT NULL AND hc.deskripsi_produk != '' THEN hc.deskripsi_produk
                WHEN hc.tipe_produk = 'iphone' THEN iphone.deskripsi_produk
                WHEN hc.tipe_produk = 'ipad' THEN ipad.deskripsi_produk
                WHEN hc.tipe_produk = 'mac' THEN mac.deskripsi_produk
                WHEN hc.tipe_produk = 'music' THEN music.deskripsi_produk
                WHEN hc.tipe_produk = 'watch' THEN watch.deskripsi_produk
                WHEN hc.tipe_produk = 'aksesoris' THEN aksesoris.deskripsi_produk
                WHEN hc.tipe_produk = 'airtag' THEN airtag.deskripsi_produk
            END as deskripsi_produk,
            CASE 
                WHEN hc.tipe_produk = 'iphone' THEN (SELECT MIN(harga) FROM admin_produk_iphone_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'ipad' THEN (SELECT MIN(harga) FROM admin_produk_ipad_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'mac' THEN (SELECT MIN(harga) FROM admin_produk_mac_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'music' THEN (SELECT MIN(harga) FROM admin_produk_music_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'watch' THEN (SELECT MIN(harga) FROM admin_produk_watch_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'aksesoris' THEN (SELECT MIN(harga) FROM admin_produk_aksesoris_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'airtag' THEN (SELECT MIN(harga) FROM admin_produk_airtag_kombinasi WHERE produk_id = hc.produk_id)
            END as harga_asli,
            CASE 
                WHEN hc.tipe_produk = 'iphone' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_iphone_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'ipad' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_ipad_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'mac' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_mac_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'music' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_music_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'watch' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_watch_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'aksesoris' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_aksesoris_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'airtag' THEN (SELECT MIN(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN harga_diskon ELSE harga END) FROM admin_produk_airtag_kombinasi WHERE produk_id = hc.produk_id)
            END as harga_terendah,
            CASE 
                WHEN hc.tipe_produk = 'iphone' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_iphone_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'ipad' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_ipad_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'mac' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_mac_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'music' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_music_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'watch' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_watch_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'aksesoris' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_aksesoris_kombinasi WHERE produk_id = hc.produk_id)
                WHEN hc.tipe_produk = 'airtag' THEN (SELECT MAX(CASE WHEN harga_diskon > 0 AND harga_diskon IS NOT NULL THEN 1 ELSE 0 END) FROM admin_produk_airtag_kombinasi WHERE produk_id = hc.produk_id)
            END as has_discount
          FROM home_checkout hc
          LEFT JOIN admin_produk_iphone iphone ON hc.tipe_produk = 'iphone' AND hc.produk_id = iphone.id
          LEFT JOIN admin_produk_ipad ipad ON hc.tipe_produk = 'ipad' AND hc.produk_id = ipad.id
          LEFT JOIN admin_produk_mac mac ON hc.tipe_produk = 'mac' AND hc.produk_id = mac.id
          LEFT JOIN admin_produk_music music ON hc.tipe_produk = 'music' AND hc.produk_id = music.id
          LEFT JOIN admin_produk_watch watch ON hc.tipe_produk = 'watch' AND hc.produk_id = watch.id
          LEFT JOIN admin_produk_aksesoris aksesoris ON hc.tipe_produk = 'aksesoris' AND hc.produk_id = aksesoris.id
          LEFT JOIN admin_produk_airtag airtag ON hc.tipe_produk = 'airtag' AND hc.produk_id = airtag.id
          LEFT JOIN admin_produk_iphone_gambar iphone_gambar ON hc.tipe_produk = 'iphone' AND hc.produk_id = iphone_gambar.produk_id
          LEFT JOIN admin_produk_ipad_gambar ipad_gambar ON hc.tipe_produk = 'ipad' AND hc.produk_id = ipad_gambar.produk_id
          LEFT JOIN admin_produk_mac_gambar mac_gambar ON hc.tipe_produk = 'mac' AND hc.produk_id = mac_gambar.produk_id
          LEFT JOIN admin_produk_music_gambar music_gambar ON hc.tipe_produk = 'music' AND hc.produk_id = music_gambar.produk_id
          LEFT JOIN admin_produk_watch_gambar watch_gambar ON hc.tipe_produk = 'watch' AND hc.produk_id = watch_gambar.produk_id
          LEFT JOIN admin_produk_aksesoris_gambar aksesoris_gambar ON hc.tipe_produk = 'aksesoris' AND hc.produk_id = aksesoris_gambar.produk_id
          LEFT JOIN admin_produk_airtag_gambar airtag_gambar ON hc.tipe_produk = 'airtag' AND hc.produk_id = airtag_gambar.produk_id
          GROUP BY hc.id
          ORDER BY hc.urutan ASC, hc.created_at DESC";
        $checkout_result = mysqli_query($db, $checkout_query);
        $checkout_items = [];
        while ($row = mysqli_fetch_assoc($checkout_result)) {
            $checkout_items[] = $row;
        }
        ?>
        <style>
            body {
                background-color: #f8f9fa;
                color: #1d1d1f;
                line-height: 1.6;
                overflow-x: hidden;
            }

            /* CONTAINER UTAMA CHECKOUT */
            .container-checkout {
                width: 100%;
                max-width: 1400px;
                margin: 50px auto 30px;
                padding: 0 20px;
                position: relative;
            }

            /* HEADING STYLE CHECKOUT */
            .section-heading-checkout {
                text-align: center;
                margin-bottom: 25px;
                position: relative;
            }

            .section-heading-checkout h1 {
                font-size: 2.5rem;
                font-weight: 700;
                color: #1d1d1f;
                display: inline-block;
                position: relative;
                margin-bottom: 15px;
                letter-spacing: -0.5px;
            }

            .section-heading-checkout h1::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 4px;
                background: linear-gradient(90deg, #007aff, #0056d6);
                border-radius: 2px;
            }

            .section-heading-checkout p {
                font-size: 1.1rem;
                color: #86868b;
                max-width: 600px;
                margin: 0 auto;
                font-weight: 400;
            }

            /* SLIDER WRAPPER CHECKOUT */
            .checkout-slider-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 24px;
                padding: 30px 0;
                margin: 0 20px;
                background: #ffffff;
                box-shadow:
                    0 10px 30px rgba(0, 0, 0, 0.05);
                border: 1px solid rgba(0, 122, 255, 0.08);
            }

            /* SLIDER TRACK CHECKOUT */
            .checkout-slider-track {
                display: flex;
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                gap: 30px;
                padding: 0 60px;
                align-items: stretch;
            }

            /* CONTENT CARD CHECKOUT */
            .content-checkout {
                flex: 0 0 calc(50% - 15px);
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow:
                    0 8px 30px rgba(0, 0, 0, 0.08),
                    0 2px 8px rgba(0, 0, 0, 0.03);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0, 0, 0, 0.05);
                display: flex;
                flex-direction: column;
                min-height: 100%;
                height: auto;
                position: relative;
            }

            .content-checkout:hover {
                transform: translateY(-8px);
                box-shadow:
                    0 15px 35px rgba(0, 122, 255, 0.1),
                    0 5px 15px rgba(0, 0, 0, 0.05);
                border-color: rgba(0, 122, 255, 0.15);
            }

            /* HEADER CARD CHECKOUT */
            .header-card-checkout {
                width: 100%;
                height: 200px;
                overflow: hidden;
                background: linear-gradient(135deg, #f5f5f7 0%, #ffffff 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                flex-shrink: 0;
            }

            .header-card-checkout::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
            }

            .header-card-checkout img {
                width: auto;
                height: 140px;
                max-width: 100%;
                object-fit: contain;
                filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .content-checkout:hover .header-card-checkout img {
                transform: scale(1.08) translateY(-3px);
                filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
            }

            /* FOOTER CARD CHECKOUT */
            .footer-card-checkout {
                padding: 25px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
                min-height: 220px;
            }

            /* BADGE CHECKOUT - WARNA iBOX */
            .badge-checkout {
                display: inline-block;
                background: linear-gradient(135deg, rgba(0, 122, 255, 0.12), rgba(0, 122, 255, 0.08));
                color: #007aff;
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 600;
                letter-spacing: 0.5px;
                margin-bottom: 15px;
                align-self: flex-start;
                border: 1px solid rgba(0, 122, 255, 0.15);
                box-shadow: 0 2px 4px rgba(0, 122, 255, 0.05);
            }

            /* Variasi warna badge untuk tipe berbeda */
            .badge-checkout.stock {
                background: linear-gradient(135deg, rgba(52, 199, 89, 0.12), rgba(52, 199, 89, 0.08));
                color: #34c759;
                border-color: rgba(52, 199, 89, 0.15);
            }

            .badge-checkout.bestseller {
                background: linear-gradient(135deg, rgba(255, 149, 0, 0.12), rgba(255, 149, 0, 0.08));
                color: #ff9500;
                border-color: rgba(255, 149, 0, 0.15);
            }

            .badge-checkout.new {
                background: linear-gradient(135deg, rgba(255, 45, 85, 0.12), rgba(255, 45, 85, 0.08));
                color: #ff2d55;
                border-color: rgba(255, 45, 85, 0.15);
            }

            .nama-checkout {
                font-size: 1.4rem;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 12px;
                line-height: 1.3;
                letter-spacing: -0.2px;
                min-height: 40px;
                display: flex;
                align-items: center;
            }

            .deskripsi-checkout {
                font-size: 0.95rem;
                color: #515154;
                margin-bottom: 20px;
                line-height: 1.6;
                flex-grow: 1;
                min-height: 70px;
            }

            .harga-container-checkout {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: auto;
                padding-top: 20px;
                border-top: 1px solid rgba(0, 0, 0, 0.05);
                flex-shrink: 0;
            }

            .harga-checkout {
                font-size: 1.2rem;
                font-weight: 700;
                color: #007aff;
                background: linear-gradient(135deg, rgba(0, 122, 255, 0.08), rgba(0, 122, 255, 0.04));
                padding: 10px 18px;
                border-radius: 12px;
                display: inline-block;
                white-space: nowrap;
                border: 1px solid rgba(0, 122, 255, 0.1);
            }

            .harga-container-checkout .harga-asli-coret {
                font-size: 0.9rem;
                font-weight: 500;
                color: #86868b;
                text-decoration: line-through;
                display: block;
                margin-bottom: 6px;
                text-align: left;
            }

            .harga-container-checkout .harga-diskon {
                color: #ff3b30 !important;
                background: linear-gradient(135deg, rgba(255, 59, 48, 0.08), rgba(255, 59, 48, 0.04)) !important;
                border: 1px solid rgba(255, 59, 48, 0.1) !important;
                font-size: 1.25rem !important;
            }

            /* TOMBOL CHECKOUT - WARNA iBOX */
            .btn-checkout {
                background: linear-gradient(135deg, #007aff 0%, #0056d6 100%);
                color: white;
                border: none;
                padding: 10px 22px;
                border-radius: 12px;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
                white-space: nowrap;
                position: relative;
                overflow: hidden;
            }

            .btn-checkout::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: 0.5s;
            }

            .btn-checkout:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
                background: linear-gradient(135deg, #0056d6 0%, #0041a8 100%);
            }

            .btn-checkout:hover::before {
                left: 100%;
            }

            .btn-checkout i {
                font-size: 0.95rem;
            }

            /* NAVIGATION BUTTONS CHECKOUT - WARNA iBOX */
            .checkout-nav-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: white;
                border: none;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-size: 1.5rem;
                color: #1d1d1f;
                box-shadow:
                    0 6px 20px rgba(0, 0, 0, 0.1),
                    0 2px 4px rgba(0, 0, 0, 0.06);
                z-index: 20;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.8);
            }

            .checkout-nav-btn:hover {
                background: #007aff;
                color: white;
                transform: translateY(-50%) scale(1.1);
                box-shadow:
                    0 10px 25px rgba(0, 122, 255, 0.3),
                    0 4px 8px rgba(0, 122, 255, 0.1);
            }

            .checkout-nav-btn:disabled {
                opacity: 0.4;
                cursor: not-allowed;
                background: #f5f5f7;
                color: #86868b;
                transform: translateY(-50%);
            }

            .checkout-nav-btn:disabled:hover {
                background: #f5f5f7;
                color: #86868b;
                transform: translateY(-50%);
                box-shadow:
                    0 6px 20px rgba(0, 0, 0, 0.1),
                    0 2px 4px rgba(0, 0, 0, 0.06);
            }

            .checkout-prev {
                left: 20px;
            }

            .checkout-next {
                right: 20px;
            }

            /* DOTS INDICATOR CHECKOUT - WARNA iBOX */
            .checkout-dots {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 14px;
                margin-top: 40px;
            }

            .checkout-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.1);
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                border: none;
                padding: 0;
            }

            .checkout-dot:hover {
                background: rgba(0, 122, 255, 0.6);
                transform: scale(1.2);
            }

            .checkout-dot.active {
                background: #007aff;
                transform: scale(1.3);
                box-shadow: 0 3px 8px rgba(0, 122, 255, 0.3);
            }

            /* RESPONSIVE DESIGN CHECKOUT */
            /* Desktop Lebar */
            @media (min-width: 1400px) {
                .section-heading-checkout h1 {
                    font-size: 1.5rem;
                }

                .checkout-slider-track {
                    padding: 0 80px;
                }

                .header-card-checkout {
                    height: 220px;
                }

                .header-card-checkout img {
                    height: 160px;
                }

                .footer-card-checkout {
                    min-height: 240px;
                    padding: 28px;
                }

                .nama-checkout {
                    font-size: 1.5rem;
                    min-height: 45px;
                }

                .deskripsi-checkout {
                    font-size: 1rem;
                    min-height: 75px;
                }

                .harga-checkout {
                    font-size: 1.25rem;
                    padding: 12px 20px;
                }

                .btn-checkout {
                    padding: 12px 24px;
                    font-size: 0.95rem;
                }
            }

            /* Desktop */
            @media (min-width: 1024px) {
                .content-checkout {
                    flex: 0 0 calc(50% - 15px);
                }
            }

            /* Tablet */
            @media (max-width: 1024px) and (min-width: 769px) {
                .container-checkout {
                    padding: 0 15px;
                }

                .section-heading-checkout h1 {
                    font-size: 1.5rem;
                }

                .checkout-slider-wrapper {
                    margin: 0 10px;
                }

                .checkout-slider-track {
                    padding: 0 50px;
                    gap: 25px;
                }

                .header-card-checkout {
                    height: 190px;
                }

                .header-card-checkout img {
                    height: 130px;
                }

                .footer-card-checkout {
                    padding: 22px;
                    min-height: 210px;
                }

                .nama-checkout {
                    font-size: 1.3rem;
                    min-height: 38px;
                }

                .deskripsi-checkout {
                    font-size: 0.92rem;
                    min-height: 65px;
                }

                .harga-checkout {
                    font-size: 1.15rem;
                    padding: 9px 16px;
                }

                .btn-checkout {
                    padding: 9px 20px;
                    font-size: 0.88rem;
                }
            }

            /* Tablet Specific (900px) - Stack Price & Button */
            @media (max-width: 900px) {
                .harga-container-checkout {
                    flex-direction: column;
                    gap: 15px;
                    align-items: stretch;
                }

                .harga-checkout,
                .btn-checkout {
                    width: 100%;
                    text-align: center;
                    justify-content: center;
                }
            }

            /* MOBILE - 1 card per slide */
            @media (max-width: 768px) {
                .container-checkout {
                    margin: 30px auto;
                    padding: 0 10px;
                }

                .section-heading-checkout h1 {
                    font-size: 1.5rem;
                }

                .section-heading-checkout p {
                    font-size: 1rem;
                    padding: 0 15px;
                }

                .checkout-slider-wrapper {
                    margin: 0 5px;
                    padding: 20px 0;
                    border-radius: 18px;
                }

                .checkout-slider-track {
                    padding: 0 30px;
                    gap: 0;
                }

                .content-checkout {
                    flex: 0 0 100%;
                    margin: 0 10px;
                    border-radius: 16px;
                    min-height: auto;
                }

                .checkout-nav-btn {
                    width: 48px;
                    height: 48px;
                    font-size: 1.3rem;
                }

                .checkout-prev {
                    left: 10px;
                }

                .checkout-next {
                    right: 10px;
                }

                .header-card-checkout {
                    height: 180px;
                    padding: 15px;
                }

                .header-card-checkout img {
                    height: 120px;
                }

                .footer-card-checkout {
                    padding: 20px;
                    min-height: 200px;
                }

                .badge-checkout {
                    font-size: 0.8rem;
                    padding: 5px 14px;
                }

                .nama-checkout {
                    font-size: 1.2rem;
                    min-height: 35px;
                }

                .deskripsi-checkout {
                    font-size: 0.9rem;
                    min-height: 60px;
                }

                .checkout-dots {
                    margin-top: 30px;
                }
            }

            /* Small Mobile */
            @media (max-width: 480px) {
                .container-checkout {
                    margin: 20px auto;
                    padding: 0 15px;
                }

                .section-heading-checkout h1 {
                    font-size: 1.5rem;
                }

                /* Sembunyikan tombol nav di layar sempit, user pakai swipe */
                .checkout-nav-btn {
                    display: none !important;
                }

                .checkout-slider-track {
                    padding: 0 10px;
                    /* Padding minimal agar card lebar */
                }

                .header-card-checkout {
                    height: 160px;
                }

                .header-card-checkout img {
                    height: 110px;
                }

                .footer-card-checkout {
                    padding: 15px;
                    min-height: auto;
                    /* Biarkan tinggi menyesuaikan konten agar tidak ada gap besar */
                }

                .nama-checkout {
                    font-size: 1.1rem;
                    min-height: auto;
                    margin-bottom: 8px;
                }

                .deskripsi-checkout {
                    font-size: 0.85rem;
                    min-height: auto;
                    margin-bottom: 15px;
                    /* Limit line clamp via CSS line-clamp would be nice here but keeping simple */
                }

                .harga-container-checkout {
                    flex-direction: column;
                    gap: 12px;
                    align-items: stretch;
                }

                .harga-checkout {
                    font-size: 1rem;
                    padding: 8px 12px;
                    width: 100%;
                    text-align: center;
                }

                .btn-checkout {
                    padding: 10px;
                    font-size: 0.9rem;
                    width: 100%;
                    justify-content: center;
                }

                .checkout-dots {
                    gap: 10px;
                    margin-top: 20px;
                }

                .checkout-dot {
                    width: 8px;
                    height: 8px;
                }
            }

            /* Auto-hide dots jika hanya 1 slide */
            .checkout-dots.hidden {
                display: none;
            }

            /* Hide navigation jika hanya 1 slide */
            .checkout-nav-btn.hidden {
                display: none;
            }

            /* Loading animation */
            @keyframes fadeInCheckout {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .content-checkout {
                animation: fadeInCheckout 0.6s ease forwards;
            }

            .content-checkout:nth-child(1) {
                animation-delay: 0.1s;
            }

            .content-checkout:nth-child(2) {
                animation-delay: 0.2s;
            }

            .content-checkout:nth-child(3) {
                animation-delay: 0.3s;
            }

            .content-checkout:nth-child(4) {
                animation-delay: 0.4s;
            }
        </style>
        <!-- HEADING SECTION -->
        <div class="section-heading-checkout">
            <h1>Checkout Sekarang</h1>
        </div>

        <!-- SLIDER WRAPPER -->
        <div class="checkout-slider-wrapper">
            <!-- Navigation Buttons -->
            <button class="checkout-nav-btn checkout-prev" id="checkoutPrevBtn">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="checkout-nav-btn checkout-next" id="checkoutNextBtn">
                <i class="bi bi-chevron-right"></i>
            </button>

            <!-- SLIDER TRACK -->
            <div class="checkout-slider-track" id="checkoutSliderTrack">
                <?php if (!empty($checkout_items)): ?>
                    <?php foreach ($checkout_items as $item):
                        $thumbnail_path = !empty($item['foto_thumbnail']) ? '../admin/uploads/' . htmlspecialchars($item['foto_thumbnail']) : 'https://via.placeholder.com/300x300?text=No+Image';
                        $nama_produk = htmlspecialchars($item['nama_produk'] ?? 'Produk Tidak Ditemukan');
                        // Gunakan deskripsi_produk dari home_checkout jika ada, jika tidak gunakan dari produk
                        $deskripsi_full = $item['deskripsi_produk'] ?? '';
                        $deskripsi = htmlspecialchars(substr($deskripsi_full, 0, 120));
                        if (strlen($deskripsi_full) > 120) {
                            $deskripsi .= '...';
                        }
                        $harga = $item['harga_terendah'] ? 'Rp ' . number_format($item['harga_terendah'], 0, ',', '.') : 'Harga tidak tersedia';
                        $label = htmlspecialchars($item['label'] ?? '');
                        $label_class = '';
                        if (stripos($label, 'stock') !== false || stripos($label, 'ready') !== false) {
                            $label_class = 'stock';
                        } elseif (stripos($label, 'bestseller') !== false || stripos($label, 'best') !== false) {
                            $label_class = 'bestseller';
                        } elseif (stripos($label, 'new') !== false || stripos($label, 'baru') !== false) {
                            $label_class = 'new';
                        }
                    ?>
                        <div class="content-checkout">
                            <div class="header-card-checkout">
                                <img src="<?php echo $thumbnail_path; ?>" alt="<?php echo $nama_produk; ?>">
                            </div>
                            <div class="footer-card-checkout">
                                <?php if ($label): ?>
                                    <span class="badge-checkout <?php echo $label_class; ?>"><?php echo $label; ?></span>
                                <?php endif; ?>
                                <h3 class="nama-checkout"><?php echo $nama_produk; ?></h3>
                                <p class="deskripsi-checkout"><?php echo $deskripsi; ?></p>
                                <div class="harga-container-checkout">
                                    <?php if (isset($item['has_discount']) && $item['has_discount']): ?>
                                        <span class="harga-asli-coret">Rp <?= number_format($item['harga_asli'], 0, ',', '.') ?></span>
                                        <span class="harga-checkout harga-diskon">Rp <?= number_format($item['harga_terendah'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="harga-checkout"><?php echo $harga; ?></span>
                                    <?php endif; ?>
                                    <button class="btn-checkout" onclick="location.href='checkout/checkout.php?id=<?php echo $item['produk_id']; ?>&type=<?php echo $item['tipe_produk']; ?>'">
                                        <i class="bi bi-bag-check"></i> Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #999; width: 100%;">
                        <p>Belum ada produk checkout yang tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- DOTS INDICATOR -->
        <div class="checkout-dots" id="checkoutDots"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Elemen DOM dengan prefix checkout
                const sliderTrack = document.getElementById('checkoutSliderTrack');
                const prevBtn = document.getElementById('checkoutPrevBtn');
                const nextBtn = document.getElementById('checkoutNextBtn');
                const dotsContainer = document.getElementById('checkoutDots');

                // Variabel slider checkout
                const cards = document.querySelectorAll('.content-checkout');
                const totalCards = cards.length;
                let currentSlideCheckout = 0;
                let cardsPerViewCheckout = 2;

                // Fungsi untuk membuat semua card sejajar
                function equalizeCheckoutCardHeights() {
                    if (cards.length === 0) return;

                    cards.forEach(card => {
                        card.style.height = 'auto';
                    });

                    if (cardsPerViewCheckout > 1) {
                        const totalSlides = Math.ceil(totalCards / cardsPerViewCheckout);

                        for (let slideIndex = 0; slideIndex < totalSlides; slideIndex++) {
                            const startIdx = slideIndex * cardsPerViewCheckout;
                            const endIdx = startIdx + cardsPerViewCheckout;
                            const slideCards = Array.from(cards).slice(startIdx, endIdx);

                            if (slideCards.length === 0) continue;

                            let maxHeight = 0;
                            slideCards.forEach(card => {
                                const cardHeight = card.offsetHeight;
                                if (cardHeight > maxHeight) maxHeight = cardHeight;
                            });

                            slideCards.forEach(card => {
                                card.style.height = `${maxHeight}px`;
                            });
                        }
                    }
                }

                // Fungsi untuk menghitung cards per view
                function calculateCheckoutCardsPerView() {
                    return window.innerWidth <= 768 ? 1 : 2;
                }

                // Fungsi untuk menghitung total slides
                function calculateCheckoutTotalSlides() {
                    cardsPerViewCheckout = calculateCheckoutCardsPerView();
                    return Math.ceil(totalCards / cardsPerViewCheckout);
                }

                let totalSlidesCheckout = calculateCheckoutTotalSlides();

                // Fungsi untuk membuat dots indicator
                function createCheckoutDots() {
                    dotsContainer.innerHTML = '';
                    totalSlidesCheckout = calculateCheckoutTotalSlides();

                    if (totalSlidesCheckout <= 1) {
                        dotsContainer.classList.add('hidden');
                    } else {
                        dotsContainer.classList.remove('hidden');

                        for (let i = 0; i < totalSlidesCheckout; i++) {
                            const dot = document.createElement('div');
                            dot.classList.add('checkout-dot');
                            if (i === currentSlideCheckout) dot.classList.add('active');
                            dot.setAttribute('data-slide', i);
                            dot.addEventListener('click', () => goToCheckoutSlide(i));
                            dotsContainer.appendChild(dot);
                        }
                    }
                }

                // Fungsi untuk update slider position
                function updateCheckoutSliderPosition() {
                    const cardWidth = cards[0].offsetWidth + 30;
                    const translateX = currentSlideCheckout * cardsPerViewCheckout * cardWidth;
                    sliderTrack.style.transform = `translateX(-${translateX}px)`;

                    const dots = document.querySelectorAll('.checkout-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentSlideCheckout);
                    });

                    updateCheckoutNavigationButtons();
                }

                // Fungsi untuk update navigation buttons
                function updateCheckoutNavigationButtons() {
                    if (totalSlidesCheckout <= 1) {
                        prevBtn.classList.add('hidden');
                        nextBtn.classList.add('hidden');
                        return;
                    }

                    prevBtn.classList.remove('hidden');
                    nextBtn.classList.remove('hidden');

                    prevBtn.disabled = currentSlideCheckout === 0;
                    nextBtn.disabled = currentSlideCheckout === totalSlidesCheckout - 1;
                }

                // Fungsi untuk pindah slide
                function goToCheckoutSlide(slideIndex) {
                    if (slideIndex < 0 || slideIndex >= totalSlidesCheckout) return;

                    sliderTrack.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';

                    currentSlideCheckout = slideIndex;
                    updateCheckoutSliderPosition();
                }

                // Fungsi untuk next slide
                function nextCheckoutSlide() {
                    if (currentSlideCheckout < totalSlidesCheckout - 1) {
                        currentSlideCheckout++;
                        updateCheckoutSliderPosition();
                    }
                }

                // Fungsi untuk prev slide
                function prevCheckoutSlide() {
                    if (currentSlideCheckout > 0) {
                        currentSlideCheckout--;
                        updateCheckoutSliderPosition();
                    }
                }

                // Event listeners untuk navigation buttons
                prevBtn.addEventListener('click', prevCheckoutSlide);
                nextBtn.addEventListener('click', nextCheckoutSlide);

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft' && e.target === document.body) {
                        prevCheckoutSlide();
                    } else if (e.key === 'ArrowRight' && e.target === document.body) {
                        nextCheckoutSlide();
                    }
                });

                // Touch/swipe untuk mobile
                let touchStartXCheckout = 0;
                let touchEndXCheckout = 0;

                sliderTrack.addEventListener('touchstart', (e) => {
                    touchStartXCheckout = e.changedTouches[0].clientX;
                });

                sliderTrack.addEventListener('touchend', (e) => {
                    touchEndXCheckout = e.changedTouches[0].clientX;
                    const swipeThreshold = 50;
                    const diff = touchStartXCheckout - touchEndXCheckout;

                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0) {
                            nextCheckoutSlide();
                        } else {
                            prevCheckoutSlide();
                        }
                    }
                });

                // Handle window resize
                let resizeTimeoutCheckout;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimeoutCheckout);
                    resizeTimeoutCheckout = setTimeout(() => {
                        cardsPerViewCheckout = calculateCheckoutCardsPerView();
                        totalSlidesCheckout = calculateCheckoutTotalSlides();

                        if (currentSlideCheckout >= totalSlidesCheckout) {
                            currentSlideCheckout = totalSlidesCheckout - 1;
                        }

                        createCheckoutDots();
                        updateCheckoutSliderPosition();
                        updateCheckoutNavigationButtons();

                        setTimeout(equalizeCheckoutCardHeights, 100);
                    }, 250);
                });

                // Inisialisasi
                cardsPerViewCheckout = calculateCheckoutCardsPerView();
                totalSlidesCheckout = calculateCheckoutTotalSlides();
                createCheckoutDots();
                updateCheckoutNavigationButtons();
                updateCheckoutSliderPosition();

                // Samakan tinggi card setelah semua gambar dimuat
                const images = document.querySelectorAll('.header-card-checkout img');
                let loadedCheckoutImages = 0;

                function checkAllCheckoutImagesLoaded() {
                    loadedCheckoutImages++;
                    if (loadedCheckoutImages === images.length) {
                        setTimeout(() => {
                            equalizeCheckoutCardHeights();
                        }, 100);
                    }
                }

                images.forEach(img => {
                    if (img.complete) {
                        checkAllCheckoutImagesLoaded();
                    } else {
                        img.addEventListener('load', checkAllCheckoutImagesLoaded);
                        img.addEventListener('error', checkAllCheckoutImagesLoaded);
                    }
                });

                if (images.length === 0) {
                    setTimeout(equalizeCheckoutCardHeights, 100);
                }
            });
        </script>
    </div>

    <footer class="ibox-footer">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background-color: #f9f9f9;
                color: #333;
                overflow-x: hidden;
            }

            .main-content {
                flex: 1;
                padding: 40px 20px;
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
            }

            .ibox-footer h1 {
                color: #1d1d1f;
                margin-bottom: 20px;
                font-weight: 600;
                font-size: 1.5rem;
            }

            .ibox-footer p {
                color: #515154;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            /* Footer Styles */
            .ibox-footer {
                background-color: #f5f5f7;
                color: #1d1d1f;
                border-top: 1px solid #d2d2d7;
                padding: 40px 0 20px;
                font-size: 12px;
                line-height: 1.33337;
                font-weight: 400;
            }

            .footer-container {
                max-width: 980px;
                margin: 0 auto;
                padding: 0 22px;
            }

            .footer-columns {
                display: flex;
                flex-wrap: wrap;
                margin-bottom: 30px;
            }

            .footer-column {
                flex: 1;
                min-width: 200px;
                margin-bottom: 20px;
                padding-right: 20px;
            }

            .footer-column-title {
                font-weight: 600;
                margin-bottom: 10px;
                color: #1d1d1f;
                font-size: 12px;
            }

            .footer-column ul {
                list-style: none;
            }

            .footer-column li {
                margin-bottom: 8px;
            }

            .footer-column a {
                color: #515154;
                text-decoration: none;
                transition: color 0.2s;
            }

            .footer-column a:hover {
                color: #1d1d1f;
                text-decoration: underline;
            }

            .footer-divider {
                border-top: 1px solid #d2d2d7;
                margin: 20px 0;
            }

            .footer-account {
                margin-bottom: 20px;
            }

            .account-link {
                color: #0066cc;
                text-decoration: none;
                font-weight: 600;
            }

            .account-link:hover {
                text-decoration: underline;
            }

            .footer-partners {
                margin-top: 30px;
            }

            .partners-title {
                font-weight: 600;
                margin-bottom: 15px;
                color: #1d1d1f;
            }

            .partners-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .partner-item {
                background-color: #fff;
                border: 1px solid #d2d2d7;
                border-radius: 8px;
                padding: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 60px;
                font-weight: 500;
                color: #1d1d1f;
                text-align: center;
                transition: all 0.2s;
            }

            .partner-item:hover {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }

            .partner-premium {
                border-left: 4px solid #ff9500;
            }

            .partner-service {
                border-left: 4px solid #007aff;
            }

            .partner-business {
                border-left: 4px solid #34c759;
            }

            .partner-education {
                border-left: 4px solid #af52de;
            }

            .footer-bottom {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #d2d2d7;
                color: #86868b;
                font-size: 11px;
            }

            .copyright {
                flex: 1;
            }

            .footer-links {
                display: flex;
                gap: 20px;
            }

            .footer-links a {
                color: #515154;
                text-decoration: none;
            }

            .footer-links a:hover {
                text-decoration: underline;
                color: #1d1d1f;
            }

            .back-to-top {
                background-color: #0071e3;
                color: white;
                border: none;
                border-radius: 20px;
                padding: 8px 16px;
                font-size: 12px;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .back-to-top:hover {
                background-color: #0056b3;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .footer-columns {
                    flex-direction: column;
                }

                .footer-column {
                    min-width: 100%;
                    padding-right: 0;
                    margin-bottom: 25px;
                }

                .partners-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .footer-bottom {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .footer-links {
                    flex-wrap: wrap;
                    gap: 10px 20px;
                }

                .back-to-top {
                    align-self: flex-end;
                    margin-top: 10px;
                }
            }

            @media (max-width: 480px) {
                .partners-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <div class="footer-container">
            <div class="footer-columns">
                <div class="footer-column">
                    <div class="footer-column-title">Belanja</div>
                    <ul>
                        <li><a href="#">Mac</a></li>
                        <li><a href="#">iPad</a></li>
                        <li><a href="#">iPhone</a></li>
                        <li><a href="#">Watch</a></li>
                        <li><a href="#">Music</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Aksesori</div>
                    <ul>
                        <li><a href="#">Layanan</a></li>
                        <li><a href="#">Layanan pelanggan</a></li>
                        <li><a href="#">Bisnis</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Financing</div>
                    <ul>
                        <li><a href="products/trade-in/trade-in.php">Trade-In</a></li>
                        <li><a href="#">In-Store Classes</a></li>
                        <li><a href="#">AppleCare</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Tentang iBox</div>
                    <ul>
                        <li><a href="#">Tentang iBox</a></li>
                        <li><a href="#">Hubungi kami</a></li>
                        <li><a href="#">Yang sering ditanyakan</a></li>
                        <li><a href="#">Cari toko</a></li>
                        <li><a href="#">Informasi Keamanan</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <div class="footer-column-title">Kebijakan</div>
                    <ul>
                        <li><a href="#">Kebijakan pengiriman</a></li>
                        <li><a href="#">Kebijakan sistem pembayaran</a></li>
                        <li><a href="#">Kebijakan privasi</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-account">
                <a href="#" class="account-link">Akun saya</a>
            </div>

            <div class="footer-partners">
                <div class="partners-title">Apple Premium Partner</div>
                <div class="partners-grid">
                    <div class="partner-item partner-premium">Premium Partner</div>
                    <div class="partner-item partner-service">Authorized Service Provider</div>
                    <div class="partner-item partner-business">Premium Business Partner</div>
                    <div class="partner-item partner-education">Premium Education Partner</div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="copyright">
                    © 2025 iBox. Hak cipta dilindungi undang-undang.
                </div>
                <div class="footer-links">
                    <a href="#">Syarat Penggunaan</a>
                    <a href="#">Peta Situs</a>
                    <a href="#">Cookies</a>
                </div>
                <button class="back-to-top" id="backToTop">
                    <i class="bi bi-arrow-up"></i> Kembali ke Atas
                </button>
            </div>
        </div>
        <script>
            // Back to top functionality
            document.getElementById('backToTop').addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Show/hide back to top button based on scroll position
            window.addEventListener('scroll', function() {
                const backToTopBtn = document.getElementById('backToTop');
                if (window.scrollY > 300) {
                    backToTopBtn.style.display = 'block';
                } else {
                    backToTopBtn.style.display = 'block'; // Always visible in this design
                }
            });

            // Highlight current section in footer on hover
            document.querySelectorAll('.footer-column a').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.style.color = '#0071e3';
                });

                link.addEventListener('mouseleave', function() {
                    this.style.color = '#515154';
                });
            });

            // Add animation to partner items on page load
            document.addEventListener('DOMContentLoaded', function() {
                const partnerItems = document.querySelectorAll('.partner-item');
                partnerItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(10px)';

                        setTimeout(() => {
                            item.style.transition = 'opacity 0.5s, transform 0.5s';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, 100);
                    }, index * 100);
                });
            });
        </script>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');
            const closeSidebar = document.getElementById('closeSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Cart Dropdown Elements
            const cartTrigger = document.getElementById('cartDropdownTrigger');
            const cartDropdown = document.getElementById('cartDropdown');
            const cartList = document.getElementById('cartItemsList');
            const cartDropdownCount = document.getElementById('cartDropdownCount');
            const cartBadge = document.getElementById('cartBadge');

            // Toggle Sidebar logic (only if not already attached)
            if (hamburgerBtn) {
                hamburgerBtn.removeEventListener('click', toggleSidebar); // remove potential dup
                hamburgerBtn.addEventListener('click', toggleSidebar);
            }
            if (closeSidebar) {
                closeSidebar.removeEventListener('click', toggleSidebar);
                closeSidebar.addEventListener('click', toggleSidebar);
            }
            if (sidebarOverlay) {
                sidebarOverlay.removeEventListener('click', toggleSidebar);
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }

            function toggleSidebar() {
                if (sidebar) {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                    document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
                }
            }

            // Handle Cart Dropdown
            let isCartOpen = false;

            if (cartTrigger) {
                cartTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    if (isCartOpen) {
                        closeCartDropdown();
                    } else {
                        openCartDropdown();
                    }
                });
            }

            function openCartDropdown() {
                if (!cartDropdown) return;
                cartDropdown.classList.add('active');
                isCartOpen = true;
                fetchCartData();
            }

            function closeCartDropdown() {
                if (!cartDropdown) return;
                cartDropdown.classList.remove('active');
                isCartOpen = false;
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (isCartOpen && cartDropdown && !cartDropdown.contains(e.target) && !cartTrigger.contains(e.target)) {
                    closeCartDropdown();
                }
            });

            function fetchCartData() {
                // Adjust path based on where this file is included
                // Assuming we are in pages/index.php, the correct relative path to pages/cart/get_cart_dropdown.php is:
                fetch('cart/get_cart_dropdown.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
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
                if (cartDropdownCount) cartDropdownCount.textContent = count;
                // Don't update badge here strictly to allow PHP server-side render priority

                if (items.length === 0) {
                    cartList.innerHTML = '<li class="cart-empty-state">Keranjang Anda kosong</li>';
                    return;
                }

                let html = '';
                items.forEach(item => {
                    // Logic to handle images
                    // API now returns image path from DB (e.g. "assets/img/...")
                    // Since we are in pages/index.php, we need to go up one level to reach assets/
                    // e.g. "../assets/img/..."

                    let imgPath;

                    if (item.image) {
                        // Check if it's a legacy asset path or a new upload
                        if (item.image.startsWith('assets/')) {
                            imgPath = '../' + item.image;
                        } else {
                            // Assume it's a filename in admin/uploads/ as per user request
                            imgPath = '../admin/uploads/' + item.image;
                        }
                    } else {
                        imgPath = '../assets/img/logo/logo.png';
                    }
                    // If it's already absolute or correct, leave it, but fallback safely

                    html += `
                        <li class="cart-item">
                            <a href="${item.checkout_url}" class="cart-item-link">
                                <div class="cart-item-img">
                                    <img src="${imgPath}" alt="${item.name}" onerror="this.src='../assets/img/logo/logo.png'">
                                </div>
                                <div class="cart-item-details">
                                    <div class="cart-item-name">${item.name}</div>
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
</body>

</html>