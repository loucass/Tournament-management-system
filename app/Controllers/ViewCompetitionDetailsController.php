<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class ViewCompetitionDetailsController
{

    private static \PDO $db;

    public function insert()
    {
        try{
            if(!authenticateController::verify(true)){
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();
            
            if (strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)) == "individuals") {

                $q1 = "SELECT c.name, c.category, u.name , u.ID , COALESCE(cp.points, 0) AS points FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "INNER JOIN users u ON ca.participantID = u.ID ";
                $q4 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = u.ID ";
                $q5 = "WHERE c.name = ? AND c.category = ? ";
                $q6 = "ORDER BY cp.points DESC;";

                $query = (string) $q1 . $q2 . $q3 . $q4 . $q5 . $q6;
                
                $st = static::$db->prepare($query);
                $st->bindValue(1,strtolower(filter_input(INPUT_GET , "competition" , FILTER_SANITIZE_STRING)));
                $st->bindValue(2,strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)));
        
                $st->execute();
        
                $competitionsDetails = $st->fetchAll();
                $competitionsDetails = json_encode($competitionsDetails);
        
                return View::make("competition dashboard" , [
                    "errorM" => null ,
                    "competitionsDetails" => $competitionsDetails ,
                ]);
                
            }else if(strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)) == "teams"){
                $q1 = "SELECT c.name, c.category, t.name , t.ID , COALESCE(cp.points, 0) AS points FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "LEFT JOIN teams t ON ca.participantID = t.ID ";
                $q4 = "LEFT JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = t.ID ";
                $q5 = "WHERE c.name = ? AND c.category = ? ";
                $q6 = "ORDER BY cp.points DESC;";

                $query = (string) $q1 . $q2 . $q3 . $q4 . $q5 . $q6;
                
                $st = static::$db->prepare($query);
                $st->bindValue(1,strtolower(filter_input(INPUT_GET , "competition" , FILTER_SANITIZE_STRING)));
                $st->bindValue(2,strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)));
        
                $st->execute();
        
                $competitionsDetails = $st->fetchAll();
                $competitionsDetails = json_encode($competitionsDetails);

        
                return View::make("competition dashboard" , [
                    "errorM" => null ,
                    "competitionsDetails" => $competitionsDetails ,
                ]);

            }else{
                return View::make("404");
            }
            
            if(strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)) == "individuals"){
                
            $q1 = "SELECT c.name, c.category, u.name , u.ID, cp.points FROM competitions c ";
            $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
            $q3 = "INNER JOIN users u ON ca.participantID = u.ID ";
            $q4 = "INNER JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = u.ID ";
            $q5 = "WHERE c.name = ? AND c.category = ? ";
            $q6 = "ORDER BY cp.points DESC;";

            $query = (string) $q1 . $q2 . $q3 . $q4 . $q5 . $q6;


            $st = static::$db->prepare($query);
            $st->bindValue(1,strtolower(filter_input(INPUT_GET , "competition" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2,strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)));

            $st->execute();

            $competitionsDetails = $st->fetchAll();
            $competitionsDetails = json_encode($competitionsDetails);
            
            return View::make("competition dashboard" , [
                "errorM" => null ,
                "competitionsDetails" => $competitionsDetails ,
            ]);
            
            }else if(strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)) == "teams"){
                $q1 = "SELECT c.name, c.category, t.name , t.ID , cp.points FROM competitions c ";
                $q2 = "LEFT JOIN competitions_applications ca ON ca.competitionID = c.ID ";
                $q3 = "INNER JOIN teams t ON ca.participantID = t.ID ";
                $q4 = "INNER JOIN competitions_points cp ON cp.competitionID = c.ID AND cp.participantID = t.ID ";
                $q5 = "WHERE c.name = ? AND c.category = ? ";
                $q6 = "ORDER BY cp.points DESC;";

                $query = (string) $q1 . $q2 . $q3 . $q4 . $q5 . $q6;


                $st = static::$db->prepare($query);
                $st->bindValue(1,strtolower(filter_input(INPUT_GET , "competition" , FILTER_SANITIZE_STRING)));
                $st->bindValue(2,strtolower(filter_input(INPUT_GET , "category" , FILTER_SANITIZE_STRING)));

                $st->execute();

                $competitionsDetails = $st->fetchAll();
                $competitionsDetails = json_encode($competitionsDetails);
                
                return View::make("competition dashboard" , [
                    "errorM" => null ,
                    "competitionsDetails" => null ,
                ]);
                
            }else{
                return View::make("404");
            }
        }catch(\Exception $r){
            return View::make("competition dashboard" , ["errorM" => $r->getMessage()]);
        }
    }
}