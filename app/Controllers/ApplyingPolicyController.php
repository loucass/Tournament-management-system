<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;

class ApplyingPolicyController
{

    /**
     * Returns students who can still be added to teams (fewer than 5 competition applications total).
     */
    public static function verifyForTeams()
    {
        // Count total competition applications per student (direct + via team)
        $q = <<<SQL
            SELECT u.name, u.ID, 
              (COALESCE(direct_apps.cnt, 0) + COALESCE(team_apps.cnt, 0)) AS applications 
              FROM users u
              LEFT JOIN (
                SELECT participantID, COUNT(*) AS cnt 
                FROM competitions_applications 
                GROUP BY participantID
              ) direct_apps ON direct_apps.participantID = u.ID
              LEFT JOIN (
                SELECT tp.ID AS teamID, COUNT(*) AS cnt 
                FROM competitions_applications ca
                JOIN teams tp ON tp.ID = ca.participantID
                WHERE ca.category = 'teams'
                GROUP BY tp.ID
              ) team_apps ON team_apps.teamID = u.teamID
              WHERE u.role = 'student'
              GROUP BY u.ID
              HAVING applications < 5
              ORDER BY u.name
            SQL;

        $st = Database::connect()->query($q);
        $result = $st->fetchAll();

        if (count($result) > 0) {
            return json_encode($result);
        }
        return json_encode([]);
    }

    /**
     * Returns how many more competitions a user can join (max 5).
     */
    public static function verify()
    {
        $userID = $_SESSION["USER"]["ID"] ?? null;
        if (!$userID) {
            return false;
        }

        // Count applications for this user (direct + via team)
        $q = <<<SQL
            SELECT 
              (COALESCE(direct_apps.cnt, 0) + COALESCE(team_apps.cnt, 0)) AS applications
              FROM users u
              LEFT JOIN (
                SELECT participantID, COUNT(*) AS cnt 
                FROM competitions_applications 
                GROUP BY participantID
              ) direct_apps ON direct_apps.participantID = u.ID
              LEFT JOIN (
                SELECT tp.ID AS teamID, COUNT(*) AS cnt 
                FROM competitions_applications ca
                JOIN teams tp ON tp.ID = ca.participantID
                WHERE ca.category = 'teams'
                GROUP BY tp.ID
              ) team_apps ON team_apps.teamID = u.teamID
              WHERE u.ID = ?
              GROUP BY u.ID
            SQL;

        $st = Database::connect()->prepare($q);
        $st->execute([$userID]);
        $result = $st->fetch();

        if ($result && $result["applications"] < 5) {
            return 5 - (int)$result["applications"];
        }
        return false;
    }
}
