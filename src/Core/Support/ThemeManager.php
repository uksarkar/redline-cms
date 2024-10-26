<?php

namespace RedlineCms\Core\Support;

use RedlineCms\Core\Model\Theme;

class ThemeManager
{
    private static ?self $instance = null;
    private array $hooks = [];
    private array $functions = [];
    private ?Theme $theme;

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

    public static function getTheme(): Theme
    {
        $instance = static::getInstance();

        if (!isset($instance->theme)) {
            $instance->changeTheme("default");
        }

        return $instance->theme;
    }

    public static function hookExists(string $name): bool
    {
        return array_key_exists($name, static::getInstance()->hooks);
    }

    public static function functionExists(string $name): bool
    {
        return array_key_exists($name, static::getInstance()->functions);
    }

    // Method to add a hook
    public static function addHook(string $name, mixed $callback): void
    {
        $instance = self::getInstance();
        $instance->hooks[$name][] = $callback;
    }

    // Method to add a function
    public static function addFunction(string $name, callable $callback): void
    {
        $instance = self::getInstance();
        $instance->functions[$name] = $callback;
    }

    // Retrieve a specific hook by name
    public static function getHook(string $name): ?array
    {
        $instance = self::getInstance();
        return $instance->hooks[$name] ?? null;
    }

    // Retrieve a specific function by name
    public static function getFunction(string $name): ?callable
    {
        $instance = self::getInstance();
        return $instance->functions[$name] ?? null;
    }

    // Get all hooks
    public static function getAllHooks(): array
    {
        return self::getInstance()->hooks;
    }

    // Get all functions
    public static function getAllFunctions(): array
    {
        return self::getInstance()->functions;
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

        if (!is_dir($themePath = Path::theme($name))) {
            throw new \Exception("No such theme in the '$themePath' path.");
        }

        // Clear previous hooks and functions
        $instance->clear();

        // Load functions.php if it exists
        if (file_exists($functionsFile = Path::join($themePath, "functions.php"))) {
            include_once $functionsFile;
        }

        // Load hooks.php if it exists
        if (file_exists($hooksFile = Path::join($themePath, "hooks.php"))) {
            include_once $hooksFile;
        }

        if (static::hookExists("install")) {
            Hook::call("install");
        }

        $theme = [
            "name" => $name,
            "template_path" => "/",
            "screenshot" => "https://placehold.co/400?text=PREVIEW"
        ];

        if (static::hookExists("describe")) {
            $theme = array_merge($theme, Hook::call("describe"));
        }

        $theme["dir"] = $name;
        $instance->theme = new Theme(...$theme);
    }
}
