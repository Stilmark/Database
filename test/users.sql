
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Hans', 'Gruber', 'hans@gruber.com', 'client', NULL, NULL),
(2, 'Lars', 'Ulrich', 'lars@metal.com', 'partner', '2020-12-24 00:00:00', NULL),
(3, 'Hans', 'Nickerdorph', 'hans@nicksport.com', 'client', '2020-12-28 00:00:00', NULL),
(4, 'Henry', 'Bakerstreet', 'wonder@tree.com', NULL, '2022-11-14 20:43:35', '2022-11-14 21:32:51');

UPDATE users SET password = SHA1(CONCAT(id,email,'salt'));
ALTER TABLE users CHANGE COLUMN password password VARCHAR(40) INVISIBLE;
