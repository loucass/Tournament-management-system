<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\View;

class SignUpController
{
    private static \PDO $db;

    public function signup(): void
    {
        try {
            static::$db = App::db();
            static::$db->beginTransaction();

            $name = strtolower(trim($_POST["userName"] ?? ''));
            $email = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                echo View::make("sign in", ["errorM" => "all fields are required"]);
                return;
            }

            // Check for duplicate email
            $st = static::$db->prepare("SELECT ID FROM users WHERE email = ?");
            $st->execute([$email]);
            if ($st->fetch()) {
                echo View::make("sign in", ["errorM" => "an account with this email already exists"]);
                return;
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert as student
            $st = static::$db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $st->execute([$name, $email, $hashedPassword]);

            static::$db->commit();

            // Auto-login after signup
            $_SESSION["signup_success"] = true;
            header("Location: /logIn?registered=1");
            exit();

        } catch (\Exception $r) {
            static::$db->rollBack();
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
