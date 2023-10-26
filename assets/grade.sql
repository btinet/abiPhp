-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Okt 2023 um 19:14
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tk_abicalc`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `grade`
--

CREATE TABLE `grade` (
  `id` int(11) NOT NULL,
  `grade` double NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `grade`
--

INSERT INTO `grade` (`id`, `grade`, `min`, `max`) VALUES
(1, 4, 300, 300),
(2, 3.9, 301, 318),
(4, 3.8, 319, 336),
(5, 3.7, 337, 354),
(6, 3.6, 355, 372),
(7, 3.5, 373, 390),
(8, 3.4, 391, 408),
(9, 3.3, 409, 426),
(10, 3.2, 427, 444),
(11, 3.1, 445, 462),
(12, 3, 463, 480),
(13, 2.9, 481, 498),
(14, 2.8, 499, 516),
(15, 2.7, 517, 534),
(16, 2.6, 535, 552),
(17, 2.4, 571, 588),
(18, 2.5, 553, 570),
(19, 2.3, 589, 606),
(20, 2.2, 607, 624),
(21, 2.1, 625, 642),
(22, 2, 643, 660),
(23, 1.9, 661, 678),
(24, 1.8, 679, 696),
(25, 1.7, 697, 714),
(26, 1.6, 715, 732),
(27, 1.5, 733, 750),
(28, 1.4, 751, 768),
(29, 1.3, 769, 786),
(30, 1.2, 787, 804),
(31, 1.1, 805, 822),
(32, 1, 823, 900);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `grade`
--
ALTER TABLE `grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
