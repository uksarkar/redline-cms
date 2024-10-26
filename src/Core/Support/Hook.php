<?php

namespace RedlineCms\Core\Support;

use Exception;
use RedlineCms\Core\Http\Route;
use RedlineCms\Middleware\AuthMiddleware;

class Hook
{
    const DEFINITIONS = [
        "describe" => "array",
        "install" => "void",
        "version" => "string",
        "define_routes" => "array",
        "define_admin_routes" => "array",
    ];

    /**
     * Call the specified hook, ensuring its type matches the defined type.
     *
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public static function call(string $name, string $namespace = null)
    {
        if (!array_key_exists($name, static::DEFINITIONS)) {
            throw new Exception("Unrecognized hook: '$name'. Check available hook names in DEFINITIONS.");
        }

        if (!ThemeManager::hookExists($name, $namespace)) {
            throw new Exception("No such hook registered for name: '$name'.");
        }

        $hook = ThemeManager::getHook($name, $namespace);

        if (is_callable($hook)) {
            $hook = self::invokeCallback($hook);
        }

        if (!self::validateType($hook, self::DEFINITIONS[$name])) {
            throw new Exception("Hook '$name' returned data of invalid type. Expected type: " . self::DEFINITIONS[$name]);
        }

        return $hook;
    }

    public static function call_define_routes(): void
    {
        if (!ThemeManager::hookExists("define_routes")) {
            return;
        }

        foreach (static::call("define_routes") as $route) {
            Route::add($route["method"] ?? "get", Path::join("custom", $route["path"]), $route["handler"], null);
        }
    }

    public static function call_define_admin_routes(): array
    {
        if (!ThemeManager::hookExists("define_admin_routes")) {
            return [];
        }

        $sidebarItems = [];
        foreach (static::call("define_admin_routes") as $route) {
            $path = Path::join("admin", "custom", $route["path"]);
            Route::add($route["method"] ?? ['GET', 'HEAD'], $path, $route["handler"], null)->middleware(AuthMiddleware::class);

            if (isset($route["label"])) {
                $sidebarItems[] = [
                    "label" => $route["label"],
                    "to" => $path,
                    "icon" => $route["icon"] ?? null
                ];
            }
        }

        return $sidebarItems;
    }

    /**
     * Invoke a callback, resolving dependencies if callable.
     *
     * @param callable $callback
     * @return mixed
     */
    protected static function invokeCallback(callable $callback)
    {
        return App::getContainer()->call($callback);
    }

    /**
     * Validates the hook return type against the expected type.
     *
     * @param mixed $value
     * @param string $expectedType
     * @return bool
     */
    protected static function validateType($value, string $expectedType): bool
    {
        switch ($expectedType) {
            case 'array':
                return is_array($value);
            case 'string':
                return is_string($value);
            case 'int':
                return is_int($value);
            case 'bool':
                return is_bool($value);
            case 'void':
                return $value === null;
            default:
                // Handle object class types
                if (class_exists($expectedType)) {
                    return $value instanceof $expectedType;
                }
                return gettype($value) === $expectedType;
        }
    }
}
