<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController;
use App\View;

class AddTeamController
{

    private static \PDO $db;

    public function add(): void
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }
            static::$db = App::db();

            static::$db->beginTransaction();

            $teamName = strtolower(trim($_POST["userName"] ?? ''));
            $teamEmail = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            // Check for duplicate team email
            $st = static::$db->prepare("SELECT * FROM teams WHERE email = ?");
            $st->execute([$teamEmail]);
            if ($st->fetch()) {
                echo View::make("add team", ["errorM" => "team with this email already exists", "students" => null]);
                return;
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Create the team
            $st = static::$db->prepare("INSERT INTO teams (name, email, password) VALUES (?, ?, ?)");
            $st->execute([$teamName, $teamEmail, $hashedPassword]);
            $teamID = static::$db->lastInsertId();

            // Assign selected students to the team
            // Use SELECT ... FOR UPDATE to prevent race condition (TOCTOU)
            $studentNames = $_POST["students"] ?? [];
            if (is_array($studentNames) && count($studentNames) > 0) {
                foreach ($studentNames as $name) {
                    $name = strtolower(trim($name));

                    // Lock the row to prevent concurrent updates
                    $st = static::$db->prepare("SELECT * FROM users WHERE name = ? AND role = 'student' AND teamID IS NULL FOR UPDATE");
                    $st->execute([$name]);
                    $student = $st->fetch();

                    if ($student) {
                        // Assign student to team
                        $st = static::$db->prepare("UPDATE users SET teamID = ? WHERE ID = ?");
                        $st->execute([$teamID, $student["ID"]]);
                    }
                }
            }

            static::$db->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            static::$db->rollBack();
            echo View::make("add team", ["errorM" => "failed to create team. please try again.", "students" => null]);
            return;
        }
    }

    public function insert(): View
    {
        if (!authenticateController::verify(true)) {
            header("Location: /logIn");
            exit();
        }

        // Get students without a team (who can still be assigned)
        $students = ApplyingPolicyController::verifyForTeams();

        return View::make("add team", [
            "errorM" => null,
            "students" => $students
        ]);
    }
}
