<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Controllers\ApplyingPolicyController;

final class ApplyingPolicyIntegrationTest extends DatabaseTestCase
{
    public function testVerifyReturnsRemainingSlotsForNewStudent(): void
    {
        $st = self::db()->prepare("SELECT ID FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $studentID = (int) $st->fetchColumn();

        $_SESSION['USER'] = ['ID' => $studentID];

        self::assertSame(5, ApplyingPolicyController::verify());
    }

    public function testVerifyReturnsZeroWhenStudentHasFiveApplications(): void
    {
        $st = self::db()->prepare("SELECT ID, name FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $student = $st->fetch();

        $this->seedApplications((int) $student['ID'], $student['name'], 5);

        $_SESSION['USER'] = ['ID' => (int) $student['ID']];

        self::assertFalse(ApplyingPolicyController::verify());
    }

    public function testVerifyCountsApplicationsJoinedThroughTeam(): void
    {
        $studentSt = self::db()->prepare("SELECT ID, name FROM users WHERE role = 'student' LIMIT 1");
        $studentSt->execute();
        $student = $studentSt->fetch();

        $teamSt = self::db()->prepare("SELECT ID FROM teams LIMIT 1");
        $teamSt->execute();
        $teamID = (int) $teamSt->fetchColumn();

        self::db()->prepare('UPDATE users SET teamID = ? WHERE ID = ?')
            ->execute([$teamID, (int) $student['ID']]);

        // The team already holds 2 applications; the student's remaining
        // capacity is counted via their teamID.
        $this->seedApplications((int) $student['ID'], $student['name'], 2);

        $_SESSION['USER'] = ['ID' => (int) $student['ID']];

        self::assertSame(3, ApplyingPolicyController::verify());
    }

    public function testVerifyForTeamsOnlyReturnsStudentsWithCapacity(): void
    {
        $students = self::db()->query("SELECT ID, name FROM users WHERE role = 'student'")->fetchAll();
        $full = $students[0];
        $this->seedApplications((int) $full['ID'], $full['name'], 5);

        $json = ApplyingPolicyController::verifyForTeams();
        $eligible = json_decode($json, true);

        $names = array_column($eligible, 'name');
        self::assertNotContains($full['name'], $names);
        self::assertCount(4, $names);
    }

    private function seedApplications(int $participantID, string $name, int $count): void
    {
        $compSt = self::db()->prepare("SELECT ID, name FROM competitions LIMIT 1");
        $compSt->execute();
        $comp = $compSt->fetch();

        for ($i = 0; $i < $count; $i++) {
            self::db()->prepare(
                'INSERT INTO competitions_applications (participantID, competitionID, competitionName, category)
                 VALUES (?, ?, ?, ?)'
            )->execute([$participantID, (int) $comp['ID'], $comp['name'], 'individuals']);
        }
    }
}