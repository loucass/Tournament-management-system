<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\Config;

/**
 * MigrateCommand — Runs database/schema.sql against the configured database.
 *
 * Smart flow:
 *   1. Connect to MySQL without a specific database.
 *   2. Check if the configured database already exists.
 *   3. If it exists → ask the user whether to drop & recreate (or skip).
 *   4. If it doesn't exist → create it automatically.
 *   5. Run all CREATE TABLE statements from schema.sql.
 *
 * Usage:
 *   php console migrate
 */
class MigrateCommand
{
    public static function run(): void
    {
        echo "  ⚡ Running migrations...\n\n";

        $host = Config::get('DB_HOST', 'localhost');
        $user = Config::get('DB_USER', 'root');
        $pass = Config::get('DB_PASS', '');
        $db   = Config::get('DB_DATABASE', 'task_2');

        $schemaPath = dirname(__DIR__, 2) . '/database/schema.sql';

        if (!file_exists($schemaPath)) {
            echo "  ❌ Schema file not found: $schemaPath\n";
            exit(1);
        }

        $sql = file_get_contents($schemaPath);
        if ($sql === false || trim($sql) === '') {
            echo "  ❌ Schema file is empty.\n";
            exit(1);
        }

        try {
            // ── Step 1: Connect to MySQL server (no database yet) ──────────
            $pdo = new \PDO(
                "mysql:host={$host};charset=utf8mb4",
                $user,
                $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            // ── Step 2: Check if database exists ───────────────────────────
            $st = $pdo->prepare(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?"
            );
            $st->execute([$db]);
            $exists = (bool) $st->fetch();

            if ($exists) {
                echo "  Database '{$db}' already exists.\n";

                if (function_exists('posix_isatty') && posix_isatty(STDIN)) {
                    echo "  Drop and recreate? All data will be lost! [y/N]: ";
                    $input = trim(fgets(STDIN));

                    if (strtolower($input) === 'y') {
                        $pdo->exec("DROP DATABASE `{$db}`");
                        $pdo->exec(
                            "CREATE DATABASE `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
                        );
                        echo "  ✅ Database '{$db}' recreated.\n";
                    } else {
                        echo "  Keeping existing database '{$db}'.\n";
                    }
                } else {
                    echo "  Using existing database '{$db}' (non-interactive mode).\n";
                }
            } else {
                $pdo->exec(
                    "CREATE DATABASE `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
                );
                echo "  ✅ Database '{$db}' created.\n";
            }

            // ── Step 3: Switch to the database ────────────────────────────
            $pdo->exec("USE `{$db}`");

            // ── Step 4: Execute schema statements ─────────────────────────
            $statements = explode(';', $sql);
            $count = 0;

            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if ($stmt === '') {
                    continue;
                }

                // Skip CREATE DATABASE and USE — we handle those above
                if (preg_match('/^(CREATE\s+DATABASE|USE)\b/i', $stmt)) {
                    continue;
                }

                $pdo->exec($stmt);
                $count++;
            }

            echo "\n  ✅ {$count} migration(s) executed successfully.\n";
            echo "     Next: php console seed:admin\n";

        } catch (\PDOException $e) {
            echo "  ❌ Migration failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
