<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class AddStudentController
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
            $st = static::$db->prepare("SELECT * FROM users WHERE email = ? and password = ?");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userEmail" , FILTER_VALIDATE_EMAIL)));
            $st->bindValue(2 , hash("sha256" , filter_input(INPUT_POST , "password" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();
            
            if(count($res) > 0){
                echo View::make("log in" , ["errorM" => "user has already saved"]);
                return;
            }
            
            $st = static::$db->prepare("INSERT INTO users VALUES(NULL , ? , ? , ?)");
            $st->bindValue(1 , strtolower(filter_input(INPUT_POST , "userName" , FILTER_SANITIZE_STRING)));
            $st->bindValue(2 , strtolower(filter_input(INPUT_POST , "userEmail" , FILTER_SANITIZE_STRING)));
            $st->bindValue(3 , hash("sha256" , filter_input(INPUT_POST , "password" , FILTER_SANITIZE_STRING)));
            $st->execute();
            $res = $st->fetchAll();

            static::$db->commit();
            header("Location: /home");
            exit();

        }catch(\Exception $r){
            static::$db->rollBack();

            echo View::make("add student" , ["errorM" => $r->getMessage()]);

            return;
        }
    }

    public function insert(): View
    {
        if(!authenticateController::verify(true)){
            header("Location: /logIn");
            exit();
        }
        return View::make("add student" , ["errorM"=>null]);
    }
}