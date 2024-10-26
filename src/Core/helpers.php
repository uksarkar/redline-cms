<?php

if (!function_exists("add_hook")) {
    function add_hook(string $name, mixed $hook)
    {
        \RedlineCms\Core\Support\ThemeManager::addHook($name, $hook);
    }
}

if (!function_exists("add_function")) {
    function add_function(string $name, callable $fn)
    {
        \RedlineCms\Core\Support\ThemeManager::addFunction($name, $fn);
    }
}
