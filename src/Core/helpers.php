<?php

if (!function_exists("add_hook")) {
    /**
     * Adds a hook to the ThemeManager.
     *
     * @param "describe"|"define_routes"|"define_admin_routes" $name The name of the hook to add. Possible values:
     *   - "describe": Used for describing the theme structure.
     *   - "define_routes": Used for defining public routes.
     *   - "define_admin_routes": Used for defining admin-specific routes.
     * @param mixed $hook The hook implementation, which can be any callable or other expected type.
     *
     * @return void
     */
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
