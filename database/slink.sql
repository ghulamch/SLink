-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 09 Jul 2025 pada 12.39
-- Versi server: 10.6.22-MariaDB-cll-lve
-- Versi PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ghulammy_slink`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `short_links`
--

CREATE TABLE `short_links` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `original_url` text NOT NULL,
  `short_link` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `visitor_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `short_links`
--

INSERT INTO `short_links` (`id`, `judul`, `original_url`, `short_link`, `user_id`, `password`, `visitor_count`, `created_at`) VALUES
(5, 'Zoom PK PM Neutron 6 April', 'https://neutron.zoom.us/j/7636274460?pwd=aitGbWEwMkdObHRzRFhrYlQ1d0ZDZz09&omn=99198793003', 'BmNoHk', 1, '$2y$10$zZ0Kd3/s7O/YDbzLL0Abx.TmGO9z0SHRKXrzfQn558HzTU/9n3PIO', 1, '2025-04-06 06:02:12'),
(7, '', 'https://simt.kemdikbud.go.id/resume?id=eyAiCH5GYKlWYTYkSNSmcQ&name=ghulamin-chalim-alwi', 'jBBbI9', 1, '$2y$10$LNxMJ6yjv1pU9SMF8fYMgOdGUP5kx6c0qiWQrViW92kpYTRo32Jmm', 1, '2025-05-27 14:11:21'),
(8, '', 'https://simt.kemdikbud.go.id/resume?id=eyAiCH5GYKlWYTYkSNSmcQ&name=ghulamin-chalim-alwi', 'ghulam', 1, '', 1, '2025-05-27 14:12:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `activation_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `nama`, `reset_token`, `activation_token`) VALUES
(1, 'ghulam', '$2y$10$NKil0mNGiV89C2UNobdCauSzL1NGu9uqjSVqoixVcSemJjJLm.jjm', 'ghulaminchalimalwi170507@gmail.com', 'Ghulamin Chalim Alwi', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `short_links`
--
ALTER TABLE `short_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `short_links`
--
ALTER TABLE `short_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `short_links`
--
ALTER TABLE `short_links`
  ADD CONSTRAINT `short_links_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
