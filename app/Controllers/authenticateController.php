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
        $allowedRoles = ['admin', 'student', 'teams'];
        if (!in_array($role, $allowedRoles)) {
            return false;
        }

        if (!$token) {
            return false;
        }

        static::$db = App::db();

        // Find token in database
        $st = static::$db->prepare("SELECT * FROM tokens WHERE token = ? AND role = ?");
        $st->execute([$token, $role]);
        $result = $st->fetch();

        if (!$result) {
            return false;
        }

        $userID = $result["userID"];

        // If teacher check is requested, verify admin role
        if ($teacher && $role !== 'admin') {
            return false;
        }

        // Fetch user from appropriate table
        if ($role === 'teams') {
            $st = static::$db->prepare("SELECT * FROM teams WHERE ID = ?");
            $st->execute([$userID]);
            $user = $st->fetch();
        } else {
            $st = static::$db->prepare("SELECT * FROM users WHERE ID = ?");
            $st->execute([$userID]);
            $user = $st->fetch();
        }

        if (!$user) {
            return false;
        }

        $_SESSION["USER"] = [
            "ID" => $user["ID"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role" => $role
        ];
        return true;
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
        $token = hash("sha256", $userAgent . $userIP . $token);
        return $token;
    }
}
