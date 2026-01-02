<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Ambil data image grid yang sudah ada
$query_grid = "SELECT * FROM home_grid ORDER BY urutan, created_at DESC";
$result_grid = mysqli_query($db, $query_grid);
$grid_count = mysqli_num_rows($result_grid);

// Hitung jumlah untuk sidebar badges
$iphone_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_iphone"))['total'];
$ipad_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_ipad"))['total'];
$mac_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_mac"))['total'];
$watch_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_watch"))['total'];
$music_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_music"))['total'];
$aksesoris_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_aksesoris"))['total'];
$airtag_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_airtag"))['total'];

$slider_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_image_slider"))['total'];
$populer_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_produk_populer"))['total'];
$terbaru_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_produk_terbaru"))['total'];
$trade_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_trade_in"))['total'];
$aksesori_home_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_aksesori"))['total'];
$checkout_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_checkout"))['total'];

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

    $query = "SELECT 
                p.*,
                (SELECT foto_thumbnail FROM {$config['gambar']} WHERE produk_id = p.id LIMIT 1) as thumbnail,
                (SELECT MIN(harga) FROM {$config['kombinasi']} WHERE produk_id = p.id) as harga_terendah
              FROM {$config['table']} p 
              WHERE p.id = '$produk_id'";

    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Image Grid - Admin Panel iBox</title>
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
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 200px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 100;
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
            position: relative;
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
            padding: 30px;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .page-title p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .btn-add {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: #fff;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.3);
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 108, 247, 0.4);
        }

        /* Table Card */
        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8f9fa;
            padding: 15px 25px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px 25px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            vertical-align: middle;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .thumbnail-img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .product-info-cell {
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }

        .tipe-badge {
            font-size: 10px;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            background: #eee;
            color: #666;
            width: fit-content;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .btn-edit:hover {
            background-color: #1976d2;
            color: #fff;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
            color: #fff;
        }

        .alert {
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            padding: 50px 25px;
            color: #999;
        }

        .no-data i {
            display: block;
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .label-badge {
            font-size: 12px;
            font-weight: 500;
            background: #e0e0e0;
            padding: 3px 10px;
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
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
                            <a href="../../products-panel/ipad/ipad.php">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $ipad_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/iphone/iphone.php">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge"><?php echo $iphone_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/mac/mac.php">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge"><?php echo $mac_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/music/music.php">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge"><?php echo $music_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/watch/watch.php">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge"><?php echo $watch_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/aksesoris/aksesoris.php">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge"><?php echo $aksesoris_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/airtag/airtag.php">
                                <i class="fas fa-tag"></i>
                                <span>AirTag</span>
                                <span class="badge"><?php echo $airtag_count; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Homepage Panel</h3>
                    <ul>
                        <li>
                            <a href="../image-slider/image-slider.php">
                                <i class="fas fa-images"></i>
                                <span>Image slider</span>
                                <span class="badge"><?php echo $slider_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../produk-populer/produk-populer.php">
                                <i class="fas fa-fire"></i>
                                <span>Produk Apple Populer</span>
                                <span class="badge"><?php echo $populer_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../produk-terbaru/produk-terbaru.php">
                                <i class="fas fa-bolt"></i>
                                <span>Produk Terbaru</span>
                                <span class="badge"><?php echo $terbaru_count; ?></span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="image-grid.php">
                                <i class="fas fa-th"></i>
                                <span>Image grid</span>
                                <span class="badge"><?php echo $grid_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../trade-in/trade-in.php">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Trade in</span>
                                <span class="badge"><?php echo $trade_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../aksesori-unggulan/aksesori-unggulan.php">
                                <i class="fas fa-gem"></i>
                                <span>Aksesori unggulan</span>
                                <span class="badge"><?php echo $aksesori_home_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../checkout-sekarang/chekout-sekarang.php">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Checkout sekarang</span>
                                <span class="badge"><?php echo $checkout_count; ?></span>
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
            <header class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-th"></i> Kelola Image Grid</h1>
                    <p>Atur produk yang tampil di bagian grid halaman utama.</p>
                </div>
                <a href="add-image-grid.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Produk ke Grid
                </a>
            </header>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php
                    if ($_GET['success'] == 'added') echo "Produk berhasil ditambahkan ke grid!";
                    if ($_GET['success'] == 'updated') echo "Data grid berhasil diperbarui!";
                    if ($_GET['success'] == 'deleted') echo "Produk berhasil dihapus dari grid!";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    if ($_GET['error'] == 'already_exists') echo "Produk tersebut sudah ada di dalam grid!";
                    if ($_GET['error'] == 'db_error') echo "Terjadi kesalahan pada database.";
                    if ($_GET['error'] == 'update_failed') echo "Gagal memperbarui data grid.";
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3>Daftar Produk Grid</h3>
                    <span class="badge" style="padding: 5px 12px; font-size: 13px;"><?php echo $grid_count; ?> Produk</span>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th>Label</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($grid_count > 0): ?>
                                <?php 
                                $no = 1;
                                while ($item = mysqli_fetch_assoc($result_grid)):
                                    $detail = getProductDetail($db, $item['tipe_produk'], $item['produk_id']);
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <div class="product-cell">
                                                <?php if (!empty($detail['thumbnail'])): ?>
                                                    <img src="../../uploads/<?php echo htmlspecialchars($detail['thumbnail']); ?>" alt="Thumbnail" class="thumbnail-img">
                                                <?php else: ?>
                                                    <div class="thumbnail-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image" style="color: #ccc;"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="product-info-cell">
                                                    <span class="product-title"><?php echo htmlspecialchars($detail['nama_produk'] ?? 'Produk tidak ditemukan'); ?></span>
                                                    <span class="tipe-badge"><?php echo strtoupper($item['tipe_produk']); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo ucfirst($item['tipe_produk']); ?></td>
                                        <td>
                                            <?php if ($item['label']): ?>
                                                <span class="label-badge"><?php echo htmlspecialchars($item['label']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 12px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $item['urutan']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="edit-image-grid.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <a href="delete-image-grid.php?id=<?php echo $item['id']; ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus produk ini dari grid?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="no-data">
                                            <i class="fas fa-th"></i>
                                            Belum ada produk yang ditambahkan ke grid.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>