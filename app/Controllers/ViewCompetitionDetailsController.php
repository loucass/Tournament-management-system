<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController;
use App\View;

class ViewCompetitionDetailsController
{

    private static \PDO $db;

    public function insert()
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            $competitionName = strtolower(trim($_GET["competition"] ?? ''));
            $category = strtolower(trim($_GET["category"] ?? ''));

            if (!in_array($category, ['individuals', 'teams'])) {
                return View::make("404", ["errorPath" => "invalid category"]);
            }

            if ($category === "individuals") {
                $q1 = "SELECT c.name, c.category, u.name AS participant_name, u.ID, COALESCE(cp.points, 0) AS points FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "INNER JOIN users u ON ca.participantID = u.ID ";
                $q4 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = u.ID ";
                $q5 = "WHERE c.name = ? AND c.category = ? ";
                $q6 = "ORDER BY cp.points DESC";
            } else {
                $q1 = "SELECT c.name, c.category, t.name AS participant_name, t.ID, COALESCE(cp.points, 0) AS points FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "LEFT JOIN teams t ON ca.participantID = t.ID ";
                $q4 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = t.ID ";
                $q5 = "WHERE c.name = ? AND c.category = ? ";
                $q6 = "ORDER BY cp.points DESC";
            }

            $query = $q1 . $q2 . $q3 . $q4 . $q5 . $q6;

            $st = static::$db->prepare($query);
            $st->execute([$competitionName, $category]);

            $competitionsDetails = json_encode($st->fetchAll());

            return View::make("competition dashboard", [
                "errorM" => null,
                "competitionsDetails" => $competitionsDetails,
            ]);

        } catch (\Exception $r) {
            return View::make("competition dashboard", ["errorM" => "failed to load competition details."]);
        }
    }
}
