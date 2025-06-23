<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di CIANJURRR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset dan Pengaturan Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f6f2; /* Warna latar belakang yang hangat */
            color: #333;
        }

        /* Header dan Navigasi */
        header {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-logo {
            font-size: 1.5em;
            font-weight: 700;
            color: #d9534f; /* Warna merah yang menarik */
        }

        .menu-container {
            position: relative;
        }

        .menu-button {
            background: none;
            border: none;
            font-size: 2em;
            cursor: pointer;
            color: #333;
            line-height: 1;
        }

        .dropdown-menu {
            display: none; /* Sembunyikan secara default */
            position: absolute;
            top: 45px;
            right: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            overflow: hidden;
            width: 200px;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            font-size: 1em;
            transition: background-color 0.2s;
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }

        /* Kelas untuk menampilkan menu dengan JS */
        .dropdown-menu.show {
            display: block;
        }

        /* Konten Utama */
        main {
            padding: 40px 20px;
            max-width: 1000px;
            margin: auto;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .hero-section h1 {
            font-size: 2.8em;
            color: #333;
            margin-bottom: 15px;
        }
        
        .hero-section span {
            color: #d9534f;
            font-weight: 700;
        }

        .hero-section p {
            font-size: 1.1em;
            color: #666;
            line-height: 1.6;
            max-width: 700px;
            margin: auto;
        }

        .product-showcase {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-description {
            padding: 20px;
            text-align: center;
        }

        .product-description h3 {
            margin-bottom: 10px;
            color: #d9534f;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            margin-top: 50px;
            color: #888;
            font-size: 0.9em;
        }

    </style>
</head>
<body>

    <header>
        <div class="brand-logo">CIANJURRR</div>
        <div class="menu-container">
            <button id="menu-button" class="menu-button">&#8942;</button>
            <div id="dropdown-menu" class="dropdown-menu">
                <a href="#">Home</a>
                <a href="../login/login.php">Pesan Cireng</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <h1>Halo, selamat datang di web <span>CIANJURRR</span></h1>
            <p>
                CIANJURRR adalah singkatan dari Cireng Banjurrr, yaitu makanan cireng yang diberi kuah yang sangat enak. Ayo dibeli, biar kalian ga penasaran!
            </p>
        </section>

        <section class="product-showcase">
            <div class="product-card">
                <img src="https://placehold.co/400x300/d9534f/ffffff?text=Cireng+Banjur" alt="Cireng Banjur dengan kuah pedas">
                <div class="product-description">
                    <h3>Kuah Pedas Nagih</h3>
                    <p>Perpaduan cireng kenyal dengan kuah pedas gurih yang dibuat dari rempah-rempah pilihan. Dijamin bikin ketagihan!</p>
                </div>
            </div>

            <div class="product-card">
                <img src="https://placehold.co/400x300/5cb85c/ffffff?text=Cireng+Original" alt="Cireng Original yang renyah">
                <div class="product-description">
                    <h3>Tekstur Sempurna</h3>
                    <p>Nikmati sensasi renyah di luar dan kenyal di dalam dari cireng berkualitas tinggi. Cocok untuk teman santai Anda.</p>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2025 CIANJURRR. Semua Hak Cipta Dilindungi.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('menu-button');
            const dropdownMenu = document.getElementById('dropdown-menu');

            // Toggle menu saat tombol diklik
            menuButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah event klik menyebar ke window
                dropdownMenu.classList.toggle('show');
            });

            // Sembunyikan menu saat mengklik di luar area menu
            window.addEventListener('click', function(event) {
                if (!menuButton.contains(event.target)) {
                    if (dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });
        });
    </script>

</body>
</html>