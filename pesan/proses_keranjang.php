<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path ke koneksi Anda

// Set header ke JSON karena kita akan mengirim response JSON
header('Content-Type: application/json');

// Cek jika user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit();
}

// Ambil data JSON yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Cek jika data keranjang ada dan merupakan array
if (!isset($data['keranjang']) || !is_array($data['keranjang'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data keranjang tidak valid.']);
    exit();
}

$keranjang = $data['keranjang'];
$user_id = $_SESSION['user_id'];

// Mulai transaksi database untuk memastikan semua query berhasil atau tidak sama sekali
$db->begin_transaction();

try {
    // 1. Hapus semua item keranjang milik user ini terlebih dahulu
    // Ini cara paling sederhana untuk menyinkronkan keranjang
    $stmt_delete = $db->prepare("DELETE FROM keranjang WHERE user_id = ?");
    $stmt_delete->bind_param("i", $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Jika keranjang tidak kosong, masukkan kembali item yang baru
    if (!empty($keranjang)) {
        // 2. Siapkan query untuk memasukkan data baru
        $query_insert = "INSERT INTO keranjang (user_id, produk_id, kuantitas) VALUES (?, ?, ?)";
        $stmt_insert = $db->prepare($query_insert);

        foreach ($keranjang as $produk_id => $kuantitas) {
            // Pastikan kuantitas valid sebelum memasukkan
            if ($kuantitas > 0) {
                $stmt_insert->bind_param("iii", $user_id, $produk_id, $kuantitas);
                $stmt_insert->execute();
            }
        }
        $stmt_insert->close();
    }
    
    // Jika semua proses berhasil, commit transaksi
    $db->commit();
    echo json_encode(['status' => 'success', 'message' => 'Keranjang berhasil diperbarui!']);

} catch (mysqli_sql_exception $exception) {
    // Jika ada error, batalkan semua perubahan (rollback)
    $db->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada database.']);
}

$db->close();
?>