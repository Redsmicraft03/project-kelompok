<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path

// Pastikan user sudah login dan metode request adalah POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login/login.php");
    exit();
}

// Validasi dan sanitasi input dari form
$user_id = $_SESSION['user_id'];
$nama_penerima = filter_input(INPUT_POST, 'nama_penerima', FILTER_SANITIZE_STRING);
$telepon_penerima = filter_input(INPUT_POST, 'telepon_penerima', FILTER_SANITIZE_STRING);
$alamat_pengiriman = filter_input(INPUT_POST, 'alamat_pengiriman', FILTER_SANITIZE_STRING);
$metode_pembayaran = filter_input(INPUT_POST, 'metode_pembayaran', FILTER_SANITIZE_STRING);

if (empty($nama_penerima) || empty($telepon_penerima) || empty($alamat_pengiriman) || empty($metode_pembayaran)) {
    // Jika ada data yang kosong, kembalikan ke checkout dengan pesan error
    header("Location: checkout.php?error=datakosong");
    exit();
}

// Mulai transaksi database
$db->begin_transaction();

try {
    // 1. Ambil item keranjang dari DB (sumber data yang paling terpercaya)
    $query_cart = "SELECT p.id as produk_id, p.harga, k.kuantitas FROM keranjang k JOIN produk p ON k.produk_id = p.id WHERE k.user_id = ?";
    $stmt_cart = $db->prepare($query_cart);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_items = $stmt_cart->get_result();

    if ($cart_items->num_rows === 0) {
        throw new Exception("Keranjang kosong.");
    }
    
    // Hitung ulang total harga di server untuk keamanan
    $total_harga = 0;
    $items_to_insert = [];
    while ($item = $cart_items->fetch_assoc()) {
        $total_harga += $item['harga'] * $item['kuantitas'];
        $items_to_insert[] = $item;
    }

    // 2. Masukkan data ke tabel 'pesanan'
    $query_order = "INSERT INTO pesanan (user_id, nama_penerima, telepon_penerima, alamat_pengiriman, metode_pembayaran, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt_order = $db->prepare($query_order);
    $stmt_order->bind_param("issssi", $user_id, $nama_penerima, $telepon_penerima, $alamat_pengiriman, $metode_pembayaran, $total_harga);
    $stmt_order->execute();
    
    // Dapatkan ID dari pesanan yang baru saja dibuat
    $pesanan_id = $db->insert_id;

    // 3. Masukkan setiap item ke tabel 'detail_pesanan'
    $query_details = "INSERT INTO detail_pesanan (pesanan_id, produk_id, kuantitas, harga_saat_pesan) VALUES (?, ?, ?, ?)";
    $stmt_details = $db->prepare($query_details);

    foreach ($items_to_insert as $item) {
        $stmt_details->bind_param("iiii", $pesanan_id, $item['produk_id'], $item['kuantitas'], $item['harga']);
        $stmt_details->execute();
    }

    // 4. Kosongkan keranjang pengguna
    $query_delete_cart = "DELETE FROM keranjang WHERE user_id = ?";
    $stmt_delete = $db->prepare($query_delete_cart);
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();
    
    // Jika semua berhasil, commit transaksi
    $db->commit();
    
    // Redirect ke halaman sukses
    header("Location: sukses.php?order_id=" . $pesanan_id);
    exit();

} catch (Exception $e) {
    // Jika ada error, batalkan semua perubahan
    $db->rollback();
    // Redirect ke halaman error atau kembali ke checkout
    header("Location: checkout.php?error=prosesgagal");
    exit();
}
?>