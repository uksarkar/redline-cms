<?php

namespace RedlineCms\Core\Support;

class Path {
    /**
     * The project root location
     */
    public static function root(): string
    {
        return realpath(__DIR__ . "/../../../");
    }

    /**
     * Join multiple path segments into one path
     */
    public static function join(...$paths): string
    {
        return array_reduce($paths, function ($carry, $path) {
            return rtrim($carry, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }, '');
    }

    /**
     * Get the path to the storage directory, with optional subdirectories
     */
    public static function storage(...$paths): string
    {
        return static::join(static::root(), "storage", ...$paths);
    }

    /**
     * Get the path to the views directory, with optional subdirectories
     */
    public static function view(...$path): string
    {
        return static::join(static::root(), "views", ...$path);
    }

    /**
     * Path to the /src folder
     */
    public static function src(...$paths): string
    {
        return static::join(static::root(), "src", ...$paths);
    }

    public static function public(...$paths): string
    {
        return static::join(getcwd(), ...$paths);
    }

    public static function theme(...$paths): string
    {
        return static::join(static::root(), "themes", ...$paths);
    }
}
