-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2024. Aug 21. 10:40
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `webshop`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `voucher` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- A tábla adatainak kiíratása `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `brand`, `voucher`, `created`) VALUES
(3001, 'ASUS ROG STRIX Z690-A GAMING WIFI D4', 136590, 'ASUS', '{\"voucher\": [\"3\"]}', '2024-08-14'),
(3002, 'LOGITECH HD Pro Webcam C920', 28990, 'Logitech', '{\"voucher\": [\"2\",\"4\"]}', '2024-08-14'),
(3003, 'WD Blue 1TB 3.5” 7200rpm 64MB SATA WD10EZEX', 12990, 'Western Digital', NULL, '2024-08-14'),
(3004, 'GeForce GTX 1660 Ti 6GB GDDR6 TUF Gaming Evo PCIE', 208690, 'ASUS', '{\"voucher\": [\"1\",\"3\"]}', '2024-08-14'),
(3005, 'WD Red Plus 2TB 3.5” 5400rpm 128MB SATA WD20EFZX', 25190, 'Western Digital', NULL, '2024-08-14'),
(3006, 'Kingston Fury 32GB Beast DDR4 3200MHz CL16 KF432C16BB/32', 48990, 'Kingston', NULL, '2024-08-14'),
(3007, 'LOGITECH K120 Magyar fekete OEM', 5790, 'Logitech', '{\"voucher\": [\"4\"]}', '2024-08-14'),
(3008, 'LOGITECH B100 fekete OEM]', 3590, 'Logitech', '{\"voucher\": [\"4\"]}', '2024-08-14');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `voucher`
--

CREATE TABLE `voucher` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`conditions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- A tábla adatainak kiíratása `voucher`
--

INSERT INTO `voucher` (`id`, `type`, `conditions`) VALUES
(1, 'price', '{\"fixed\": 1000}'),
(2, 'percentage', '{\"percent\": 15}'),
(3, 'percentage', '{\"percent\": 5, \"brand\":\"ASUS\"}'),
(4, 'group', '{\"min_items\": 3, \"brand\":\"Logitech\"}');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
