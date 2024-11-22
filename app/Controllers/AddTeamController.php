<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class AddTeamController
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
            $st = static::$db->prepare("SELECT * FROM teams WHERE email = ? and password = ?");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userEmail" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , hash("sha256" , strtolower($_POST['password'])));
            $st->execute();
            $res = $st->fetchAll();
            
            if(count($res) > 0){
                echo View::make("add team" , ["errorM" => "team has already created"]);
                return;
            }
            
            $st = static::$db->prepare("INSERT INTO teams VALUES(NULL , ? , ? , ?)");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userName" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "userEmail" , FILTER_SANITIZE_STRING)));
            $st->bindValue(3 , hash("sha256" , filter_input(INPUT_POST , "password" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();
            $teamID = static::$db->lastInsertId();

            foreach($_POST["students"] as $name){
                $st = static::$db->prepare("SELECT * FROM users WHERE name = ?");
                $st->bindValue(1 , strtolower(trim($name)));
                $st->execute();
                $res = $st->fetchAll();
                
                $st = static::$db->prepare("INSERT INTO teams_participants VALUES(NULL , ? , ? , ? , ?)");
                $st->bindValue(1 , $teamID);
                $st->bindValue(2 , $res[0]["name"]);
                $st->bindValue(3 , $res[0]["email"]);
                $st->bindValue(4 , $res[0]["password"]);
                $st->execute();
                
            }
            
            foreach($_POST["students"] as $name){
                $st = static::$db->prepare("SELECT * FROM users WHERE name = ?");
                $st->bindValue(1 , strtolower(trim($name)));
                $st->execute();
                $res = $st->fetchAll();
                
                $st = static::$db->prepare("DELETE FROM users WHERE ID = ?");
                $st->bindValue(1 , $res[0]["ID"]);
                $st->execute();
            }

            static::$db->commit();
            header("Location: /home");
            exit();
            
        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("add team" , ["errorM" => $r->getMessage()]);

            return;
        }
    }

    public function insert(): View
    {
        if(!authenticateController::verify(true)){
            header("Location: /logIn");
            exit();
        }

        return View::make("add team" , [
            "errorM" => null ,
            "students" => ApplyingPolicyController::verifyForTeams()
            ]);
    }
}