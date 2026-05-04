#!/bin/bash

echo "==============================================="
echo "OAGB Database Restoration"
echo "==============================================="
echo ""
echo "This script will:"
echo "1. Create the database 'korakund_ordem'"
echo "2. Create user 'korakund_advogados' with production credentials"
echo "3. Import all tables and data from korakund_ordem.sql"
echo ""
echo "Prerequisites:"
echo "- MySQL/MariaDB must be running"
echo "- You need MySQL root access"
echo "- korakund_ordem.sql file must be in this directory"
echo ""
read -p "Press Enter to continue..."

echo ""
echo "Step 1: Creating database and user..."
echo ""

# Create database and user
mysql -u root -p < setup_complete_database.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to create database and user"
    echo "Make sure MySQL is running and you have root access"
    exit 1
fi

echo ""
echo "Step 2: Importing database structure and data..."
echo ""

# Import the complete database dump
mysql -u root -p korakund_ordem < korakund_ordem.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to import database"
    echo "Make sure korakund_ordem.sql exists in this directory"
    exit 1
fi

echo ""
echo "Step 3: Testing connection with new credentials..."
echo ""

# Test the connection
php test_mysqli.php

echo ""
echo "==============================================="
echo "Database restoration completed!"
echo "==============================================="
echo ""
echo "Database Details:"
echo "- Name: korakund_ordem"
echo "- User: korakund_advogados"
echo "- Password: GV@R4ra&rI{4}"
echo "- Host: localhost"
echo ""
echo "Your local environment now matches production credentials."
echo "You can now run the website locally and deploy smoothly."
echo ""