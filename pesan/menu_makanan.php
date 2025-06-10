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

// Ambil data makanan & minuman
$result_makanan = mysqli_query($db, "SELECT * FROM produk WHERE jenis = 'Makanan'");
$result_minuman = mysqli_query($db, "SELECT * FROM produk WHERE jenis = 'Minuman'");

// Fungsi untuk membuat kartu menu, sama seperti sebelumnya
function render_menu_card($row, $keranjang) {
    $produk_id = $row['id'];
    $kuantitas = isset($keranjang[$produk_id]) ? $keranjang[$produk_id] : 0;
    $display_beli = $kuantitas > 0 ? 'style="display: none;"' : '';
    $display_qty = $kuantitas > 0 ? 'style="display: flex;"' : 'style="display: none;"';

    echo '
    <div class="menu-card" data-id="'. $produk_id .'" data-harga="'. $row['harga'] .'">
        <h3>'. htmlspecialchars($row['nama_produk']) .'</h3>
        <p class="harga">Rp '. number_format($row['harga']) .'</p>
        <div class="action-button">
            <button class="btn btn-beli" '. $display_beli .'>Beli</button>
            <div class="quantity-selector" '. $display_qty .'>
                <button class="btn-qty btn-minus">-</button>
                <span class="quantity">'. $kuantitas .'</span>
                <button class="btn-qty btn-plus">+</button>
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
    <title>Pilih Menu</title>
    <style>
        /* Salin CSS dari kode sebelumnya, tidak ada perubahan di sini */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fc; margin: 0; padding-bottom: 120px; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; }
        h1, h2 { color: #333; text-align: center; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-top: 20px; }
        .menu-card { background-color: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); overflow: hidden; text-align: center; padding-bottom: 15px; transition: transform 0.2s; }
        .menu-card:hover { transform: translateY(-5px); }
        .menu-card h3 { margin: 10px 0 5px 0; color: #333; height: 40px; }
        .menu-card .harga { color: #007bff; font-weight: bold; margin-bottom: 15px; }
        .action-button .btn { background-color: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; transition: background-color 0.2s; }
        .action-button .btn:hover { background-color: #0056b3; }
        .quantity-selector { display: flex; justify-content: center; align-items: center; gap: 10px; }
        .quantity-selector .btn-qty { background-color: #6c757d; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; font-size: 18px; cursor: pointer; }
        .quantity-selector .quantity { font-size: 16px; font-weight: bold; }
        .summary-bar { position: fixed; bottom: 0; left: 0; width: 100%; background-color: #fff; padding: 15px 30px; box-shadow: 0 -4px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; }
        .summary-bar .total-harga { font-size: 18px; font-weight: bold; color: #333; }
        
        /* Grup Tombol di Kanan */
        .summary-bar .button-group {
            display: flex;
            align-items: center;
        }

        .summary-bar .btn { padding: 12px 20px; font-size: 16px; font-weight: bold; border-radius: 8px; cursor: pointer; text-decoration: none; border: none; margin-left: 10px; /* Jarak antar tombol */ }
        
        /* == PERUBAHAN CSS DI SINI == */
        .btn-kembali { background-color: #6c757d; color: white;}
        .btn-update { background-color: #28a745; color: white; }
        .btn-lihat-keranjang { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h1>Pilih Menu Favorit Anda</h1>
    
    <section id="makanan">
        <h2>Makanan</h2>
        <div class="menu-grid">
            <?php while ($row = mysqli_fetch_assoc($result_makanan)) { render_menu_card($row, $keranjang_db); } ?>
        </div>
    </section>

    <section id="minuman" style="margin-top: 50px;">
        <h2>Minuman</h2>
        <div class="menu-grid">
            <?php while ($row = mysqli_fetch_assoc($result_minuman)) { render_menu_card($row, $keranjang_db); } ?>
        </div>
    </section>
</div>

<div class="summary-bar">
    <div class="total-harga">Total: <span id="total-harga-display">Rp 0</span></div>
    <div class="button-group">
        <a href="../user/menu_user.php" class="btn btn-kembali">Kembali ke Menu</a>
        <button id="btn-masukkan-keranjang" class="btn btn-update">Masukkan Keranjang</button>
        <a href="../pesan/keranjang.php" class="btn btn-lihat-keranjang">Lihat Keranjang</a>
    </div>
</div>

<script>
// JavaScript tidak ada perubahan, tetap sama seperti sebelumnya
document.addEventListener('DOMContentLoaded', function() {
    const keranjang_lokal = {};

    function initKeranjang() {
        document.querySelectorAll('.menu-card').forEach(card => {
            const id = card.dataset.id;
            const kuantitas = parseInt(card.querySelector('.quantity').textContent);
            if (kuantitas > 0) {
                keranjang_lokal[id] = kuantitas;
            }
        });
        calculateTotal();
    }

    function calculateTotal() {
        let totalHarga = 0;
        for (const id in keranjang_lokal) {
            const card = document.querySelector(`.menu-card[data-id='${id}']`);
            const harga = parseInt(card.dataset.harga);
            totalHarga += harga * keranjang_lokal[id];
        }
        document.getElementById('total-harga-display').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
    }

    document.querySelectorAll('.menu-grid').forEach(grid => {
        grid.addEventListener('click', function(e) {
            const card = e.target.closest('.menu-card');
            if (!card) return;

            const id = card.dataset.id;
            const beliBtn = card.querySelector('.btn-beli');
            const qtySelector = card.querySelector('.quantity-selector');
            const qtySpan = card.querySelector('.quantity');
            
            let kuantitas = keranjang_lokal[id] || 0;

            if (e.target.classList.contains('btn-beli')) {
                kuantitas = 1;
                beliBtn.style.display = 'none';
                qtySelector.style.display = 'flex';
            } else if (e.target.classList.contains('btn-plus')) {
                kuantitas++;
            } else if (e.target.classList.contains('btn-minus')) {
                kuantitas--;
            }
            
            if (kuantitas > 0) {
                keranjang_lokal[id] = kuantitas;
                qtySpan.textContent = kuantitas;
            } else {
                delete keranjang_lokal[id];
                beliBtn.style.display = 'inline-block';
                qtySelector.style.display = 'none';
            }
            
            calculateTotal();
        });
    });

    document.getElementById('btn-masukkan-keranjang').addEventListener('click', function() {
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
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghubungi server.');
        });
    });

    initKeranjang();
});
</script>

</body>
</html>