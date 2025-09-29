-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 14. Apr 2025 um 02:52
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `marketplace`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `brief1` varchar(255) DEFAULT NULL,
  `brief2` varchar(255) DEFAULT NULL,
  `brief3` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','purchased','shipped','delivered') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `ads`
--

INSERT INTO `ads` (`id`, `user_id`, `category_id`, `title`, `brief1`, `brief2`, `brief3`, `description`, `price`, `photo`, `created_at`, `status`) VALUES
(2, 5, 3, 'Iphone 11', 'With minor cosmetic wear', 'Battery 80% condition', 'Comes with the original box and charger', 'Selling my trusty iPhone 11! This phone has been a great companion, and I\'ve taken excellent care of it. It\'s perfect for anyone looking for a reliable and stylish smartphone. The iPhone 11 features the powerful A13 Bionic chip, a fantastic dual-camera system, and a vibrant Liquid Retina display. 1  Everything works perfectly, and there are only a few minor cosmetic blemishes, which I\'ve tried to capture in the photos. It comes with the original box and charger. Feel free to ask any questions or request more photos. Don\'t miss out on this fantastic deal!', 300.00, '1743710416_offer-image-2.png', '2025-03-19 11:29:37', 'active'),
(3, 5, 4, 'Vostro 7620', '16 GB RAM DDR4', 'Proccesor: Intel i7 12700k', 'Videocard: RTX 3050ti', 'Selling my Dell Vostro 7620, a powerful and reliable laptop perfect for work, study, and entertainment. This laptop is equipped with [specify processor, RAM, storage, and graphics card], delivering high performance and speed. I\'ve taken excellent care of it, and it\'s in great condition. The screen is free of scratches, and the keyboard and touchpad work like new. It comes with the original charger. I\'m happy to answer any questions and provide additional photos upon request. Don\'t miss the opportunity to get this dependable laptop at a great price!', 841.00, 'offer-image-3.png', '2025-03-25 08:05:08', 'active'),
(4, 5, 3, 'Samsung S23 Ultra', 'The phone is in excellent condition', 'Work perfectly', 'It comes with the original box and charger', 'Hi everyone! I\'m selling my Samsung Galaxy S23, in excellent condition. This phone is a true flagship, boasting an amazing camera, powerful processor, and vibrant display. It\'s perfect for anyone who appreciates quality and performance. I\'ve always taken great care of my phone, and it looks like new. There are no scratches or scuffs on the screen. All functions work perfectly. It comes with the original box and charger. I\'m happy to answer any questions you may have and provide additional photos upon request. Don\'t hesitate to reach out if you\'re interested!', 1050.00, 'offer-image-4.png', '2025-03-31 10:24:56', 'active'),
(5, 4, 1, 'Apple iPad 10', 'Minor cosmetic wear', '32 GB SSD', 'Pink color', 'Hello everyone! I\'m selling my beloved 10th generation iPad in a beautiful pink color. This iPad has been very well taken care of, and I hope it finds a new owner who will love it as much as I do. It\'s perfect for watching movies, playing games, working, and much more. iPadOS runs smoothly, and all functions work perfectly. There are a few minor cosmetic blemishes, which I have tried to show in the photos. I am ready to answer any of your questions and provide additional photos upon request. Feel free to contact me if you are interested!', 550.00, 'offer-image-5.png', '2025-04-03 12:05:26', 'active'),
(6, 3, 2, 'Dell OptiPlex 7040 Office PC', 'Processor: Intel Core i5-6500', 'RAM: 8GB DDR4', 'Storage: 256GB SSD', 'This is a solid, working PC, ideal for office tasks, general use, or studying. It\'s been used in an office environment, so it has some minor cosmetic wear, as expected for used equipment. The fans function, but they might be a bit louder under heavier loads. The system runs smoothly. Clean installation of Windows 10. All ports are working correctly.\r\n\r\nI\'m selling because we\'ve upgraded our office computers. It\'s a great value for the price! Price: 150 euros. Negotiable.', 150.00, 'offer-image-6.png', '2025-04-12 19:56:55', 'active'),
(7, 7, 3, 'Google Pixel 3', '4GB RAM', '64GB ROM', 'Pure Android operating system', 'The phone is in good condition, with some minor scratches on the body. The screen is in excellent shape. The battery holds a decent charge, lasting through a normal day.\r\n\r\nI\'m selling because I\'ve upgraded to a newer model. This Pixel 3 has served me well!\r\n\r\nIncludes: Phone and USB-C cable.\r\n\r\nPrice: 200 euros. Slight negotiation possible.', 200.00, 'offer-image-7.png', '2025-04-12 20:10:36', 'active'),
(8, 4, 1, 'Samsung Galaxy Tab S6 Lite', 'Included S Pen', 'Vivid display', 'All-day battery life', 'This Samsung Galaxy Tab S6 Lite is an excellent tool for study, work, and entertainment. The included S Pen allows for drawing, note-taking, document editing, and more. The tablet features a bright display, perfect for video viewing, reading, and graphic tasks. The Exynos 9611 processor ensures smooth performance, and the 4GB of RAM enables multitasking. The 64GB of storage provides ample space for your files. The tablet is in excellent condition and has been well-maintained. I\'m selling because I\'ve upgraded to a newer model.', 210.00, 'offer-image-8.png', '2025-04-13 09:49:06', 'active'),
(9, 7, 4, 'Dell XPS 13 9360', 'Intel Core i7-8550U', '16 GB RAM', '512GB SSD', 'This Dell XPS 13 9360 is a premium laptop that combines elegant design, high performance, and portability. The InfinityEdge display lets you enjoy vibrant and sharp visuals, while the thin and light body makes it ideal for working on the go. The Intel Core i7-8550U processor and 16GB of RAM ensure smooth performance even with the most demanding applications. The 512GB SSD provides fast boot times and application loading. The laptop is in excellent condition and has been well cared for. I\'m selling because I\'ve upgraded to a newer model.', 350.00, 'offer-image-9.png', '2025-04-13 10:36:21', 'active'),
(10, 6, 2, 'Lenovo ThinkCentre M910q', '8GB DDR4', '256GB SSD', 'Intel Core i5-7500T', 'This Lenovo ThinkCentre M910q is an excellent choice for those who value compactness and performance. Despite its small size, this mini-PC can handle most everyday tasks with ease. The Intel Core i5-7500T processor and 8GB of RAM ensure smooth operation, and the fast 256GB SSD provides quick system and application loading.\r\n\r\nThe PC is in good working condition. There\'s a minor quirk: occasionally, the fan might be a little noisy upon startup, but it doesn\'t affect performance at all.\r\n\r\nI\'m selling because I\'ve upgraded my PC setup. Great value for the price!', 150.00, 'offer-image-10.png', '2025-04-13 10:46:27', 'active'),
(11, 6, 4, 'MacBook Air 13\" (2015)', '8 GB RAM', '128 GB SSD', 'Long battery life', 'his MacBook Air 13\" (2015) is a reliable companion for work and entertainment. The Intel Core i5, 8GB of RAM, and SSD ensure fast and smooth performance.\r\n\r\nImportant: the laptop has a display issue. Sometimes, after turning it on, the screen might flicker or show distorted colors for the first few minutes. After warming up, the problem disappears, and the laptop works stably. This is likely related to the display cable and may require repair.\r\n\r\nThe laptop is otherwise in good condition, with no damage to the body. I\'m selling it at a low price due to the mentioned defect.\r\n\r\nPrice: 120 euros. Negotiable', 120.00, 'offer-image-11.png', '2025-04-13 10:54:03', 'active'),
(12, 6, 2, 'HP EliteDesk 800 G2', '16GB DDR4', '512GB SSD', 'Intel Core i7-6700', 'This HP EliteDesk 800 G2 is a robust PC that can handle any task you throw at it. The Intel Core i7-6700 processor and 16GB of RAM ensure smooth performance, even with the most demanding applications. The 512GB SSD allows for quick system and application boot times.\r\n\r\nA minor quirk: sometimes when running heavy applications, there may be a slight fan noise.\r\n\r\nThe PC is in excellent working condition, used for professional tasks. I\'m selling it because I\'ve upgraded my PC setup.', 250.00, 'offer-image-12.png', '2025-04-13 11:07:06', 'active'),
(13, 3, 3, 'Samsung Galaxy S9', 'Exynos 9810', '4GB RAM', '64GB ROM', 'This Samsung Galaxy S9 is a good option for those seeking a compact and functional smartphone. Exynos 9810, 4GB RAM, and 64GB ROM.\r\n\r\nImportant: the phone has a display problem. Sometimes, especially after prolonged use, the screen may exhibit lines or flickering. This is likely a display cable issue and will require repair.\r\n\r\nThe phone is otherwise in good condition. Selling at a low price due to the defect.', 145.00, 'offer-image-13.png', '2025-04-13 11:16:22', 'active'),
(14, 3, 4, 'Lenovo ThinkPad T460', 'Intel Core i5-6300U', '8 GB DDR4', '256GB SSD', 'This Lenovo ThinkPad T460 is an excellent choice for anyone needing a dependable and productive laptop for travel or remote work. It features an Intel Core i5-6300U processor, 8GB of RAM, and a fast 256GB SSD, allowing it to handle most everyday tasks with ease.\r\n\r\nThere\'s a minor issue: occasionally, the touchpad may not respond correctly to touch input. It might need cleaning or replacement.\r\n\r\nThe laptop is in good condition overall and has been well-maintained. I\'m selling it because I\'ve upgraded to a newer model.', 195.00, 'offer-image-14.png', '2025-04-13 12:47:09', 'active'),
(15, 4, 1, 'Samsung Galaxy Tab S9', 'Snapdragon 8 Gen 2', '128 GB SSD', 'Included S Pen', 'This Samsung Galaxy Tab S9 is a premium tablet that combines elegant design, high performance, and functionality. The vibrant AMOLED display allows you to enjoy high-quality images, and the powerful Snapdragon 8 Gen 2 processor ensures smooth performance even with the most demanding applications. The included S Pen opens up a wide range of possibilities for creativity and productivity.\r\n\r\nThere\'s a minor quirk: sometimes when connecting to certain Wi-Fi networks, unstable connections can occur, possibly requiring a firmware update or router configuration.\r\n\r\nThe tablet is in excellent condition and has been well cared for. I\'m selling it because I\'ve upgraded to the Ultra model', 600.00, 'offer-image-15.png', '2025-04-13 12:54:20', 'active'),
(16, 7, 1, 'Apple iPad Pro 11', 'Liquid Retina Display', 'A12Z Bionic Processor', 'Apple Pencil and Magic Keyboard', 'This Apple iPad Pro 11 is a premium tablet that combines elegant design, high performance, and functionality. The Liquid Retina display lets you enjoy high-quality images, and the powerful A12Z Bionic processor ensures smooth performance even with the most demanding applications. Support for Apple Pencil and Magic Keyboard opens up a wide range of possibilities for creativity and productivity.\r\n\r\nThere\'s a minor quirk: Occasionally, when connecting to certain Bluetooth devices, there can be unstable connections.\r\n\r\nThe tablet is in excellent condition and has been well cared for. I\'m selling it because I\'ve upgraded to a larger model.', 560.00, 'offer-image-16.png', '2025-04-13 13:22:53', 'active');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` enum('tablet','pc','phone','laptop') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'tablet'),
(2, 'pc'),
(3, 'phone'),
(4, 'laptop');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `trade_requests`
--

CREATE TABLE `trade_requests` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` int(11) NOT NULL DEFAULT 2000,
  `registered_at` datetime DEFAULT current_timestamp(),
  `role` varchar(50) DEFAULT 'user',
  `gender` enum('male','female','other') NOT NULL,
  `birth_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `balance`, `registered_at`, `role`, `gender`, `birth_date`) VALUES
(3, 'Alice', 'alice23dvs@gmail.com', '$2y$10$zpnAh6qcOSH/mKgvAiqFqekTn7BRRezpXZTNTeSlLwUPxgzCy5/XK', 0, '2025-03-11 09:08:12', 'user', 'female', '2004-01-12'),
(4, 'Charley', 'charleyvdsv2@gmail.com', '$2y$10$rRctY9F23l/0fPpoqID1a.LYsPQY2DuRNfUypGyOvpFLnhX.rqMY.', 1291, '2025-03-14 13:18:02', 'user', 'male', '1993-06-17'),
(5, 'Thomas', 'thomas43f43@gmail.com', '$2y$10$V.GEyhhmE4mT/sJPh7d/ru/AL3Bo2IrK.fKFOP2c4QAydTOUSidTm', 4202, '2025-03-18 09:02:25', 'user', 'male', '2007-02-08'),
(6, 'Mark', 'mark4324d@gmail.com', '$2y$10$86./geL08vmi6BCw3Mtrpe7LOcVNh64sBi1iu3YG8taj8tgb4wAQe', 1041, '2025-04-03 13:57:19', 'user', 'male', '2004-06-26'),
(7, 'Gwen', 'gwen32fw5s@gmail.com', '$2y$10$OUuqvV3OfBhSM6o5tf7.ouw4tORheqKAa/Bifk5OGjEkDX/SqcpLW', 0, '2025-04-02 15:11:20', 'user', 'female', '1998-07-23');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `buyer_id` (`buyer_id`,`seller_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indizes für die Tabelle `trade_requests`
--
ALTER TABLE `trade_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indizes für die Tabelle `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT für Tabelle `trade_requests`
--
ALTER TABLE `trade_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ads_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `trade_requests`
--
ALTER TABLE `trade_requests`
  ADD CONSTRAINT `trade_requests_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trade_requests_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trade_requests_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `ads` (`id`);

--
-- Constraints der Tabelle `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
