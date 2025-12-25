<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBox - Responsive Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            position: relative;
            transition: all 0.3s ease;
        }

        .list-menu > ul > li {
            position: relative;
        }

        .list-menu > ul > li > a {
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

        .list-menu > ul > li > a:hover {
            color: #007aff;
            border-bottom-color: #007aff;
        }

        .list-menu > ul > li > a.active {
            color: #007aff;
            border-bottom-color: #007aff;
        }

        /* MODIFIKASI: Dropdown Styles dengan efek toggle */
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
        }

        .dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            max-height: 500px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* MODIFIKASI: Class untuk menyembunyikan menu lainnya saat dropdown aktif */
        .list-menu ul.focused-mode > li:not(.active-menu-item) > a {
            opacity: 0.3;
            pointer-events: none;
            transform: translateY(5px);
        }

        .list-menu ul.focused-mode > li:not(.active-menu-item) .dropdown {
            display: none;
        }

        .list-menu ul.focused-mode > li.active-menu-item > a {
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
        }

        .dropdown-category:first-child {
            margin-top: 0;
        }

        /* Specific dropdown contents */
        .mac-dropdown { min-width: 300px; }
        .ipad-dropdown { min-width: 250px; }
        .iphone-dropdown { min-width: 280px; }
        .watch-dropdown { min-width: 250px; }
        .music-dropdown { min-width: 200px; }
        .aksesori-dropdown { min-width: 280px; }
        .layanan-dropdown { min-width: 220px; }
        .event-dropdown { min-width: 200px; }
        .bisnis-dropdown { min-width: 200px; }
        .edukasi-dropdown { min-width: 200px; }

        /* MODIFIKASI: Close button untuk focused mode */
        .dropdown-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
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
        }

        .sidebar-menu ul {
            list-style: none;
            transition: all 0.3s ease;
        }

        /* Menu Item Utama */
        .sidebar-menu > ul > li {
            border-bottom: 1px solid #f5f5f5;
            transition: all 0.3s ease;
        }

        .sidebar-menu > ul > li:last-child {
            border-bottom: none;
        }

        .sidebar-menu > ul > li > a {
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

        .sidebar-menu > ul > li > a .menu-arrow {
            font-size: 14px;
            color: #999;
            transition: transform 0.3s ease;
        }

        .sidebar-menu > ul > li > a:hover {
            background-color: #f8faff;
            color: #007aff;
        }

        .sidebar-menu > ul > li > a::before {
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

        .sidebar-menu > ul > li > a:hover::before {
            transform: translateX(0);
        }

        /* MODIFIKASI: Sidebar focused mode */
        .sidebar.focused-mode .sidebar-menu > ul > li:not(.active-menu-item) {
            display: none;
        }

        .sidebar.focused-mode .sidebar-menu > ul > li.active-menu-item {
            border-bottom: none;
        }

        .sidebar.focused-mode .sidebar-header {
            display: none;
        }

        .sidebar.focused-mode .sidebar-bottom {
            display: none;
        }

        /* Sub-Dropdown Container */
        .sidebar-dropdown {
            background-color: #f9f9f9;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }

        .sidebar-dropdown.active {
            max-height: 1000px;
            border-left-color: #007aff;
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
            
            .list-menu > ul > li > a {
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
            
            .sidebar-menu > ul > li > a {
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
    </style>
</head>

<body>
    <nav class="navbar-container">
        <div class="wrapper">
            <div class="nav-top-container">
                <div class="navbar-top">
                    <div class="logo-hamburger-container">
                        <button class="hamburger-menu" id="hamburgerBtn">
                            <i class="bi bi-list"></i>
                        </button>
                        <div class="logo">
                            <img src="img/logo/logo.png" alt="iBox Logo">
                        </div>
                    </div>
                    <div class="search-bar-menu">
                        <form action="">
                            <input type="text" placeholder="Cari produk di iBox">
                        </form>
                    </div>
                    <div class="nav-other-menu">
                        <div class="user-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="bag-icon">
                            <i class="bi bi-bag"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-top-container">
                <div class="header-top">
                    <div class="list-menu">
                        <ul id="mainMenuList">
                            <li>
                                <a href="#" class="menu-trigger" data-target="mac">Mac</a>
                                <div class="dropdown mac-dropdown" id="mac-dropdown">
                                    <button class="dropdown-close-btn" data-close="mac">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">MacBook</div>
                                    <ul>
                                        <li><a href="#">MacBook Air M1</a></li>
                                        <li><a href="#">MacBook Air M2</a></li>
                                        <li><a href="#">MacBook Pro 13"</a></li>
                                        <li><a href="#">MacBook Pro 14"</a></li>
                                        <li><a href="#">MacBook Pro 16"</a></li>
                                    </ul>
                                    <div class="dropdown-category">Desktop</div>
                                    <ul>
                                        <li><a href="#">iMac 24"</a></li>
                                        <li><a href="#">Mac Studio</a></li>
                                        <li><a href="#">Mac Mini</a></li>
                                        <li><a href="#">Mac Pro</a></li>
                                    </ul>
                                    <div class="dropdown-category">Aksesori</div>
                                    <ul>
                                        <li><a href="#">Magic Keyboard</a></li>
                                        <li><a href="#">Magic Mouse</a></li>
                                        <li><a href="#">Magic Trackpad</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="ipad">iPad</a>
                                <div class="dropdown ipad-dropdown" id="ipad-dropdown">
                                    <button class="dropdown-close-btn" data-close="ipad">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">iPad Pro</div>
                                    <ul>
                                        <li><a href="#">iPad Pro 11" M2</a></li>
                                        <li><a href="#">iPad Pro 12.9" M2</a></li>
                                    </ul>
                                    <div class="dropdown-category">iPad Air</div>
                                    <ul>
                                        <li><a href="#">iPad Air M1</a></li>
                                        <li><a href="#">iPad Air 5th Gen</a></li>
                                    </ul>
                                    <div class="dropdown-category">iPad & iPad Mini</div>
                                    <ul>
                                        <li><a href="#">iPad 10th Gen</a></li>
                                        <li><a href="#">iPad Mini 6th Gen</a></li>
                                    </ul>
                                    <div class="dropdown-category">Aksesori</div>
                                    <ul>
                                        <li><a href="#">Apple Pencil</a></li>
                                        <li><a href="#">Magic Keyboard</a></li>
                                        <li><a href="#">Smart Folio</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="iphone">iPhone</a>
                                <div class="dropdown iphone-dropdown" id="iphone-dropdown">
                                    <button class="dropdown-close-btn" data-close="iphone">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">iPhone 15 Series</div>
                                    <ul>
                                        <li><a href="#">iPhone 15 Pro Max</a></li>
                                        <li><a href="#">iPhone 15 Pro</a></li>
                                        <li><a href="#">iPhone 15 Plus</a></li>
                                        <li><a href="#">iPhone 15</a></li>
                                    </ul>
                                    <div class="dropdown-category">iPhone 14 Series</div>
                                    <ul>
                                        <li><a href="#">iPhone 14 Pro Max</a></li>
                                        <li><a href="#">iPhone 14 Pro</a></li>
                                        <li><a href="#">iPhone 14 Plus</a></li>
                                        <li><a href="#">iPhone 14</a></li>
                                    </ul>
                                    <div class="dropdown-category">iPhone 13 Series</div>
                                    <ul>
                                        <li><a href="#">iPhone 13 Pro Max</a></li>
                                        <li><a href="#">iPhone 13 Pro</a></li>
                                        <li><a href="#">iPhone 13</a></li>
                                        <li><a href="#">iPhone 13 Mini</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="watch">Watch</a>
                                <div class="dropdown watch-dropdown" id="watch-dropdown">
                                    <button class="dropdown-close-btn" data-close="watch">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Apple Watch Series</div>
                                    <ul>
                                        <li><a href="#">Apple Watch Ultra 2</a></li>
                                        <li><a href="#">Apple Watch Series 9</a></li>
                                        <li><a href="#">Apple Watch SE</a></li>
                                    </ul>
                                    <div class="dropdown-category">Model Berdasarkan Ukuran</div>
                                    <ul>
                                        <li><a href="#">41mm & 45mm</a></li>
                                        <li><a href="#">44mm & 40mm</a></li>
                                    </ul>
                                    <div class="dropdown-category">Tali Apple Watch</div>
                                    <ul>
                                        <li><a href="#">Sport Band</a></li>
                                        <li><a href="#">Sport Loop</a></li>
                                        <li><a href="#">Braided Solo Loop</a></li>
                                        <li><a href="#">Leather Link</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="music">Music</a>
                                <div class="dropdown music-dropdown" id="music-dropdown">
                                    <button class="dropdown-close-btn" data-close="music">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">AirPods</div>
                                    <ul>
                                        <li><a href="#">AirPods Pro 2</a></li>
                                        <li><a href="#">AirPods 3rd Gen</a></li>
                                        <li><a href="#">AirPods 2nd Gen</a></li>
                                        <li><a href="#">AirPods Max</a></li>
                                    </ul>
                                    <div class="dropdown-category">HomePod & Speaker</div>
                                    <ul>
                                        <li><a href="#">HomePod mini</a></li>
                                        <li><a href="#">HomePod 2nd Gen</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <a href="#" class="menu-trigger" data-target="aksesori">Aksesori</a>
                                <div class="dropdown aksesori-dropdown" id="aksesori-dropdown">
                                    <button class="dropdown-close-btn" data-close="aksesori">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="dropdown-category">Case & Pelindung</div>
                                    <ul>
                                        <li><a href="#">Case iPhone</a></li>
                                        <li><a href="#">Case iPad</a></li>
                                        <li><a href="#">Case MacBook</a></li>
                                        <li><a href="#">Pelindung Layar</a></li>
                                    </ul>
                                    <div class="dropdown-category">Charger & Kabel</div>
                                    <ul>
                                        <li><a href="#">Charger MagSafe</a></li>
                                        <li><a href="#">Kabel USB-C</a></li>
                                        <li><a href="#">Wireless Charger</a></li>
                                        <li><a href="#">Power Bank</a></li>
                                    </ul>
                                    <div class="dropdown-category">Audio</div>
                                    <ul>
                                        <li><a href="#">EarPods</a></li>
                                        <li><a href="#">Headphones</a></li>
                                        <li><a href="#">Speaker Bluetooth</a></li>
                                    </ul>
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
    </nav>

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
                <img src="img/logo/logo.png" alt="iBox Logo">
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
                        <div class="dropdown-category">MacBook</div>
                        <a href="#">MacBook Air M1</a>
                        <a href="#">MacBook Air M2</a>
                        <a href="#">MacBook Pro 13"</a>
                        <a href="#">MacBook Pro 14"</a>
                        <a href="#">MacBook Pro 16"</a>
                        
                        <div class="dropdown-category">Desktop</div>
                        <a href="#">iMac 24"</a>
                        <a href="#">Mac Studio</a>
                        <a href="#">Mac Mini</a>
                        <a href="#">Mac Pro</a>
                        
                        <div class="dropdown-category">Aksesori</div>
                        <a href="#">Magic Keyboard</a>
                        <a href="#">Magic Mouse</a>
                        <a href="#">Magic Trackpad</a>
                    </div>
                </li>
                
                <!-- Menu iPad dengan Sub-Dropdown -->
                <li class="has-dropdown" data-menu="ipad">
                    <a href="#" class="sidebar-menu-trigger">
                        iPad
                        <span class="menu-arrow">›</span>
                    </a>
                    <div class="sidebar-dropdown">
                        <div class="dropdown-category">iPad Pro</div>
                        <a href="#">iPad Pro 11" M2</a>
                        <a href="#">iPad Pro 12.9" M2</a>
                        
                        <div class="dropdown-category">iPad Air</div>
                        <a href="#">iPad Air M1</a>
                        <a href="#">iPad Air 5th Gen</a>
                        
                        <div class="dropdown-category">iPad & iPad Mini</div>
                        <a href="#">iPad 10th Gen</a>
                        <a href="#">iPad Mini 6th Gen</a>
                        
                        <div class="dropdown-category">Aksesori</div>
                        <a href="#">Apple Pencil</a>
                        <a href="#">Magic Keyboard</a>
                        <a href="#">Smart Folio</a>
                    </div>
                </li>
                
                <!-- Menu iPhone dengan Sub-Dropdown -->
                <li class="has-dropdown" data-menu="iphone">
                    <a href="#" class="sidebar-menu-trigger">
                        iPhone
                        <span class="menu-arrow">›</span>
                    </a>
                    <div class="sidebar-dropdown">
                        <div class="dropdown-category">iPhone 15 Series</div>
                        <a href="#">iPhone 15 Pro Max</a>
                        <a href="#">iPhone 15 Pro</a>
                        <a href="#">iPhone 15 Plus</a>
                        <a href="#">iPhone 15</a>
                        
                        <div class="dropdown-category">iPhone 14 Series</div>
                        <a href="#">iPhone 14 Pro Max</a>
                        <a href="#">iPhone 14 Pro</a>
                        <a href="#">iPhone 14 Plus</a>
                        <a href="#">iPhone 14</a>
                        
                        <div class="dropdown-category">iPhone 13 Series</div>
                        <a href="#">iPhone 13 Pro Max</a>
                        <a href="#">iPhone 13 Pro</a>
                        <a href="#">iPhone 13</a>
                        <a href="#">iPhone 13 Mini</a>
                    </div>
                </li>
                
                <!-- Menu Watch dengan Sub-Dropdown -->
                <li class="has-dropdown" data-menu="watch">
                    <a href="#" class="sidebar-menu-trigger">
                        Watch
                        <span class="menu-arrow">›</span>
                    </a>
                    <div class="sidebar-dropdown">
                        <div class="dropdown-category">Apple Watch Series</div>
                        <a href="#">Apple Watch Ultra 2</a>
                        <a href="#">Apple Watch Series 9</a>
                        <a href="#">Apple Watch SE</a>
                        
                        <div class="dropdown-category">Model Berdasarkan Ukuran</div>
                        <a href="#">41mm & 45mm</a>
                        <a href="#">44mm & 40mm</a>
                        
                        <div class="dropdown-category">Tali Apple Watch</div>
                        <a href="#">Sport Band</a>
                        <a href="#">Sport Loop</a>
                        <a href="#">Braided Solo Loop</a>
                        <a href="#">Leather Link</a>
                    </div>
                </li>
                
                <!-- Menu Music dengan Sub-Dropdown -->
                <li class="has-dropdown" data-menu="music">
                    <a href="#" class="sidebar-menu-trigger">
                        Music
                        <span class="menu-arrow">›</span>
                    </a>
                    <div class="sidebar-dropdown">
                        <div class="dropdown-category">AirPods</div>
                        <a href="#">AirPods Pro 2</a>
                        <a href="#">AirPods 3rd Gen</a>
                        <a href="#">AirPods 2nd Gen</a>
                        <a href="#">AirPods Max</a>
                        
                        <div class="dropdown-category">HomePod & Speaker</div>
                        <a href="#">HomePod mini</a>
                        <a href="#">HomePod 2nd Gen</a>
                    </div>
                </li>
                
                <!-- Menu Aksesori dengan Sub-Dropdown -->
                <li class="has-dropdown" data-menu="aksesori">
                    <a href="#" class="sidebar-menu-trigger">
                        Aksesori
                        <span class="menu-arrow">›</span>
                    </a>
                    <div class="sidebar-dropdown">
                        <div class="dropdown-category">Case & Pelindung</div>
                        <a href="#">Case iPhone</a>
                        <a href="#">Case iPad</a>
                        <a href="#">Case MacBook</a>
                        <a href="#">Pelindung Layar</a>
                        
                        <div class="dropdown-category">Charger & Kabel</div>
                        <a href="#">Charger MagSafe</a>
                        <a href="#">Kabel USB-C</a>
                        <a href="#">Wireless Charger</a>
                        <a href="#">Power Bank</a>
                        
                        <div class="dropdown-category">Audio</div>
                        <a href="#">EarPods</a>
                        <a href="#">Headphones</a>
                        <a href="#">Speaker Bluetooth</a>
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
            
            // Tutup semua dropdown di sidebar
            document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
                dropdown.classList.remove('active');
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
                            }
                            activeSidebarMenuItem.classList.remove('active-menu-item');
                        }
                        
                        // Buka dropdown menu yang baru diklik
                        const dropdown = menuItem.querySelector('.sidebar-dropdown');
                        if (dropdown) {
                            dropdown.classList.add('active');
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
            
            // Tutup semua dropdown
            closeAllDropdowns();
            
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

        // Panggil fungsi saat load dan resize
        window.addEventListener('load', adjustSearchBar);
        window.addEventListener('resize', adjustSearchBar);
    </script>
</body>

</html>