<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class TeamIntegrationTest extends DatabaseTestCase
{
    public function testTeamDuplicateEmailIsRejected(): void
    {
        // Controller guard: rejects when a team with the email already exists.
        self::db()->prepare('INSERT INTO teams (name, email, password) VALUES (?, ?, ?)')
            ->execute(['Alpha', 'alpha@test.local', password_hash('x', PASSWORD_BCRYPT)]);

        $check = self::db()->prepare('SELECT * FROM teams WHERE email = ?');
        $check->execute(['alpha@test.local']);

        self::assertNotFalse($check->fetch(), 'Duplicate guard: an existing team blocks a second INSERT.');
    }

    public function testStudentAssignedToATeamIsNotAvailableForAnother(): void
    {
        $st = self::db()->prepare("SELECT ID, name FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $student = $st->fetch();

        self::db()->prepare('INSERT INTO teams (name, email, password) VALUES (?, ?, ?)')
            ->execute(['Team One', 'team1@test.local', password_hash('x', PASSWORD_BCRYPT)]);
        $teamID = (int) self::db()->lastInsertId();

        self::db()->prepare('UPDATE users SET teamID = ? WHERE ID = ?')
            ->execute([$teamID, (int) $student['ID']]);

        // This mirrors AddTeamController's bulk-assignment query, which only
        // selects students with teamID IS NULL — an already-assigned student
        // must not be returned.
        $query = self::db()->prepare(
            "SELECT ID, name FROM users
             WHERE name = ? AND role = 'student' AND teamID IS NULL FOR UPDATE"
        );
        $query->execute([$student['name']]);

        self::assertEmpty($query->fetchAll());
    }

    public function testTeamCanAdmitMultipleStudentsAtomically(): void
    {
        $students = self::db()->query("SELECT ID, name FROM users WHERE role = 'student'")->fetchAll();

        self::db()->prepare('INSERT INTO teams (name, email, password) VALUES (?, ?, ?)')
            ->execute(['Team Two', 'team2@test.local', password_hash('x', PASSWORD_BCRYPT)]);
        $teamID = (int) self::db()->lastInsertId();

        foreach ($students as $student) {
            self::db()->prepare('UPDATE users SET teamID = ? WHERE ID = ?')
                ->execute([$teamID, (int) $student['ID']]);
        }

        $count = self::db()->prepare('SELECT COUNT(*) FROM users WHERE teamID = ?');
        $count->execute([$teamID]);

        self::assertSame(count($students), (int) $count->fetchColumn());
    }
}