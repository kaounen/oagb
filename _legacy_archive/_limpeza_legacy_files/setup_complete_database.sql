-- Complete database setup script for OAGB
-- This script creates the database, user, and restores all data
-- Run this script as MySQL root user

-- Create database
CREATE DATABASE IF NOT EXISTS `korakund_ordem` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create user (drop if exists to avoid conflicts)
DROP USER IF EXISTS 'korakund_advogados'@'localhost';
CREATE USER 'korakund_advogados'@'localhost' IDENTIFIED BY 'GV@R4ra&rI{4';

-- Grant all privileges on the database
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'localhost';

-- Also create user for 127.0.0.1 (some applications use this instead of localhost)
DROP USER IF EXISTS 'korakund_advogados'@'127.0.0.1';
CREATE USER 'korakund_advogados'@'127.0.0.1' IDENTIFIED BY 'GV@R4ra&rI{4';
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'127.0.0.1';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Use the database
USE `korakund_ordem`;

-- Show confirmation
SELECT 'Database and user created successfully! Now import korakund_ordem.sql' as status;