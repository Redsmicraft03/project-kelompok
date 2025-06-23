<?php
session_start();
include("../koneksi/koneksi.php"); // Sesuaikan path koneksi Anda

// Proteksi Halaman Admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// --- BAGIAN LOGIKA PEMROSESAN (TETAP SAMA) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // JIKA TOMBOL UPDATE STATUS DITEKAN
    if (isset($_POST['update_status'])) {
        $pesanan_id = $_POST['pesanan_id'];
        $new_status = $_POST['new_status'];
        $update_query = "UPDATE pesanan SET status_pesanan = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($db, $update_query);
        mysqli_stmt_bind_param($stmt_update, "si", $new_status, $pesanan_id);
        mysqli_stmt_execute($stmt_update);

    // JIKA TOMBOL HAPUS PESANAN DITEKAN
    } elseif (isset($_POST['hapus_pesanan'])) {
        $pesanan_id = $_POST['pesanan_id'];
        mysqli_begin_transaction($db);
        try {
            $delete_details_query = "DELETE FROM detail_pesanan WHERE pesanan_id = ?";
            $stmt_details = mysqli_prepare($db, $delete_details_query);
            mysqli_stmt_bind_param($stmt_details, "i", $pesanan_id);
            mysqli_stmt_execute($stmt_details);

            $delete_order_query = "DELETE FROM pesanan WHERE id = ?";
            $stmt_order = mysqli_prepare($db, $delete_order_query);
            mysqli_stmt_bind_param($stmt_order, "i", $pesanan_id);
            mysqli_stmt_execute($stmt_order);

            mysqli_commit($db);
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($db);
        }
    }
    header("Location: admin_pesanan.php");
    exit();
}


// --- BAGIAN PENGAMBILAN DATA (TETAP SAMA) ---
$pesanan_query = "SELECT * FROM pesanan ORDER BY tanggal_pesanan DESC";
$pesanan_result = mysqli_query($db, $pesanan_query);
$semua_pesanan = [];
while ($row = mysqli_fetch_assoc($pesanan_result)) {
    $semua_pesanan[$row['id']] = $row;
}
$detail_query = "SELECT dp.pesanan_id, dp.kuantitas, dp.harga_saat_pesan, pr.nama_produk FROM detail_pesanan dp JOIN produk pr ON dp.produk_id = pr.id";
$detail_result = mysqli_query($db, $detail_query);
$detail_pesanan_grouped = [];
while ($row = mysqli_fetch_assoc($detail_result)) {
    $detail_pesanan_grouped[$row['pesanan_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan Pelanggan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: auto; }
        .order-card { background: #fff; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .order-header { background: #f9f9f9; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .order-body { display: flex; flex-wrap: wrap; padding: 20px; gap: 20px;}
        .customer-details, .product-details { flex: 1; min-width: 300px; }
        .product-table { width: 100%; border-collapse: collapse; }
        .product-table th, .product-table td { padding: 10px; border: 1px solid #eee; text-align: left; }
        .info-group { margin-bottom: 12px; }
        .info-group strong { display: block; color: #555; font-size: 14px; margin-bottom: 4px;}
        .order-footer { padding: 15px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .order-total { font-size: 1.2em; font-weight: bold; }
        .actions { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
        .status-form { display: flex; gap: 10px; }
        .status-form .btn { padding: 8px 15px; border: none; border-radius: 5px; color: white; cursor: pointer; }
        .btn-proses { background-color: #337ab7; }
        .btn-terkirim { background-color: #5cb85c; }
        .btn-batal { background-color: #f0ad4e; }
        .btn-hapus { background-color: #d9534f; }
        .status-display { padding: 5px 12px; color: white; border-radius: 15px; font-size: 14px; text-transform: capitalize; }
        .status-display.pending { background-color: #f0ad4e; }
        .status-display.proses { background-color: #337ab7; }
        .status-display.terkirim { background-color: #5cb85c; }
        .status-display.dibatalkan { background-color: #777; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .back-link { color: #008CBA; text-decoration: none; font-size: 16px; }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>Kelola Pesanan Pelanggan</h1>
        <a href="../admin/admin_dashboard.php" class="back-link">&larr; Kembali ke Dashboard</a>
    </div>

    <?php if (empty($semua_pesanan)): ?>
        <p>Belum ada pesanan yang masuk.</p>
    <?php else: ?>
        <?php foreach ($semua_pesanan as $pesanan): ?>
            <div class="order-card">
                <div class="order-header">
                    <h3>Pesanan #<?php echo $pesanan['id']; ?></h3>
                    <div class="order-meta"><?php echo date('d F Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?></div>
                </div>
                <div class="order-body">
                    <div class="customer-details">
                        <h4>Info Pengiriman</h4>
                        <div class="info-group"><strong>Nama Penerima:</strong> <span><?php echo htmlspecialchars($pesanan['nama_penerima']); ?></span></div>
                        <div class="info-group"><strong>Telepon:</strong> <span><?php echo htmlspecialchars($pesanan['telepon_penerima']); ?></span></div>
                        <div class="info-group"><strong>Alamat:</strong> <span><?php echo nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></span></div>
                        <div class="info-group"><strong>Metode Pembayaran:</strong> <span><?php echo htmlspecialchars($pesanan['metode_pembayaran']); ?></span></div>
                    </div>
                    <div class="product-details">
                        <h4>Detail Produk</h4>
                        <table class="product-table">
                            <thead><tr><th>Nama Produk</th><th>Kuantitas</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php if (isset($detail_pesanan_grouped[$pesanan['id']])): ?>
                                    <?php foreach ($detail_pesanan_grouped[$pesanan['id']] as $detail): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                                            <td><?php echo $detail['kuantitas']; ?></td>
                                            <td>Rp <?php echo number_format($detail['harga_saat_pesan']); ?></td>
                                            <td>Rp <?php echo number_format($detail['kuantitas'] * $detail['harga_saat_pesan']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="order-footer">
                    <div class="order-total">
                        Total Pesanan: Rp <?php echo number_format($pesanan['total_harga']); ?>
                    </div>
                    <div class="actions">
                        
                        <?php
                            // Ambil status dari database
                            $status = $pesanan['status_pesanan'];
                            
                            // Definisikan "kamus" untuk label status
                            $statusLabels = [
                                'pending'    => 'Menunggu Konfirmasi',
                                'proses'     => 'Sedang Diproses',
                                'terkirim'   => 'Telah Terkirim',
                                'dibatalkan' => 'Dibatalkan'
                            ];

                            // Tentukan teks yang akan ditampilkan, jika tidak ada di kamus, tampilkan apa adanya
                            $displayText = $statusLabels[$status] ?? ucfirst($status);
                        ?>
                        
                        <span class="status-display <?php echo $status; ?>"><?php echo $displayText; ?></span>
                        <form method="POST" action="admin_pesanan.php" class="status-form">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            
                            <input type="hidden" name="update_status" value="1">
                            <?php if ($pesanan['status_pesanan'] == 'pending'): ?>
                                <button type="submit" name="new_status" value="proses" class="btn btn-proses">Proses</button>
                            <?php elseif ($pesanan['status_pesanan'] == 'proses'): ?>
                                <button type="submit" name="new_status" value="terkirim" class="btn btn-terkirim">Kirim</button>
                            <?php endif; ?>
                            
                            <?php if ($pesanan['status_pesanan'] != 'terkirim' && $pesanan['status_pesanan'] != 'dibatalkan'): ?>
                                <button type="submit" name="new_status" value="dibatalkan" class="btn btn-batal" onclick="return confirm('Anda yakin ingin membatalkan pesanan ini?');">Batal</button>
                            <?php endif; ?>
                        </form>
                        
                        <form method="POST" action="admin_pesanan.php" class="status-form">
                            <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                            <button type="submit" name="hapus_pesanan" value="1" class="btn btn-hapus" onclick="return confirm('PERINGATAN!\nAnda akan menghapus pesanan ini secara permanen dari database.\n\nAksi ini tidak dapat dibatalkan. Lanjutkan?');">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>