<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: trade-in.php');
    exit();
}

$id = $_GET['id'];
$query = "SELECT 
            hti.*,
            CASE 
                WHEN hti.tipe_produk = 'iphone' THEN (SELECT nama_produk FROM admin_produk_iphone WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'ipad' THEN (SELECT nama_produk FROM admin_produk_ipad WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'mac' THEN (SELECT nama_produk FROM admin_produk_mac WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'music' THEN (SELECT nama_produk FROM admin_produk_music WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'watch' THEN (SELECT nama_produk FROM admin_produk_watch WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'aksesoris' THEN (SELECT nama_produk FROM admin_produk_aksesoris WHERE id = hti.produk_id)
                WHEN hti.tipe_produk = 'airtag' THEN (SELECT nama_produk FROM admin_produk_airtag WHERE id = hti.produk_id)
            END as nama_produk,
            CASE 
                WHEN hti.tipe_produk = 'iphone' THEN (SELECT foto_thumbnail FROM admin_produk_iphone_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'ipad' THEN (SELECT foto_thumbnail FROM admin_produk_ipad_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'mac' THEN (SELECT foto_thumbnail FROM admin_produk_mac_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'music' THEN (SELECT foto_thumbnail FROM admin_produk_music_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'watch' THEN (SELECT foto_thumbnail FROM admin_produk_watch_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'aksesoris' THEN (SELECT foto_thumbnail FROM admin_produk_aksesoris_gambar WHERE produk_id = hti.produk_id LIMIT 1)
                WHEN hti.tipe_produk = 'airtag' THEN (SELECT foto_thumbnail FROM admin_produk_airtag_gambar WHERE produk_id = hti.produk_id LIMIT 1)
            END as foto_thumbnail
          FROM home_trade_in hti 
          WHERE hti.id = '$id'";
$result = mysqli_query($db, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    header('Location: trade-in.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Trade In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: #f5f7fb; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .detail-row { display: flex; margin-bottom: 15px; border-bottom: 1px solid #f9f9f9; padding-bottom: 15px; }
        .detail-label { width: 200px; font-weight: 600; color: #555; }
        .detail-value { flex: 1; color: #333; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; }
        .btn-back { background: #f0f0f0; color: #333; }
        .btn-edit { background: #4a6cf7; color: white; }
        .thumbnail { width: 200px; height: 200px; object-fit: contain; border: 1px solid #eee; border-radius: 10px; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-eye"></i> Detail Trade-in</h1>
            <div style="display: flex; gap: 10px;">
                <a href="trade-in.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                <a href="edit-trade-in.php?id=<?php echo $item['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Produk</div>
            <div class="detail-value">
                <strong><?php echo htmlspecialchars($item['nama_produk'] ?: 'Produk Tidak Ditemukan'); ?></strong><br>
                <small style="color: #4a6cf7; font-weight:600;"><?php echo strtoupper($item['tipe_produk']); ?></small>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Gambar</div>
            <div class="detail-value">
                <?php if($item['foto_thumbnail']): ?>
                    <img src="../../uploads/<?php echo $item['foto_thumbnail']; ?>" class="thumbnail">
                <?php else: ?>
                    <p class="text-muted">Tidak ada gambar</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Label Promo</div>
            <div class="detail-value">
                <span style="background:#bf4800; color:white; padding:4px 10px; border-radius:5px; font-size:14px;">
                    <?php echo htmlspecialchars($item['label_promo'] ?: '-'); ?>
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Urutan</div>
            <div class="detail-value"><?php echo $item['urutan']; ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Dibuat Pada</div>
            <div class="detail-value"><?php echo $item['created_at']; ?></div>
        </div>
    </div>
</body>
</html>
