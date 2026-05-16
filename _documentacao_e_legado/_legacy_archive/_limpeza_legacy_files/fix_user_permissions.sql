-- Complete fix for korakund_advogados user permissions
-- Run this in phpMyAdmin SQL tab as root user

-- First, let's see what users exist
SELECT User, Host FROM mysql.user WHERE User LIKE 'korakund%';

-- Drop existing users to clean up
DROP USER IF EXISTS 'korakund_advogados'@'localhost';
DROP USER IF EXISTS 'korakund_advogados'@'127.0.0.1';
DROP USER IF EXISTS 'korakund_advogados'@'%';

-- Create fresh user with correct permissions
CREATE USER 'korakund_advogados'@'localhost' IDENTIFIED BY 'GV@R4ra&rI{4}';
CREATE USER 'korakund_advogados'@'127.0.0.1' IDENTIFIED BY 'GV@R4ra&rI{4}';

-- Grant all privileges on the database
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'localhost' WITH GRANT OPTION;
GRANT ALL PRIVILEGES ON `korakund_ordem`.* TO 'korakund_advogados'@'127.0.0.1' WITH GRANT OPTION;

-- Grant specific privileges that might be needed
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, 
      CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, 
      SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER, REFERENCES 
      ON `korakund_ordem`.* TO 'korakund_advogados'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, 
      CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, 
      SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER, REFERENCES 
      ON `korakund_ordem`.* TO 'korakund_advogados'@'127.0.0.1';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Verify the grants
SHOW GRANTS FOR 'korakund_advogados'@'localhost';

-- Test the connection
SELECT 'User permissions fixed successfully!' as status;