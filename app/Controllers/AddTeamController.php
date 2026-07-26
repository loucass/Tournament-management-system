<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Database;
use App\Controllers\authenticateController;
use App\View;

class AddTeamController
{

    public function add(): void
    {
        try {
            if (!authenticateController::verify(true)) {
                header("Location: /logIn");
                exit();
            }
            Database::connect()->beginTransaction();

            $teamName = strtolower(trim($_POST["userName"] ?? ''));
            $teamEmail = strtolower(trim($_POST["userEmail"] ?? ''));
            $password = $_POST["password"] ?? '';

            // Check for duplicate team email
            $st = Database::connect()->prepare("SELECT * FROM teams WHERE email = ?");
            $st->execute([$teamEmail]);
            if ($st->fetch()) {
                echo View::make("add team", ["errorM" => "team with this email already exists", "students" => null]);
                return;
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Create the team
            $st = Database::connect()->prepare("INSERT INTO teams (name, email, password) VALUES (?, ?, ?)");
            $st->execute([$teamName, $teamEmail, $hashedPassword]);
            $teamID = Database::connect()->lastInsertId();

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
                $st = Database::connect()->prepare("SELECT ID, name FROM users WHERE name IN ($placeholders) AND role = 'student' AND teamID IS NULL FOR UPDATE");
                $st->execute(array_values($studentNames));
                $availableStudents = $st->fetchAll();

                // Assign all available students to the team
                foreach ($availableStudents as $student) {
                    $st = Database::connect()->prepare("UPDATE users SET teamID = ? WHERE ID = ?");
                    $st->execute([$teamID, $student["ID"]]);
                }
            }

            Database::connect()->commit();
            header("Location: /home");
            exit();

        } catch (\Exception $r) {
            Database::connect()->rollBack();
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
