-- Setup script for OAGB local database with production credentials
-- Run this script as MySQL root user to create database and user with same credentials as production

-- Create database
CREATE DATABASE IF NOT EXISTS `korakund_ordem` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create user (drop if exists to avoid conflicts)
DROP USER IF EXISTS 'korakund_advogados'@'localhost';
CREATE USER 'korakund_advogados'@'localhost' IDENTIFIED BY 'GV@R4ra&rI{4';

-- Grant all privileges on the database
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'localhost';

-- Also grant privileges for 127.0.0.1 (some applications use this instead of localhost)
DROP USER IF EXISTS 'korakund_advogados'@'127.0.0.1';
CREATE USER 'korakund_advogados'@'127.0.0.1' IDENTIFIED BY 'GV@R4ra&rI{4';
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'127.0.0.1';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Use the database
USE `korakund_ordem`;

-- Show confirmation
SELECT 'Database korakund_ordem created successfully!' as status;
SELECT 'User korakund_advogados created with full privileges!' as status;