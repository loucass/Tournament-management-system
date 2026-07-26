<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\RouteNotFoundException;

class App
{
    private static \PDO $db;

    public function __construct(protected Router $router, protected array $request)
    {
        $config = $this->loadConfig();
        $dns = sprintf(
            "mysql:host=%s;dbname=%s;charset=utf8mb4",
            $config['DB_HOST'] ?? 'localhost',
            $config['DB_DATABASE'] ?? 'task_2'
        );
        static::$db = new \PDO($dns, $config['DB_USER'] ?? 'root', $config['DB_PASS'] ?? '');
        static::$db->setAttribute(\PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION);
        static::$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE , \PDO::FETCH_ASSOC);
    }

    private function loadConfig(): array
    {
        $envFile = dirname(__DIR__) . '/.env';
        if (!file_exists($envFile)) {
            return [];
        }
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $config = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }
        return $config;
    }

    public static function db(): \PDO
    {
        return static::$db;
    }

    public static function setCookies(string $name, string|array $value, string $period)
    {
        setcookie($name, $value, ["expires" => strtotime($period), "secure" => true, "httponly" => true, "samesite" => "none"]);
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

    public function run()
    {
        try {
            echo $this->router->resolve($this->request['uri'], strtolower($this->request['method']));
        } catch (RouteNotFoundException) {
            $errorPath =  $this->request['uri'] . " " . strtolower($this->request['method']);
            echo View::make("404" , ["errorPath" => $errorPath]);
        }
    }

}