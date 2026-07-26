<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\Database;

/**
 * SeedCommand — Seeds initial data into the database.
 *
 * Commands:
 *   php console seed:admin      Create the administrator account
 *   php console seed:demo       Seed competitions, students, teams
 */
class SeedCommand
{
    /**
     * Create the admin user.
     * Prompts for email/password, or uses defaults when run non-interactively.
     */
    public static function admin(): void
    {
        echo "  ⚡ Seeding admin user...\n\n";

        // Non-interactive: read from environment or use defaults
        $email    = getenv('ADMIN_EMAIL') ?: 'admin@tournament.local';
        $password = getenv('ADMIN_PASSWORD') ?: 'admin123';
        $name     = getenv('ADMIN_NAME') ?: 'Admin';

        // Interactive mode if running in a TTY
        if (function_exists('posix_isatty') && posix_isatty(STDIN)) {
            echo "  Enter admin email [{$email}]: ";
            $input = trim(fgets(STDIN));
            if ($input !== '') {
                $email = $input;
            }

            echo "  Enter admin password [{$password}]: ";
            $input = trim(fgets(STDIN));
            if ($input !== '') {
                $password = $input;
            }

            echo "  Enter admin name [{$name}]: ";
            $input = trim(fgets(STDIN));
            if ($input !== '') {
                $name = $input;
            }
        }

        try {
            $pdo = Database::connect();

            // Check if admin already exists
            $st = $pdo->prepare("SELECT ID FROM users WHERE email = ?");
            $st->execute([$email]);
            if ($st->fetch()) {
                echo "  ⚠️  Admin with email '{$email}' already exists. Skipping.\n";
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $st = $pdo->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')"
            );
            $st->execute([$name, $email, $hashedPassword]);

            echo "  ✅ Admin user created:\n";
            echo "     Name:  {$name}\n";
            echo "     Email: {$email}\n";
            echo "     Role:  admin\n";
        } catch (\PDOException $e) {
            echo "  ❌ Failed to seed admin: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * Seed demo data: competitions, sample students, and a team.
     */
    public static function demo(): void
    {
        echo "  ⚡ Seeding demo data...\n\n";

        try {
            $pdo = Database::connect();
            $pdo->beginTransaction();

            // --- Competitions ---
            $competitions = [
                ['Cyber Arena', 'individuals'],
                ['Algorithm Sprint', 'individuals'],
                ['Capture The Flag', 'teams'],
                ['Hackathon X', 'teams'],
                ['Code Battle', 'individuals'],
            ];

            $compCount = 0;
            foreach ($competitions as [$name, $category]) {
                $st = $pdo->prepare("SELECT ID FROM competitions WHERE name = ?");
                $st->execute([$name]);
                if (!$st->fetch()) {
                    $st = $pdo->prepare(
                        "INSERT INTO competitions (name, category) VALUES (?, ?)"
                    );
                    $st->execute([$name, $category]);
                    $compCount++;
                    echo "     📋 Created competition: {$name} ({$category})\n";
                }
            }

            // --- Sample Students ---
            $students = [
                ['Alice Johnson', 'alice@demo.local'],
                ['Bob Smith', 'bob@demo.local'],
                ['Charlie Brown', 'charlie@demo.local'],
                ['Diana Ross', 'diana@demo.local'],
                ['Eve Williams', 'eve@demo.local'],
            ];

            $studentCount = 0;
            foreach ($students as [$name, $email]) {
                $st = $pdo->prepare("SELECT ID FROM users WHERE email = ?");
                $st->execute([$email]);
                if (!$st->fetch()) {
                    $hashed = password_hash('demo123', PASSWORD_BCRYPT);
                    $st = $pdo->prepare(
                        "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')"
                    );
                    $st->execute([$name, $email, $hashed]);
                    $studentCount++;
                    echo "     👤 Created student: {$name}\n";
                }
            }

            // --- Team ---
            $st = $pdo->prepare("SELECT ID FROM teams WHERE email = ?");
            $st->execute(['squad@demo.local']);
            if (!$st->fetch()) {
                $hashed = password_hash('team123', PASSWORD_BCRYPT);
                $st = $pdo->prepare(
                    "INSERT INTO teams (name, email, password) VALUES (?, ?, ?)"
                );
                $st->execute(['Squad Alpha', 'squad@demo.local', $hashed]);
                echo "     🏆 Created team: Squad Alpha (squad@demo.local / team123)\n";
            }

            $pdo->commit();

            echo "\n  ✅ Demo data seeded:\n";
            echo "     {$compCount} competition(s)\n";
            echo "     {$studentCount} student(s)\n";
            echo "     Credentials: alice@demo.local / demo123\n";
            echo "     Credentials: squad@demo.local / team123\n";
        } catch (\PDOException $e) {
            $pdo->rollBack();
            echo "  ❌ Failed to seed demo data: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
