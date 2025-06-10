<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Proses jika ada aksi hapus item
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['produk_id'])) {
    $produk_id_to_delete = (int)$_GET['produk_id'];
    $delete_query = "DELETE FROM keranjang WHERE user_id = ? AND produk_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("ii", $user_id, $produk_id_to_delete);
    $stmt->execute();
    $stmt->close();
    // Redirect kembali ke halaman keranjang untuk refresh
    header("Location: keranjang.php");
    exit();
}


// Ambil semua item di keranjang milik user, gabungkan dengan data produk
$query = "SELECT p.id as produk_id, p.nama_produk, p.harga, k.kuantitas
          FROM keranjang k
          JOIN produk p ON k.produk_id = p.id
          WHERE k.user_id = ?";
          
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table th, .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .cart-table th {
            background-color: #f8f9fa;
        }
        .cart-item-info {
            display: flex;
            align-items: center;
        }
        .cart-item-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .btn-delete {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-delete:hover {
            text-decoration: underline;
        }
        .cart-summary {
            margin-top: 30px;
            text-align: right;
        }
        .grand-total {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
        }
        .cart-actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-checkout {
            background-color: #007bff;
            color: white;
        }
        .empty-cart {
            text-align: center;
            padding: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Keranjang Belanja Anda</h1>

    <?php if ($result->num_rows > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Kuantitas</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                while($item = $result->fetch_assoc()): 
                    $subtotal = $item['harga'] * $item['kuantitas'];
                    $grand_total += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="cart-item-info">
                            <span><?= htmlspecialchars($item['nama_produk']) ?></span>
                        </div>
                    </td>
                    <td>Rp <?= number_format($item['harga']) ?></td>
                    <td><?= $item['kuantitas'] ?></td>
                    <td>Rp <?= number_format($subtotal) ?></td>
                    <td>
                        <a href="keranjang.php?action=delete&produk_id=<?= $item['produk_id'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <p class="grand-total">Total: Rp <?= number_format($grand_total) ?></p>
        </div>

        <div class="cart-actions">
            <a href="menu_makanan.php" class="btn btn-back">Kembali ke Menu Makanan</a>
            <a href="checkout.php" class="btn btn-checkout">Lanjut ke Pembayaran</a>
        </div>

    <?php else: ?>
        <div class="empty-cart">
            <h2>Keranjang Anda masih kosong.</h2>
            <a href="menu_makanan.php" class="btn btn-checkout">Mulai Belanja</a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
<?php
$stmt->close();
$db->close();
?>