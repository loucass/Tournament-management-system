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

            // Assign selected students to the team with a single bulk query
            $studentNames = $_POST["students"] ?? [];
            if (!is_array($studentNames)) {
                $studentNames = [];
            }
            
            // Normalize and deduplicate names
            $studentNames = array_unique(array_map(fn($n) => strtolower(trim($n)), $studentNames));
            
            if (count($studentNames) > 0) {
                // Use bulk SELECT with FOR UPDATE for all students at once (prevents N+1 race conditions)
                $placeholders = implode(',', array_fill(0, count($studentNames), '?'));
                $st = static::$db->prepare("SELECT ID, name FROM users WHERE name IN ($placeholders) AND role = 'student' AND teamID IS NULL FOR UPDATE");
                $st->execute(array_values($studentNames));
                $availableStudents = $st->fetchAll();

                // Assign all available students to the team
                foreach ($availableStudents as $student) {
                    $st = static::$db->prepare("UPDATE users SET teamID = ? WHERE ID = ?");
                    $st->execute([$teamID, $student["ID"]]);
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
