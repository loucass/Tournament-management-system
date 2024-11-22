<?php

//http://127.0.1.1:8080/

declare(strict_types=1);

namespace App;

use App\Exceptions\RouteNotFoundException;

class App
{
    private static \PDO $db;

    public function __construct(protected Router $router, protected array $request)
    {
        $dns= "mysql:host=localhost;dbname=task_2;charset=utf8mb4";
        static::$db = new \PDO($dns, "root", "");
        static::$db->setAttribute(\PDO::ATTR_ERRMODE , \PDO::ERRMODE_EXCEPTION);
        static::$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE , \PDO::FETCH_ASSOC);
    }

    public static function db(): \PDO
    {
        return static::$db;
    }

    public static function SetCookies(string $name, string|array $value, string $period)
    {
        setcookie($name, $value, ["expires" => strtotime($period), "secure" => true, "httponly" => true, "samesite" => "none"]);
        return strtotime($period);
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

    public static function convertToArray(array $data , string $field):string
    {
        $resultOfArray = "[";
        foreach($data as $value){
            $resultOfArray .= " ' " . $value[$field] . " ' ,";
        }
        $lengthOfSTR = strlen($resultOfArray)-2;
        $resultOfArray = substr_replace($resultOfArray , ""  , $lengthOfSTR , 2);
        $resultOfArray .= "]";
        return $resultOfArray;
    }
}