<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Paginator;
use App\Services\Database;
use App\View;

class ViewCompetitionController
{
    private const PER_PAGE = 10;

    public function insert(): void
    {
        try {
            if (!authenticateController::verify(false)) {
                header("Location: /logIn");
                exit();
            }

            $role       = $_SESSION["USER"]["role"] ?? '';
            $currentPage = max(1, (int) ($_GET['page'] ?? 1));
            $offset     = ($currentPage - 1) * self::PER_PAGE;

            if ($role !== "admin") {
                // ── Non-admin: joined competitions ──────────────────────
                $countSt = Database::connect()->prepare(<<<SQL
                    SELECT COUNT(DISTINCT c.ID) AS total
                       FROM competitions c
                       INNER JOIN competitions_applications ca ON ca.competitionID = c.ID
                       LEFT JOIN competitions_points cp ON cp.competitionID = c.ID
                       WHERE ca.participantID = ?
                    SQL
                );
                $countSt->execute([$_SESSION["USER"]["ID"]]);
                $total = (int) $countSt->fetch()['total'];

                $st = Database::connect()->prepare(<<<SQL
                    SELECT c.name, c.category, cp.participantName AS winner
                       FROM competitions c
                       INNER JOIN competitions_applications ca ON ca.competitionID = c.ID
                       LEFT JOIN competitions_points cp ON cp.competitionID = c.ID
                       WHERE ca.participantID = ?
                       GROUP BY c.ID, c.name, c.category
                       ORDER BY c.name
                       LIMIT ? OFFSET ?
                    SQL
                );
                $st->execute([$_SESSION["USER"]["ID"], self::PER_PAGE, $offset]);
                $joinedPaginator = new Paginator($st->fetchAll(), $total, $currentPage, self::PER_PAGE);

                // ── Non-admin: available competitions ────────────────────
                $availCountSt = Database::connect()->prepare(<<<SQL
                    SELECT COUNT(DISTINCT c.ID) AS total
                       FROM competitions c
                       LEFT JOIN competitions_applications ca
                         ON ca.competitionID = c.ID AND ca.participantID = ? AND ca.category = ?
                       WHERE ca.participantID IS NULL
                    SQL
                );
                $availCountSt->execute([$_SESSION["USER"]["ID"], $role]);
                $availTotal = (int) $availCountSt->fetch()['total'];

                $st = Database::connect()->prepare(<<<SQL
                    SELECT c.name, c.category, cp.participantName AS winner
                       FROM competitions c
                       LEFT JOIN competitions_applications ca
                         ON ca.competitionID = c.ID AND ca.participantID = ? AND ca.category = ?
                       LEFT JOIN competitions_points cp ON cp.competitionID = c.ID
                       WHERE ca.participantID IS NULL
                       GROUP BY c.ID, c.name, c.category
                       ORDER BY c.name
                       LIMIT ? OFFSET ?
                    SQL
                );
                $st->execute([$_SESSION["USER"]["ID"], $role, self::PER_PAGE, $offset]);
                $availPaginator = new Paginator($st->fetchAll(), $availTotal, $currentPage, self::PER_PAGE);

                echo View::make("competition list", [
                    "errorM"             => null,
                    "competitions"       => json_encode($joinedPaginator->items),
                    "NONcompetitions"    => json_encode($availPaginator->items),
                    "pagination"         => json_encode($joinedPaginator->toArray()),
                    "availPagination"    => json_encode($availPaginator->toArray()),
                ]);
                return;
            }

            // ── Admin: all competitions ──────────────────────────────────
            $countSt = Database::connect()->query(
                "SELECT COUNT(*) AS total FROM (SELECT c.ID FROM competitions c GROUP BY c.ID) sub"
            );
            $total = (int) $countSt->fetch()['total'];

            $st = Database::connect()->prepare(<<<SQL
                SELECT c.name, c.category, cp.participantName AS winner,
                        COALESCE(MAX(cp.points), 0) AS max_points
                   FROM competitions c
                   LEFT JOIN competitions_points cp ON cp.competitionID = c.ID
                   GROUP BY c.ID, c.name, c.category
                   ORDER BY c.name
                   LIMIT ? OFFSET ?
                SQL
            );
            $st->execute([self::PER_PAGE, $offset]);
            $paginator = new Paginator($st->fetchAll(), $total, $currentPage, self::PER_PAGE);

            echo View::make("competition list", [
                "errorM"       => null,
                "competitions" => json_encode($paginator->items),
                "pagination"   => json_encode($paginator->toArray()),
            ]);

        } catch (\Exception $r) {
            echo View::make("competition list", ["errorM" => "failed to load competitions."]);
        }
    }
}