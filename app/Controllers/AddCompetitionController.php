<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class AddCompetitionController
{

    private static \PDO $db;

    public function add(): void
    {
        try{
            if(!authenticateController::verify(true)){
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            $competitionName = strtolower(trim($_POST["competitionName"] ?? ''));
            $competitionCategory = strtolower(trim($_POST["competitionCategory"] ?? ''));

            $st = static::$db->prepare("SELECT * FROM competitions WHERE name = ?");
            $st->execute([$competitionName]);
            
            if($st->fetch()){
                echo View::make("add competition" , ["errorM" => "competition already exists"]);
                return;
            }
            
            $st = static::$db->prepare("INSERT INTO competitions (name, category) VALUES(?, ?)");
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