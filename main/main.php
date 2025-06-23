<?php
// Mulai sesi di baris paling atas untuk mengelola status login
session_start();

// --- PUSAT DATA KONTEN ---
// Data konten tetap sama, menggunakan array PHP yang dinamis
$menu_saus = [
    ['icon' => '🌶️', 'nama' => 'Saus Padang', 'deskripsi' => 'Saus pedas khas Padang yang kaya akan rempah-rempah tradisional Indonesia.'],
    ['icon' => '⚫', 'nama' => 'Black Pepper', 'deskripsi' => 'Saus lada hitam yang gurih dengan aroma rempah yang menggoda selera.'],
    ['icon' => '🍋', 'nama' => 'Asam Manis', 'deskripsi' => 'Perpaduan sempurna antara rasa asam segar dan manis yang menyegarkan.'],
    ['icon' => '🍅', 'nama' => 'Saus Tomat', 'deskripsi' => 'Saus tomat klasik yang disukai semua kalangan dengan rasa yang familiar.'],
    ['icon' => '🧄', 'nama' => 'Bawang Putih', 'deskripsi' => 'Saus bawang putih yang harum dan gurih, cocok untuk pecinta rasa savory.'],
    ['icon' => '🌿', 'nama' => 'Herbs Special', 'deskripsi' => 'Saus herbs istimewa dengan campuran daun-daunan segar yang aromatik.']
];
$keunggulan = [
    ['icon' => '🎨', 'judul' => 'Custom Saus', 'deskripsi' => 'Pilih dan kombinasikan saus sesuai selera Anda untuk pengalaman rasa yang unik.'],
    ['icon' => '🔥', 'judul' => 'Selalu Fresh', 'deskripsi' => 'Cireng dibuat fresh setiap hari dengan bahan-bahan berkualitas terbaik.'],
    ['icon' => '💝', 'judul' => 'Harga Terjangkau', 'deskripsi' => 'Nikmati kelezatan cireng premium dengan harga yang ramah di kantong.'],
    ['icon' => '⚡', 'judul' => 'Pelayanan Cepat', 'deskripsi' => 'Pesanan Anda akan disiapkan dengan cepat tanpa mengurangi kualitas rasa.']
];
$reviews = [
    ['rating' => 5, 'teks' => 'Cireng Banjurr ini enak banget! Saus Padangnya bikin nagih, pedasnya pas dan bumbunya meresap sempurna. Pasti akan order lagi!', 'avatar' => 'SA', 'nama' => 'Sari Amelia', 'info' => 'Ibu Rumah Tangga'],
    ['rating' => 5, 'teks' => 'Konsepnya unik banget, bisa custom saus sesuai selera. Aku suka banget yang black pepper, gurih dan aromanya wangi!', 'avatar' => 'RD', 'nama' => 'Rizky Dwi', 'info' => 'Mahasiswa'],
    ['rating' => 5, 'teks' => 'Harganya terjangkau tapi rasanya premium! Saus asam manisnya seger banget, cocok buat yang ga terlalu suka pedas.', 'avatar' => 'MF', 'nama' => 'Maya Fitri', 'info' => 'Karyawan Swasta']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cireng Banjurr - Cireng Kuah Saus Pilihan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* == PERUBAHAN CSS UTAMA ADA DI BAGIAN HEADER & NAVBAR == */
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #F7931E;
            --text-color: #333;
            --bg-light: #FFF5F0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; background-color: #fdfcfa; color: var(--text-color); }

        /* Header dan Navigasi */
        .header {
            background-color: #ffffff;
            padding: 15px 5%; /* Padding menggunakan persen agar lebih fleksibel */
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .brand-logo {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        .navbar {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }
        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .nav-links a:hover {
            color: var(--primary-color);
        }
        .nav-actions {
            display: flex;
            gap: 15px;
        }
        .nav-actions .btn {
            padding: 8px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-login {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        .btn-login:hover {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-register {
            background-color: var(--primary-color);
            border: 2px solid var(--primary-color);
            color: white;
        }
        .btn-register:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Tombol Hamburger untuk Mobile */
        .hamburger-menu {
            display: none; /* Sembunyi di desktop */
            font-size: 2em;
            background: none;
            border: none;
            cursor: pointer;
        }

        /* Aturan Responsive untuk Navbar */
        @media (max-width: 992px) {
            .navbar {
                display: none; /* Sembunyikan navbar di mobile */
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: white;
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }
            .navbar.active {
                display: flex; /* Tampilkan saat aktif */
            }
            .nav-links {
                flex-direction: column;
                width: 100%;
                text-align: center;
                gap: 20px;
            }
            .nav-actions {
                flex-direction: column;
                width: 100%;
                margin-top: 20px;
            }
            .nav-actions .btn {
                width: 100%;
                text-align: center;
            }
            .hamburger-menu {
                display: block; /* Tampilkan tombol hamburger di mobile */
            }
        }

        /* Konten Utama (CSS Lainnya Tetap Sama) */
        main { padding: 0 20px; max-width: 1100px; margin: auto; }
        section { padding: 60px 0; }
        .section-title { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: var(--primary-color); position: relative; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 80px; height: 4px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); border-radius: 2px; }
        .hero-section { text-align: center; padding: 80px 0; }
        .hero-section h1 { font-size: 3em; margin-bottom: 15px; }
        .hero-section span { color: var(--primary-color); }
        .hero-section p { font-size: 1.2em; color: #666; line-height: 1.6; max-width: 700px; margin: auto; margin-bottom: 30px; }
        .cta-button { background: var(--primary-color); color: white; padding: 15px 30px; border: none; border-radius: 50px; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4); }
        .cta-button:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255, 107, 53, 0.5); }
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); transition: transform 0.3s, box-shadow 0.3s; padding: 25px; text-align: center; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); }
        .card .icon { font-size: 3rem; margin-bottom: 1rem; }
        .card h3 { margin-bottom: 10px; color: var(--primary-color); }
        .card p { color: #666; }
        .review-card .stars { color: #FFD700; margin-bottom: 1rem; }
        .review-card .review-text { font-style: italic; margin-bottom: 1rem; }
        .review-card .reviewer { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 1rem; }
        .review-card .avatar { width: 50px; height: 50px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .review-card .reviewer-info { text-align: left; }
        .review-card .reviewer-info h4 { margin: 0; color: var(--text-color); }
        .review-card .reviewer-info p { margin: 0; font-size: 0.9em; }
        footer { text-align: center; padding: 20px; margin-top: 50px; color: #888; font-size: 0.9em; background-color: #fff; }
    </style>
</head>
<body>

    <header class="header">
        <a href="#home" class="brand-logo">🥟 Cireng Banjurr</a>
        
        <nav class="navbar" id="navbar">
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#reviews">Review</a></li>
            </ul>
            <div class="nav-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="akun.php" class="btn btn-login">Akun Saya</a>
                    <a href="../logout/logout.php" class="btn btn-register">Logout</a>
                <?php else: ?>
                    <a href="../login/login.php" class="btn btn-login">Login</a>
                    <a href="../login/register.php" class="btn btn-register">Register</a>
                <?php endif; ?>
            </div>
        </nav>
        
        <button class="hamburger-menu" id="hamburger-menu">☰</button>
    </header>

    <main>
        <section id="home" class="hero-section">
            <h1>Renyah di Luar, Kenyal di Dalam, <span>Banjir Saus!</span></h1>
            <p>
                Selamat datang di Cireng Banjurr! Kami menyajikan sensasi cireng tradisional dengan sentuhan modern. Pilih saus favoritmu dan rasakan ledakan rasa di setiap gigitan.
            </p>
            <a href="#menu" class="cta-button">Lihat Pilihan Saus</a>
        </section>

        <section id="menu">
            <h2 class="section-title">Pilihan Saus Favorit</h2>
            <div class="grid-container">
                <?php foreach ($menu_saus as $saus): ?>
                    <div class="card">
                        <div class="icon"><?= $saus['icon'] ?></div>
                        <h3><?= htmlspecialchars($saus['nama']) ?></h3>
                        <p><?= htmlspecialchars($saus['deskripsi']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <section id="features">
            <h2 class="section-title">Keunggulan Kami</h2>
            <div class="grid-container">
                <?php foreach ($keunggulan as $item): ?>
                    <div class="card">
                        <div class="icon"><?= $item['icon'] ?></div>
                        <h3><?= htmlspecialchars($item['judul']) ?></h3>
                        <p><?= htmlspecialchars($item['deskripsi']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section id="reviews">
            <h2 class="section-title">Apa Kata Mereka</h2>
            <div class="grid-container">
                 <?php foreach ($reviews as $review): ?>
                    <div class="card review-card">
                        <div class="stars"><?= str_repeat('⭐', $review['rating']) ?></div>
                        <p class="review-text">"<?= htmlspecialchars($review['teks']) ?>"</p>
                        <div class="reviewer">
                            <div class="avatar"><?= htmlspecialchars($review['avatar']) ?></div>
                            <div class="reviewer-info">
                                <h4><?= htmlspecialchars($review['nama']) ?></h4>
                                <p><?= htmlspecialchars($review['info']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Cireng Banjurrr. Dibuat dengan ❤️</p>
    </footer>

    <script>
        // JavaScript untuk fungsionalitas menu hamburger
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerButton = document.getElementById('hamburger-menu');
            const navbar = document.getElementById('navbar');

            hamburgerButton.addEventListener('click', function() {
                // Toggle kelas 'active' pada navbar
                navbar.classList.toggle('active');
            });
        });
    </script>

</body>
</html>