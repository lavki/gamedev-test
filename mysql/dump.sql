-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Час створення: Трв 06 2020 р., 16:19
-- Версія сервера: 5.7.18
-- Версія PHP: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `test_task`
--

-- --------------------------------------------------------

--
-- Структура таблиці `boosterpack`
--

CREATE TABLE `boosterpack` (
  `id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bank` decimal(10,2) NOT NULL DEFAULT '0.00',
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `boosterpack`
--

INSERT INTO `boosterpack` (`id`, `price`, `bank`, `time_created`, `time_updated`) VALUES
(1, '5.00', '0.00', '2020-03-30 00:17:28', '2020-05-03 17:41:01'),
(2, '20.00', '0.00', '2020-03-30 00:17:28', '2020-05-03 17:41:01'),
(3, '50.00', '14.00', '2020-03-30 00:17:28', '2020-05-06 12:41:09');

-- --------------------------------------------------------

--
-- Структура таблиці `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `assign_id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `assign_id`, `text`, `parent_id`, `time_created`, `time_updated`) VALUES
(1, 1, 1, 'Ну чо ассигн проверим', NULL, '2020-03-27 21:39:44', '2020-05-03 17:41:02'),
(2, 1, 1, 'Второй коммент', NULL, '2020-03-27 21:39:55', '2020-05-03 17:41:02'),
(3, 2, 1, 'Второй коммент от второго человека', NULL, '2020-03-27 21:40:22', '2020-05-03 17:41:02'),
(4, 1, 2, 'comment №1', NULL, '2020-05-05 19:14:17', '2020-05-06 11:54:43'),
(5, 1, 2, 'comment №2', 4, '2020-05-05 19:14:17', '2020-05-06 11:54:48'),
(6, 1, 2, 'comment №3', 5, '2020-05-05 19:14:17', '2020-05-06 11:54:53'),
(7, 1, 2, 'comment №5', NULL, '2020-05-05 19:14:17', '2020-05-06 11:54:56'),
(8, 1, 2, 'comment №4', 5, '2020-05-05 19:14:17', '2020-05-06 11:55:01'),
(17, 1, 2, 'comment №6', NULL, '2020-05-06 11:55:27', '2020-05-06 11:55:27');

-- --------------------------------------------------------

--
-- Структура таблиці `likes`
--

CREATE TABLE `likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `type` enum('post','comment') NOT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `relation_id`, `type`, `time_created`, `time_updated`) VALUES
(1, 2, 2, 'post', '2020-05-06 14:19:10', '2020-05-06 14:19:10'),
(2, 2, 2, 'post', '2020-05-06 14:19:11', '2020-05-06 14:19:11'),
(3, 2, 2, 'post', '2020-05-06 14:19:11', '2020-05-06 14:19:11'),
(4, 1, 2, 'comment', '2020-05-06 14:19:11', '2020-05-06 14:19:11');

-- --------------------------------------------------------

--
-- Структура таблиці `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `text` text NOT NULL,
  `img` varchar(1024) DEFAULT NULL,
  `time_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `post`
--

INSERT INTO `post` (`id`, `user_id`, `text`, `img`, `time_created`, `time_updated`) VALUES
(1, 1, 'Тестовый постик 1', '/images/posts/1.png', '2018-08-30 13:31:14', '2020-05-03 17:41:02'),
(2, 1, 'Печальный пост', '/images/posts/2.png', '2018-10-11 01:33:27', '2020-05-03 17:41:02');

-- --------------------------------------------------------

--
-- Структура таблиці `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `personaname` varchar(50) NOT NULL DEFAULT '',
  `avatarfull` varchar(150) NOT NULL DEFAULT '',
  `rights` tinyint(4) NOT NULL DEFAULT '0',
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wallet_total_refilled` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wallet_total_withdrawn` decimal(10,2) NOT NULL DEFAULT '0.00',
  `likes` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `time_created` datetime NOT NULL,
  `time_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `personaname`, `avatarfull`, `rights`, `wallet_balance`, `wallet_total_refilled`, `wallet_total_withdrawn`, `likes`, `time_created`, `time_updated`) VALUES
(1, 'admin@niceadminmail.pl', '$2y$10$Bp2gZAOuOv7U0BWyUy/qiOsDFy7KZ0D6B9i7OaOiaT7lPP5jxp7HK', 'AdminProGod', 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/96/967871835afdb29f131325125d4395d55386c07a_full.jpg', 0, '4.00', '104.00', '100.00', 902, '2019-07-26 01:53:54', '2020-05-06 13:09:14'),
(2, 'simpleuser@niceadminmail.pl', '$2y$10$vmlrunKDD/ESJFyufZYUlevSsEybdQn2RmqKl44QD1P.8Sc1RvN8m', 'simpleuser', 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/86/86a0c845038332896455a566a1f805660a13609b_full.jpg', 0, '25.00', '125.00', '100.00', 52, '2019-07-26 01:53:54', '2020-05-06 14:19:11');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `boosterpack`
--
ALTER TABLE `boosterpack`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `time_created` (`time_created`),
  ADD KEY `time_updated` (`time_updated`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `boosterpack`
--
ALTER TABLE `boosterpack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблиці `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблиці `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблиці `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
