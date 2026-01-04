<?php
require '../../db/db.php';
session_start();

// Handle User ID (Fallback or Session)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 for dev if not set
$is_logged_in = isset($_SESSION['user_id']);
$user_initials = '';
if ($is_logged_in) {
    $firstname = isset($_SESSION['user_firstname']) ? $_SESSION['user_firstname'] : '';
    $lastname = isset($_SESSION['user_lastname']) ? $_SESSION['user_lastname'] : '';
    $first_initial = !empty($firstname) ? strtoupper(substr($firstname, 0, 1)) : '';
    $last_initial = !empty($lastname) ? strtoupper(substr($lastname, 0, 1)) : '';
    $user_initials = $first_initial . $last_initial;
}

// --- Address Logic ---

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_action'])) {
    $action = $_POST['address_action'];
    
    if ($action === 'add' || $action === 'edit') {
        $label = $_POST['label_alamat'];
        $nama = $_POST['username'];
        $email = $_POST['email'];
        $hp = $_POST['no_hp'];
        $alamat_lengkap = $_POST['alamat_lengkap'];
        $kota = $_POST['kota'];
        $provinsi = $_POST['provinsi'];
        $kecamatan = $_POST['kecamatan'];
        $kodepos = $_POST['kode_post'];
        
        if ($action === 'add') {
            $stmt = $db->prepare("INSERT INTO user_alamat (user_id, label_alamat, username, email, no_hp, alamat_lengkap, kota, provinsi, kecamatan, kode_post) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssssss", $user_id, $label, $nama, $email, $hp, $alamat_lengkap, $kota, $provinsi, $kecamatan, $kodepos);
            if ($stmt->execute()) {
                $_SESSION['selected_alamat_id'] = $stmt->insert_id;
            }
        } elseif ($action === 'edit') {
            $id = $_POST['address_id'];
            $stmt = $db->prepare("UPDATE user_alamat SET label_alamat=?, username=?, email=?, no_hp=?, alamat_lengkap=?, kota=?, provinsi=?, kecamatan=?, kode_post=? WHERE id=? AND user_id=?");
            $stmt->bind_param("sssssssssii", $label, $nama, $email, $hp, $alamat_lengkap, $kota, $provinsi, $kecamatan, $kodepos, $id, $user_id);
            $stmt->execute();
            $_SESSION['selected_alamat_id'] = $id; // Keep selected
        }
    } elseif ($action === 'delete') {
        $id = $_POST['address_id'];
        $stmt = $db->prepare("DELETE FROM user_alamat WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        
        // If deleted address was selected, unset it
        if (isset($_SESSION['selected_alamat_id']) && $_SESSION['selected_alamat_id'] == $id) {
            unset($_SESSION['selected_alamat_id']);
        }
    } elseif ($action === 'select') {
        $_SESSION['selected_alamat_id'] = $_POST['address_id'];
    }
    
    // Redirect to avoid resubmission (optional, but good practice)
    // header("Location: " . $_SERVER['REQUEST_URI']);
    // exit;
}

// Fetch Selected Address
$selected_address = null;
if (isset($_SESSION['selected_alamat_id'])) {
    $stmt = $db->prepare("SELECT * FROM user_alamat WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_SESSION['selected_alamat_id'], $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $selected_address = $res->fetch_assoc();
    } else {
        unset($_SESSION['selected_alamat_id']);
    }
}

// Context: If no address selected, try to get the latest one
if (!$selected_address) {
    $stmt = $db->prepare("SELECT * FROM user_alamat WHERE user_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $selected_address = $res->fetch_assoc();
        $_SESSION['selected_alamat_id'] = $selected_address['id'];
    }
}

// Fetch All Addresses
$all_addresses = [];
$stmt = $db->prepare("SELECT * FROM user_alamat WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $all_addresses[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - iBox Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        /* Breadcrumb CSS */
        .breadcrumb-container {
            padding: 15px 0;
            background-color: #fff;
            font-size: 14px;
            color: #86868b;
            border-bottom: 1px solid #e5e5e7;
        }
        .breadcrumb-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
            display: flex;
            align-items: center;
        }
        .breadcrumb-container a { 
            color: #1d1d1f; 
            text-decoration: none; 
            transition: color 0.2s;
        }
        .breadcrumb-container a:hover { 
            color: #007aff; 
        }
        .breadcrumb-separator { 
            margin: 0 10px; 
            color: #d2d2d7; 
            font-size: 12px;
        }
        .breadcrumb-current { 
            color: #86868b; 
            font-weight: 400; 
        }
    </style>
    <style>
        /* Address Management Styles */
        .address-display-card {
            border: 1px solid #d2d2d7;
            border-radius: 12px;
            padding: 20px;
            background: #fbfbfd;
        }
        .address-label {
            font-size: 13px;
            font-weight: 600;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }
        .address-name {
            font-size: 17px;
            font-weight: 600;
            color: #1d1d1f;
            margin-bottom: 4px;
        }
        .address-text {
            font-size: 15px;
            color: #1d1d1f;
            line-height: 1.4;
        }
        .address-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn-address-action {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-change-address {
            background: white;
            border: 1px solid #d2d2d7;
            color: #1d1d1f;
        }
        .btn-change-address:hover {
            border-color: #007aff;
            color: #007aff;
        }
        .btn-edit-address {
            background: transparent;
            border: none;
            color: #007aff;
            padding: 8px 0;
        }
        .btn-edit-address:hover {
            text-decoration: underline;
        }
        
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; 
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 2000;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: white;
            width: 90%;
            max-width: 600px;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            animation: modalSlideUp 0.3s ease;
        }
        @keyframes modalSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e5e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #1d1d1f;
        }
        .btn-close-modal {
            background: none;
            border: none;
            font-size: 24px;
            color: #86868b;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        .modal-body {
            padding: 25px;
            overflow-y: auto;
        }
        .address-list-item {
            border: 1px solid #e5e5e7;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .address-list-item:hover {
            border-color: #007aff;
            background: #f5f5f7;
        }
        .address-list-item.selected {
            border-color: #007aff;
            background: #f0f7ff;
            box-shadow: 0 0 0 1px #007aff inset;
        }
        .address-content {
            flex: 1;
        }
        .address-select-indicator {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #d2d2d7;
            margin-left: 15px;
            position: relative;
        }
        .address-list-item.selected .address-select-indicator {
            border-color: #007aff;
            background: #007aff;
        }
        .address-list-item.selected .address-select-indicator::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 10px; height: 10px;
            background: white;
            border-radius: 50%;
        }
        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e5e5e7;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-modal {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-secondary { background: #e5e5e7; color: #1d1d1f; }
        .btn-primary { background: #007aff; color: white; }
        .btn-danger { background: #ff3b30; color: white; }
        
        /* Form Styles update */
        .form-grid-modal {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .full-width { grid-column: span 2; }
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
                        <div class="bag-icon">
                            <i class="bi bi-bag"></i>
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
    
    <!-- Breadcrumb -->
    <!-- Breadcrumb -->
    <div class="breadcrumb-container">
        <div class="breadcrumb-inner">
            <a href="../index.php"><i class="fas fa-home" style="margin-right: 5px;"></i> Home</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Checkout</span>
        </div>
    </div>

    <?php
    // Ambil ID dari URL
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Konfigurasi tabel dan field untuk setiap tipe produk
    $product_config = [
        'airtag' => [
            'table' => 'admin_produk_airtag',
            'table_gambar' => 'admin_produk_airtag_gambar',
            'table_kombinasi' => 'admin_produk_airtag_kombinasi',
            'variant_fields' => ['warna', 'pack', 'aksesoris'],
            'order_by' => 'warna, pack'
        ],
        'ipad' => [
            'table' => 'admin_produk_ipad',
            'table_gambar' => 'admin_produk_ipad_gambar',
            'table_kombinasi' => 'admin_produk_ipad_kombinasi',
            'variant_fields' => ['warna', 'penyimpanan', 'konektivitas'],
            'order_by' => 'warna, penyimpanan'
        ],
        'iphone' => [
            'table' => 'admin_produk_iphone',
            'table_gambar' => 'admin_produk_iphone_gambar',
            'table_kombinasi' => 'admin_produk_iphone_kombinasi',
            'variant_fields' => ['warna', 'penyimpanan', 'konektivitas'],
            'order_by' => 'warna, penyimpanan'
        ],
        'mac' => [
            'table' => 'admin_produk_mac',
            'table_gambar' => 'admin_produk_mac_gambar',
            'table_kombinasi' => 'admin_produk_mac_kombinasi',
            'variant_fields' => ['warna', 'processor', 'penyimpanan', 'ram'],
            'order_by' => 'warna, penyimpanan'
        ],
        'music' => [
            'table' => 'admin_produk_music',
            'table_gambar' => 'admin_produk_music_gambar',
            'table_kombinasi' => 'admin_produk_music_kombinasi',
            'variant_fields' => ['warna', 'tipe', 'konektivitas'],
            'order_by' => 'warna, tipe'
        ],
        'watch' => [
            'table' => 'admin_produk_watch',
            'table_gambar' => 'admin_produk_watch_gambar',
            'table_kombinasi' => 'admin_produk_watch_kombinasi',
            'variant_fields' => ['warna_case', 'ukuran_case', 'tipe_koneksi', 'material'],
            'order_by' => 'warna_case, ukuran_case',
            'col_warna_img' => 'warna_case' // Konfigurasi khusus untuk kolom warna image
        ],
        'aksesoris' => [
            'table' => 'admin_produk_aksesoris',
            'table_gambar' => 'admin_produk_aksesoris_gambar',
            'table_kombinasi' => 'admin_produk_aksesoris_kombinasi',
            'variant_fields' => ['warna', 'tipe', 'ukuran'],
            'order_by' => 'warna, tipe'
        ]
    ];

    // Fungsi universal untuk mengambil data produk dengan auto-detect tipe
    // (Pengganti fungsi lama getAirTagProduct & getIPadProduct)
    function getProduct($db, $id, $config) {
        // Jika ada parameter tipe spesifik di URL, prioritaskan itu
        // (Opsional, untuk backward compatibility atau resolusi konflik)
        if (isset($_GET['tipe']) && isset($config[strtolower(trim($_GET['tipe']))])) {
             $specific_type = strtolower(trim($_GET['tipe']));
             return getProductByType($db, $id, $specific_type, $config[$specific_type]);
        }
        
        // Auto-detect: Loop check ke semua tabel konfigurasi
        foreach ($config as $type => $cfg) {
             $product = getProductByType($db, $id, $type, $cfg);
             if ($product) {
                 return $product; // Ketemu! Kembalikan produk
             }
        }
        
        return null; // Tidak ketemu di tabel manapun
    }
    
    // Fungsi helper untuk mengambil data dari tipe spesifik
    function getProductByType($db, $id, $type, $cfg) {
        $id = mysqli_real_escape_string($db, $id);
        
        // Query untuk mengambil data produk utama
        $query = "SELECT 
                    p.id,
                    p.nama_produk,
                    p.deskripsi_produk,
                    p.kategori
                  FROM {$cfg['table']} p
                  WHERE p.id = '$id'
                  LIMIT 1";
        
        $result = mysqli_query($db, $query);
        
        // Debug: Uncomment untuk melihat error
        // if (!$result) {
        //     echo "Error: " . mysqli_error($db) . "<br>";
        //     echo "Query: " . $query . "<br>";
        // }
        
        if ($result && mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            $product['type'] = $type; // Simpan tipe yang ditemukan
            
            // Tentukan field warna untuk query gambar secara dinamis dari config
            // Default ke 'warna' jika tidak diset di config
            $col_warna_img = isset($cfg['col_warna_img']) ? $cfg['col_warna_img'] : 'warna';
            
            // Ambil semua gambar produk
            $gambar_query = "SELECT {$col_warna_img} as warna, foto_thumbnail, foto_produk 
                            FROM {$cfg['table_gambar']} 
                            WHERE produk_id = '$id'";
            $gambar_result = mysqli_query($db, $gambar_query);
            $product['images'] = [];
            
            while ($img = mysqli_fetch_assoc($gambar_result)) {
                $product['images'][] = $img;
            }
            
            // Buat query untuk kombinasi berdasarkan field yang ada
            $variant_fields_str = implode(', ', $cfg['variant_fields']);
            $kombinasi_query = "SELECT 
                                {$variant_fields_str},
                                harga, 
                                harga_diskon,
                                jumlah_stok,
                                status_stok
                               FROM {$cfg['table_kombinasi']} 
                               WHERE produk_id = '$id'
                               ORDER BY {$cfg['order_by']}";
            $kombinasi_result = mysqli_query($db, $kombinasi_query);
            $product['variants'] = [];
            
            while ($variant = mysqli_fetch_assoc($kombinasi_result)) {
                $product['variants'][] = $variant;
            }
            
            return $product;
        }
        
        return null; // Tidak ditemukan di tabel ini
    }

    // Ambil data produk (Auto Detect)
    $product = null;
    $product_type = null; // Ini akan diisi otomatis setelah produk ditemukan
    
    if ($product_id > 0) {
        $product = getProduct($db, $product_id, $product_config);
        if ($product) {
            $product_type = $product['type']; // Set tipe produk yang ditemukan untuk logic selanjutnya
        }
    }
    // Proses grouping varian untuk UI
    $grouped_options = [];
    $initial_price = 0;
    
    if ($product && !empty($product['variants'])) {
        $variant_fields = $product_config[$product['type']]['variant_fields'];
        
        // Inisialisasi array grouping
        foreach ($variant_fields as $field) {
            $grouped_options[$field] = [];
        }
        
        // Loop semua varian untuk mengumpulkan opsi unik
        foreach ($product['variants'] as $variant) {
            // Set harga awal dari varian pertama (termurah karena sudah diorder)
            if ($initial_price == 0) {
                $initial_price = $variant['harga_diskon'] > 0 ? $variant['harga_diskon'] : $variant['harga'];
            }
            
            foreach ($variant_fields as $field) {
                // Pastikan nilai field ada
                if (isset($variant[$field]) && !in_array($variant[$field], $grouped_options[$field])) {
                    $grouped_options[$field][] = $variant[$field];
                }
            }
        }
    }
    ?>

    <!-- Checkout Content -->
    <div class="checkout-wrapper">
        <style>
            .checkout-wrapper {
                max-width: 1400px;
                margin: 40px auto;
                padding: 0 5%;
            }

            .checkout-grid {
                display: grid;
                grid-template-columns: 1fr 400px;
                gap: 30px;
                margin-bottom: 40px;
            }

            .product-showcase {
                background: white;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            }

            .product-header {
                margin-bottom: 30px;
            }

            .product-badge {
                display: inline-block;
                padding: 6px 16px;
                background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
                color: white;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 15px;
            }

            .product-title {
                font-size: 36px;
                font-weight: 800;
                color: #1d1d1f;
                margin-bottom: 15px;
                line-height: 1.2;
            }

            .product-description {
                font-size: 16px;
                color: #6e6e73;
                line-height: 1.6;
                margin-bottom: 30px;
            }

            .image-gallery {
                display: grid;
                grid-template-columns: 120px 1fr;
                gap: 20px;
                margin-bottom: 40px;
            }

            .thumbnail-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .thumbnail-item {
                width: 120px;
                height: 120px;
                border-radius: 12px;
                border: 3px solid transparent;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #f5f5f7;
                padding: 10px;
                box-sizing: border-box;
            }

            .thumbnail-item:hover,
            .thumbnail-item.active {
                border-color: #007aff;
                transform: scale(1.05);
            }

            .thumbnail-item img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .main-image {
                background: #f5f5f7;
                border-radius: 20px;
                padding: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 500px;
            }

            .main-image img {
                max-width: 100%;
                max-height: 500px;
                object-fit: contain;
            }

            .variants-section {
                margin-bottom: 30px;
            }

            .section-title {
                font-size: 20px;
                font-weight: 700;
                color: #1d1d1f;
                margin-bottom: 20px;
            }

            .variant-group {
                margin-bottom: 25px;
                border: 1px solid #e5e5e7;
                border-radius: 16px;
                padding: 20px;
                background: white;
            }

            .variant-group-title {
                font-size: 14px;
                font-weight: 600;
                color: #86868b;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .variant-options {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
            }

            .variant-option-btn {
                padding: 12px 24px;
                border: 1px solid #d2d2d7;
                border-radius: 12px;
                background: white;
                color: #1d1d1f;
                font-size: 15px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                min-width: 80px;
                text-align: center;
            }

            .variant-option-btn:hover {
                border-color: #007aff;
            }

            .variant-option-btn.selected {
                border-color: #007aff;
                background-color: #f2f7ff;
                color: #007aff;
                font-weight: 600;
                box-shadow: 0 0 0 1px #007aff inset;
            }

            .variant-option-btn.disabled {
                opacity: 0.5;
                cursor: not-allowed;
                border-color: #e5e5e7;
                background-color: #f5f5f7;
                color: #86868b;
            }

            .variant-card:hover {
                border-color: #007aff;
                box-shadow: 0 6px 20px rgba(0, 122, 255, 0.15);
                transform: translateY(-2px);
            }

            .variant-card.selected {
                border-color: #007aff;
                background: linear-gradient(135deg, rgba(0, 122, 255, 0.05) 0%, rgba(0, 81, 213, 0.05) 100%);
            }

            .variant-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
            }

            .variant-attribute-row {
                margin-bottom: 8px;
            }
            
            .variant-attribute-row:last-child {
                margin-bottom: 0;
            }

            .var-attr-label {
                font-size: 11px;
                color: #86868b;
                display: block;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 2px;
            }

            .var-attr-value {
                font-size: 15px;
                color: #1d1d1f;
                font-weight: 600;
                display: block;
            }

            .variant-name {
                font-size: 18px;
                font-weight: 700;
                color: #1d1d1f;
            }

            .variant-price {
                font-size: 22px;
                font-weight: 800;
                color: #007aff;
            }

            .variant-price.discounted {
                color: #ff3b30;
            }

            .variant-price-original {
                font-size: 14px;
                color: #86868b;
                text-decoration: line-through;
                margin-left: 8px;
            }

            .variant-details {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
                margin-bottom: 10px;
            }

            .variant-detail-item {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                color: #6e6e73;
            }

            .variant-detail-item i {
                color: #007aff;
            }

            .variant-stock {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #34c759;
                color: white;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }

            .variant-stock.low {
                background: #ff9500;
            }

            .variant-stock.out {
                background: #ff3b30;
            }

            .checkout-sidebar {
                background: white;
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
                height: fit-content;
                align-self: flex-start;
                position: -webkit-sticky;
                position: sticky;
                top: 150px;
                z-index: 90;
            }

            .quantity-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 25px;
                padding-bottom: 25px;
                border-bottom: 2px solid #e5e5e7;
            }

            .quantity-selector {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #f5f5f7;
                padding: 5px;
                border-radius: 12px;
            }

            .qty-btn {
                width: 32px;
                height: 32px;
                border: none;
                background: white;
                border-radius: 8px;
                cursor: pointer;
                font-weight: bold;
                font-size: 16px;
                color: #1d1d1f;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                transition: all 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .qty-btn:hover {
                background: #007aff;
                color: white;
            }

            .qty-btn:active {
                transform: scale(0.95);
            }

            #productQuantity {
                width: 40px;
                text-align: center;
                border: none;
                background: transparent;
                font-weight: 600;
                font-size: 16px;
                color: #1d1d1f;
                -moz-appearance: textfield;
            }
            
            #productQuantity:focus {
                outline: none;
            }

            #productQuantity::-webkit-outer-spin-button,
            #productQuantity::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .sidebar-title {
                font-size: 24px;
                font-weight: 800;
                color: #1d1d1f;
                margin-bottom: 25px;
                padding-bottom: 20px;
                border-bottom: 2px solid #e5e5e7;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 18px;
                font-size: 15px;
            }

            .summary-label {
                color: #6e6e73;
                font-weight: 500;
            }

            .summary-value {
                font-weight: 700;
                color: #1d1d1f;
            }

            .summary-total {
                display: flex;
                justify-content: space-between;
                padding-top: 25px;
                margin-top: 25px;
                border-top: 2px solid #e5e5e7;
            }

            .summary-total .summary-label {
                font-size: 18px;
                font-weight: 700;
                color: #1d1d1f;
            }

            .summary-total .summary-value {
                font-size: 28px;
                font-weight: 800;
                color: #007aff;
            }

            .btn-checkout {
                width: 100%;
                padding: 18px;
                background: linear-gradient(135deg, #34c759 0%, #28a745 100%);
                color: white;
                border: none;
                border-radius: 14px;
                font-size: 17px;
                font-weight: 700;
                cursor: pointer;
                margin-top: 25px;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
                box-shadow: 0 6px 20px rgba(52, 199, 89, 0.3);
            }

            .btn-checkout:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(52, 199, 89, 0.4);
            }

            .btn-checkout:active {
                transform: translateY(-1px);
            }

            .btn-cart {
                width: 100%;
                padding: 18px;
                background: white;
                color: #007aff;
                border: 2px solid #007aff;
                border-radius: 14px;
                font-size: 17px;
                font-weight: 700;
                cursor: pointer;
                margin-top: 15px;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 1px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .btn-cart:hover {
                background-color: #f0f7ff;
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 122, 255, 0.15);
            }

            .btn-cart:active {
                transform: translateY(-1px);
            }

            .btn-cart.disabled {
                opacity: 0.5;
                cursor: not-allowed;
                border-color: #e5e5e7;
                color: #86868b;
                background: #f5f5f7;
            }

            .security-badge {
                margin-top: 25px;
                padding: 18px;
                background: linear-gradient(135deg, #f5f5f7 0%, #e8e8ed 100%);
                border-radius: 12px;
                text-align: center;
            }

            .security-badge i {
                color: #34c759;
                font-size: 24px;
                margin-bottom: 8px;
            }

            .security-badge p {
                font-size: 13px;
                color: #6e6e73;
                margin: 0;
                font-weight: 600;
            }

            .empty-state {
                text-align: center;
                padding: 80px 20px;
                background: white;
                border-radius: 20px;
            }

            .empty-state i {
                font-size: 100px;
                color: #e5e5e7;
                margin-bottom: 25px;
            }

            .empty-state h2 {
                font-size: 32px;
                font-weight: 800;
                color: #1d1d1f;
                margin-bottom: 15px;
            }

            .empty-state p {
                font-size: 16px;
                color: #6e6e73;
                margin-bottom: 30px;
            }

            .btn-back {
                display: inline-block;
                padding: 14px 35px;
                background: linear-gradient(135deg, #007aff 0%, #0051d5 100%);
                color: white;
                text-decoration: none;
                border-radius: 12px;
                font-weight: 700;
                transition: all 0.3s ease;
                box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
            }

            .btn-back:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 30px rgba(0, 122, 255, 0.4);
            }

            @media (max-width: 1024px) {
                .checkout-grid {
                    grid-template-columns: 1fr;
                }

                .checkout-sidebar {
                    position: static;
                }

                .image-gallery {
                    grid-template-columns: 1fr;
                }

                .thumbnail-list {
                    flex-direction: row;
                    overflow-x: auto;
                    padding: 10px;
                }
            }

            @media (max-width: 768px) {
                .product-title {
                    font-size: 28px;
                }

                .main-image {
                    min-height: 300px;
                    padding: 20px;
                }
            }

            /* Styles for new sections */
            .checkout-section {
                background: white;
                border-radius: 20px;
                padding: 40px;
                margin-top: 30px;
                box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
            }

            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group.full-width {
                grid-column: span 2;
            }

            .form-label {
                display: block;
                font-size: 14px;
                font-weight: 500;
                color: #1d1d1f;
                margin-bottom: 8px;
            }

            .form-input {
                width: 100%;
                padding: 12px 15px;
                border: 1px solid #d2d2d7;
                border-radius: 12px;
                font-size: 15px;
                color: #1d1d1f;
                transition: all 0.2s;
            }

            .form-input:focus {
                border-color: #007aff;
                outline: none;
                box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
            }

            .payment-methods {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 15px;
            }

            .payment-option {
                border: 1px solid #d2d2d7;
                border-radius: 12px;
                padding: 15px;
                cursor: pointer;
                transition: all 0.2s;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                text-align: center;
            }

            .payment-option:hover {
                border-color: #007aff;
                background-color: #f5f5f7;
            }

            .payment-option.selected {
                border-color: #007aff;
                background-color: #f0f7ff;
                box-shadow: 0 0 0 1px #007aff inset;
            }

            .payment-icon {
                font-size: 24px;
                color: #1d1d1f;
            }
            
            .payment-name {
                font-size: 13px;
                font-weight: 500;
                color: #1d1d1f;
            }
        </style>

        <?php if ($product): ?>
            <div class="checkout-grid">
                <!-- Main Content Column -->
                <div class="checkout-main">
                    <!-- Product Showcase -->
                    <div class="product-showcase">
                    <div class="product-header">
                        <span class="product-badge"><?php echo htmlspecialchars($product['kategori']); ?></span>
                        <h1 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                        <p class="product-description"><?php echo nl2br(htmlspecialchars($product['deskripsi_produk'])); ?></p>
                    </div>

                    <!-- Image Gallery -->
                    <?php if (!empty($product['images'])): ?>
                        <div class="image-gallery">
                            <div class="thumbnail-list">
                                <?php foreach ($product['images'] as $index => $image): ?>
                                    <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                                         onclick="changeMainImage('<?php echo htmlspecialchars($image['foto_thumbnail']); ?>', this)">
                                        <img src="../../admin/uploads/<?php echo htmlspecialchars($image['foto_thumbnail']); ?>" 
                                             alt="<?php echo htmlspecialchars($image['warna']); ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="main-image">
                                <img id="mainProductImage" 
                                     src="../../admin/uploads/<?php echo htmlspecialchars($product['images'][0]['foto_thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Variants Section -->
                    <div class="variants-section">
                        <h2 class="section-title">Pilih Varian</h2>
                        <div class="variant-groups-container">
                            <?php 
                            // Mapping nama field ke label yang user-friendly
                            $field_labels = [
                                'warna' => 'Warna',
                                'warna_case' => 'Warna Case',
                                'penyimpanan' => 'Kapasitas',
                                'konektivitas' => 'Konektivitas',
                                'processor' => 'Chip',
                                'ram' => 'Memori',
                                'tipe' => 'Tipe',
                                'ukuran_case' => 'Ukuran Case',
                                'tipe_koneksi' => 'Konektivitas',
                                'material' => 'Bahan Case',
                                'ukuran' => 'Ukuran',
                                'pack' => 'Paket',
                                'aksesoris' => 'Aksesoris Tambahan'
                            ];

                            // Tampilkan grup opsi
                            foreach ($grouped_options as $field => $options): 
                                // Skip jika tidak ada opsi
                                if (empty($options)) continue;
                                
                                $label = isset($field_labels[$field]) ? $field_labels[$field] : ucfirst($field);
                            ?>
                                <div class="variant-group" data-group-field="<?php echo $field; ?>">
                                    <h3 class="variant-group-title">Pilih <?php echo $label; ?></h3>
                                    <div class="variant-options">
                                        <?php foreach ($options as $option): ?>
                                            <button class="variant-option-btn" 
                                                    onclick="selectOption('<?php echo $field; ?>', '<?php echo htmlspecialchars($option); ?>', this)">
                                                <?php echo htmlspecialchars($option); ?>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Hidden input untuk menyimpan state -->
                        <div id="selectionState" style="display: none;"></div>
                    </div>
                </div>

                <!-- Payment Method Section (Moved up) -->
                <div class="checkout-section">
                    <h2 class="section-title">Metode Pembayaran</h2>
                    <div class="payment-methods">
                        <div class="payment-option selected" onclick="selectPayment(this)">
                            <i class="fas fa-university payment-icon"></i>
                            <span class="payment-name">Transfer Bank</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment(this)">
                            <i class="fas fa-credit-card payment-icon"></i>
                            <span class="payment-name">Kartu Kredit</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment(this)">
                            <i class="fas fa-wallet payment-icon"></i>
                            <span class="payment-name">E-Wallet</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment(this)">
                            <i class="fas fa-store payment-icon"></i>
                            <span class="payment-name">Gerai Retail</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Section -->
                <div class="checkout-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 class="section-title" style="margin-bottom: 0;">Alamat Pengiriman</h2>
                        <?php if (!$selected_address): ?>
                            <button class="btn-address-action btn-change-address" onclick="openAddressModal('add')">
                                <i class="fas fa-plus"></i> Tambah Alamat
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($selected_address): ?>
                        <div class="address-display-card">
                            <span class="address-label"><?php echo htmlspecialchars($selected_address['label_alamat'] ?: 'Alamat Utama'); ?></span>
                            <div class="address-name"><?php echo htmlspecialchars($selected_address['username']); ?> <span style="font-weight: 400; color: #6e6e73;">| <?php echo htmlspecialchars($selected_address['no_hp']); ?></span></div>
                            <div class="address-text">
                                <?php echo nl2br(htmlspecialchars($selected_address['alamat_lengkap'])); ?><br>
                                <?php echo htmlspecialchars($selected_address['kecamatan']); ?>, <?php echo htmlspecialchars($selected_address['kota']); ?><br>
                                <?php echo htmlspecialchars($selected_address['provinsi']); ?>, <?php echo htmlspecialchars($selected_address['kode_post']); ?>
                            </div>
                            <div class="address-actions">
                                <button class="btn-address-action btn-change-address" onclick="openAddressListModal()">
                                    Ganti Alamat
                                </button>
                                <button class="btn-address-action btn-edit-address" onclick='openEditAddressModal(<?php echo json_encode($selected_address); ?>)'>
                                    Edit Alamat
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="padding: 40px; background: #fbfbfd; border: 1px dashed #d2d2d7; border-radius: 12px;">
                            <i class="fas fa-map-marker-alt" style="font-size: 40px; color: #d2d2d7; margin-bottom: 15px;"></i>
                            <p style="color: #86868b; margin-bottom: 20px;">Belum ada alamat pengiriman yang dipilih.</p>
                            <button class="btn-address-action btn-change-address" onclick="openAddressModal('add')">
                                Tambah Alamat Baru
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                </div> <!-- End of .checkout-main -->

                <!-- Checkout Sidebar -->
                <div class="checkout-sidebar">
                    <h2 class="sidebar-title">Ringkasan Belanja</h2>
                    
                    <div class="quantity-container">
                        <span class="summary-label">Jumlah</span>
                        <div class="quantity-selector">
                            <button class="qty-btn" onclick="updateQuantity(-1)">−</button>
                            <input type="number" id="productQuantity" value="1" min="1" readonly>
                            <button class="qty-btn" onclick="updateQuantity(1)">+</button>
                        </div>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Harga Produk</span>
                        <span class="summary-value" id="productPrice">Rp 0</span>
                    </div>
                    
                    <div class="summary-row" id="discountRow" style="display: none;">
                        <span class="summary-label">Diskon</span>
                        <span class="summary-value" id="discountAmount" style="color: #34c759;">- Rp 0</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Pajak (11%)</span>
                        <span class="summary-value" id="taxAmount">Rp 0</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Ongkir</span>
                        <span class="summary-value" style="color: #34c759;">Gratis</span>
                    </div>
                    
                    <div class="summary-total">
                        <span class="summary-label">Total</span>
                        <span class="summary-value" id="totalPrice">Rp 0</span>
                    </div>
                    
                    <button class="btn-checkout" onclick="processCheckout()" disabled style="opacity: 0.5; cursor: not-allowed;">
                        <i class="fas fa-lock"></i> Checkout Sekarang
                    </button>

                    <button class="btn-cart" onclick="addToCart()" disabled>
                        <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                    </button>

                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <p>Pembayaran Aman & Terpercaya</p>
                        <p style="font-size: 11px; margin-top: 5px; opacity: 0.7;">256-bit SSL Encryption</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h2>Produk Tidak Ditemukan</h2>
                <p>Maaf, produk yang Anda cari tidak tersedia atau sudah tidak ada.</p>
                <a href="../index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Data varian dari PHP
        const productVariants = <?php echo json_encode($product['variants']); ?>;
        const requiredFields = <?php echo json_encode(array_keys($grouped_options)); ?>;
        const productImages = <?php echo json_encode($product['images']); ?>;
        const colorFieldName = "<?php 
           $pType = $product['type'];
           echo isset($product_config[$pType]['col_warna_img']) ? $product_config[$pType]['col_warna_img'] : 'warna';
        ?>";
        let selectedAttributes = {};
        let currentQuantity = 1;
        let currentVariant = null;
        
        function changeMainImage(thumbnail, element) {
            // Update main image
            document.getElementById('mainProductImage').src = '../../admin/uploads/' + thumbnail;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail-item').forEach(item => {
                item.classList.remove('active');
            });
            element.classList.add('active');
        }

        function selectOption(field, value, btnElement) {
            // Update state seleksi
            selectedAttributes[field] = value;
            
            // Update UI tombol
            const group = btnElement.closest('.variant-group');
            group.querySelectorAll('.variant-option-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            btnElement.classList.add('selected');

            // Ganti gambar jika yang dipilih adalah warna
            if (field === colorFieldName) {
                const matchingImage = productImages.find(img => img.warna === value);
                if (matchingImage) {
                    const mainImg = document.getElementById('mainProductImage');
                    
                    // Update Main Image
                    mainImg.style.opacity = '0.5';
                    setTimeout(() => {
                        mainImg.src = '../../admin/uploads/' + matchingImage.foto_thumbnail;
                        mainImg.style.opacity = '1';
                    }, 200);
                    
                    // Update active thumbnail
                    document.querySelectorAll('.thumbnail-item').forEach(item => {
                        const img = item.querySelector('img');
                        if (img && img.alt === value) {
                            item.classList.add('active');
                            // Scroll thumbnail ke view jika perlu (untuk mobile)
                            item.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                        } else {
                            item.classList.remove('active');
                        }
                    });
                }
            }

            // Cek apakah semua atribut sudah dipilih
            checkSelection();
        }

        function checkSelection() {
            // Cek kelengkapan
            const isComplete = requiredFields.every(field => selectedAttributes[field]);
            
            if (isComplete) {
                // Cari varian yang cocok
                const matchedVariant = productVariants.find(variant => {
                    return requiredFields.every(field => variant[field] === selectedAttributes[field]);
                });

                if (matchedVariant) {
                    currentVariant = matchedVariant;
                    updateCheckoutInfo(matchedVariant);
                } else {
                    currentVariant = null;
                    // Kombinasi tidak tersedia
                    showUnavailable();
                }
            }
        }

        function updateCheckoutInfo(variant) {
            const unitPrice = parseInt(variant.harga_diskon > 0 ? variant.harga_diskon : variant.harga);
            const unitOriginalPrice = parseInt(variant.harga);
            
            const price = unitPrice * currentQuantity;
            const originalPrice = unitOriginalPrice * currentQuantity;
            const hasDiscount = variant.harga_diskon > 0 && variant.harga_diskon < variant.harga;
            
            // Hitung pajak dan total
            const tax = price * 0.11;
            const total = price + tax;
            const discount = hasDiscount ? (originalPrice - price) : 0;
            
            // Update UI
            document.getElementById('productPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('taxAmount').textContent = 'Rp ' + Math.round(tax).toLocaleString('id-ID');
            document.getElementById('totalPrice').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
            
            // Show/hide discount row
            if (hasDiscount) {
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmount').textContent = '- Rp ' + discount.toLocaleString('id-ID');
            } else {
                document.getElementById('discountRow').style.display = 'none';
            }

            // Aktifkan tombol checkout dan cart
            const btnCheckout = document.querySelector('.btn-checkout');
            const btnCart = document.querySelector('.btn-cart');
            
            btnCheckout.disabled = false;
            btnCheckout.innerHTML = '<i class="fas fa-lock"></i> Checkout Sekarang';
            btnCheckout.style.opacity = '1';
            btnCheckout.style.cursor = 'pointer';

            btnCart.disabled = false;
            btnCart.classList.remove('disabled');
        }

        function showUnavailable() {
            const btnCheckout = document.querySelector('.btn-checkout');
            const btnCart = document.querySelector('.btn-cart');

            btnCheckout.disabled = true;
            btnCheckout.innerHTML = '<i class="fas fa-times"></i> Stok Tidak Tersedia';
            btnCheckout.style.opacity = '0.5';
            btnCheckout.style.cursor = 'not-allowed';

            btnCart.disabled = true;
            btnCart.classList.add('disabled');
            
            // Reset harga
            document.getElementById('productPrice').textContent = '-';
            document.getElementById('taxAmount').textContent = '-';
            document.getElementById('totalPrice').textContent = '-';
        }

        function updateQuantity(change) {
            const input = document.getElementById('productQuantity');
            let newValue = currentQuantity + change;
            
            // Validate min 1
            if (newValue < 1) newValue = 1;
            
            // Validate max stock if variant selected
            if (currentVariant && currentVariant.jumlah_stok) {
                // If stock is limited
                const stock = parseInt(currentVariant.jumlah_stok);
                if (newValue > stock) {
                    alert('Maaf, stok hanya tersedia ' + stock);
                    return;
                }
            }
            
            currentQuantity = newValue;
            input.value = currentQuantity;
            
            // Update prices if variant selected
            if (currentVariant) {
                updateCheckoutInfo(currentVariant);
            }
        }

        function selectPayment(element) {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
        }

        function processCheckout() {
            const isComplete = requiredFields.every(field => selectedAttributes[field]);
            
            if (!isComplete) {
                alert('Silakan lengkapi pilihan varian produk terlebih dahulu!');
                return;
            }
            
            // TODO: Implement checkout process
            alert('Fitur checkout akan segera tersedia!\n\nProduk Anda akan segera diproses.');
        }

        function addToCart() {
            const isComplete = requiredFields.every(field => selectedAttributes[field]);
            
            if (!isComplete) {
                alert('Silakan lengkapi pilihan varian produk terlebih dahulu!');
                return;
            }

            // Get thumbnail from current main image src
            const mainImgSrc = document.getElementById('mainProductImage').src;
            const thumbnail = mainImgSrc.split('/').pop(); // Extract filename

            // Prepare data
            const formData = new FormData();
            formData.append('product_id', <?php echo $product_id; ?>);
            formData.append('jumlah', currentQuantity);
            formData.append('tipe', '<?php echo $product_type; ?>');
            formData.append('thumbnail', thumbnail);

            // Send to API
            const btnCart = document.querySelector('.btn-cart');
            const originalText = btnCart.innerHTML;
            btnCart.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            btnCart.disabled = true;

            fetch('api/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show custom success modal
                    document.getElementById('cartSuccessModal').classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    alert('Gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghubungi server.');
            })
            .finally(() => {
                btnCart.innerHTML = originalText;
                btnCart.disabled = false;
            });
        }

        // Auto-select first options on load (optional)
        /*
        window.addEventListener('DOMContentLoaded', function() {
            // Bisa ditambahkan logic auto-select opsi pertama disini jika diinginkan
        });
        */
    </script>

    <!-- Address List Modal -->
    <div id="addressListModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Pilih Alamat Pengiriman</h3>
                <button class="btn-close-modal" onclick="closeModal('addressListModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 20px;">
                    <button class="btn-address-action btn-change-address" style="width: 100%; border-style: dashed;" onclick="openAddressModal('add')">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i> Tambah Alamat Baru
                    </button>
                </div>
                
                <div class="address-list">
                    <?php foreach ($all_addresses as $addr): ?>
                        <div class="address-list-item <?php echo ($selected_address && $selected_address['id'] == $addr['id']) ? 'selected' : ''; ?>" 
                             onclick="selectAddress(<?php echo $addr['id']; ?>)">
                            <div class="address-content">
                                <span class="address-label" style="font-size: 11px;"><?php echo htmlspecialchars($addr['label_alamat'] ?: 'Utama'); ?></span>
                                <div style="font-weight: 600; margin-bottom: 4px;"><?php echo htmlspecialchars($addr['username']); ?></div>
                                <div style="font-size: 14px; color: #6e6e73;"><?php echo htmlspecialchars($addr['no_hp']); ?></div>
                                <div style="font-size: 14px; color: #6e6e73; margin-top: 4px;">
                                    <?php echo htmlspecialchars($addr['alamat_lengkap']); ?>, <?php echo htmlspecialchars($addr['kota']); ?>
                                </div>
                            </div>
                            <div class="address-select-indicator"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div id="addressFormModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title" id="modalFormTitle">Tambah Alamat</h3>
                <button class="btn-close-modal" onclick="closeModal('addressFormModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addressForm" method="POST">
                    <input type="hidden" name="address_action" id="formAction" value="add">
                    <input type="hidden" name="address_id" id="addressId" value="">
                    
                    <div class="form-grid-modal">
                        <div class="form-group">
                            <label class="form-label">Label Alamat</label>
                            <input type="text" name="label_alamat" id="labelAlamat" class="form-input" placeholder="Rumah, Kantor, dll">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="username" id="namaUser" class="form-input" required placeholder="Nama Lengkap">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor HP</label>
                            <input type="tel" name="no_hp" id="nomorHp" class="form-input" required placeholder="08xxxxxxxxxx">
                        </div>
                         <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-input" required placeholder="Email">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="alamatLengkap" class="form-input" rows="3" required placeholder="Nama jalan, nomor rumah, detail lainnya"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="provinsi" id="provinsi" class="form-input" required placeholder="Provinsi">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kota/Kabupaten</label>
                            <input type="text" name="kota" id="kota" class="form-input" required placeholder="Kota">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" name="kecamatan" id="kecamatan" class="form-input" required placeholder="Kecamatan">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="kode_post" id="kodePost" class="form-input" required placeholder="Kode Pos">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnDeleteAddress" class="btn-modal btn-danger" style="margin-right: auto; display: none;" onclick="deleteAddress()">Hapus</button>
                <button type="button" class="btn-modal btn-secondary" onclick="closeModal('addressFormModal')">Batal</button>
                <button type="button" class="btn-modal btn-primary" onclick="document.getElementById('addressForm').submit()">Simpan</button>
            </div>
        </div>
    </div>
    
    <!-- Hidden Select Form -->
    <form id="selectAddressForm" method="POST" style="display: none;">
        <input type="hidden" name="address_action" value="select">
        <input type="hidden" name="address_id" id="selectAddressId">
    </form>
    
    <form id="deleteAddressForm" method="POST" style="display: none;">
        <input type="hidden" name="address_action" value="delete">
        <input type="hidden" name="address_id" id="deleteAddressId">
    </form>

    <!-- Cart Success Modal -->
    <div id="cartSuccessModal" class="modal-overlay">
        <div class="modal-box" style="max-width: 400px; text-align: center;">
            <div class="modal-body" style="padding: 40px 30px;">
                <div style="width: 70px; height: 70px; background: #34c759; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-check" style="font-size: 35px; color: white;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 700; color: #1d1d1f; margin-bottom: 10px;">Berhasil Disimpan</h3>
                <p style="font-size: 15px; color: #6e6e73; margin-bottom: 30px;">Produk telah berhasil ditambahkan ke keranjang belanja Anda.</p>
                <div style="display: flex; gap: 15px; flex-direction: column;">
                     <button class="btn-modal btn-primary" onclick="closeModal('cartSuccessModal')" style="width: 100%; padding: 14px; font-size: 16px; border-radius: 12px; background: #007aff;">OK, Lanjut Belanja</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal-overlay">
        <div class="modal-box" style="max-width: 450px;">
            <div class="modal-body" style="padding: 40px 30px; text-align: center;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff3b30 0%, #ff6b60 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 10px 30px rgba(255, 59, 48, 0.3);">
                    <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 700; color: #1d1d1f; margin-bottom: 12px;">Hapus Alamat?</h3>
                <p style="font-size: 15px; color: #6e6e73; margin-bottom: 35px; line-height: 1.5;">Apakah Anda yakin ingin menghapus alamat ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button class="btn-modal btn-secondary" onclick="closeModal('deleteConfirmModal')" style="flex: 1; padding: 14px; font-size: 16px; border-radius: 12px; transition: all 0.2s;">
                        <i class="fas fa-times" style="margin-right: 6px;"></i>Batal
                    </button>
                    <button class="btn-modal btn-danger" onclick="confirmDeleteAddress()" style="flex: 1; padding: 14px; font-size: 16px; border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 59, 48, 0.3); transition: all 0.2s;">
                        <i class="fas fa-trash-alt" style="margin-right: 6px;"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddressListModal() {
            document.getElementById('addressListModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function openAddressModal(type) {
            closeModal('addressListModal'); // Close list if open
            const modal = document.getElementById('addressFormModal');
            const form = document.getElementById('addressForm');
            
            // Reset form
            form.reset();
            document.getElementById('btnDeleteAddress').style.display = 'none';
            
            if (type === 'add') {
                document.getElementById('modalFormTitle').textContent = 'Tambah Alamat Baru';
                document.getElementById('formAction').value = 'add';
                document.getElementById('addressId').value = '';
            }
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function openEditAddressModal(data) {
            openAddressModal('edit'); // Open and reset
            
            // Fill data
            document.getElementById('modalFormTitle').textContent = 'Edit Alamat';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('addressId').value = data.id;
            
            document.getElementById('labelAlamat').value = data.label_alamat || '';
            document.getElementById('namaUser').value = data.username || '';
            document.getElementById('nomorHp').value = data.no_hp || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('alamatLengkap').value = data.alamat_lengkap || '';
            document.getElementById('provinsi').value = data.provinsi || '';
            document.getElementById('kota').value = data.kota || '';
            document.getElementById('kecamatan').value = data.kecamatan || '';
            document.getElementById('kodePost').value = data.kode_post || '';
            
            // Show delete button
            document.getElementById('btnDeleteAddress').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }
        
        function selectAddress(id) {
            document.getElementById('selectAddressId').value = id;
            document.getElementById('selectAddressForm').submit();
        }
        
        function deleteAddress() {
            // Show modern confirmation modal instead of alert
            document.getElementById('deleteConfirmModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function confirmDeleteAddress() {
            const id = document.getElementById('addressId').value;
            document.getElementById('deleteAddressId').value = id;
            document.getElementById('deleteAddressForm').submit();
        }
        
        // Close modal when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>
</html>
