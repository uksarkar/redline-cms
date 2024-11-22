<?php

namespace RedlineCms\Core\Support;

class Env
{
    private const ENV_CACHE_FILE_DIR = "core";
    private const ENV_CACHE_FILE_NAME = "cached.env.php";

    private static Env $instance;

    private function __construct(string $envPath = null)
    {
        $this->loadEnvWithCache($envPath);
    }

    private static function getInstance(string $envPath = null): self
    {
        if (!isset(static::$instance)) {
            static::$instance = new static($envPath);
        }

        return static::$instance;
    }

    private function loadEnvWithCache(string $envPath = null): void
    {
        if (!$envPath) {
            $envPath = Path::root(".env");
        }

        if (!file_exists($envPath)) {
            return;
        }

        $cachePath = Path::storage(static::ENV_CACHE_FILE_DIR, static::ENV_CACHE_FILE_NAME);

        $envModifiedTime = filemtime($envPath);
        $cacheModifiedTime = file_exists($cachePath) ? filemtime($cachePath) : 0;

        if ($envModifiedTime > $cacheModifiedTime) {
            // Regenerate cache
            $envVars = $this->parseEnvFile($envPath);
            $this->createEnvCache($envVars);
        } else {
            // Load from cache
            $envVars = require $cachePath;
        }

        // Set environment variables
        foreach ($envVars as $key => $value) {
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }

    private function parseEnvFile(string $filePath): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $envVars = [];

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Parse key=value
            [$key, $value] = explode('=', $line, 2);

            // Trim and strip quotes
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            // Apply type checking
            if (is_numeric($value)) {
                $envVars[$key] = strpos($value, '.') !== false ? (float)$value : (int)$value;
            } elseif (in_array(strtolower($value), ['true', 'false'], true)) {
                $envVars[$key] = strtolower($value) === 'true';
            } else {
                $envVars[$key] = (string)$value;
            }
        }

        return $envVars;
    }

    private function createEnvCache(array $envVars): void
    {
        $cacheContent = '<?php return ' . var_export($envVars, true) . ';';
        Storage::fsOf(Path::storage(self::ENV_CACHE_FILE_DIR))->write(self::ENV_CACHE_FILE_NAME, $cacheContent);
    }

    public static function int(string $envPath = null)
    {
        return static::getInstance($envPath);
    }

    public static function get(string $key, string $fallback = null)
    {
        return $_ENV[$key] ?? $fallback;
    }

    public static function set(string $key, mixed $value)
    {
        $_ENV[$key] = $value;
    }

    public static function merge(array $envs)
    {
        $_ENV = array_merge($_ENV, $envs);
    }

    public static function is(string $key, mixed $value)
    {
        return ($_ENV[$key] ?? null) === $value;
    }

    public static function has(string $key)
    {
        return array_key_exists($key, $_ENV);
    }
}
