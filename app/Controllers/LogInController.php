<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Services\Database;
use App\Controllers\authenticateController;
use App\View;

class LogInController
{

    public function login(): void
    {
        try {
            Database::connect()->beginTransaction();

            $email = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            // Auto-detect role: check teams table first, then users table
            $userRole = '';
            $user = null;

            // Try teams table
            $st = Database::connect()->prepare("SELECT * FROM teams WHERE email = ?");
            $st->execute([$email]);
            $user = $st->fetch();

            if ($user && password_verify($password, $user["password"])) {
                $userRole = 'teams';
            } else {
                // Try users table (admin or student)
                $st = Database::connect()->prepare("SELECT * FROM users WHERE email = ?");
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
            $st = Database::connect()->prepare("SELECT * FROM tokens WHERE role = ? AND userID = ?");
            $st->execute([$userRole, $ID]);
            $existingToken = $st->fetch();

            if (!$existingToken) {
                $token = authenticateController::create();
                $st = Database::connect()->prepare("INSERT INTO tokens VALUES(NULL, ?, ?, ?)");
                $st->execute([$token, $userRole, $ID]);
            } else {
                $st = Database::connect()->prepare("UPDATE tokens SET token = ? WHERE role = ? AND userID = ?");
                $st->execute([authenticateController::refresh(), $userRole, $ID]);
            }

            App::setCookies("role", $userRole, "+7 days");

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            Database::connect()->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            Database::connect()->rollBack();
            echo View::make("log in", ["errorM" => "login failed. please try again."]);
            return;
        }
    }

    public function insertLogin(): View
    {
        return View::make("log in", ["errorM" => null]);
    }
}
