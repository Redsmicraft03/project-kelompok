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

// Hitung statistik pesanan
$stats_query = "SELECT 
    COUNT(*) as total_pesanan,
    SUM(CASE WHEN status_pesanan = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status_pesanan = 'proses' THEN 1 ELSE 0 END) as proses,
    SUM(CASE WHEN status_pesanan = 'terkirim' THEN 1 ELSE 0 END) as terkirim,
    SUM(CASE WHEN DATE(tanggal_pesanan) = CURDATE() THEN 1 ELSE 0 END) as hari_ini
    FROM pesanan";
$stats_result = mysqli_query($db, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan Pelanggan - Kio-Food</title>
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

        .back-btn {
            background: var(--gray-500);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: var(--gray-700);
            transform: translateY(-2px);
        }

        .container {
            padding: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .stat-card.pending .icon { color: var(--warning); }
        .stat-card.proses .icon { color: var(--info); }
        .stat-card.terkirim .icon { color: var(--success); }
        .stat-card.total .icon { color: var(--primary-color); }
        .stat-card.today .icon { color: var(--secondary-color); }

        .stat-card h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--dark-color);
        }

        .stat-card p {
            color: var(--gray-500);
            font-size: 14px;
        }

        .order-grid {
            display: grid;
            gap: 25px;
        }

        .order-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-id {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
        }

        .order-id i {
            margin-right: 10px;
        }

        .order-date {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        .order-body {
            padding: 25px;
        }

        .order-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }

        .section {
            background: var(--gray-100);
            padding: 20px;
            border-radius: var(--border-radius);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .info-item {
            margin-bottom: 12px;
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-700);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .info-value {
            color: var(--gray-800);
            font-size: 15px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-table th {
            background: var(--gray-200);
            color: var(--gray-700);
            font-weight: 600;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        .product-table td {
            padding: 12px;
            border-bottom: 1px solid var(--gray-200);
            font-size: 14px;
        }

        .product-table tbody tr:hover {
            background: var(--gray-100);
        }

        .order-footer {
            background: var(--gray-100);
            padding: 20px 25px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .order-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--success);
            display: flex;
            align-items: center;
        }

        .order-total i {
            margin-right: 8px;
        }

        .order-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: capitalize;
            display: flex;
            align-items: center;
        }

        .status-badge i {
            margin-right: 6px;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-badge.proses {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-badge.terkirim {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-badge.dibatalkan {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-proses {
            background: var(--info);
        }

        .btn-terkirim {
            background: var(--success);
        }

        .btn-batal {
            background: var(--warning);
            color: var(--dark-color);
        }

        .btn-hapus {
            background: var(--danger);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .status-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            color: var(--gray-300);
        }

        .empty-state h3 {
            font-size: 28px;
            margin-bottom: 10px;
            color: var(--gray-700);
        }

        .empty-state p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
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
                grid-template-columns: repeat(2, 1fr);
            }

            .order-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .order-actions {
                justify-content: center;
            }

            .status-form {
                flex-direction: column;
                width: 100%;
            }

            .status-form .btn {
                width: 100%;
                justify-content: center;
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

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
            <a href="../admin/admin_dashboard.php" class="menu-item">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="#" class="menu-item active">
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
                    <i class="fas fa-shopping-cart"></i>
                    Kelola Pesanan Pelanggan
                </h1>
            </div>
            <a href="../admin/admin_dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </header>

        <div class="container">
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="icon"><i class="fas fa-list-alt"></i></div>
                    <h3><?php echo $stats['total_pesanan']; ?></h3>
                    <p>Total Pesanan</p>
                </div>
                <div class="stat-card pending">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <h3><?php echo $stats['pending']; ?></h3>
                    <p>Menunggu Konfirmasi</p>
                </div>
                <div class="stat-card proses">
                    <div class="icon"><i class="fas fa-cog"></i></div>
                    <h3><?php echo $stats['proses']; ?></h3>
                    <p>Sedang Diproses</p>
                </div>
                <div class="stat-card terkirim">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3><?php echo $stats['terkirim']; ?></h3>
                    <p>Telah Terkirim</p>
                </div>
                <div class="stat-card today">
                    <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    <h3><?php echo $stats['hari_ini']; ?></h3>
                    <p>Pesanan Hari Ini</p>
                </div>
            </div>

            <?php if (empty($semua_pesanan)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Belum Ada Pesanan</h3>
                    <p>Pesanan dari pelanggan akan muncul di sini. Pastikan website Anda dapat diakses dengan baik oleh pelanggan.</p>
                </div>
            <?php else: ?>
                <div class="order-grid">
                    <?php foreach ($semua_pesanan as $pesanan): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-id">
                                    <i class="fas fa-receipt"></i>
                                    Pesanan #<?php echo $pesanan['id']; ?>
                                </div>
                                <div class="order-date">
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('d F Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?>
                                </div>
                            </div>

                            <div class="order-body">
                                <div class="order-content">
                                    <div class="section">
                                        <h4 class="section-title">
                                            <i class="fas fa-user"></i>
                                            Info Pengiriman
                                        </h4>
                                        <div class="info-item">
                                            <div class="info-label">Nama Penerima</div>
                                            <div class="info-value"><?php echo htmlspecialchars($pesanan['nama_penerima']); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Telepon</div>
                                            <div class="info-value"><?php echo htmlspecialchars($pesanan['telepon_penerima']); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Alamat</div>
                                            <div class="info-value"><?php echo nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Metode Pembayaran</div>
                                            <div class="info-value"><?php echo htmlspecialchars($pesanan['metode_pembayaran']); ?></div>
                                        </div>
                                    </div>

                                    <div class="section">
                                        <h4 class="section-title">
                                            <i class="fas fa-box"></i>
                                            Detail Produk
                                        </h4>
                                        <table class="product-table">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($detail_pesanan_grouped[$pesanan['id']])): ?>
                                                    <?php foreach ($detail_pesanan_grouped[$pesanan['id']] as $detail): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                                                            <td><?php echo $detail['kuantitas']; ?></td>
                                                            <td>Rp <?php echo number_format($detail['harga_saat_pesan'], 0, ',', '.'); ?></td>
                                                            <td>Rp <?php echo number_format($detail['kuantitas'] * $detail['harga_saat_pesan'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="order-footer">
                                <div class="order-total">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Total: Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?>
                                </div>

                                <div class="order-actions">
                                    <?php
                                        $status = $pesanan['status_pesanan'];
                                        $statusLabels = [
                                            'pending'    => 'Menunggu Konfirmasi',
                                            'proses'     => 'Sedang Diproses',
                                            'terkirim'   => 'Telah Terkirim',
                                            'dibatalkan' => 'Dibatalkan'
                                        ];
                                        $displayText = $statusLabels[$status] ?? ucfirst($status);
                                    ?>
                                    
                                    <span class="status-badge <?php echo $status; ?>">
                                        <?php if($status == 'pending'): ?>
                                            <i class="fas fa-clock"></i>
                                        <?php elseif($status == 'proses'): ?>
                                            <i class="fas fa-cog"></i>
                                        <?php elseif($status == 'terkirim'): ?>
                                            <i class="fas fa-check-circle"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                        <?php endif; ?>
                                        <?php echo $displayText; ?>
                                    </span>

                                    <form method="POST" action="admin_pesanan.php" class="status-form">
                                        <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        
                                        <?php if ($pesanan['status_pesanan'] == 'pending'): ?>
                                            <button type="submit" name="new_status" value="diproses" class="btn btn-proses">
                                                <i class="fas fa-play"></i>
                                                Proses
                                            </button>
                                        <?php elseif ($pesanan['status_pesanan'] == 'diproses'): ?>
                                            <button type="submit" name="new_status" value="dikirim" class="btn btn-terkirim">
                                                <i class="fas fa-shipping-fast"></i>
                                                Kirim
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($pesanan['status_pesanan'] != 'dikirim' && $pesanan['status_pesanan'] != 'dibatalkan'): ?>
                                            <button type="submit" name="new_status" value="dibatalkan" class="btn btn-batal" 
                                                    onclick="return confirm('Anda yakin ingin membatalkan pesanan ini?');">
                                                <i class="fas fa-ban"></i>
                                                Batal
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <form method="POST" action="admin_pesanan.php" class="status-form">
                                        <input type="hidden" name="pesanan_id" value="<?php echo $pesanan['id']; ?>">
                                        <button type="submit" name="hapus_pesanan" value="1" class="btn btn-hapus" 
                                                onclick="return confirm('PERINGATAN!\nAnda akan menghapus pesanan ini secara permanen dari database.\n\nAksi ini tidak dapat dibatalkan. Lanjutkan?');">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

        // Add loading state to buttons
        // document.querySelectorAll('form button').forEach(button => {
        //     button.addEventListener('click', function(e) {
        //         const form = this.closest('form');
        //         if (form.checkValidity()) {
        //             const originalText = this.innerHTML;
        //             this.innerHTML = '<div class="loading"></div> Memproses...';
        //             this.disabled = true;
                    
        //             // Re-enable if form doesn't submit for some reason
        //             setTimeout(() => {
        //                 this.innerHTML = originalText;
        //                 this.disabled = false;
        //             }, 5000);
        //         }
        //     });
        // });

        // Smooth animations on page load
        document.addEventListener('DOMContentLoaded', () => {
            const statCards = document.querySelectorAll('.stat-card');
            const orderCards = document.querySelectorAll('.order-card');
            
            statCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            orderCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, (index * 150) + 500);
            });
        });
    </script>
</body>
</html>