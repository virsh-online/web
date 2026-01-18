SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT 1,
  `fullname` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `virsh` (
  `id` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `virsh` text NOT NULL,
  `illustration` varchar(256) NOT NULL DEFAULT '',
  `illustration_enabled` tinyint(4) NOT NULL DEFAULT 1,
  `youtube` varchar(255) NOT NULL DEFAULT '',
  `youtube_enabled` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `url` varchar(512) NOT NULL,
  `icon` varchar(100) NOT NULL DEFAULT '',
  `enabled` tinyint(4) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `virsh`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_enabled` (`enabled`),
  ADD KEY `idx_sort_order` (`sort_order`);


ALTER TABLE `admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `virsh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
