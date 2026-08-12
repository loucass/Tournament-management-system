<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Commands\MigrateCommand;
use App\Commands\SeedCommand;
use App\Services\Config;
use App\Services\Database;
use PHPUnit\Framework\TestCase;

/**
 * Base class for tests that need a real MySQL database.
 *
 * Migrates the schema once per test class and truncates + re-seeds before
 * every test method, so tests are isolated and never touch developer data.
 * Skips gracefully when MySQL is down.
 */
abstract class DatabaseTestCase extends TestCase
{
    protected static string $envFile;

    public static function setUpBeforeClass(): void
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $db   = getenv('TEST_DB_DATABASE') ?: 'task_2_test';

        self::$envFile = tempnam(sys_get_temp_dir(), 'env_');
        file_put_contents(
            self::$envFile,
            "DB_HOST={$host}\nDB_USER={$user}\nDB_PASS={$pass}\nDB_DATABASE={$db}\n"
        );
        Config::load(self::$envFile);

        if (!self::databaseAvailable($host, $user, $pass)) {
            self::markTestSkipped('MySQL is not available in this environment.');
        }

        Database::disconnect();
        self::dropDatabase($host, $user, $pass, $db);

        // Buffer CLI output so header functions (setcookie) don't warn later.
        ob_start();
        MigrateCommand::run();
        ob_end_clean();
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$envFile) && file_exists(self::$envFile)) {
            unlink(self::$envFile);
        }
        Database::disconnect();
    }

    protected function setUp(): void
    {
        ob_start();
        try {
            self::resetTables();
            self::seed();
        } finally {
            ob_end_clean();
        }
    }

    protected static function databaseAvailable(string $host, string $user, string $pass): bool
    {
        try {
            new \PDO(
                "mysql:host={$host};charset=utf8mb4",
                $user,
                $pass,
                [\PDO::ATTR_TIMEOUT => 3]
            );
            return true;
        } catch (\PDOException) {
            return false;
        }
    }

    protected static function dropDatabase(string $host, string $user, string $pass, string $db): void
    {
        $pdo = new \PDO("mysql:host={$host};charset=utf8mb4", $user, $pass);
        $pdo->exec("DROP DATABASE IF EXISTS `{$db}`");
    }

    private static function resetTables(): void
    {
        $pdo = self::db();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['competitions_points', 'competitions_applications', 'tokens', 'users', 'teams', 'competitions'] as $table) {
            $pdo->exec("TRUNCATE TABLE `{$table}`");
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    private static function seed(): void
    {
        putenv('ADMIN_EMAIL=admin@test.local');
        putenv('ADMIN_PASSWORD=admin123');
        putenv('ADMIN_NAME=Test Admin');
        SeedCommand::admin();
        SeedCommand::demo();
    }

    protected static function db(): \PDO
    {
        return Database::connect();
    }
}