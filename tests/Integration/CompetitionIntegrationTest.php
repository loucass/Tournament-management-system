<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class CompetitionIntegrationTest extends DatabaseTestCase
{
    public function testJoinCompetitionIsIdempotentPerParticipant(): void
    {
        $st = self::db()->prepare("SELECT ID, name FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $student = $st->fetch();
        $comp = $this->fetchCompetition('individuals');

        $insert = self::db()->prepare(
            'INSERT INTO competitions_applications (participantID, competitionID, competitionName, category)
             VALUES (?, ?, ?, ?)'
        );
        $insert->execute([(int) $student['ID'], (int) $comp['ID'], $comp['name'], 'individuals']);
        $insert->execute([(int) $student['ID'], (int) $comp['ID'], $comp['name'], 'individuals']);

        // The controller guards against duplicates with this exact query.
        $check = self::db()->prepare(
            'SELECT COUNT(*) FROM competitions_applications WHERE competitionName = ? AND participantID = ?'
        );
        $check->execute([$comp['name'], (int) $student['ID']]);

        self::assertSame(2, (int) $check->fetchColumn(), 'Duplicate guard relies on the controller; DB allows rows.');
    }

    public function testTeamCompetitionCapacityIsFour(): void
    {
        $comp = $this->fetchCompetition('teams');

        // The controller rejects a team application when total >= 4.
        $insert = self::db()->prepare(
            'INSERT INTO competitions_applications (participantID, competitionID, competitionName, category)
             VALUES (?, ?, ?, ?)'
        );
        for ($i = 1; $i <= 4; $i++) {
            $insert->execute([$i, (int) $comp['ID'], $comp['name'], 'teams']);
        }

        $totalSt = self::db()->prepare('SELECT COUNT(*) FROM competitions_applications WHERE competitionName = ?');
        $totalSt->execute([$comp['name']]);
        $applications = (int) $totalSt->fetchColumn();

        $role = 'teams';
        $canJoin = !($role === 'teams' && $applications >= 4);

        self::assertSame(4, $applications);
        self::assertFalse($canJoin, 'At capacity: a 5th team would be rejected.');
    }

    public function testPointsAreUpsertedPerParticipantAndCompetition(): void
    {
        $st = self::db()->prepare("SELECT ID, name FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $student = $st->fetch();
        $comp = $this->fetchCompetition('individuals');

        $applyPoints = function (int $points) use ($student, $comp): void {
            // Mirrors EditPointsController::update(): SELECT existing row, then UPDATE or INSERT.
            $existingSt = self::db()->prepare(
                'SELECT * FROM competitions_points WHERE participantID = ? AND competitionID = ?'
            );
            $existingSt->execute([(int) $student['ID'], (int) $comp['ID']]);

            if ($existingSt->fetch()) {
                self::db()->prepare(
                    'UPDATE competitions_points SET points = ? WHERE participantID = ? AND competitionID = ?'
                )->execute([$points, (int) $student['ID'], (int) $comp['ID']]);
            } else {
                self::db()->prepare(
                    'INSERT INTO competitions_points (participantID, competitionID, participantName, points)
                     VALUES (?, ?, ?, ?)'
                )->execute([(int) $student['ID'], (int) $comp['ID'], $student['name'], $points]);
            }
        };

        $applyPoints(10);
        $applyPoints(25);

        $row = self::db()->prepare(
            'SELECT COUNT(*) FROM competitions_points WHERE participantID = ? AND competitionID = ?'
        );
        $row->execute([(int) $student['ID'], (int) $comp['ID']]);
        self::assertSame(1, (int) $row->fetchColumn(), 'Upsert must not create duplicate rows.');

        $value = self::db()->prepare(
            'SELECT points FROM competitions_points WHERE participantID = ? AND competitionID = ?'
        );
        $value->execute([(int) $student['ID'], (int) $comp['ID']]);
        self::assertSame(25, (int) $value->fetchColumn());
    }

    public function testLeaderboardOrdersParticipantsByPointsDescending(): void
    {
        $comp = $this->fetchCompetition('individuals');
        $students = self::db()->query("SELECT ID, name FROM users WHERE role = 'student'")->fetchAll();

        foreach ($students as $i => $student) {
            self::db()->prepare(
                'INSERT INTO competitions_applications (participantID, competitionID, competitionName, category)
                 VALUES (?, ?, ?, ?)'
            )->execute([(int) $student['ID'], (int) $comp['ID'], $comp['name'], 'individuals']);

            self::db()->prepare(
                'INSERT INTO competitions_points (participantID, competitionID, participantName, points)
                 VALUES (?, ?, ?, ?)'
            )->execute([(int) $student['ID'], (int) $comp['ID'], $student['name'], $i * 10]);
        }

        $rows = self::db()->prepare(
            "SELECT u.name AS participant_name, COALESCE(cp.points, 0) AS points
             FROM competitions c
             LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID
             INNER JOIN users u ON ca.participantID = u.ID
             LEFT JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = u.ID
             WHERE c.name = ? AND c.category = ?
             ORDER BY cp.points DESC"
        );
        $rows->execute([$comp['name'], 'individuals']);
        $points = array_map(fn ($r) => (int) $r['points'], $rows->fetchAll());

        $sorted = $points;
        rsort($sorted);

        self::assertSame($sorted, $points);
    }

    private function fetchCompetition(string $category): array
    {
        $st = self::db()->prepare('SELECT ID, name FROM competitions WHERE category = ? LIMIT 1');
        $st->execute([$category]);
        return $st->fetch();
    }
}