<?php

namespace RedlineCms\Core\Support;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function flashOut(string $key)
    {
        $value = $_SESSION[$key] ?? null;
        static::forget($key);

        return $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }
}
