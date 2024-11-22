<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class JoinCompetitionController
{

    private static \PDO $db;

    public function join(): void
    {
        try{
            if(!authenticateController::verify(false)){
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            static::$db->beginTransaction();

            foreach($_POST["competitions"] as $competition){
                $st = static::$db->prepare("SELECT * FROM competitions_applications WHERE competitionName = ? AND participantID = ?");
                $st->bindValue(1 , trim($competition));
                $st->bindValue(2 , $_SESSION["USER"]["ID"]);
                $st->execute();
                $res = $st->fetchAll();
                
                if(count($res) > 0){
                    echo View::make("join competition" , ["errorM" => "competition has already added"]);
                    return;
                }
                
                $st = static::$db->prepare("SELECT ID , competitionName , COUNT(*) as total FROM competitions_applications WHERE competitionName = ? GROUP BY competitionName");
                $st->bindValue(1 , trim($competition));
                $st->execute();
                $totalApplications = $st->fetchAll()[0]["total"];

                if($_COOKIE["role"] == "teams" && $totalApplications >= 4 
                || $_COOKIE["role"] == "individuals" && $totalApplications >= 20
                ){
                    echo View::make("join competition" , ["errorM" => "competition is full"]);
                    return;
                }


                $st = static::$db->prepare("SELECT ID FROM competitions WHERE name = ?");
                $st->bindValue(1 , trim($competition));
                $st->execute();
                $CompetitionID = $st->fetchAll()[0]["ID"];
                
                $st = static::$db->prepare("INSERT INTO competitions_applications VALUES(NULL , ? , ? , ? , ?)");
                $st->bindValue(1 , $_SESSION["USER"]["ID"]);
                $st->bindValue(2 , $CompetitionID);
                $st->bindValue(3 , trim($competition));
                $st->bindValue(4 , $_COOKIE["role"]);
                $st->execute();
                $res = $st->fetchAll();
            }

            static::$db->commit();
            header("Location: /home");
            exit();

        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("join competition" , ["errorM" => $r->getMessage()]);

            return;
        }
    }

    public function insert(): View
    {
        if(!authenticateController::verify(false)){
            header("Location: /logIn");
            exit();
        }
        static::$db = App::db();
        $st = static::$db->prepare("SELECT name FROM competitions WHERE category = ?");
        $st->bindValue(1,$_COOKIE["role"]);
        $st->execute();
        $res = $st->fetchAll();
        $competitions = App::convertToArray($res , "name");

        return View::make("join competition" , [
            "errorM" => null ,
            "competitions" => $competitions
            ]);
    }
}