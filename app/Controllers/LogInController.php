<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class LogInController
{

    private static \PDO $db;

    public function login(): void
    {
        try{
            static::$db = App::db();
            
            static::$db->beginTransaction();
            
            $st = static::$db->prepare('SELECT * FROM ' . $_POST["userRole"] . " WHERE email = ? AND password = ?");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userEmail" , FILTER_VALIDATE_EMAIL)));
            $st->bindValue(2 ,hash("sha256" , filter_input(INPUT_POST , "password" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();
            
            if(count($res) == 0){
                echo View::make("log in" , ["errorM" => "no such user"]);
                return;
            }
            $ID = $res[0]["ID"];
            
            $st = static::$db->prepare("SELECT * FROM tokens WHERE role = ? and userID = ?");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userRole" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , $ID);
            $st->execute();
            $res = $st->fetchAll();
            
            if(count($res) == 0){
                $token = authenticateController::create();
    
                $st = static::$db->prepare("INSERT INTO tokens VALUES(NULL , ? , ? , ?)");
                $st->bindValue(1 , $token);
                $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "userRole" , FILTER_SANITIZE_STRING)));
                $st->bindValue(3 , $ID );
                $st->execute();
            }else{
                $st = static::$db->prepare("UPDATE tokens SET token = ? WHERE role = ? and userID = ?");

                $st->bindValue(1 , authenticateController::refresh());
                $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "userRole" , FILTER_SANITIZE_STRING)));
                $st->bindValue(3 , $ID);
                $st->execute();

            }

            App::SetCookies("role" , $_POST["userRole"] , "+7 days");
            static::$db->commit();
            header("Location: /home");
            exit();

        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("log in" , ["errorM" => $r->getMessage()]);

            return;
        }
    }

    public function insertLogin(): View
    {
        return View::make("log in" , ["errorM"=>null]);
    }
}