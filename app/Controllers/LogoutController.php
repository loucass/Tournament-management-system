<?php

declare(strict_types=1);

namespace App\Controllers;

use App\App;
use App\Controllers\authenticateController ;
use App\View;

class LogoutController
{

    public function logout(): void
    {
        foreach($_COOKIE as $key => $value){
            App::setCookies($key , $value , "-1 days");
        }
        // Clear session completely
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: /");
        exit();
    }
}