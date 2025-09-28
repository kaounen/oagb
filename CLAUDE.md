# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is the website for the Ordem dos Advogados da Guiné-Bissau (OAGB - Guinea-Bissau Bar Association). It's a PHP-based web application that manages lawyer registrations, news, events, and provides public-facing content about the organization.

## Architecture

### Core Structure
- **Frontend**: Static HTML pages with PHP includes for dynamic content
- **Backend**: Plain PHP with PDO for database operations
- **Database**: MySQL with tables for lawyers, news, events, and configuration
- **Admin Panel**: CodeIgniter-based management system in `/gestaoCODIGNITER/`

### Key Directories
- `/` - Main website files (PHP pages and static assets)
- `/includes/` - Shared PHP functions and components
- `/gestaoCODIGNITER/` - Admin panel using CodeIgniter framework
- `/css/`, `/js/`, `/img/` - Frontend assets
- `/ajax/` - AJAX request handlers

### Database Configuration
- Database connection: `connect.php` (main) and `connectawf.php` (alternative)
- Database setup script: `setup_database.php`
- Main database: `korakund_ordem`
- Key tables: `advogados`, `noticias`, `agenda`, `advogados_estagiarios`

### Common File Patterns
- Page files: `*.php` (e.g., `noticias.php`, `evento.php`, `agenda.php`)
- Include files: `navbar_include.php`, `footer_include.php`
- Utility functions: `includes/functions.php`
- Database models: `*_model.php` files

## Development Commands

### Database Setup

**Complete Database Restoration (Recommended):**
```bash
# Windows (XAMPP)
restore_database.bat

# Linux/Mac
./restore_database.sh
```

**Manual Steps:**
```bash
# 1. Create database and user (run as MySQL root)
mysql -u root -p < setup_complete_database.sql

# 2. Import all data from SQL dump
mysql -u root -p korakund_ordem < korakund_ordem.sql

# 3. Test connection
php test_mysqli.php
```

### Testing Database Connection
```bash
# Test MySQL connection
php test_mysqli.php

# Test general connection
php test_connection.php
```

### Local Development
- Uses XAMPP environment
- Production credentials: 
  - Database: `korakund_ordem`
  - User: `korakund_advogados`
  - Password: `GV@R4ra&rI{4}`
- Use `add_database_user.sql` to set up local environment with same credentials as production

## Key Features

### Public Website
- Lawyer directory and search (`pesquisa-advogados.php`, `advogados_inscritos_php.php`)
- News management (`noticias.php`, `blog.php`)
- Event calendar (`agenda.php`, `evento.php`)
- About pages (`apresentacao-historia.php`, `comissoes-especializadas.php`)
- Contact forms (`contacto.php`, `solicitacao-advogados.php`)

### Admin Panel (CodeIgniter)
- Located in `/gestaoCODIGNITER/`
- Uses Grocery CRUD for data management
- Controllers: `Admin.php`, `Examples.php`, `Welcome.php`

### Utility Functions (`includes/functions.php`)
- Input sanitization: `clean_input()`
- Date formatting: `format_date_pt()`, `format_datetime()`
- Email sending: `send_email()`
- File uploads: `upload_file()`
- User authentication helpers
- Configuration management

## Database Schema

### Main Tables
- `advogados`: Licensed lawyers with registration details
- `advogados_estagiarios`: Law student trainees
- `noticias`: News articles with categories and content
- `agenda`: Events calendar
- `configuracoes_site`: Site configuration key-value pairs

### Common Patterns
- All tables use `created_at`/`updated_at` timestamps
- UTF-8 encoding with `utf8mb4_unicode_ci` collation
- Status fields typically use ENUM values
- Regional data includes Guinea-Bissau specific locations

## Security Notes
- Database credentials are stored in plain text in `connect.php`
- Password hashing uses PHP's `password_hash()` function
- Input sanitization via `htmlspecialchars()` and custom functions
- Security headers configured in `connect.php`

## Localization
- Portuguese language throughout
- Guinea-Bissau timezone: `Africa/Bissau`
- Local phone number validation for +245 country code
- Portuguese date formatting with month names