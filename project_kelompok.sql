-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pwl_project_kelompok`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `kuantitas` int(11) NOT NULL,
  `harga_saat_pesan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `produk_id`, `kuantitas`, `harga_saat_pesan`) VALUES
(1, 1, 1, 3, 1000),
(2, 2, 3, 2, 13000),
(3, 2, 4, 1, 13000),
(4, 2, 5, 1, 40000);

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `kuantitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id`, `user_id`, `produk_id`, `kuantitas`) VALUES
(5, 4, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `telepon_penerima` varchar(20) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `total_harga` int(11) NOT NULL,
  `status_pesanan` enum('pending','diproses','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `tanggal_pesanan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `user_id`, `nama_penerima`, `telepon_penerima`, `alamat_pengiriman`, `metode_pembayaran`, `total_harga`, `status_pesanan`, `tanggal_pesanan`) VALUES
(1, 3, 'pwl_project_kelompok', '081218030162', 'Jl. Madu Indah No.95, Bambu Apus', 'Transfer Bank', 3000, 'dikirim', '2025-06-23 13:51:54'),
(2, 4, 'agus budiyono', '09120923091', 'jakarta selatan sonoan dikit', 'COD', 79000, 'dikirim', '2025-06-23 14:18:20');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `jenis` enum('Makanan','Minuman') NOT NULL,
  `gambar` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `deskripsi`, `harga`, `jenis`, `gambar`) VALUES
(1, 'jus', 'ini jus mangga namanya, pake mangga jawa', 1000, 'Minuman', 'upload_gambar/68596ffd5f339_1750691837.jpg'),
(2, 'cireng bumbu rujak', 'asam manis', 13000, 'Makanan', 'upload_gambar/68596f2957774_1750691625.jpeg'),
(3, 'Cireng asam manis', 'cireng dengan bumbu kuah asam manis', 13000, 'Makanan', 'upload_gambar/68596b588be07_1750690648.jpg'),
(4, 'cireng kuah bbq', 'cireng dengan kuah bbq', 13000, 'Makanan', 'upload_gambar/68596f1037d84_1750691600.png'),
(5, 'cireng sebaskom', 'cireng paket besar untuk bersama', 40000, 'Makanan', 'upload_gambar/68596f5c73ebc_1750691676.webp'),
(6, 'jus strawberry', 'ini jus strawbery', 12000, 'Minuman', 'upload_gambar/68596a46855fc_1750690374.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `usia` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `jenis_kelamin`, `usia`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'lelly', 'Perempuan', 23, 'lely@gmail.com', '$2y$10$GQIXC4mw9ImEV3Zo.Fv22uQW.lsNIP3z5Mzj692o8OCL.xOYOB4OW', 'user', '2025-06-19 15:02:01'),
(2, 'hanif', 'Laki-laki', 21, 'hanif@gmail.com', '$2y$10$SpiGE4oUl2rCv0FdkLdmeu79b35h9FUUy632SFBWXlaBA/yK6Pn3e', 'admin', '2025-06-19 15:10:01'),
(3, 'minmin', 'Laki-laki', 13, 'minmin@gmai.com', '$2y$10$itnBNMwzEd2MVdllmacLmOMDxlYEN7J20BHfWVD98OgbODpcslg7O', 'admin', '2025-06-23 13:50:28'),
(4, 'agus', 'Laki-laki', 34, 'agus@gmail.com', '$2y$10$C4kQRi.bwyjsOnd1LI2QSOvgXK1QxFjZG1fQWN9o.7Fa3Inf0NroW', 'user', '2025-06-23 14:05:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_produk` (`user_id`,`produk_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
