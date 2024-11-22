<?php

declare(strict_types=1);

namespace App\Controllers;

use App\{App , View};
use App\Exceptions\{AuthenticationFailedException , RouteNotFoundException};

class HomeController
{
    public function index()
    {
        try {
            $auth = authenticateController::verify(false);

            if (!$auth) {
                header("Location: /logIn");
                exit();
            } else {
                if (authenticateController::verify(true)) {
                    $q1 = "SELECT u.name , u.ID FROM users u ";
                    $q2 = "SELECT t.ID , t.name FROM teams t ";
                    $q3 = "SELECT tp.ID , tp.name FROM teams_participants tp ";

                    $st = App::db()->prepare($q1);
                    $st->execute();

                    $participants = $st->fetchAll();

                    $st = App::db()->prepare($q2);
                    $st->execute();

                    $teams = $st->fetchAll();

                    $st = App::db()->prepare($q3);
                    $st->execute();

                    $teamsParticipants = $st->fetchAll();

                    echo View::make("home" , [
                        "participants" =>$participants,
                        "teams" =>$teams,
                        "teamsParticipants" =>$teamsParticipants
                        ]);
                    }
                    else{
                        if($auth){
                        echo View::make("home" , ["errorM"=>null]);
                    }else{
                        header("Location: /logIn");
                        exit();
                    }
                }
            }
        } catch (\Exception $r) {
            header("Location: /logIn");
            exit();
        }
    }
}