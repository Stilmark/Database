DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `category_id` tinyint DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL /*!80023 INVISIBLE */,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `firstName`, `lastName`, `email`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Hans', 'Gruber', 'hans@gruber.com', 1, NULL, NULL, NULL),
(2, 'Lars', 'Ulrich', 'lars@metal.com', 2, '2020-12-24 00:00:00', NULL, '2023-09-10 16:37:53'),
(3, 'Hans', 'Nickerdorph', 'hans@nicksport.com', 1, '2020-12-28 00:00:00', NULL, NULL),
(4, 'Henry', 'Bakerstreet', 'wonder@tree.com', 3, '2022-11-14 20:43:35', '2022-11-14 21:32:51', NULL);

UPDATE users SET password = SHA1(CONCAT(id,email,'salt'));
ALTER TABLE users CHANGE COLUMN password password VARCHAR(40) INVISIBLE;
