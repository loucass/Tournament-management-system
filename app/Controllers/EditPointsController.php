<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;
use App\Controllers\authenticateController;
use App\View;

class EditPointsController
{

    public function update()
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }
            Database::connect()->beginTransaction();

            $category = strtolower(trim($_POST["category"] ?? ''));
            $competitionName = strtolower(trim($_POST["competitionName"] ?? ''));
            $participantID = $_POST["ID"] ?? null;
            $participantName = strtolower(trim($_POST["participantName"] ?? ''));
            $points = (int)($_POST["points"] ?? 0);

            // Find competition ID
            $st = Database::connect()->prepare("SELECT ID FROM competitions WHERE category = ? AND name = ?");
            $st->execute([$category, $competitionName]);
            $compRow = $st->fetch();

            if (!$compRow) {
                echo "error: competition not found";
                return;
            }

            $competitionID = $compRow["ID"];

            // Check if points record exists
            $st = Database::connect()->prepare("SELECT * FROM competitions_points WHERE participantID = ? AND competitionID = ?");
            $st->execute([$participantID, $competitionID]);
            $existing = $st->fetch();

            if ($existing) {
                $st = Database::connect()->prepare("UPDATE competitions_points SET points = ? WHERE participantID = ? AND competitionID = ?");
                $st->execute([$points, $participantID, $competitionID]);
            } else {
                $st = Database::connect()->prepare("INSERT INTO competitions_points VALUES(NULL, ?, ?, ?, ?)");
                $st->execute([$participantID, $competitionID, $competitionName, $points]);
            }

            echo "done";
            Database::connect()->commit();

        } catch (\PDOException $e) {
            echo "error: " . $e->getMessage();
            Database::connect()->rollBack();
        }
    }
}
