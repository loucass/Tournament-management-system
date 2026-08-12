<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\RouteNotFoundException;

/**
 * App — Application kernel.
 *
 * Bootstraps the router and dispatches the request.  Config loading and
 * database connection management have been delegated to the dedicated
 * Config and Database services.
 */
class App
{
    public function __construct(protected Router $router, protected array $request)
    {
        // Database is lazily initialised via Database::connect()
        // Config is lazily loaded via Config::get()
    }

    public static function setCookies(string $name, string|array $value, string $period): int|false
    {
        setcookie($name, $value, [
            "expires" => strtotime($period),
            "secure" => true,
            "httponly" => true,
            "samesite" => "none",
        ]);
        return strtotime($period);
    }

    public static function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . static::csrf_token() . '">' . "\n";
    }

    public static function verify_csrf(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }
        $token = $_POST['_csrf_token'] ?? '';
        if (empty($token) || !hash_equals(static::csrf_token(), $token)) {
            http_response_code(419);
            echo View::make("404", ["errorPath" => "CSRF token validation failed"]);
            exit();
        }
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve($this->request['uri'], strtolower($this->request['method']));
        } catch (RouteNotFoundException) {
            http_response_code(404);
            $errorPath = $this->request['uri'] . " " . strtolower($this->request['method']);
            echo View::make("404", ["errorPath" => $errorPath]);
        }
    }
}
