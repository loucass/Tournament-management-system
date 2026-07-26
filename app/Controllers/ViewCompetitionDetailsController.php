<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Paginator;
use App\Services\Database;
use App\Controllers\authenticateController;
use App\View;

class ViewCompetitionDetailsController
{
    private const PER_PAGE = 10;

    public function insert(): View
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }

            $competitionName = strtolower(trim($_GET["competition"] ?? ''));
            $category        = strtolower(trim($_GET["category"] ?? ''));
            $currentPage     = max(1, (int) ($_GET['page'] ?? 1));
            $offset          = ($currentPage - 1) * self::PER_PAGE;

            if (!in_array($category, ['individuals', 'teams'], true)) {
                return View::make("404", ["errorPath" => "invalid category"]);
            }

            if ($category === "individuals") {
                $countQuery = <<<SQL
                    SELECT COUNT(*) AS total
                        FROM competitions c
                        LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID
                        INNER JOIN users u ON ca.participantID = u.ID
                        WHERE c.name = ? AND c.category = ?
                    SQL;

                $dataQuery = <<<SQL
                    SELECT c.name, c.category, u.name AS participant_name, u.ID,
                             COALESCE(cp.points, 0) AS points
                        FROM competitions c
                        LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID
                        INNER JOIN users u ON ca.participantID = u.ID
                        LEFT JOIN competitions_points cp
                          ON cp.competitionID = c.ID AND cp.participantID = u.ID
                        WHERE c.name = ? AND c.category = ?
                        ORDER BY cp.points DESC
                        LIMIT ? OFFSET ?
                    SQL;
            } else {
                $countQuery = <<<SQL
                    SELECT COUNT(*) AS total
                        FROM competitions c
                        LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID
                        LEFT JOIN teams t ON ca.participantID = t.ID
                        WHERE c.name = ? AND c.category = ?
                    SQL;

                $dataQuery = <<<SQL
                    SELECT c.name, c.category, t.name AS participant_name, t.ID,
                             COALESCE(cp.points, 0) AS points
                        FROM competitions c
                        LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID
                        LEFT JOIN teams t ON ca.participantID = t.ID
                        LEFT JOIN competitions_points cp
                          ON cp.competitionID = c.ID AND cp.participantID = t.ID
                        WHERE c.name = ? AND c.category = ?
                        ORDER BY cp.points DESC
                        LIMIT ? OFFSET ?
                    SQL;
            }

            // Total for pagination
            $countSt = Database::connect()->prepare($countQuery);
            $countSt->execute([$competitionName, $category]);
            $total = (int) $countSt->fetch()['total'];

            // Data for current page
            $st = Database::connect()->prepare($dataQuery);
            $st->execute([$competitionName, $category, self::PER_PAGE, $offset]);
            $paginator = new Paginator($st->fetchAll(), $total, $currentPage, self::PER_PAGE);

            return View::make("competition dashboard", [
                "errorM"             => null,
                "competitionsDetails" => json_encode($paginator->items),
                "pagination"         => json_encode($paginator->toArray()),
            ]);

        } catch (\Exception $r) {
            return View::make("competition dashboard", ["errorM" => "failed to load competition details."]);
        }
    }
}
