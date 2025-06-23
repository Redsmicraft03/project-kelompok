<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

include '../koneksi/koneksi.php'; // Sesuaikan path

// Ambil data keranjang user saat ini dari DB untuk tampilan awal
$keranjang_db = [];
$user_id = $_SESSION['user_id'];
$query_keranjang = "SELECT produk_id, kuantitas FROM keranjang WHERE user_id = $user_id";
$result_keranjang = mysqli_query($db, $query_keranjang);
while ($row = mysqli_fetch_assoc($result_keranjang)) {
    $keranjang_db[$row['produk_id']] = $row['kuantitas'];
}

// Ambil data makanan & minuman dengan gambar
$result_makanan = mysqli_query($db, "SELECT * FROM produk WHERE jenis = 'Makanan' ORDER BY nama_produk ASC");
$result_minuman = mysqli_query($db, "SELECT * FROM produk WHERE jenis = 'Minuman' ORDER BY nama_produk ASC");

// Fungsi untuk membuat kartu menu dengan gambar
function render_menu_card($row, $keranjang) {
    $produk_id = $row['id'];
    $kuantitas = isset($keranjang[$produk_id]) ? $keranjang[$produk_id] : 0;
    $display_beli = $kuantitas > 0 ? 'style="display: none;"' : '';
    $display_qty = $kuantitas > 0 ? 'style="display: flex;"' : 'style="display: none;"';
    
    // Handle gambar produk
    $gambar_src = '';
    if (!empty($row['gambar']) && file_exists('../'.$row['gambar'])) {
        $gambar_src = htmlspecialchars('../'.$row['gambar']);
    }

    echo '
    <div class="menu-card" data-id="'. $produk_id .'" data-harga="'. $row['harga'] .'">
        <div class="card-image">
            ' . (!empty($gambar_src) ? 
                '<img src="'. $gambar_src .'" alt="'. htmlspecialchars($row['nama_produk']) .'" loading="lazy">' : 
                '<div class="placeholder-image">
                    <i class="fas fa-utensils"></i>
                    <span>No Image</span>
                 </div>'
            ) . '
            <div class="card-overlay">
                <div class="quick-view">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>
        <div class="card-content">
            <h3 class="product-name">'. htmlspecialchars($row['nama_produk']) .'</h3>
            ' . (!empty($row['deskripsi']) ? 
                '<p class="product-description">'. htmlspecialchars(substr($row['deskripsi'], 0, 80)) . (strlen($row['deskripsi']) > 80 ? '...' : '') .'</p>' : 
                '<p class="product-description">Cireng lezat dengan cita rasa yang menggugah selera</p>'
            ) . '
            <div class="price-section">
                <span class="product-price">Rp '. number_format($row['harga'], 0, ',', '.') .'</span>
                <span class="price-per-unit">per porsi</span>
            </div>
            <div class="action-section">
                <button class="btn btn-add-to-cart" '. $display_beli .'>
                    <i class="fas fa-plus"></i>
                    <span>Tambah</span>
                </button>
                <div class="quantity-controls" '. $display_qty .'>
                    <button class="btn-qty btn-minus">
                        <i class="fas fa-minus"></i>
                    </button>
                    <span class="quantity-display">
                        <span class="quantity">'. $kuantitas .'</span>
                        <span class="qty-label">item</span>
                    </span>
                    <button class="btn-qty btn-plus">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Menu - Kio-Food</title>
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
            --shadow-card: 0 6px 20px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            padding-bottom: 140px;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: var(--shadow-lg);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header h1 {
            font-size: 36px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            animation: slideDown 0.8s ease-out;
        }

        .header p {
            font-size: 18px;
            opacity: 0.9;
            animation: slideUp 0.8s ease-out 0.2s both;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section {
            margin-bottom: 60px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease-out;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .section-subtitle {
            color: var(--gray-500);
            font-size: 18px;
            margin-top: 15px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            padding: 0;
        }

        .menu-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            transition: var(--transition);
            position: relative;
            animation: fadeInUp 0.6s ease-out;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .card-image {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, var(--gray-200), var(--gray-100));
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .menu-card:hover .card-image img {
            transform: scale(1.05);
        }

        .placeholder-image {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .placeholder-image i {
            font-size: 48px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .placeholder-image span {
            font-size: 14px;
            opacity: 0.7;
            font-weight: 500;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .menu-card:hover .card-overlay {
            opacity: 1;
        }

        .quick-view {
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
        }

        .quick-view:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow);
        }

        .card-content {
            padding: 25px;
        }

        .product-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .product-description {
            color: var(--gray-600);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 15px;
            min-height: 42px;
        }

        .price-section {
            display: flex;
            align-items: baseline;
            gap: 8px;
            margin-bottom: 20px;
        }

        .product-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--success);
        }

        .price-per-unit {
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 500;
        }

        .action-section {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn {
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-add-to-cart {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            width: 100%;
            border-radius: 25px;
        }

        .btn-add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: var(--gray-100);
            border-radius: 25px;
            padding: 8px;
        }

        .btn-qty {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-minus {
            background: var(--danger);
        }

        .btn-plus {
            background: var(--success);
        }

        .btn-qty:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow);
        }

        .quantity-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .quantity {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1;
        }

        .qty-label {
            font-size: 11px;
            color: var(--gray-500);
            font-weight: 500;
        }

        .summary-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 20px 30px;
            box-shadow: 0 -6px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border-top: 3px solid var(--primary-color);
        }

        .summary-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .total-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-icon {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            display: none;
        }

        .cart-count.show {
            display: flex;
        }

        .total-info h3 {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 2px;
            font-weight: 500;
        }

        .total-harga {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .button-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .summary-bar .btn {
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-back {
            background: var(--gray-500);
            color: white;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--success), #20c997);
            color: white;
        }

        .btn-view-cart {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .summary-bar .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .empty-section {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray-500);
        }

        .empty-section i {
            font-size: 80px;
            margin-bottom: 20px;
            color: var(--gray-300);
        }

        .empty-section h3 {
            font-size: 28px;
            margin-bottom: 12px;
            color: var(--gray-700);
        }

        .empty-section p {
            font-size: 16px;
            max-width: 400px;
            margin: 0 auto;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--success), #20c997);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification.show {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 28px;
            }

            .header p {
                font-size: 16px;
            }

            .container {
                padding: 30px 15px;
            }

            .menu-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }

            .section-title {
                font-size: 28px;
            }

            .summary-bar {
                padding: 15px 20px;
            }

            .summary-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .button-group {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }

            .summary-bar .btn {
                flex: 1;
                min-width: 120px;
            }

            .card-image {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 30px 15px;
            }

            .header h1 {
                font-size: 24px;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-text"></span>
    </div>

    <header class="header">
        <div class="header-content">
            <h1>
                <i class="fas fa-utensils"></i>
                Pilih Menu Favorit Anda
            </h1>
        </div>
    </header>

    <div class="container">
        <section id="makanan" class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-hamburger"></i>
                    Makanan
                </h2>
                <p class="section-subtitle">Cireng segar dengan berbagai varian rasa yang lezat dan menggugah selera</p>
            </div>
            <div class="menu-grid">
                <?php 
                if (mysqli_num_rows($result_makanan) > 0) {
                    $index = 0;
                    while ($row = mysqli_fetch_assoc($result_makanan)) { 
                        echo '<div style="animation-delay: ' . ($index * 0.1) . 's;">';
                        render_menu_card($row, $keranjang_db);
                        echo '</div>';
                        $index++;
                    }
                } else {
                    echo '<div class="empty-section">
                            <i class="fas fa-utensils"></i>
                            <h3>Belum Ada Menu Makanan</h3>
                            <p>Menu makanan sedang dalam persiapan. Silakan cek kembali nanti.</p>
                          </div>';
                }
                ?>
            </div>
        </section>

        <section id="minuman" class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-glass-water"></i>
                    Minuman
                </h2>
                <p class="section-subtitle">Minuman segar dan menyegarkan untuk menemani santapan cireng Anda</p>
            </div>
            <div class="menu-grid">
                <?php 
                if (mysqli_num_rows($result_minuman) > 0) {
                    $index = 0;
                    while ($row = mysqli_fetch_assoc($result_minuman)) { 
                        echo '<div style="animation-delay: ' . ($index * 0.1) . 's;">';
                        render_menu_card($row, $keranjang_db);
                        echo '</div>';
                        $index++;
                    }
                } else {
                    echo '<div class="empty-section">
                            <i class="fas fa-glass-water"></i>
                            <h3>Belum Ada Menu Minuman</h3>
                            <p>Menu minuman sedang dalam persiapan. Silakan cek kembali nanti.</p>
                          </div>';
                }
                ?>
            </div>
        </section>
    </div>

    <div class="summary-bar">
        <div class="summary-content">
            <div class="total-section">
                <div class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cart-count">0</span>
                </div>
                <div class="total-info">
                    <h3>Total Belanja</h3>
                    <div class="total-harga" id="total-harga-display">Rp 0</div>
                </div>
            </div>
            <div class="button-group">
                <a href="../user/menu_user.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button id="btn-masukkan-keranjang" class="btn btn-update">
                    <i class="fas fa-cart-plus"></i>
                    Update Keranjang
                </button>
                <a href="../pesan/keranjang.php" class="btn btn-view-cart">
                    <i class="fas fa-shopping-cart"></i>
                    Lihat Keranjang
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const keranjang_lokal = {};

            function showNotification(message, type = 'success') {
                const notification = document.getElementById('notification');
                const notificationText = document.getElementById('notification-text');
                
                notificationText.textContent = message;
                
                if (type === 'error') {
                    notification.style.background = 'linear-gradient(135deg, var(--danger), #c82333)';
                } else {
                    notification.style.background = 'linear-gradient(135deg, var(--success), #20c997)';
                }
                
                notification.classList.add('show');
                
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 4000);
            }

            function updateCartCount() {
                const cartCount = document.getElementById('cart-count');
                const totalItems = Object.values(keranjang_lokal).reduce((sum, qty) => sum + qty, 0);
                
                if (totalItems > 0) {
                    cartCount.textContent = totalItems;
                    cartCount.classList.add('show');
                } else {
                    cartCount.classList.remove('show');
                }
            }

            function initKeranjang() {
                document.querySelectorAll('.menu-card').forEach(card => {
                    const id = card.dataset.id;
                    const quantityElement = card.querySelector('.quantity');
                    if (quantityElement) {
                        const kuantitas = parseInt(quantityElement.textContent);
                        if (kuantitas > 0) {
                            keranjang_lokal[id] = kuantitas;
                        }
                    }
                });
                calculateTotal();
                updateCartCount();
            }

            function calculateTotal() {
                let totalHarga = 0;
                for (const id in keranjang_lokal) {
                    const card = document.querySelector(`.menu-card[data-id='${id}']`);
                    if (card) {
                        const harga = parseInt(card.dataset.harga);
                        totalHarga += harga * keranjang_lokal[id];
                    }
                }
                document.getElementById('total-harga-display').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            }

            function updateCardDisplay(card, kuantitas) {
                const addBtn = card.querySelector('.btn-add-to-cart');
                const qtyControls = card.querySelector('.quantity-controls');
                const qtySpan = card.querySelector('.quantity');

                if (kuantitas > 0) {
                    addBtn.style.display = 'none';
                    qtyControls.style.display = 'flex';
                    qtySpan.textContent = kuantitas;
                } else {
                    addBtn.style.display = 'flex';
                    qtyControls.style.display = 'none';
                }
            }

            function animateCard(card) {
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                }, 150);
            }

            document.querySelectorAll('.menu-grid').forEach(grid => {
                grid.addEventListener('click', function(e) {
                    const card = e.target.closest('.menu-card');
                    if (!card) return;

                    const id = card.dataset.id;
                    let kuantitas = keranjang_lokal[id] || 0;

                    if (e.target.closest('.btn-add-to-cart')) {
                        kuantitas = 1;
                        animateCard(card);
                    } else if (e.target.closest('.btn-plus')) {
                        kuantitas++;
                        animateCard(card);
                    } else if (e.target.closest('.btn-minus')) {
                        kuantitas--;
                        animateCard(card);
                    } else if (e.target.closest('.quick-view')) {
                        // Quick view functionality bisa ditambahkan di sini
                        showNotification('Fitur quick view segera hadir!', 'info');
                        return;
                    }
                    
                    if (kuantitas > 0) {
                        keranjang_lokal[id] = kuantitas;
                    } else {
                        delete keranjang_lokal[id];
                    }
                    
                    updateCardDisplay(card, kuantitas);
                    calculateTotal();
                    updateCartCount();
                });
            });

            document.getElementById('btn-masukkan-keranjang').addEventListener('click', function() {
                const button = this;
                const originalContent = button.innerHTML;
                
                // Show loading state
                button.innerHTML = '<div class="loading"></div> Memproses...';
                button.disabled = true;

                fetch('proses_keranjang.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ keranjang: keranjang_lokal })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showNotification(data.message || 'Keranjang berhasil diperbarui!');
                        // Optional: Update UI to reflect changes
                    } else {
                        showNotification('Error: ' + (data.message || 'Terjadi kesalahan'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat menghubungi server.', 'error');
                })
                .finally(() => {
                    // Restore button state
                    button.innerHTML = originalContent;
                    button.disabled = false;
                });
            });

            // Initialize animations with staggered delays
            const cards = document.querySelectorAll('.menu-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Smooth scroll to sections
            const sectionLinks = document.querySelectorAll('a[href^="#"]');
            sectionLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    if (targetSection) {
                        targetSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Intersection Observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all menu cards for scroll animations
            document.querySelectorAll('.menu-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Initialize everything
            initKeranjang();

            // Add pulse animation to cart icon when items are added
            const originalUpdateCartCount = updateCartCount;
            updateCartCount = function() {
                const cartIcon = document.querySelector('.cart-icon');
                const oldCount = parseInt(document.getElementById('cart-count').textContent) || 0;
                
                originalUpdateCartCount();
                
                const newCount = parseInt(document.getElementById('cart-count').textContent) || 0;
                if (newCount > oldCount) {
                    cartIcon.style.animation = 'none';
                    cartIcon.offsetHeight; // Trigger reflow
                    cartIcon.style.animation = 'pulse 0.6s ease-out';
                }
            };

            // Add pulse keyframe
            const style = document.createElement('style');
            style.textContent = `
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.2); }
                    100% { transform: scale(1); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>