<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Services\Database;
use App\Exceptions\{AuthenticationFailedException, RouteNotFoundException};

class HomeController
{
    public function index()
    {
        try {
            // Authenticate the user
            $auth = authenticateController::verify(false);

            if (!$auth) {
                header("Location: /logIn");
                exit();
            }

            // Check if user is admin (teacher)
            $isAdmin = ($_SESSION["USER"]["role"] ?? '') === 'admin';

            if ($isAdmin) {
                // Admin view: fetch all students, teams, and team members
                $st = Database::connect()->prepare("SELECT u.name, u.ID FROM users u WHERE u.role = 'student' ORDER BY u.name");
                $st->execute();
                $participants = $st->fetchAll();

                $st = Database::connect()->prepare("SELECT t.ID, t.name FROM teams t ORDER BY t.name");
                $st->execute();
                $teams = $st->fetchAll();

                // Get team members (students who have a teamID set)
                $st = Database::connect()->prepare("SELECT u.ID, u.name, u.teamID FROM users u WHERE u.teamID IS NOT NULL ORDER BY u.name");
                $st->execute();
                $teamMembers = $st->fetchAll();

                echo View::make("home", [
                    "participants" => $participants,
                    "teams" => $teams,
                    "teamsParticipants" => $teamMembers
                ]);
            } else {
                // Student / team view
                echo View::make("home", ["errorM" => null]);
            }
        } catch (\Exception $r) {
            header("Location: /logIn");
            exit();
        }
    }
}
