<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Ambil semua data pesanan utama milik user, urutkan dari yang terbaru
$query_pesanan = "SELECT id, tanggal_pesanan, total_harga, status_pesanan 
                  FROM pesanan 
                  WHERE user_id = ? 
                  ORDER BY tanggal_pesanan DESC";
$stmt_pesanan = $db->prepare($query_pesanan);
$stmt_pesanan->bind_param("i", $user_id);
$stmt_pesanan->execute();
$result_pesanan = $stmt_pesanan->get_result();

$daftar_pesanan = [];
$pesanan_ids = [];
while ($row = $result_pesanan->fetch_assoc()) {
    $daftar_pesanan[] = $row;
    $pesanan_ids[] = $row['id'];
}
$stmt_pesanan->close();

// 2. Ambil semua detail item dari semua pesanan di atas dalam satu query (lebih efisien)
$detail_pesanan = [];
if (!empty($pesanan_ids)) {
    // Membuat placeholder '?' sebanyak jumlah pesanan
    $placeholders = implode(',', array_fill(0, count($pesanan_ids), '?'));
    $types = str_repeat('i', count($pesanan_ids)); // Tipe data integer

    $query_detail = "SELECT pd.pesanan_id, p.nama_produk, pd.kuantitas 
                     FROM detail_pesanan pd 
                     JOIN produk p ON pd.produk_id = p.id 
                     WHERE pd.pesanan_id IN ($placeholders)";
    
    $stmt_detail = $db->prepare($query_detail);
    // Bind semua ID pesanan ke query
    $stmt_detail->bind_param($types, ...$pesanan_ids);
    $stmt_detail->execute();
    $result_detail = $stmt_detail->get_result();
    
    while ($row = $result_detail->fetch_assoc()) {
        // Kelompokkan detail item berdasarkan pesanan_id
        $detail_pesanan[$row['pesanan_id']][] = $row;
    }
    $stmt_detail->close();
}
$db->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .order-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            overflow: hidden;
        }
        .order-header {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-header .order-id {
            font-weight: bold;
            color: #007bff;
        }
        .order-header .order-date {
            font-size: 0.9em;
            color: #6c757d;
        }
        .order-body {
            padding: 20px;
        }
        .order-body h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .order-items ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }
        .order-items li {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .order-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }
        .total-price {
            font-size: 1.1em;
        }
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            color: white;
            text-transform: capitalize;
        }
        .status.pending { background-color: #ffc107; color: #333; }
        .status.diproses { background-color: #17a2b8; }
        .status.dikirim { background-color: #007bff; }
        .status.selesai { background-color: #28a745; }
        .status.dibatalkan { background-color: #dc3545; }
        .no-orders {
            text-align: center;
            padding: 50px;
            background-color: #fff;
            border-radius: 12px;
        }
        .btn-menu {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .page-actions {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Riwayat Pesanan Saya</h1>

    <div class="page-actions">
        <a href="../user/menu_user.php" class="btn-menu">Kembali ke Menu Utama</a>
    </div>

    <?php if (!empty($daftar_pesanan)): ?>
        <?php foreach ($daftar_pesanan as $pesanan): ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-id">Pesanan #<?= $pesanan['id'] ?></span>
                    <span class="order-date"><?= date('d F Y, H:i', strtotime($pesanan['tanggal_pesanan'])) ?></span>
                </div>
                <div class="order-body">
                    <h4>Detail Item:</h4>
                    <div class="order-items">
                        <ul>
                            <?php if (isset($detail_pesanan[$pesanan['id']])): ?>
                                <?php foreach ($detail_pesanan[$pesanan['id']] as $item): ?>
                                    <li>
                                        <span><?= htmlspecialchars($item['nama_produk']) ?></span>
                                        <span>x <?= $item['kuantitas'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="order-footer">
                    <span class="total-price">Total: Rp <?= number_format($pesanan['total_harga']) ?></span>
                    <span class="status <?= $pesanan['status_pesanan'] ?>">
                        <?= str_replace('_', ' ', $pesanan['status_pesanan']) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-orders">
            <h2>Anda belum memiliki riwayat pesanan.</h2>
            <p>Ayo mulai pesan menu favorit Anda!</p>
            <a href="../user/menu_makanan.php" class="btn-menu">Mulai Belanja</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>