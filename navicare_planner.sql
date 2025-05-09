
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_time` varchar(50) DEFAULT NULL,
  -- `user_id` int(11) DEFAULT NULL, -- Optional: if you implement user accounts
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_date_idx` (`event_date`) -- Index for faster date lookups
  -- KEY `user_id_idx` (`user_id`) -- Optional index
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;