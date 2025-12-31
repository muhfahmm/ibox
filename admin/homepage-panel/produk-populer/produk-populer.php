<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Ambil data produk populer yang sudah ada
$query_populer = "SELECT * FROM home_produk_populer ORDER BY urutan, created_at DESC";
$result_populer = mysqli_query($db, $query_populer);
$populer_count = mysqli_num_rows($result_populer);
$slider_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_image_slider"))['total'];

// Fungsi untuk mengambil detail produk berdasarkan tipe
function getProductDetail($db, $tipe, $produk_id)
{
    $configs = [
        'iphone' => [
            'table' => 'admin_produk_iphone',
            'gambar' => 'admin_produk_iphone_gambar',
            'kombinasi' => 'admin_produk_iphone_kombinasi'
        ],
        'ipad' => [
            'table' => 'admin_produk_ipad',
            'gambar' => 'admin_produk_ipad_gambar',
            'kombinasi' => 'admin_produk_ipad_kombinasi'
        ],
        'mac' => [
            'table' => 'admin_produk_mac',
            'gambar' => 'admin_produk_mac_gambar',
            'kombinasi' => 'admin_produk_mac_kombinasi'
        ],
        'music' => [
            'table' => 'admin_produk_music',
            'gambar' => 'admin_produk_music_gambar',
            'kombinasi' => 'admin_produk_music_kombinasi'
        ],
        'watch' => [
            'table' => 'admin_produk_watch',
            'gambar' => 'admin_produk_watch_gambar',
            'kombinasi' => 'admin_produk_watch_kombinasi'
        ],
        'aksesoris' => [
            'table' => 'admin_produk_aksesoris',
            'gambar' => 'admin_produk_aksesoris_gambar',
            'kombinasi' => 'admin_produk_aksesoris_kombinasi'
        ],
        'airtag' => [
            'table' => 'admin_produk_airtag',
            'gambar' => 'admin_produk_airtag_gambar',
            'kombinasi' => 'admin_produk_airtag_kombinasi'
        ]
    ];

    if (!isset($configs[$tipe])) return null;

    $config = $configs[$tipe];

    // Query untuk mengambil detail produk
    $query = "SELECT 
                p.*,
                (SELECT foto_thumbnail FROM {$config['gambar']} WHERE produk_id = p.id LIMIT 1) as thumbnail,
                (SELECT MIN(harga) FROM {$config['kombinasi']} WHERE produk_id = p.id) as harga_terendah
              FROM {$config['table']} p 
              WHERE p.id = '$produk_id'";

    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result);
}

// Hitung jumlah per kategori untuk sidebar (sama seperti di airtag.php)
$tables = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'watch' => 'admin_produk_watch',
    'music' => 'admin_produk_music',
    'aksesoris' => 'admin_produk_aksesoris',
    'airtag' => 'admin_produk_airtag'
];

$category_data = [];
foreach ($tables as $key => $table_name) {
    $count_query = "SELECT COUNT(*) as total, MIN(id) as first_id FROM $table_name";
    $count_result = mysqli_query($db, $count_query);
    $data = mysqli_fetch_assoc($count_result);

    $category_data[$key] = [
        'count' => $data['total'],
        'first_id' => $data['first_id']
    ];
}

$airtag_first_id_query = "SELECT MIN(id) as first_id FROM admin_produk_airtag";
$airtag_first_id_result = mysqli_query($db, $airtag_first_id_query);
$airtag_first_id_data = mysqli_fetch_assoc($airtag_first_id_result);
$airtag_first_id = $airtag_first_id_data['first_id'];

// Ambil data produk terbaru dari tabel home_produk_terbaru
$query = "SELECT 
            hpt.*,
            CASE 
                WHEN hpt.tipe_produk = 'iphone' THEN iphone.nama_produk
                WHEN hpt.tipe_produk = 'ipad' THEN ipad.nama_produk
                WHEN hpt.tipe_produk = 'mac' THEN mac.nama_produk
                WHEN hpt.tipe_produk = 'music' THEN music.nama_produk
                WHEN hpt.tipe_produk = 'watch' THEN watch.nama_produk
                WHEN hpt.tipe_produk = 'aksesoris' THEN aksesoris.nama_produk
                WHEN hpt.tipe_produk = 'airtag' THEN airtag.nama_produk
            END as nama_produk,
            CASE 
                WHEN hpt.tipe_produk = 'iphone' THEN iphone_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'ipad' THEN ipad_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'mac' THEN mac_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'music' THEN music_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'watch' THEN watch_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'aksesoris' THEN aksesoris_gambar.foto_thumbnail
                WHEN hpt.tipe_produk = 'airtag' THEN airtag_gambar.foto_thumbnail
            END as foto_thumbnail,
            CASE 
                WHEN hpt.tipe_produk = 'iphone' THEN MIN(iphone_kombinasi.harga)
                WHEN hpt.tipe_produk = 'ipad' THEN MIN(ipad_kombinasi.harga)
                WHEN hpt.tipe_produk = 'mac' THEN MIN(mac_kombinasi.harga)
                WHEN hpt.tipe_produk = 'music' THEN MIN(music_kombinasi.harga)
                WHEN hpt.tipe_produk = 'watch' THEN MIN(watch_kombinasi.harga)
                WHEN hpt.tipe_produk = 'aksesoris' THEN MIN(aksesoris_kombinasi.harga)
                WHEN hpt.tipe_produk = 'airtag' THEN MIN(airtag_kombinasi.harga)
            END as harga_terendah
          FROM home_produk_terbaru hpt
          LEFT JOIN admin_produk_iphone iphone ON hpt.tipe_produk = 'iphone' AND hpt.produk_id = iphone.id
          LEFT JOIN admin_produk_ipad ipad ON hpt.tipe_produk = 'ipad' AND hpt.produk_id = ipad.id
          LEFT JOIN admin_produk_mac mac ON hpt.tipe_produk = 'mac' AND hpt.produk_id = mac.id
          LEFT JOIN admin_produk_music music ON hpt.tipe_produk = 'music' AND hpt.produk_id = music.id
          LEFT JOIN admin_produk_watch watch ON hpt.tipe_produk = 'watch' AND hpt.produk_id = watch.id
          LEFT JOIN admin_produk_aksesoris aksesoris ON hpt.tipe_produk = 'aksesoris' AND hpt.produk_id = aksesoris.id
          LEFT JOIN admin_produk_airtag airtag ON hpt.tipe_produk = 'airtag' AND hpt.produk_id = airtag.id
          LEFT JOIN admin_produk_iphone_gambar iphone_gambar ON hpt.tipe_produk = 'iphone' AND hpt.produk_id = iphone_gambar.produk_id
          LEFT JOIN admin_produk_ipad_gambar ipad_gambar ON hpt.tipe_produk = 'ipad' AND hpt.produk_id = ipad_gambar.produk_id
          LEFT JOIN admin_produk_mac_gambar mac_gambar ON hpt.tipe_produk = 'mac' AND hpt.produk_id = mac_gambar.produk_id
          LEFT JOIN admin_produk_music_gambar music_gambar ON hpt.tipe_produk = 'music' AND hpt.produk_id = music_gambar.produk_id
          LEFT JOIN admin_produk_watch_gambar watch_gambar ON hpt.tipe_produk = 'watch' AND hpt.produk_id = watch_gambar.produk_id
          LEFT JOIN admin_produk_aksesoris_gambar aksesoris_gambar ON hpt.tipe_produk = 'aksesoris' AND hpt.produk_id = aksesoris_gambar.produk_id
          LEFT JOIN admin_produk_airtag_gambar airtag_gambar ON hpt.tipe_produk = 'airtag' AND hpt.produk_id = airtag_gambar.produk_id
          LEFT JOIN admin_produk_iphone_kombinasi iphone_kombinasi ON hpt.tipe_produk = 'iphone' AND hpt.produk_id = iphone_kombinasi.produk_id
          LEFT JOIN admin_produk_ipad_kombinasi ipad_kombinasi ON hpt.tipe_produk = 'ipad' AND hpt.produk_id = ipad_kombinasi.produk_id
          LEFT JOIN admin_produk_mac_kombinasi mac_kombinasi ON hpt.tipe_produk = 'mac' AND hpt.produk_id = mac_kombinasi.produk_id
          LEFT JOIN admin_produk_music_kombinasi music_kombinasi ON hpt.tipe_produk = 'music' AND hpt.produk_id = music_kombinasi.produk_id
          LEFT JOIN admin_produk_watch_kombinasi watch_kombinasi ON hpt.tipe_produk = 'watch' AND hpt.produk_id = watch_kombinasi.produk_id
          LEFT JOIN admin_produk_aksesoris_kombinasi aksesoris_kombinasi ON hpt.tipe_produk = 'aksesoris' AND hpt.produk_id = aksesoris_kombinasi.produk_id
          LEFT JOIN admin_produk_airtag_kombinasi airtag_kombinasi ON hpt.tipe_produk = 'airtag' AND hpt.produk_id = airtag_kombinasi.produk_id
          GROUP BY hpt.id, hpt.produk_id, hpt.tipe_produk
          ORDER BY hpt.urutan ASC, hpt.created_at DESC";
$result = mysqli_query($db, $query);

// Hitung jumlah produk terbaru
$terbaru_count = mysqli_num_rows($result);

// Hitung jumlah produk kategori lain untuk sidebar DAN AMBIL ID PERTAMA
$tables = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'watch' => 'admin_produk_watch',
    'music' => 'admin_produk_music',
    'aksesoris' => 'admin_produk_aksesoris',
    'airtag' => 'admin_produk_airtag'
];

// Array untuk menyimpan jumlah dan ID pertama setiap kategori
$category_data = [];

foreach ($tables as $key => $table_name) {
    $count_query = "SELECT COUNT(*) as total, MIN(id) as first_id FROM $table_name";
    $count_result = mysqli_query($db, $count_query);
    $data = mysqli_fetch_assoc($count_result);
    
    $category_data[$key] = [
        'count' => $data['total'],
        'first_id' => $data['first_id']
    ];
}

// Dapatkan ID pertama untuk AirTag
$airtag_first_id_query = "SELECT MIN(id) as first_id FROM admin_produk_airtag";
$airtag_first_id_result = mysqli_query($db, $airtag_first_id_query);
$airtag_first_id_data = mysqli_fetch_assoc($airtag_first_id_result);
$airtag_first_id = $airtag_first_id_data['first_id'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Produk Populer</title>
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
            background-color: #f5f7fb;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo i {
            font-size: 28px;
            color: #4a6cf7;
        }

        .logo h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .menu-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            padding: 0 20px;
            margin-bottom: 15px;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu ul li {
            margin-bottom: 5px;
        }

        .sidebar-menu ul li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu ul li a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
            padding-left: 25px;
        }

        .sidebar-menu ul li.active a {
            background-color: rgba(74, 108, 247, 0.15);
            color: #fff;
            border-left: 4px solid #4a6cf7;
        }

        .sidebar-menu ul li a i {
            font-size: 18px;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .sidebar-menu ul li a span {
            font-size: 15px;
            font-weight: 400;
        }

        .badge {
            background-color: #4a6cf7;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: auto;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .logout-btn {
            margin-left: auto;
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
            transition: color 0.3s;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #fff;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .btn-add {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
            color: white;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #eaeaea;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .card-body {
            padding: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
        }

        table thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
        }

        table tbody td {
            padding: 15px;
            font-size: 15px;
            vertical-align: top;
        }

        .product-info {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .thumbnail-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #4a6cf7;
        }

        .product-details {
            flex: 1;
        }

        .product-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stat-badge {
            background: #f8f9fa;
            border: 1px solid #eaeaea;
            border-radius: 15px;
            padding: 3px 10px;
            font-size: 12px;
            color: #666;
        }

        .stat-badge i {
            margin-right: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-delete, .btn-view {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }

        .btn-edit:hover {
            background-color: #bbdefb;
        }

        .btn-view {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .btn-view:hover {
            background-color: #ffeaa7;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }

        .btn-delete:hover {
            background-color: #ffcdd2;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .no-data i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .price-range {
            font-weight: 600;
            color: #4a6cf7;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-tersedia {
            background-color: #d4edda;
            color: #155724;
        }

        .status-habis {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }
            
            .product-info {
                flex-direction: column;
            }
            
            .product-stats {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar (sama dengan airtag.php) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-apple-alt"></i>
                    <h2>iBox Admin</h2>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3 class="section-title">Panel Produk</h3>
                    <ul>
                        <li>
                            <a href="../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/categories/kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/ipad/ipad.php<?php echo $category_data['ipad']['first_id'] ? '?id=' . $category_data['ipad']['first_id'] : ''; ?>">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $category_data['ipad']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/iphone/iphone.php<?php echo $category_data['iphone']['first_id'] ? '?id=' . $category_data['iphone']['first_id'] : ''; ?>">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge"><?php echo $category_data['iphone']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/mac/mac.php<?php echo $category_data['mac']['first_id'] ? '?id=' . $category_data['mac']['first_id'] : ''; ?>">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge"><?php echo $category_data['mac']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/music/music.php<?php echo $category_data['music']['first_id'] ? '?id=' . $category_data['music']['first_id'] : ''; ?>">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge"><?php echo $category_data['music']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/watch/watch.php<?php echo $category_data['watch']['first_id'] ? '?id=' . $category_data['watch']['first_id'] : ''; ?>">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge"><?php echo $category_data['watch']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/aksesoris/aksesoris.php<?php echo $category_data['aksesoris']['first_id'] ? '?id=' . $category_data['aksesoris']['first_id'] : ''; ?>">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge"><?php echo $category_data['aksesoris']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="airtag.php<?php echo $airtag_first_id ? '?id=' . $airtag_first_id : ''; ?>">
                                <i class="fas fa-tag"></i>
                                <span>AirTag</span>
                                <span class="badge"><?php echo $category_data['airtag']['count']; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Homepage Panel</h3>
                    <ul>
                        <li>
                            <a href="../../homepage-panel/image-slider/image-slider.php">
                                <i class="fas fa-images"></i>
                                <span>Image slider</span>
                                <span class="badge"><?php echo $slider_count; ?></span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="../../homepage-panel/produk-populer/produk-populer.php">
                                <i class="fas fa-fire"></i>
                                <span>Produk Apple Populer</span>
                                <span class="badge"><?php echo $populer_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/produk-terbaru/produk-terbaru.php">
                                <i class="fas fa-bolt"></i>
                                <span>Produk Terbaru</span>
                                <span class="badge"><?php echo $terbaru_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/image-grid/image-grid.php">
                                <i class="fas fa-th"></i>
                                <span>Image grid</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/trade-in/trade-in.php">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Trade in</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/aksesori-unggulan/aksesori-unggulan.php">
                                <i class="fas fa-gem"></i>
                                <span>Aksesori unggulan</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/checkout-sekarang/chekout-sekarang.php">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Checkout sekarang</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Lainnya</h3>
                    <ul>
                        <li>
                            <a href="../../other/users/users.php">
                                <i class="fas fa-users"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../other/orders/order.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Pesanan</span>
                                <span class="badge badge-warning">5</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../other/settings/settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=4a6cf7&color=fff" alt="Admin">
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($admin_username); ?></h4>
                        <p>Admin iBox</p>
                    </div>
                    <a href="../../auth/logout.php" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-fire me-2"></i> Kelola Produk Populer</h1>
                <a href="add-produk-populer.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Produk Populer
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list me-2"></i> Daftar Produk Populer</h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <?php if (mysqli_num_rows($result_populer) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produk</th>
                                        <th>Tipe</th>
                                        <th>Label</th>
                                        <th>Urutan</th>
                                        <th>Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($item = mysqli_fetch_assoc($result_populer)):
                                        $detail = getProductDetail($db, $item['tipe_produk'], $item['produk_id']);
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $item['id']; ?></strong></td>
                                            <td>
                                                <div class="product-info">
                                                    <?php if (!empty($detail['thumbnail'])): ?>
                                                        <img src="../../uploads/<?php echo htmlspecialchars($detail['thumbnail']); ?>"
                                                            alt="Thumbnail" class="thumbnail-img">
                                                    <?php else: ?>
                                                        <div class="thumbnail-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-image" style="color: #ccc; font-size: 24px;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="product-details">
                                                        <span class="tipe-badge">
                                                            <?php echo strtoupper($item['tipe_produk']); ?>
                                                        </span>
                                                        <div class="product-title">
                                                            <?php echo htmlspecialchars($detail['nama_produk'] ?? 'Produk tidak ditemukan'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo ucfirst($item['tipe_produk']); ?>
                                            </td>
                                            <td>
                                                <span class="label-badge" style="background: #ff6b6b; color: white; padding: 3px 10px; border-radius: 15px;">
                                                    <?php echo htmlspecialchars($item['label']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $item['urutan']; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($detail['harga_terendah'])): ?>
                                                    <div class="price-range">
                                                        Rp <?php echo number_format($detail['harga_terendah'], 0, ',', '.'); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada harga</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="edit-produk-populer.php?id=<?php echo $item['id']; ?>" class="btn-edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="delete-produk-populer.php?id=<?php echo $item['id']; ?>" class="btn-delete"
                                                        onclick="return confirm('Yakin ingin menghapus produk ini dari daftar populer?')">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-data">
                                <i class="fas fa-fire" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
                                <h4>Belum ada produk populer</h4>
                                <p>Mulai dengan menambahkan produk populer pertama Anda</p>
                                <a href="add-produk-populer.php" class="btn-add mt-3" style="display: inline-flex;">
                                    <i class="fas fa-plus"></i> Tambah Produk Populer
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>