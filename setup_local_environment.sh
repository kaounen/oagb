#!/bin/bash

echo "==============================================="
echo "OAGB Local Environment Setup"
echo "==============================================="
echo ""
echo "This script will:"
echo "1. Create the local database with production credentials"
echo "2. Set up the database tables and sample data"
echo ""
echo "Prerequisites:"
echo "- MySQL/MariaDB must be running"
echo "- MySQL root access"
echo ""
read -p "Press Enter to continue..."

echo ""
echo "Step 1: Creating database and user..."
echo ""

# Run the SQL setup script
mysql -u root -p < setup_local_database.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to create database and user"
    echo "Make sure MySQL is running and you have root access"
    exit 1
fi

echo ""
echo "Step 2: Setting up tables and sample data..."
echo ""

# Run the PHP setup script
php setup_database.php

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to setup tables"
    echo "Check the PHP output above for details"
    exit 1
fi

echo ""
echo "Step 3: Testing connection..."
echo ""

# Test the connection
php test_mysqli.php

echo ""
echo "==============================================="
echo "Setup completed successfully!"
echo "==============================================="
echo ""
echo "Your local database now uses the same credentials as production:"
echo "- Database: korakund_ordem"
echo "- User: korakund_advogados"
echo "- Password: GV@R4ra&rI{4}"
echo ""
echo "You can now develop locally and deploy smoothly to production."
echo ""