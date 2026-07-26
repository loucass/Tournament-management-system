<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;

class authenticateController
{

    private static \PDO $db;

    public static function verify(bool $teacher = false)
    {
        if($teacher){
            if($_COOKIE["JTK"] && $_COOKIE["role"] == "teachers"){
                static::$db = App::db();
                $st = static::$db->prepare("SELECT * FROM tokens WHERE token = ? AND role = 'teachers'");
                $st->bindValue(1,$_COOKIE["JTK"]);
                $st->execute();
        
                $result = $st->fetchAll();
                
                if(count($result)>0){
        
                    $st = static::$db->prepare("SELECT * FROM teachers where ID = ?");
                    $st->bindValue(1, $result[0]["userID"]);
                    $st->execute();
                    $result = $st->fetchAll();
        
                    $_SESSION["USER"] = [
                        "ID"=>$result[0]["ID"],
                        "name"=>$result[0]["name"],
                        "email"=>$result[0]["email"],
                        "role"=> "teachers"
                        ];
                    return true;
                }
                return false;
            }
            return false;
        }else{
            // if not teacher
            if($_COOKIE["JTK"]){
                static::$db = App::db();
                $st = static::$db->prepare("SELECT * FROM tokens WHERE token = ? AND role = ?");
                $st->bindValue(1,$_COOKIE["JTK"]);
                $st->bindValue(2,$_COOKIE["role"]);
                $st->execute();
    
                $result = $st->fetchAll();
                
                if(count($result)>0){
    
                    $st = static::$db->prepare("SELECT * FROM " . $_COOKIE["role"] . " where ID = ?");
                    $st->bindValue(1, $result[0]["userID"]);
                    $st->execute();
                    $result = $st->fetchAll();
    
                    $_SESSION["USER"] = [
                        "ID"=>$result[0]["ID"],
                        "name"=>$result[0]["name"],
                        "email"=>$result[0]["email"],
                        "role"=>$_COOKIE["role"]
                        ];
                    return true;
                }
                return false;
            }
            return false;
        }
    }
    public static function create()
    {
        $token = static::getToken();
        App::SetCookies("JTK", $token, "+7 days");
        return $token;
    }
    public static function refresh()
    {
        $token = static::getToken();
        App::SetCookies("JTK", $token, "+7 days");
        return $token;
    }
    private static function getToken(): string
    {
        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $userIP = $_SERVER["REMOTE_ADDR"];
        $token = bin2hex(random_bytes(64));
        $token = hash("sha256" , (string) $userAgent . $userIP . $token);
        return $token;
    }
}