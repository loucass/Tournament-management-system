<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController;
use App\View;

class JoinCompetitionController
{

    private static \PDO $db;

    public function join(): void
    {
        try {
            if (!authenticateController::verify(false)) {
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            static::$db->beginTransaction();

            $competitions = $_POST["competitions"] ?? [];
            if (!is_array($competitions)) {
                echo View::make("join competition", ["errorM" => "no competitions selected", "competitions" => null]);
                return;
            }

            foreach ($competitions as $competitionName) {
                $competitionName = trim($competitionName);
                $participantID = $_SESSION["USER"]["ID"];
                $role = $_SESSION["USER"]["role"];

                // Check if already applied
                $st = static::$db->prepare("SELECT * FROM competitions_applications WHERE competitionName = ? AND participantID = ?");
                $st->execute([$competitionName, $participantID]);
                if ($st->fetch()) {
                    echo View::make("join competition", ["errorM" => "already applied to this competition", "competitions" => null]);
                    return;
                }

                // Check capacity limits
                $st = static::$db->prepare("SELECT COUNT(*) as total FROM competitions_applications WHERE competitionName = ?");
                $st->execute([$competitionName]);
                $totalApplications = $st->fetch()["total"];

                if (($role === "teams" && $totalApplications >= 4) ||
                    ($role === "student" && $totalApplications >= 20)) {
                    echo View::make("join competition", ["errorM" => "competition is full", "competitions" => null]);
                    return;
                }

                // Get competition ID
                $st = static::$db->prepare("SELECT ID FROM competitions WHERE name = ?");
                $st->execute([$competitionName]);
                $compRow = $st->fetch();
                if (!$compRow) {
                    echo View::make("join competition", ["errorM" => "competition not found", "competitions" => null]);
                    return;
                }
                $CompetitionID = $compRow["ID"];

                // Insert application
                $st = static::$db->prepare("INSERT INTO competitions_applications VALUES(NULL, ?, ?, ?, ?)");
                $st->execute([$participantID, $CompetitionID, $competitionName, $role]);
            }

            static::$db->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            static::$db->rollBack();
            echo View::make("join competition", ["errorM" => "failed to join competition. please try again.", "competitions" => null]);
            return;
        }
    }

    public function insert(): View
    {
        if (!authenticateController::verify(false)) {
            header("Location: /logIn");
            exit();
        }
        static::$db = App::db();
        $role = $_SESSION["USER"]["role"] ?? '';

        // Get available competitions for this user's category
        $st = static::$db->prepare("SELECT name FROM competitions WHERE category = ?");
        $st->execute([$role === 'teams' ? 'teams' : 'individuals']);
        $res = $st->fetchAll();
        $competitions = App::convertToArray($res , "name");

        return View::make("join competition", [
            "errorM" => null,
            "competitions" => $competitions
        ]);
    }
}
