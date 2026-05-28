-- phpMyAdmin SQL Dump

-- version 5.2.1

-- https://www.phpmyadmin.net/

--

-- ホスト: 127.0.0.1:3307

-- 生成日時: 2026-05-28 09:51:58

-- サーバのバージョン： 10.4.32-MariaDB

-- PHP のバージョン: 8.2.12



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";





/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;



--

-- データベース: `book_management`

--



-- --------------------------------------------------------



--

-- テーブルの構造 `migrations`

--



CREATE TABLE `migrations` (

  `id` int(10) UNSIGNED NOT NULL,

  `migration` varchar(255) NOT NULL,

  `batch` int(11) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



--

-- ダンプしたテーブルのインデックス

--



--

-- テーブルのインデックス `migrations`

--

ALTER TABLE `migrations`

  ADD PRIMARY KEY (`id`);



--

-- ダンプしたテーブルの AUTO_INCREMENT

--



--

-- テーブルの AUTO_INCREMENT `migrations`

--

ALTER TABLE `migrations`

  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;