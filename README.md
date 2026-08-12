<div align="center">

# Tournament management system

**Full-Stack Tournament Management System**  
*Built with Pure PHP — No Framework, No Bloat*

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-7B3BE6?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/license-MIT-ff00aa?style=flat)](LICENSE)
[![CI](https://github.com/loucass/Tournament-management-system/actions/workflows/ci.yml/badge.svg)](https://github.com/loucass/Tournament-management-system/actions/workflows/ci.yml)

</div>

---

## 🎯 Overview

**Tournament management system** is a production-grade, custom-built PHP MVC application that digitizes the entire lifecycle of school and college sports tournaments. From team formation and competition registration to real-time scoring and leaderboards — it replaces spreadsheets, paper forms, and manual tracking with an automated, role-based platform.

> **Why this matters:** Most student projects lean on Laravel or Symfony. This one was built from scratch — no framework, no ORM, no magic. Every layer was deliberately engineered: a custom MVC kernel, a PSR-4 autoloader, a service-oriented architecture with dedicated Config and Database services, a CLI console with migration and seeding commands, and a pagination engine. It demonstrates deep understanding of MVC architecture, database design, authentication flows, and security engineering without training wheels.

---

## ✨ Features

### 👑 Role-Based Access
| Role | Capabilities |
|------|-------------|
| **Admin** | Create competitions, add students, form teams, award points, manage everything |
| **Student** | Register individually, join competitions, track results, form teams |
| **Team** | Log in as a group entity, compete in team-based events, view rankings |

### 🏆 Core Functionality
- **Competition Engine** — Create individual or team-based challenges with configurable capacity limits
- **Team Builder** — Assemble squads from registered students with atomic transactions and duplicate prevention
- **Live Scoring** — Real-time point updates via AJAX with instant leaderboard recalculation
- **Leaderboard** — Dynamic golden-square top-4 display with paginated ranking table
- **Application Policy** — Automated enforcement of competition limits per participant

### 🛡️ Security
| Layer | Protection |
|-------|-----------|
| **Passwords** | BCrypt hashing via `password_hash()` / `password_verify()` |
| **CSRF** | Per-session tokens checked on every state-changing request via `hash_equals()` |
| **SQL Injection** | Prepared statements everywhere — zero string interpolation in queries |
| **XSS** | `htmlspecialchars()` with `ENT_QUOTES` on all user-generated output |
| **Session** | `session_regenerate_id()` after login, token rotation, proper logout destruction |
| **Headers** | X-Frame-Options, X-Content-Type-Options, XSS-Protection, Referrer-Policy, Permissions-Policy |
| **CORS** | Restrictive `Access-Control-Allow-Origin` with credential support |

---

## 🏗️ Architecture

### Custom MVC Framework (Zero Dependencies)

```
Tournament-management-system/
├── app/
│   ├── App.php                  # Application bootstrap, CSRF, cookie helpers
│   ├── Router.php               # Request router (GET/POST) with 404 handling
│   ├── View.php                 # Template renderer with parameter extraction
│   ├── Paginator.php            # Pagination helper (LIMIT/OFFSET + metadata)
│   ├── Services/
│   │   ├── Config.php           # Environment config loader (.env + env vars)
│   │   └── Database.php         # PDO singleton connection manager
│   ├── Commands/
│   │   ├── MigrateCommand.php   # Schema migrator with smart DB detection
│   │   └── SeedCommand.php      # Admin + demo data seeder (interactive)
│   ├── Controllers/             # 13 action controllers
│   │   ├── authenticateController.php  # Token-based auth middleware
│   │   ├── ApplyingPolicyController.php # Competition limit enforcement
│   │   ├── SignUpController.php        # Public self-registration
│   │   └── ...                  # CRUD controllers for all entities
│   └── Exceptions/              # Custom exception classes
├── database/
│   └── schema.sql               # Full relational schema (7 tables)
├── public/
│   ├── index.php                # Front controller (single entry point)
│   ├── .htaccess                # Apache URL rewriting
│   └── assets/css/app.css       # Synthwave Sunset design system
├── views/                       # 10 PHP template files
│   ├── log in.php               # Login with auto-role detection
│   ├── sign in.php              # Student self-registration
│   ├── home.php                 # Role-aware dashboard
│   ├── competition dashboard.php # Live leaderboard with AJAX scoring
│   └── ...
├── console                      # CLI entry point (php console)
├── storage/                     # Writable storage
├── .env                         # Environment configuration
└── composer.json                # PSR-4 autoloading
```

### Database Schema (Normalized Relational)

```
users                      → Unified: students + admins (role: admin|student, teamID)
teams                      → Group entities with independent login
competitions               → Individual and team challenges
tokens                     → Session-bound authentication tokens
competitions_applications  → Many-to-many: participants → competitions
competitions_points        → Scoring records with per-participant points
```

### Request Lifecycle

```
Browser ──GET/POST──→ public/index.php (Front Controller)
                            │
                            ▼
                     Router::resolve()
                            │
                            ▼
                     Controller::action()
                            │
                            ├── authenticateController::verify()  [auth check]
                            │
                            ▼
                     Database::connect()->prepare(...) → MySQL
                            │
                            ▼
                     View::make() → PHP template rendering
                            │
                            ▼
                     Response (HTML / JSON)
```

Each POST request is also intercepted by `App::verify_csrf()` before routing, and all controllers use `Database::connect()` — a singleton PDO managed by the dedicated `Database` service.

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 8.0 / MariaDB 10.3+
- Composer

### Installation

```bash
# 1. Clone
git clone https://github.com/loucass/Tournament-management-system.git
cd Tournament-management-system

# 2. Install dependencies (generates PSR-4 autoloader)
composer install --no-dev

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 4. Run migrations (creates database + tables)
php console migrate

# The CLI will:
#   • Connect to MySQL using credentials from .env
#   • Check if the database already exists
#   • If it does → ask whether to drop & recreate (all data is lost)
#   • If it doesn't → create it automatically
#   • Execute all CREATE TABLE statements

# 5. Create an admin account
php console seed:admin

# Interactive mode (default):
#   Enter admin email [admin@tournament.local]:
#   Enter admin password [admin123]:
#   Enter admin name [Admin]:
#
# Non-interactive (CI / Docker):
#   ADMIN_EMAIL=admin@school.com ADMIN_PASSWORD=securepass php console seed:admin

# 6. (Optional) Seed demo data
php console seed:demo
# Creates: 5 competitions, 5 students (demo123), 1 team (team123)

# 7. Start the development server
composer serve
# Or: php -S localhost:8080 -t public/
```

---

## 🎮 CLI Console

The project includes a full-featured CLI console at the project root:

```
php console help

  ⚡ TOURNAMENT CONSOLE
  ─────────────────────────────
  php console migrate            Run database migrations
  php console seed:admin         Create the admin user
  php console seed:demo          Seed demo data
  php console help               Show this help
```

### `php console migrate`

Handles database creation intelligently:

1. Connects to MySQL using credentials from `.env` (no database selected)
2. Checks `INFORMATION_SCHEMA` to see if the database already exists
3. **Database exists** → prompts: *"Drop and recreate? All data will be lost! [y/N]"*  
   - `y` → drops and recreates, then runs schema
   - `N` → runs schema against existing database (safe for updates)
4. **Database doesn't exist** → creates it automatically, then runs schema
5. Executes all `CREATE TABLE` statements from `database/schema.sql`

### `php console seed:admin`

Creates the admin user. Works in two modes:

| Mode | Trigger | Behavior |
|------|---------|----------|
| **Interactive** | Run in a terminal (TTY) | Prompts for email, password, name |
| **Non-interactive** | Run in CI / Docker | Reads `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME` env vars |

### `php console seed:demo`

Seeds a complete demo environment:
- 5 competitions (3 individual, 2 team)
- 5 sample students (password: `demo123`)
- 1 team (Squad Alpha — `squad@demo.local` / `team123`)

---

## 🔧 Development

```bash
# Built-in server
php -S localhost:8080 -t public/

# Or via Composer
composer serve
```

### Testing & CI

| Gate | What runs |
|------|-----------|
| **Lint & analysis** | `php -l` syntax check on every file + PHPStan level 5 |
| **Unit tests** | Router, Config, Paginator, View — no database needed (PHP 8.2/8.3/8.4) |
| **Integration tests** | Real MySQL in a service container: migrations, seeding, auth tokens, application limits, scoring, team building |
| **Smoke test** | Production install (`--no-dev`), boot the server, probe routes for 200/404 |

Run the same checks locally:

```bash
composer install          # dev dependencies (phpunit, phpstan)
composer check            # lint:syntax + analyse + test
composer test:unit        # unit tests only (no DB required)
composer test:integration # integration tests (requires local MySQL)
```

Integration tests build an isolated database (`task_2_test` by default) and
skip cleanly when MySQL is unavailable. Configure your local DB with
environment variables:

```bash
DB_HOST=127.0.0.1 DB_USER=root DB_PASS=secret composer test:integration
```

### Production Deployment

Configure Apache/Nginx to serve from `public/` directory:
- Set document root to `/path/to/Tournament-management-system/public`
- Apache: ensure `mod_rewrite` is enabled + `AllowOverride All`
- Nginx: route all non-file requests to `index.php`
- Set environment variables for database credentials (override `.env`)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Runtime** | PHP 8.0+ (strict types, constructor promotion, named arguments) |
| **Database** | MySQL 8.0 / MariaDB 10.3+ (PDO prepared statements) |
| **Autoloading** | Composer PSR-4 |
| **Auth** | Session + cookie tokens with SHA-256 + `random_bytes()` |
| **CLI** | Custom console with migrate/seed commands (interactive + CI modes) |
| **Frontend** | Vanilla JS + CSS |

---

<div align="center">

**Built with ⚡ by a developer who understands the stack — not just a framework user**

</div>
