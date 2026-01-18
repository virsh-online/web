-- Migration 1.2.0: Add author field to virsh table

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Add author field to virsh table if it doesn't exist
ALTER TABLE `virsh` ADD COLUMN IF NOT EXISTS `author` varchar(255) NOT NULL DEFAULT '' AFTER `title`;

COMMIT;
