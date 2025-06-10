<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil item dari keranjang untuk ditampilkan di ringkasan pesanan
// Ini juga untuk memastikan keranjang tidak kosong
$query = "SELECT p.id as produk_id, p.nama_produk, p.harga, k.kuantitas
          FROM keranjang k
          JOIN produk p ON k.produk_id = p.id
          WHERE k.user_id = ?";
          
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Jika keranjang kosong, redirect kembali ke menu
if ($result->num_rows === 0) {
    header("Location: menu_makanan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 30px 0;
        }
        .container {
            max-width: 900px;
            margin: auto;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        .form-section, .summary-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        .form-section {
            flex: 2; /* Lebar 2/3 */
            min-width: 400px;
        }
        .summary-section {
            flex: 1; /* Lebar 1/3 */
            min-width: 300px;
        }
        h1, h2 {
            color: #333;
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .payment-options label {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .payment-options input[type="radio"] {
            margin-right: 10px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #333;
            display: flex;
            justify-content: space-between;
            font-size: 1.2em;
            font-weight: bold;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #28a745;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-section">
        <h1>Detail Pengiriman & Pembayaran</h1>
        <form action="proses_checkout.php" method="POST">
            <div class="form-group">
                <label for="nama_penerima">Nama Lengkap</label>
                <input type="text" id="nama_penerima" name="nama_penerima" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telepon_penerima">Nomor Telepon</label>
                <input type="tel" id="telepon_penerima" name="telepon_penerima" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="alamat_pengiriman">Alamat Lengkap</label>
                <textarea id="alamat_pengiriman" name="alamat_pengiriman" class="form-control" rows="4" required></textarea>
            </div>
            
            <h2>Metode Pembayaran</h2>
            <div class="form-group payment-options">
                <label>
                    <input type="radio" name="metode_pembayaran" value="COD" checked> COD (Bayar di Tempat)
                </label>
                <label>
                    <input type="radio" name="metode_pembayaran" value="Transfer Bank"> Transfer Bank
                </label>
                <label>
                    <input type="radio" name="metode_pembayaran" value="E-Wallet"> E-Wallet (QRIS)
                </label>
            </div>

            <button type="submit" class="btn-submit">Selesaikan Pesanan</button>
        </form>
    </div>

    <div class="summary-section">
        <h2>Ringkasan Pesanan</h2>
        <?php
        $grand_total = 0;
        // Pindahkan pointer result ke awal untuk looping lagi
        $result->data_seek(0); 
        while($item = $result->fetch_assoc()): 
            $subtotal = $item['harga'] * $item['kuantitas'];
            $grand_total += $subtotal;
        ?>
            <div class="summary-item">
                <span><?= htmlspecialchars($item['nama_produk']) ?> (x<?= $item['kuantitas'] ?>)</span>
                <strong>Rp <?= number_format($subtotal) ?></strong>
            </div>
        <?php endwhile; ?>

        <div class="summary-total">
            <span>TOTAL</span>
            <strong>Rp <?= number_format($grand_total) ?></strong>
        </div>
    </div>
</div>

</body>
</html>