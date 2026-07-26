<div align="center">

# Tournament management system

**Full-Stack Tournament Management System**  
*Built with Pure PHP — No Framework, No Bloat*

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-7B3BE6?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/license-MIT-ff00aa?style=flat)](LICENSE)

</div>

---

## 🎯 Overview

**Tournament management system** is a production-grade, custom-built PHP MVC application that digitizes the entire lifecycle of school and college sports tournaments. From team formation and competition registration to real-time scoring and leaderboards — it replaces spreadsheets, paper forms, and manual tracking with an automated, role-based platform.

> **Why this matters:** Most student projects rely on Laravel or Symfony. This one was built from scratch — a deliberate architectural choice that demonstrates deep understanding of MVC patterns, database design, authentication flows, and security engineering without a framework's training wheels.

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
- **Team Builder** — Assemble squads from registered students with duplicate prevention and atomic transactions
- **Live Scoring** — Real-time point updates via AJAX with instant leaderboard recalculation
- **Leaderboard** — Dynamic golden-square top-4 display with full ranking table
- **Application Policy** — Automated enforcement of 5-competition max per student (direct + via team)

### 🛡️ Security
- **BCrypt password hashing** — Industry-standard credential storage
- **CSRF protection** — Per-session tokens validated on every state-changing request
- **SQL injection prevention** — Prepared statements + strict whitelist validation on all dynamic queries
- **XSS prevention** — `htmlspecialchars()` on all user-generated output
- **Session security** — `session_regenerate_id()` after login, proper session destruction on logout
- **Security headers** — X-Frame-Options, X-Content-Type-Options, XSS-Protection, Referrer-Policy, Permissions-Policy

---

## 🏗️ Architecture

### Custom MVC Framework (Zero Dependencies)

```
Tournament-management-system/
├── app/                      # Application core
│   ├── App.php               # Application bootstrap, CSRF, config
│   ├── Router.php            # Request router (GET/POST) with 404 handling
│   ├── View.php              # Template renderer with parameter extraction
│   ├── Controllers/          # 13 action controllers
│   │   ├── authenticateController.php  # Token-based auth middleware
│   │   ├── ApplyingPolicyController.php # Competition limit enforcement
│   │   ├── SignUpController.php        # Public self-registration
│   │   └── ...                # CRUD controllers for all entities
│   └── Exceptions/           # Custom exception classes
├── database/
│   └── schema.sql            # Full relational schema
├── public/                   # Web root
│   ├── index.php             # Front controller (single entry point)
│   ├── .htaccess             # Apache URL rewriting
│   └── assets/css/           # Design system stylesheets
├── views/                    # PHP template files (10 views)
│   ├── log in.php            # Login with auto-role detection
│   ├── sign in.php           # Student self-registration
│   ├── home.php              # Role-aware dashboard
│   ├── competition dashboard.php  # Live leaderboard with AJAX scoring
│   └── ...
├── storage/                  # Writable storage
├── .env                      # Environment configuration
└── composer.json             # PSR-4 autoloading
```

### Database Schema (Normalized Relational)

```
users        → Unified: students + admins (role, teamID)
teams        → Group entities with independent login
competitions → Individual and team challenges
tokens       → Session-bound authentication tokens
competitions_applications  → Many-to-many: participants → competitions
competitions_points        → Scoring records per participant
```

### Data Flow

```
Browser → public/index.php (Front Controller)
              ↓
         Router::resolve()
              ↓
         Controller::action()
              ↓
         App::db() → PDO → MySQL
              ↓
         View::make() → Template Rendering
              ↓
         Response (HTML / JSON)
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.0+
- MySQL 8.0 / MariaDB 10.3+
- Composer
- Apache with `mod_rewrite` (or use PHP built-in server)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/loucass/Tournament-management-system.git
cd Tournament-management-system

# 2. Install dependencies (PSR-4 autoloader)
composer install --no-dev

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 4. Create the database
mysql -u root -p < database/schema.sql

# 5. Start the development server
composer serve
# Or: php -S localhost:8080 -t public/
```

### Default Admin Account

| Field    | Value               |
|----------|---------------------|
| Role     | Admin (teacher)     |
| Email    | admin@school.com    |
| Password | admin               |

---

## 🔧 Development

### Built-In Server
```bash
php -S localhost:8080 -t public/
```

### Production Deployment
Configure Apache/Nginx to serve from `public/` directory with:
- `RewriteEngine On` → route all requests through `index.php`
- Set `AllowOverride All` on the `public/` directory
- Point document root to `/path/to/Tournament-management-system/public`
- Set environment variables for database credentials

---

## 🛠️ Tech Stack

- **Runtime:** PHP 8.0+ (strict types, constructor promotion, named arguments)
- **Database:** MySQL 8.0 / MariaDB 10.3+ (PDO prepared statements)
- **Autoloading:** Composer PSR-4
- **Frontend:** Vanilla JS + CSS
- **Auth:** Session + cookie tokens with SHA-256 + random entropy

---

<div align="center">

**Built with ⚡ by a developer who understands the stack — not just a framework user**

</div>
