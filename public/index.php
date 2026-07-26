<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\Controllers\AddCompetitionController;
use App\Controllers\AddStudentController;
use App\Controllers\AddTeamController;
use App\Controllers\EditPointsController;
use App\Controllers\HomeController;
use App\Controllers\JoinCompetitionController;
use App\Controllers\LogInController;
use App\Controllers\LogoutController;
use App\Controllers\ViewCompetitionController;
use App\Controllers\ViewCompetitionDetailsController;
use App\Router;

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');

// Security headers (Helmet-style)
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
header_remove("X-Powered-By");

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");

$router = new Router();

$router
    ->get('/', [HomeController::class, 'index'])
    ->get('/home', [HomeController::class, 'index'])

    ->get('/logIn', [LogInController::class, 'insertLogin'])
    ->post('/logIn', [LogInController::class, 'login'])

    ->get('/addStudent', [AddStudentController::class, 'insert'])
    ->post('/addStudent', [AddStudentController::class, 'add'])

    ->get('/addTeam', [AddTeamController::class, 'insert'])
    ->post('/addTeam', [AddTeamController::class, 'add'])

    ->get('/addCompetition', [AddCompetitionController::class, 'insert'])
    ->post('/addCompetition', [AddCompetitionController::class, 'add'])

    ->get('/joinCompetition', [JoinCompetitionController::class, 'insert'])
    ->post('/joinCompetition', [JoinCompetitionController::class, 'join'])

    ->get('/viewCompetition', [ViewCompetitionController::class, 'insert'])

    ->get('/viewCompetitionDetails', [ViewCompetitionDetailsController::class, 'insert'])

    ->post('/editPoints', [EditPointsController::class, 'update'])

    ->get('/logout', [LogoutController::class, 'logout'])
;

(
    new App(
        $router,
        ['uri' => $_SERVER['REQUEST_URI'] ?? '/', 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']
    )
)->run();