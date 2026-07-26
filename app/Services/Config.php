<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Config — Loads environment variables from .env and provides typed access.
 *
 * Loads on first call to get() and caches the result for the lifetime
 * of the request.  Falls back to real environment variables so Docker /
 * production deployments can override without a .env file.
 */
class Config
{
    private static ?array $config = null;
    private static string $envPath = '';

    /**
     * Explicitly load (or reload) configuration from a .env file path.
     * If $path is null, tries the project root .env file.
     */
    public static function load(?string $path = null): void
    {
        self::$envPath = $path ?? dirname(__DIR__, 2) . '/.env';
        self::$config = [];

        if (!file_exists(self::$envPath)) {
            return; // All get() calls will fall through to real env-vars
        }

        $lines = file(self::$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, '[')) {
                continue; // Skip blank lines, comments, and INI section headers
            }
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        }
    }

    /**
     * Get a config value by key.
     *
     * Priority:  .env file value → real environment variable → $default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$config === null) {
            self::load();
        }

        // 1. Return cached value from .env
        if (array_key_exists($key, self::$config ?? [])) {
            return self::$config[$key];
        }

        // 2. Fall back to real environment variable
        $envValue = getenv($key);
        if ($envValue !== false) {
            return $envValue;
        }

        // 3. Default
        return $default;
    }

    /**
     * Return all loaded config as an array (useful for debugging).
     */
    public static function all(): array
    {
        if (self::$config === null) {
            self::load();
        }
        return self::$config ?? [];
    }
}
