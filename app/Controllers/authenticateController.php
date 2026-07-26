<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;

class authenticateController
{

    private static \PDO $db;

    public static function verify(bool $teacher = false)
    {
        $token = $_COOKIE["JTK"] ?? '';
        $role = $_COOKIE["role"] ?? '';

        // Whitelist allowed roles to prevent SQL injection
        $allowedRoles = ['teachers', 'users', 'teams', 'teams_participants'];
        if (!in_array($role, $allowedRoles)) {
            $role = '';
        }

        if($teacher){
            if($token && $role == "teachers"){
                static::$db = App::db();
                $st = static::$db->prepare("SELECT * FROM tokens WHERE token = ? AND role = 'teachers'");
                $st->bindValue(1, $token);
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
            if($token){
                static::$db = App::db();
                $st = static::$db->prepare("SELECT * FROM tokens WHERE token = ? AND role = ?");
                $st->bindValue(1, $token);
                $st->bindValue(2, $role);
                $st->execute();
    
                $result = $st->fetchAll();
                
                if(count($result)>0){
    
                    $st = static::$db->prepare("SELECT * FROM " . $role . " where ID = ?");
                    $st->bindValue(1, $result[0]["userID"]);
                    $st->execute();
                    $result = $st->fetchAll();
    
                    $_SESSION["USER"] = [
                        "ID"=>$result[0]["ID"],
                        "name"=>$result[0]["name"],
                        "email"=>$result[0]["email"],
                        "role"=> $role
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
        $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? 'cli';
        $userIP = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
        $token = bin2hex(random_bytes(64));
        $token = hash("sha256" , (string) $userAgent . $userIP . $token);
        return $token;
    }
}