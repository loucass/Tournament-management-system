<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Controllers\authenticateController;

final class AuthenticationIntegrationTest extends DatabaseTestCase
{
    public function testVerifyReturnsFalseWithoutToken(): void
    {
        $_COOKIE = ['JTK' => '', 'role' => 'student'];

        self::assertFalse(authenticateController::verify());
    }

    public function testVerifyRejectsTokenWithUntrustedRole(): void
    {
        $_COOKIE = ['JTK' => 'whatever', 'role' => "admin' OR '1'='1"];

        self::assertFalse(authenticateController::verify());
    }

    public function testVerifyRejectsTokenWithNoRole(): void
    {
        $_COOKIE = ['JTK' => 'whatever'];

        self::assertFalse(authenticateController::verify());
    }

    public function testVerifyAcceptsValidTokenForStudent(): void
    {
        $st = self::db()->prepare("SELECT ID FROM users WHERE role = 'student' LIMIT 1");
        $st->execute();
        $studentID = (int) $st->fetchColumn();

        $token = authenticateController::create();
        $st = self::db()->prepare('INSERT INTO tokens (token, role, userID) VALUES (?, ?, ?)');
        $st->execute([$token, 'student', $studentID]);

        $_COOKIE = ['JTK' => $token, 'role' => 'student'];
        unset($_SESSION['USER']);

        self::assertTrue(authenticateController::verify());
        self::assertSame($studentID, $_SESSION['USER']['ID']);
        self::assertSame('student', $_SESSION['USER']['role']);
    }

    public function testVerifyRejectsStaleToken(): void
    {
        $_COOKIE = ['JTK' => 'nonexistent-token', 'role' => 'student'];

        self::assertFalse(authenticateController::verify());
    }

    public function testCreateGeneratesUniqueHashedTokens(): void
    {
        $a = authenticateController::create();
        $b = authenticateController::create();

        self::assertNotSame($a, $b);
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $a);
        self::assertNotSame($a, hash('sha256', 'raw'));
    }
}