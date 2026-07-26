<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Database — PDO connection manager.
 *
 * Provides a lazy-initialised singleton PDO instance.  Every call to
 * connect() returns the same connection so controllers don't need to
 * store their own $db property.
 */
class Database
{
    private static ?\PDO $connection = null;

    /**
     * Return the shared PDO connection, creating it on the first call.
     */
    public static function connect(): \PDO
    {
        if (self::$connection === null) {
            $host = Config::get('DB_HOST', 'localhost');
            $db   = Config::get('DB_DATABASE', 'task_2');
            $user = Config::get('DB_USER', 'root');
            $pass = Config::get('DB_PASS', '');

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $host,
                $db
            );

            self::$connection = new \PDO($dsn, $user, $pass);
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            self::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        }

        return self::$connection;
    }

    /**
     * Disconnect and reset the singleton (useful for testing).
     */
    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
