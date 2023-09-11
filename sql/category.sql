
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Client'),
(2, 'Partner'),
(3, 'Supplier');