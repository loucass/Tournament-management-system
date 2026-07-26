<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController;
use App\View;

class ViewCompetitionController
{

    private static \PDO $db;

    public function insert()
    {
        try {
            if (!authenticateController::verify(false)) {
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            $role = $_SESSION["USER"]["role"] ?? '';

            if ($role !== "admin") {
                // Non-teacher: show competitions they've joined and available ones
                $q1 = "SELECT c.name, c.category, cp.participantName AS winner FROM competitions c ";
                $q2 = "INNER JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
                $q4 = "WHERE ca.participantID = ? ";
                $q5 = "GROUP BY c.ID, c.name, c.category";

                $query = $q1 . $q2 . $q3 . $q4 . $q5;

                $st = static::$db->prepare($query);
                $st->execute([$_SESSION["USER"]["ID"]]);
                $competitions = json_encode($st->fetchAll());

                // Competitions not yet joined
                $q1 = "SELECT c.name, c.category, cp.participantName AS winner FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID AND ca.participantID = ? AND ca.category = ? ";
                $q3 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
                $q4 = "WHERE ca.participantID IS NULL ";
                $q5 = "GROUP BY c.ID, c.name, c.category";

                $query = $q1 . $q2 . $q3 . $q4 . $q5;

                $st = static::$db->prepare($query);
                $st->execute([$_SESSION["USER"]["ID"], $role]);
                $NONcompetitions = json_encode($st->fetchAll());

                echo View::make("competition list", [
                    "errorM" => null,
                    "competitions" => $competitions,
                    "NONcompetitions" => $NONcompetitions,
                ]);
                return;
            }

            // Admin view: all competitions with winners
            $q1 = "SELECT c.name, c.category, cp.participantName AS winner, COALESCE(MAX(cp.points), 0) AS max_points FROM competitions c ";
            $q2 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
            $q3 = "GROUP BY c.ID, c.name, c.category";

            $query = $q1 . $q2 . $q3;

            $st = static::$db->query($query);
            $competitions = json_encode($st->fetchAll());

            echo View::make("competition list", [
                "errorM" => null,
                "competitions" => $competitions,
            ]);
        } catch (\Exception $r) {
            echo View::make("competition list", ["errorM" => "failed to load competitions."]);
        }
    }
}
