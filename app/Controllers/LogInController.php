<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController;
use App\View;

class LogInController
{

    private static \PDO $db;

    public function login(): void
    {
        try {
            static::$db = App::db();
            static::$db->beginTransaction();

            $email = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            // Auto-detect role: check teams table first, then users table
            $userRole = '';
            $user = null;

            // Try teams table
            $st = static::$db->prepare("SELECT * FROM teams WHERE email = ?");
            $st->execute([$email]);
            $user = $st->fetch();

            if ($user && password_verify($password, $user["password"])) {
                $userRole = 'teams';
            } else {
                // Try users table (admin or student)
                $st = static::$db->prepare("SELECT * FROM users WHERE email = ?");
                $st->execute([$email]);
                $user = $st->fetch();

                if ($user && password_verify($password, $user["password"])) {
                    $userRole = $user["role"]; // 'admin' or 'student'
                }
            }

            if (!$user || !$userRole) {
                echo View::make("log in", ["errorM" => "invalid email or password"]);
                return;
            }

            $ID = $user["ID"];

            // Check existing token
            $st = static::$db->prepare("SELECT * FROM tokens WHERE role = ? AND userID = ?");
            $st->execute([$userRole, $ID]);
            $existingToken = $st->fetch();

            if (!$existingToken) {
                $token = authenticateController::create();
                $st = static::$db->prepare("INSERT INTO tokens VALUES(NULL, ?, ?, ?)");
                $st->execute([$token, $userRole, $ID]);
            } else {
                $st = static::$db->prepare("UPDATE tokens SET token = ? WHERE role = ? AND userID = ?");
                $st->execute([authenticateController::refresh(), $userRole, $ID]);
            }

            App::SetCookies("role", $userRole, "+7 days");

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            static::$db->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            static::$db->rollBack();
            echo View::make("log in", ["errorM" => "login failed. please try again."]);
            return;
        }
    }

    public function insertLogin(): View
    {
        return View::make("log in", ["errorM" => null]);
    }
}
