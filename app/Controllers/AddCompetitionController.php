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

            static::$db->beginTransaction();
            $st = static::$db->prepare("SELECT * FROM competitions WHERE name = ?");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "competitionName" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();
            
            if(count($res) > 0){
                echo View::make("add competition" , ["errorM" => "competition has already added"]);
                return;
            }
            
            $st = static::$db->prepare("INSERT INTO competitions VALUES(NULL , ? , ?)");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "competitionName" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "competitionCategory" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();

            static::$db->commit();
            header("Location: /home");
            exit();

        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("add competition" , ["errorM" => $r->getMessage()]);

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