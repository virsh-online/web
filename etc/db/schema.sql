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
  `virsh` text NOT NULL,
  `illustration` varchar(256) NOT NULL,
  `youtube` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `virsh`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `virsh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
