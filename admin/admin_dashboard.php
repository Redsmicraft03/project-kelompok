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

// Hitung statistik sederhana
$total_produk = mysqli_num_rows($result);
$query_pesanan = "SELECT COUNT(*) as total_pesanan FROM pesanan WHERE DATE(tanggal_pesanan) = CURDATE()";
$result_pesanan = mysqli_query($db, $query_pesanan);
$pesanan_hari_ini = mysqli_fetch_assoc($result_pesanan)['total_pesanan'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kio-Food</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #F7931E;
            --dark-color: #2c3e50;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-500: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e3f2fd 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 0;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }

        .sidebar-header h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 0 20px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            margin-bottom: 8px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.25);
        }

        .menu-item i {
            font-size: 18px;
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            background: var(--light-bg);
        }

        .header {
            background: white;
            padding: 20px 30px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .header-title i {
            color: var(--primary-color);
            margin-right: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .logout-btn {
            background: var(--danger);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .container {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-right: 20px;
        }

        .stat-icon.products {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .stat-icon.orders {
            background: linear-gradient(135deg, var(--info), #0056b3);
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--gray-500);
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .card-title i {
            margin-right: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .btn-success {
            background: var(--success);
        }

        .btn-warning {
            background: var(--warning);
            color: var(--dark-color);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th {
            background: var(--gray-100);
            color: var(--gray-700);
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid var(--gray-200);
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--gray-100);
        }

        .product-name {
            font-weight: 600;
            color: var(--dark-color);
        }

        .product-type {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .price {
            font-weight: 600;
            color: var(--success);
            font-size: 16px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: var(--gray-300);
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--gray-700);
        }

        .mobile-menu-btn {
            display: none;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .header {
                padding: 15px 20px;
            }

            .container {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-store"></i> Kio-Food</h2>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="../admin/admin_pesanan.php" class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                Pesanan Pelanggan
            </a>
            <a href="../CRUD/create.php" class="menu-item">
                <i class="fas fa-plus-circle"></i>
                Tambah Produk
            </a>
        </nav>
    </div>

    <div class="main-content">
        <header class="header">
            <div class="header-left">
                <button class="mobile-menu-btn" id="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="header-title">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard Admin
                </h1>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['admin_email'], 0, 1)); ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong>
                </div>
                <a href="../login/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </header>

        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon products">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_produk; ?></h3>
                        <p>Total Produk</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pesanan_hari_ini; ?></h3>
                        <p>Pesanan Hari Ini</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-list"></i>
                        Daftar Produk
                    </h2>
                    <a href="../CRUD/create.php" class="btn btn-success">
                        <i class="fas fa-plus"></i>
                        Tambah Produk Baru
                    </a>
                </div>

                <div class="table-container">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-tag"></i> Nama Produk</th>
                                    <th><i class="fas fa-layer-group"></i> Jenis</th>
                                    <th><i class="fas fa-dollar-sign"></i> Harga</th>
                                    <th><i class="fas fa-cogs"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($produk = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="product-name"><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                                        <td>
                                            <span class="product-type"><?php echo htmlspecialchars($produk['jenis']); ?></span>
                                        </td>
                                        <td class="price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="../CRUD/update.php?id=<?php echo $produk['id']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>
                                                <a href="../CRUD/delete.php?id=<?php echo $produk['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Anda yakin ingin menghapus produk ini?');">
                                                    <i class="fas fa-trash"></i>
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>Belum Ada Produk</h3>
                            <p>Mulai dengan menambahkan produk pertama Anda</p>
                            <a href="../CRUD/create.php" class="btn btn-primary" style="margin-top: 20px;">
                                <i class="fas fa-plus"></i>
                                Tambah Produk Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Smooth animations
        document.addEventListener('DOMContentLoaded', () => {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>