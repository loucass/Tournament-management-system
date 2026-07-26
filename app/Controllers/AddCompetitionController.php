<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;
use App\Controllers\authenticateController ;
use App\View;

class AddCompetitionController
{

    public function add(): void
    {
        try{
            if(!authenticateController::verify(true)){
                header("Location: /logIn");
                exit();
            }
            Database::connect();

            $competitionName = strtolower(trim($_POST["competitionName"] ?? ''));
            $competitionCategory = strtolower(trim($_POST["competitionCategory"] ?? ''));

            $st = Database::connect()->prepare("SELECT * FROM competitions WHERE name = ?");
            $st->execute([$competitionName]);
            
            if($st->fetch()){
                echo View::make("add competition" , ["errorM" => "competition already exists"]);
                return;
            }
            
            $st = Database::connect()->prepare("INSERT INTO competitions (name, category) VALUES(?, ?)");
            $st->execute([$competitionName, $competitionCategory]);

            header("Location: /home");
            exit();

        }catch(\Exception $r){
            echo View::make("add competition" , ["errorM" => "failed to add competition. please try again."]);
            return;
        }
    }

    public function insert(): View
    {
        if(!authenticateController::verify(true)){
            header("Location: /logIn");
            exit();
        }
        return View::make("add competition" , ["errorM"=>null]);
    }
}