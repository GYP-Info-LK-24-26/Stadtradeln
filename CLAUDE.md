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

GitHub Actions deploys via FTP on push to `main`. Requires `FTP_PASSWORD` secret.

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
- `src/Repository/` - Database access layer (User, Team, Tour, RateLimit, PasswordReset, RememberToken repositories)
- `src/Models/` - Data classes (User, Team, Tour)
- `src/Core/` - Framework: Router, Database (singleton mysqli), Session, View, Request, Csrf
- `templates/` - PHP templates; every page sets `$title` (and optionally `$layout` = `app`|`auth`|`landing`, `$scripts`, `$inlineScript`) and wraps its content in `require layout/header.php` … `require layout/footer.php`
- `public/css/app.css` - The only stylesheet: token-based design system (light/dark via `prefers-color-scheme`). No per-page `<style>` blocks; reuse its components (`.card`, `.stat`, `.btn-*`, `.field`/`.input`, `.alert-*`, `.rank-row`, `.dialog`, `.empty`, …)
- `public/fonts/` - Self-hosted Plus Jakarta Sans (variable woff2, OFL). Don't load fonts or other assets from external hosts (render blocking, GDPR, offline PWA)
- `public/js/` - `app.js` (shared UI behavior, see below), `dashboard.js` (tour dialog), `zxcvbn.js` (self-hosted, password strength), `password-strength.js` (meter UI)

**UI helpers**: `View::asset()` (cache-busted URLs), `View::number()` (German number format), `View::avatar()` (initials avatar), `Icon::svg('name')` (inline Lucide icons). `app.js` provides declarative behaviors: `data-dialog-open="id"` / `data-dialog-close` for native `<dialog>`s, `form[data-confirm]` for confirmation dialogs (instead of `confirm()`), `data-count-to` count-up numbers, `data-inline-edit` name editing, `data-menu` dropdowns, `data-password-toggle` show-password button (render via `partials/password-toggle.php` inside `.input-wrap`, right after the password input). Staggered entrance animations use the `.reveal` class with `style="--i: n"`. Keep the `<script>0</script>` after the stylesheet in `layout/header.php`: it makes Firefox wait for `app.css` before the first paint.

**Session**: 30-minute inactivity timeout. Use `Session::requireLogin()` to guard protected routes. `Session::isLoggedIn()` reloads name and team from the database once per request, so changes made on other devices apply immediately. „Angemeldet bleiben“ (`App\Core\RememberMe`): selector/validator cookie `remember` (30 days, HttpOnly, SameSite=Lax); the `remember_tokens` table stores only the SHA-256 hash of the validator. `Session::isLoggedIn()` restores an expired/missing session from the cookie and rotates the token. Logout deletes the device's token; password change/reset deletes all tokens of the user. Use `Session::getDisplayName()` to get the user's full name.

**User Model**: Users are identified by email (login) with a single `name` attribute for display. Use `$user->name` or `$user->getDisplayName()` to get the name.

**Event period**: `App\Core\Event` defines the campaign period (10.10.–31.10. of the current year). Tours can only be saved for event days up to today, with at most 10 tours and 300 km per user per day (`DashboardController::MAX_TOURS_PER_DAY` / `MAX_DISTANCE_PER_DAY`); all km totals (dashboard, team, leaderboard) only count tours within the period.

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

**CSRF**: Every POST form must contain `<?= Csrf::field() ?>` (hidden field with the per-session token from `App\Core\Csrf`) right after the opening `<form>` tag. The `Router` rejects every POST without a valid token with 403 and renders `pages/csrf-error` (link back to the form via the Referer path). Keep all state-changing routes POST-only.

**Rate Limiting**: `RateLimitRepository` tracks attempts per key in the `rate_limits` table: IP (`getClientIp()`, `REMOTE_ADDR` only — never trust `X-Forwarded-For`) or account (`userKey()`, `emailKey()`). Per-IP limits are deliberately generous because the whole school shares one public IP; per-account limits protect individual passwords. `record()` deletes rows older than `RETENTION_MINUTES` (60, the longest window); raise it when adding a longer window. Protected endpoints:
- `POST /login` — 100 failed attempts per IP and 10 per account (`emailKey()`) per 15 minutes (`login_failed`)
- `POST /register` — 100 attempts per IP per 60 minutes (`register`)
- `POST /forgot-password` — 50 attempts per IP per 60 minutes (`password_reset`); plus a 10-minute cooldown per account between reset emails
- `POST /settings`, `POST /settings/email` — 5 wrong current-password entries per **account** per 15 minutes, shared by both forms (`password_check`, key `RateLimitRepository::userKey()`)

**Password Strength**: Client-side only (no server-side length check; passwords are never trimmed), using self-hosted zxcvbn (`public/js/zxcvbn.js`). The meter is rendered server-side (static HTML in templates); `password-strength.js` updates it via a `data-score` attribute. Minimum required score: 2 ("Mäßig"). Applied on `/register`, `/reset-password`, and `/settings` (password change).

## Code Conventions

- Namespace: `App\` maps to `src/`
- German UI text and error messages
- HTML escaping: use `htmlspecialchars()`
- Request parameters: read via `Request::post()` / `Request::get()` (always strings, arrays become `''`), never `$_POST`/`$_GET` directly
- Absolute URLs (e.g. in emails): use `AuthController::APP_URL`, never `HTTP_HOST`
- Password hashing: use PHP's `password_hash()`
