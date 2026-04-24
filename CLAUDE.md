# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Stadtradeln is a German-language PHP web application for tracking bicycle tours and team competitions. It uses a custom MVC architecture with no external dependencies (no Composer).

## Running Locally

```bash
# Set document root to public/ directory — index.php acts as router script
cd public/
php -S localhost:8000 index.php
```

Requires Apache mod_rewrite or equivalent URL rewriting for production.
For Apache, the vhost must set `DocumentRoot` to `public/` and `AllowOverride All`.

## Setup

1. Copy `config/database.example.php` to `config/database.php`
2. Fill in MySQL credentials
3. Run `database/schema.sql` to create tables

## Deployment

GitHub Actions deploys via FTP on release creation. Requires `FTP_PASSWORD` secret.

## Architecture

**Single Entry Point**: All requests route through `public/index.php` which contains the PSR-4 autoloader and route definitions.

**Request Flow**:
```
Request → Router → Controller → Repository → Database
                       ↓
              View::render() → Template → Response
```

**Key Directories**:
- `src/Controllers/` - Request handlers (Auth, Dashboard, Team, Leaderboard, Settings, Home)
- `src/Repository/` - Database access layer (User, Team, Tour, RateLimit, PasswordReset repositories)
- `src/Models/` - Data classes (User, Team, Tour)
- `src/Core/` - Framework: Router, Database (singleton mysqli), Session, View
- `templates/` - PHP templates with `layout/main.php` wrapper
- `public/css/` - Stylesheets (Forest Trail theme)
- `public/js/` - JavaScript: `zxcvbn.js` (self-hosted, password strength), `password-strength.js` (meter UI)

**Session**: 30-minute inactivity timeout. Use `Session::requireLogin()` to guard protected routes. Use `Session::getDisplayName()` to get the user's full name.

**User Model**: Users are identified by email (login) with a single `name` attribute for display. Use `$user->name` or `$user->getDisplayName()` to get the name.

**Database**: All queries must use prepared statements via `Database::getConnection()` (mysqli).

## Routes

Routes are defined in `public/index.php`. Main routes:
- `/` - Home
- `/login`, `/register`, `/logout` - Authentication
- `/dashboard` - User dashboard with tour management
- `/team`, `/team/join` - Team operations
- `/leaderboard` - Rankings
- `/settings` - User settings

## Security

**Rate Limiting**: `RateLimitRepository` tracks attempts by IP in the `rate_limits` table. Protected endpoints:
- `POST /login` — 10 failed attempts per IP per 15 minutes (`login_failed`)
- `POST /register` — 5 attempts per IP per 60 minutes (`register`)
- `POST /forgot-password` — 5 attempts per IP per 60 minutes (`password_reset`)
- `POST /settings/email` — 5 attempts per IP per 60 minutes (`email_change`)

**Password Strength**: Client-side only, using self-hosted zxcvbn (`public/js/zxcvbn.js`). The meter is rendered server-side (static HTML in templates); `password-strength.js` updates it via a `data-score` attribute. Minimum required score: 2 ("Mäßig"). Applied on `/register`, `/reset-password`, and `/settings` (password change).

## Code Conventions

- Namespace: `App\` maps to `src/`
- German UI text and error messages
- HTML escaping: use `htmlspecialchars()`
- Password hashing: use PHP's `password_hash()`
