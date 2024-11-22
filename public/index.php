<?php

declare(strict_types=1);
namespace App;

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

session_start();

require '../app/Router.php';
require '../app/View.php';
require '../app/App.php';
//require '../app/Exceptions/AuthenticationFailedException.php';
//require '../app/Exceptions/RouteNotFoundException.php';

// controllers
require '../app/Controllers/HomeController.php';
require '../app/Controllers/AuthenticateController.php';
require '../app/Controllers/ApplyingPolicyController.php';

require '../app/Controllers/LogInController.php';
require '../app/Controllers/LogoutController.php';
require '../app/Controllers/AddStudentController.php';
require '../app/Controllers/AddTeamController.php';
require '../app/Controllers/AddCompetitionController.php';
require '../app/Controllers/JoinCompetitionController.php';
require '../app/Controllers/ViewCompetitionController.php';
require '../app/Controllers/ViewCompetitionDetailsController.php';
require '../app/Controllers/EditPointsController.php';

// $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
// $dotenv->load();

define('STORAGE_PATH', __DIR__ . '/../storage');
define('VIEW_PATH', __DIR__ . '/../views');

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

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
        ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']]
    )
)->run();