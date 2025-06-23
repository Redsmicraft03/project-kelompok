<?php
session_start();
// Sesuaikan path ke file koneksi Anda
include("../koneksi/koneksi.php");

// Proteksi Halaman: Jika user bukan admin, tendang ke halaman login
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// Ambil semua data produk dari database untuk ditampilkan (READ)
$query = "SELECT * FROM produk ORDER BY jenis DESC, nama_produk ASC";
$result = mysqli_query($db, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kio-Food</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; font-size: 24px; }
        .btn { padding: 10px 15px; text-decoration: none; color: white; border-radius: 5px; border: none; cursor: pointer; display: inline-block; }
        
        /* Grup untuk tombol utama */
        .btn-group { display: flex; gap: 10px; }
        .btn-add { background-color: #4CAF50; }
        .btn-view-orders { background-color: #008CBA; } /* Warna baru untuk tombol pesanan */

        .btn-edit { background-color: #ffc107; color: #333; }
        .btn-delete { background-color: #f44336; }

        .admin-info { text-align: right; width: 100%; }
        .admin-info a { color: #d9534f; text-decoration: none; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; }
        .actions a { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard Admin</h1>
            <div class="btn-group">
                <a href="../CRUD/create.php" class="btn btn-add">Tambah Produk</a>
                <a href="../admin/admin_pesanan.php" class="btn btn-view-orders">Pesanan Pelanggan</a>
            </div>
        </div>

        <div class="admin-info">
            <span>Halo, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong></span> | 
            <a href="../login/logout.php">Logout</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Jenis</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($produk = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                            <td><?php echo htmlspecialchars($produk['jenis']); ?></td>
                            <td>Rp <?php echo number_format($produk['harga']); ?></td>
                            <td class="actions">
                                <a href="../CRUD/update.php?id=<?php echo $produk['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="../CRUD/delete.php?id=<?php echo $produk['id']; ?>" class="btn btn-delete" onclick="return confirm('Anda yakin ingin menghapus produk ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">Belum ada produk yang ditambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>