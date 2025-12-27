<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../../auth/login.php'); exit;
}

$id = $_GET['id'] ?? null;
if(!$id) { header('Location: airtag.php'); exit; }

$p = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM admin_produk_airtag WHERE id='$id'"));
if(!$p) { header('Location: airtag.php'); exit; }

// Sidebar stats
$iphone_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_iphone"))['total'];
$ipad_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_ipad"))['total'];
$mac_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_mac"))['total'];
$watch_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_watch"))['total'];
$music_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_music"))['total'];
$aksesoris_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_aksesoris"))['total'];
$airtag_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_airtag"))['total'];

// Images
$imgs = [];
$q_img = mysqli_query($db, "SELECT * FROM admin_produk_airtag_gambar WHERE produk_id='$id'");
while($r = mysqli_fetch_assoc($q_img)) $imgs[$r['warna']] = $r;

// Combos
$combos = [];
$q_com = mysqli_query($db, "SELECT * FROM admin_produk_airtag_kombinasi WHERE produk_id='$id' ORDER BY warna ASC, pack ASC");
while($r = mysqli_fetch_assoc($q_com)) $combos[] = $r;

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail AirTag - <?php echo htmlspecialchars($p['nama_produk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f5f7fb; }
        .admin-container { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 280px; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%); color: #fff; display: flex; flex-direction: column; box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1); position: sticky; top: 0; height: 100vh; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .logo { display: flex; align-items: center; gap: 15px; }
        .logo i { font-size: 28px; color: #4a6cf7; }
        .logo h2 { font-size: 20px; font-weight: 600; }
        .sidebar-menu { flex: 1; padding: 20px 0; overflow-y: auto; }
        .menu-section { margin-bottom: 30px; }
        .section-title { font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; color: rgba(255, 255, 255, 0.5); padding: 0 20px; margin-bottom: 15px; }
        .sidebar-menu ul { list-style: none; }
        .sidebar-menu ul li a { display: flex; align-items: center; padding: 12px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: 0.3s; }
        .sidebar-menu ul li a:hover { background: rgba(255,255,255,0.05); color: #fff; padding-left: 25px; }
        .sidebar-menu ul li.active a { background: rgba(74, 108, 247, 0.15); color: #fff; border-left: 4px solid #4a6cf7; }
        .sidebar-menu ul li a i { font-size: 18px; margin-right: 12px; width: 24px; text-align: center; }
        .badge { background: #4a6cf7; color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 10px; margin-left: auto; }
        .sidebar-footer { padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* Content */
        .main-content { flex: 1; padding: 40px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); background: #fff; }
        .img-thumb { width:100px; height:100px; object-fit:cover; border-radius:10px; border:2px solid #eee; }
    </style>
</head>
<body>
<div class="admin-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-apple-alt"></i><h2>iBox Admin</h2></div></div>
        <div class="sidebar-menu">
            <div class="menu-section">
                <h3 class="section-title">Panel Produk</h3>
                <ul>
                    <li><a href="../../index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="../categories/kategori.php"><i class="fas fa-tags"></i><span>Kategori</span></a></li>
                    <li><a href="../ipad/ipad.php"><i class="fas fa-tablet-alt"></i><span>iPad</span><span class="badge"><?php echo $ipad_count; ?></span></a></li>
                    <li><a href="../iphone/iphone.php"><i class="fas fa-mobile-alt"></i><span>iPhone</span><span class="badge"><?php echo $iphone_count; ?></span></a></li>
                    <li><a href="../mac/mac.php"><i class="fas fa-laptop"></i><span>Mac</span><span class="badge"><?php echo $mac_count; ?></span></a></li>
                    <li><a href="../music/music.php"><i class="fas fa-headphones-alt"></i><span>Music</span><span class="badge"><?php echo $music_count; ?></span></a></li>
                    <li><a href="../watch/watch.php"><i class="fas fa-clock"></i><span>Watch</span><span class="badge"><?php echo $watch_count; ?></span></a></li>
                    <li><a href="../aksesoris/aksesoris.php"><i class="fas fa-toolbox"></i><span>Aksesoris</span><span class="badge"><?php echo $aksesoris_count; ?></span></a></li>
                    <li class="active"><a href="airtag.php"><i class="fas fa-tag"></i><span>AirTag</span><span class="badge"><?php echo $airtag_count; ?></span></a></li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold m-0"><?php echo htmlspecialchars($p['nama_produk']); ?></h2>
                    <a href="airtag.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>
                
                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-images me-2 text-primary"></i> Galeri Warna</h5>
                <div class="row mb-5">
                    <?php foreach($imgs as $warna => $data): 
                        $gallery = json_decode($data['foto_produk'], true) ?? [];
                    ?>
                    <div class="col-md-6 mb-3">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($warna); ?></h6>
                            <div class="d-flex gap-3">
                                <img src="../../uploads/<?php echo $data['foto_thumbnail']; ?>" class="img-thumb">
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php foreach($gallery as $g): ?>
                                        <img src="../../uploads/<?php echo $g; ?>" class="img-thumb" style="width:60px;height:60px;">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-list me-2 text-primary"></i> Varian & Stok</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Warna</th><th>Pack</th><th>Aksesoris</th><th>Harga</th><th>Stok</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($combos as $c): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($c['warna']); ?></span></td>
                                <td><?php echo htmlspecialchars($c['pack']); ?></td>
                                <td><?php echo htmlspecialchars($c['aksesoris'] ?? '-'); ?></td>
                                <td>Rp <?php echo number_format($c['harga'],0,',','.'); ?></td>
                                <td>
                                    <strong><?php echo $c['jumlah_stok']; ?></strong>
                                    <span class="badge <?php echo $c['jumlah_stok']>0?'bg-success':'bg-danger'; ?> ms-2">
                                        <?php echo $c['jumlah_stok']>0?'Tersedia':'Habis'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="edit-airtag.php?id=<?php echo $id; ?>" class="btn btn-primary px-4"><i class="fas fa-edit me-2"></i> Edit Data</a>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
