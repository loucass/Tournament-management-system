<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Services\Database;

class authenticateController
{

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

        // Find token in database
        $st = Database::connect()->prepare("SELECT * FROM tokens WHERE token = ? AND role = ?");
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
            $st = Database::connect()->prepare("SELECT * FROM teams WHERE ID = ?");
            $st->execute([$userID]);
            $user = $st->fetch();
        } else {
            $st = Database::connect()->prepare("SELECT * FROM users WHERE ID = ?");
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
        $token = self::getToken();
        App::setCookies("JTK", $token, "+7 days");
        return $token;
    }

    public static function refresh()
    {
        $token = self::getToken();
        App::setCookies("JTK", $token, "+7 days");
        return $token;
    }

    private static function getToken(): string
    {
        $token = bin2hex(random_bytes(64));
        $token = hash("sha256", $token);
        return $token;
    }
}