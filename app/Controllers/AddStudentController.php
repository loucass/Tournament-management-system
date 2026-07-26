<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;
use App\Controllers\authenticateController;
use App\View;

class AddStudentController
{

    public function add(): void
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }
            Database::connect()->beginTransaction();

            $name = strtolower(trim($_POST["userName"] ?? ''));
            $email = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            // Check for duplicate email
            $st = Database::connect()->prepare("SELECT * FROM users WHERE email = ?");
            $st->execute([$email]);
            $existing = $st->fetch();

            if ($existing) {
                echo View::make("add student", ["errorM" => "user with this email already exists"]);
                return;
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert as student (unified users table)
            $st = Database::connect()->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $st->execute([$name, $email, $hashedPassword]);

            Database::connect()->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            Database::connect()->rollBack();
            echo View::make("add student", ["errorM" => "failed to add student. please try again."]);
            return;
        }
    }

    public function insert(): View
    {
        if (!authenticateController::verify(true)) {
            header("Location: /logIn");
            exit();
        }
        return View::make("add student", ["errorM" => null]);
    }
}
