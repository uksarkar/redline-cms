<?php

namespace RedlineCms\Core\Support;

use RedlineCms\Core\Model\Theme;

class ThemeManager
{
    private static ?self $instance = null;
    private array $hooks = [];
    private array $functions = [];
    private ?Theme $theme;
    private static $namespace;

    // Private constructor to prevent direct instantiation
    private function __construct() {}

    // Retrieve the single instance of ThemeManager
    private static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static function getCurrentNamespace(): string
    {
        if (isset(static::$namespace)) {
            return static::$namespace;
        }

        return static::getInstance()->getTheme()->dir;
    }

    public static function getTheme(): Theme
    {
        $instance = static::getInstance();

        if (!isset($instance->theme)) {
            $instance->changeTheme("default");
        }

        return $instance->theme;
    }

    public static function hookExists(string $name, string $namespace = null): bool
    {
        $instance = static::getInstance();
        $namespace = $namespace ?? static::getCurrentNamespace();
        return array_key_exists($namespace, $instance->hooks) && array_key_exists($name, $instance->hooks[$namespace]);
    }

    public static function functionExists(string $name, string $namespace = null): bool
    {
        $instance = static::getInstance();
        $namespace = $namespace ?? static::getCurrentNamespace();
        return array_key_exists($namespace, $instance->hooks) && array_key_exists($name, $instance->functions[$namespace]);
    }

    // Method to add a hook
    public static function addHook(string $name, mixed $hook, string $namespace = null): void
    {
        $instance = self::getInstance();
        $instance->hooks[$namespace ?? static::getCurrentNamespace()][$name] = $hook;
    }

    // Method to add a function
    public static function addFunction(string $name, callable $callback, string $namespace = null): void
    {
        $instance = self::getInstance();
        $instance->functions[$namespace ?? static::getCurrentNamespace()][$name] = $callback;
    }

    // Retrieve a specific hook by name
    public static function getHook(string $name, string $namespace = null)
    {
        $instance = self::getInstance();
        return $instance->hooks[$namespace ?? static::getCurrentNamespace()][$name] ?? null;
    }

    // Retrieve a specific function by name
    public static function getFunction(string $name): ?callable
    {
        $instance = self::getInstance();
        return $instance->functions[$namespace ?? static::getCurrentNamespace()][$name] ?? null;
    }

    // Get all hooks
    public static function getAllHooks(string $namespace = null): array
    {
        return self::getInstance()->hooks[$namespace ?? static::getCurrentNamespace()];
    }

    // Get all functions
    public static function getAllFunctions(string $namespace = null): array
    {
        return self::getInstance()->functions[$namespace ?? static::getCurrentNamespace()];
    }

    // Clear hooks and functions (e.g., on theme switch)
    public static function clear(): void
    {
        $instance = self::getInstance();
        $instance->hooks = [];
        $instance->functions = [];
        $instance->theme = null;
    }

    public static function changeTheme(string $name): void
    {
        $instance = self::getInstance();
        // Clear previous hooks and functions
        $instance->clear();
        $instance->theme = static::getThemeData($name);

        // initial hooks
        if (static::hookExists("install")) {
            Hook::call("install");
        }
    }

    public static function getThemeData(string $name): Theme
    {
        if (!is_dir($themePath = Path::theme($name))) {
            throw new \Exception("No such theme in the '$themePath' path.");
        }

        static::$namespace = $name;

        // Load functions.php if it exists
        if (file_exists($functionsFile = Path::join($themePath, "functions.php"))) {
            include_once $functionsFile;
        }

        // Load hooks.php if it exists
        if (file_exists($hooksFile = Path::join($themePath, "hooks.php"))) {
            include_once $hooksFile;
        }

        $theme = [
            "name" => $name,
            "template_path" => "/",
            "screenshot" => "https://placehold.co/335x250?text=PREVIEW"
        ];

        if (static::hookExists("describe", $name)) {
            $theme = array_merge($theme, Hook::call("describe", $name));
        }
        $theme["dir"] = $name;

        static::$namespace = null;
        return new Theme(...$theme);
    }
}
