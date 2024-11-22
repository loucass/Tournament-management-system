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
            App::SetCookies($key , $value , "-1 days");
        }
        header("Location: /");
        exit();
    }
}