@echo off
echo ===============================================
echo OAGB Local Environment Setup
echo ===============================================
echo.
echo This script will:
echo 1. Create the local database with production credentials
echo 2. Set up the database tables and sample data
echo.
echo Prerequisites:
echo - XAMPP must be running (MySQL service)
echo - MySQL root access (usually no password in XAMPP)
echo.
pause

echo.
echo Step 1: Creating database and user...
echo.

REM Run the SQL setup script
mysql -u root -p < setup_local_database.sql

if %errorlevel% neq 0 (
    echo ERROR: Failed to create database and user
    echo Make sure MySQL is running and you have root access
    pause
    exit /b 1
)

echo.
echo Step 2: Setting up tables and sample data...
echo.

REM Run the PHP setup script
php setup_database.php

if %errorlevel% neq 0 (
    echo ERROR: Failed to setup tables
    echo Check the PHP output above for details
    pause
    exit /b 1
)

echo.
echo Step 3: Testing connection...
echo.

REM Test the connection
php test_mysqli.php

echo.
echo ===============================================
echo Setup completed successfully!
echo ===============================================
echo.
echo Your local database now uses the same credentials as production:
echo - Database: korakund_ordem
echo - User: korakund_advogados
echo - Password: GV@R4ra&rI{4}
echo.
echo You can now develop locally and deploy smoothly to production.
echo.
pause