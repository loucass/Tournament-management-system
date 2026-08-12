<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class MigrationIntegrationTest extends DatabaseTestCase
{
    private const TABLES = [
        'users',
        'teams',
        'competitions',
        'tokens',
        'competitions_applications',
        'competitions_points',
    ];

    public function testMigrationCreatesAllTables(): void
    {
        $rows = self::db()->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach (self::TABLES as $table) {
            self::assertContains($table, $rows, "Missing table: {$table}");
        }
    }

    public function testSeedAdminCreatesAdminUser(): void
    {
        $st = self::db()->prepare(
            "SELECT role, password FROM users WHERE email = 'admin@test.local'"
        );
        $st->execute();
        $admin = $st->fetch();

        self::assertNotFalse($admin);
        self::assertSame('admin', $admin['role']);
        self::assertTrue(password_verify('admin123', $admin['password']));
    }

    public function testSeedDemoCreatesExpectedData(): void
    {
        $competitions = (int) self::db()->query('SELECT COUNT(*) FROM competitions')->fetchColumn();
        $students     = (int) self::db()->query(
            "SELECT COUNT(*) FROM users WHERE role = 'student'"
        )->fetchColumn();
        $teams        = (int) self::db()->query('SELECT COUNT(*) FROM teams')->fetchColumn();

        self::assertSame(5, $competitions);
        self::assertSame(5, $students);
        self::assertSame(1, $teams);
    }

    public function testSchemaHasExpectedCompetitionColumns(): void
    {
        $st = self::db()->query('SHOW COLUMNS FROM competitions');
        $columns = array_column($st->fetchAll(), 'Field');

        self::assertContains('ID', $columns);
        self::assertContains('name', $columns);
        self::assertContains('category', $columns);
    }

    public function testUsersRoleEnumAllowsAdminAndStudent(): void
    {
        $st = self::db()->query("SHOW COLUMNS FROM users WHERE Field = 'role'");
        $row = $st->fetch();

        self::assertStringContainsString("'admin'", $row['Type']);
        self::assertStringContainsString("'student'", $row['Type']);
    }
}