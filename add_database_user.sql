-- Add production user credentials to existing korakund_ordem database
-- Run this script as MySQL root user

-- Create user (drop if exists to avoid conflicts)
DROP USER IF EXISTS 'korakund_advogados'@'localhost';
CREATE USER 'korakund_advogados'@'localhost' IDENTIFIED BY 'GV@R4ra&rI{4';

-- Grant all privileges on the existing database
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'localhost';

-- Also grant privileges for 127.0.0.1 (some applications use this instead of localhost)
DROP USER IF EXISTS 'korakund_advogados'@'127.0.0.1';
CREATE USER 'korakund_advogados'@'127.0.0.1' IDENTIFIED BY 'GV@R4ra&rI{4';
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'127.0.0.1';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Show confirmation
SELECT 'User korakund_advogados created with full privileges on korakund_ordem!' as status;