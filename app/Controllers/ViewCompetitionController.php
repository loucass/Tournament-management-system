<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class ViewCompetitionController
{

    private static \PDO $db;

    public function insert()
    {
        try{
            if(!authenticateController::verify(false)){
                header("Location: /logIn");
                exit();
            }
        static::$db = App::db();

        if($_COOKIE["role"] != "teachers"){

            $q1 = "SELECT c.name, c.category, cp.participantName AS winner FROM competitions c ";
            $q2 = "INNER JOIN competitions_applications ca ON ca.competitionID = c.ID ";
            $q3 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
            $q4 = "WHERE ca.participantID = ? ";
            $q5 = "GROUP BY c.ID, c.name, c.category ";

            $query = (string) $q1 . $q2 . $q3 . $q4 . $q5;


            $st = static::$db->prepare($query);
            $st->bindValue(1, $_SESSION["USER"]["ID"]);
            $st->execute();
            
            $competitions = $st->fetchAll();
            $competitions = json_encode($competitions);
            
            $q1 = "SELECT c.name, c.category, cp.participantName AS winner FROM competitions c ";
            $q2 = "LEFT JOIN Competitions_Applications ca ON ca.competitionID = c.ID AND ca.participantID = ? AND ca.category = ? ";
            $q3 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
            $q4 = "WHERE ca.participantID IS NULL ";
            $q5 = "GROUP BY c.ID, c.name, c.category";

            $query = $q1 . $q2 . $q3 . $q4 . $q5;

            $st = static::$db->prepare($query);
            $st->bindValue(1, $_SESSION["USER"]["ID"]);
            $st->bindValue(2, $_SESSION["USER"]["role"]);
            $st->execute();
            
            $NONcompetitions = $st->fetchAll();
            $NONcompetitions = json_encode($NONcompetitions);
            
            echo View::make("competition list" , [
            "errorM" => null ,
            "competitions" => $competitions ,
            "NONcompetitions" => $NONcompetitions ,
            ]);
            
        }
        
        $q1 = "SELECT c.name , c.category , cp.participantName AS winner , COALESCE(MAX(cp.points) , 0) AS max FROM competitions c ";
        $q2 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID ";
        $q3 = "GROUP BY c.ID , c.name , c.category";

        $query = (string) $q1 . $q2 . $q3;

        $st = static::$db->query($query);

        $competitions = $st->fetchAll();
        $competitions = json_encode($competitions);


        echo View::make("competition list" , [
            "errorM" => null ,
            "competitions" => $competitions ,
            ]);
        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("competition list" , ["errorM" => $r->getMessage()]);

            return;
        }
    }
}