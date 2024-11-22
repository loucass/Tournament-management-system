<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class EditPointsController
{

    private static \PDO $db;

    public function update()
    {
        static::$db = App::db();
    
        try{
            if(!authenticateController::verify(true)){
                header("Location: /logIn");
                exit();
            }
            static::$db->beginTransaction();
            
            $query = "SELECT c.ID competitionID FROM competitions c WHERE c.category = ? AND c.name = ? ";

            $st = static::$db->prepare($query);
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "category" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "competitionName" , FILTER_SANITIZE_STRING)));
            $st->execute();
            
            $competitionID = ($st->fetchAll())[0]["competitionID"];
            
            $query = "SELECT * FROM competitions_points WHERE participantID = ? AND competitionID = ?";

            $st = static::$db->prepare($query);
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "ID" , FILTER_VALIDATE_INT)));
            $st->bindValue(2 , $competitionID);
            $st->execute();
            
            if(count($st->fetchAll()) > 0){
                $query = "UPDATE competitions_points SET points = ? WHERE participantID = ? AND competitionID = ?";
                
                $st = static::$db->prepare($query);
                $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "points" , FILTER_VALIDATE_INT)));
                $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "ID" , FILTER_VALIDATE_INT)));
                $st->bindValue(3 , $competitionID);
                $st->execute();

                echo "done";

                static::$db->commit();
                
                return;
            }
            
            $query = "INSERT INTO competitions_points VALUES(NULL, ?, ?, ?, ?, ?)";

            $st = static::$db->prepare($query);
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "ID" , FILTER_VALIDATE_INT)));
            $st->bindValue(2 , $competitionID);
            $st->bindValue(3 , strtolower(filter_input(INPUT_POST , "competitionName" , FILTER_SANITIZE_STRING)));
            $st->bindValue(4 , strtolower(filter_input(INPUT_POST , "participantName" , FILTER_SANITIZE_STRING)));
            $st->bindValue(5 , strtolower(filter_input(INPUT_POST , "points" , FILTER_VALIDATE_INT)));
            $st->execute();

            echo "done";

            static::$db->commit();

        }catch(\PDOException $e){
            echo "error \n";
            echo $e->getMessage();
            static::$db->rollBack();
        }
        return;
    }
}