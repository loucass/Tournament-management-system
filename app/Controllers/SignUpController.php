<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;
use App\View;

class SignUpController
{

    public function signup(): void
    {
        try {
            Database::connect()->beginTransaction();

            $name = strtolower(trim($_POST["userName"] ?? ''));
            $email = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                echo View::make("sign in", ["errorM" => "all fields are required"]);
                return;
            }

            // Check for duplicate email
            $st = Database::connect()->prepare("SELECT ID FROM users WHERE email = ?");
            $st->execute([$email]);
            if ($st->fetch()) {
                echo View::make("sign in", ["errorM" => "an account with this email already exists"]);
                return;
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert as student
            $st = Database::connect()->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $st->execute([$name, $email, $hashedPassword]);

            Database::connect()->commit();

            // Auto-login after signup
            $_SESSION["signup_success"] = true;
            header("Location: /logIn?registered=1");
            exit();

        } catch (\Exception $r) {
            Database::connect()->rollBack();
            echo View::make("sign in", ["errorM" => "registration failed. please try again."]);
            return;
        }
    }

    public function showForm(): View
    {
        $registered = isset($_GET['registered']);
        return View::make("sign in", [
            "errorM" => null,
            "registered" => $registered
        ]);
    }
}
