# OAGB Website Constitution

## Core Principles

### I. Mobile-First Development
Development via Claude Code web on mobile; All changes must be small, focused commits; Clear documentation for each change; Work in feature branches (e.g., 002-padronizar-header-mobile)

### II. Component-Based Structure
Reusable PHP includes (navbar, footer, etc.); Consistent styling across all pages; Maintain desktop and mobile compatibility; Follow Bootstrap 5 framework

### III. Security First (NON-NEGOTIABLE)
No API keys or secrets in code; Use .gitignore for sensitive files; Environment variables for configuration; Regular security audits

### IV. Documentation Required
Every change documented in MD files; State saved in ESTADO_ACTUAL_SESSAO.md; Fix summaries (e.g., NAVBAR_MOBILE_FIX_SUMMARY.md); Code comments for complex logic

### V. Testing & Quality
Test on both desktop and mobile; Verify navbar scroll effects; Check carousel transitions; Validate responsive design

## Technology Stack

**Backend**: PHP 7.4+ with PDO/MySQL
**Frontend**: Bootstrap 5, jQuery, Font Awesome, Lineicons
**Database**: MySQL (korakund_ordem)
**Fonts**: Open Sans, Libre Baskerville
**Version Control**: Git + GitHub (kaounen/oagb)

## File Structure

```
/                   - Main PHP pages (index.php, noticias.php, etc.)
/includes/          - Reusable components (navbar, footer, functions)
/css/               - Stylesheets
/js/                - JavaScript files
/img/               - Images and assets
/gestao/            - Admin panel (CodeIgniter)
/ajax/              - AJAX handlers
*.md                - Documentation files
```

## Development Workflow

1. **Branch Naming**: `00X-descriptive-name` (e.g., 002-padronizar-header-mobile)
2. **Commits**: Small, descriptive, in Portuguese or English
3. **Documentation**: Update ESTADO_ACTUAL_SESSAO.md after significant changes
4. **Push**: Always to feature branch first, then PR to master
5. **Testing**: Test locally before push, verify on mobile when possible

## Key Features

### Desktop
- Fixed navbar with scroll effect (logo 70% → 50%)
- Topbar changes white → golden on scroll
- Smooth carousel transitions
- Facts cards overlapping slider

### Mobile  
- Centered logo with menu button below
- Expandable dark menu
- Carousel with overlay content
- Responsive design (Bootstrap breakpoint: 991.98px)

## Governance

This constitution guides all development on OAGB website.
All commits must respect these principles.
Use feature branches for all changes.
Document everything for mobile development continuity.

**Version**: 1.0.0 | **Ratified**: 2025-01-15 | **Last Amended**: 2025-01-15
