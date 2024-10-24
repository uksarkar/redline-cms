<?php

namespace RedlineCms\Core\Support;

use RedlineCms\Service\ViewContext;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class View
{
    public static function create(): Environment
    {
        $loader = new FilesystemLoader(Path::view()); // Admin view paths
        $loader->addPath(Path::theme(), "themes");

        return new Environment($loader, [
            'cache' => Path::storage("cache"), // Path for compiled templates
            'debug' => true, // Enable debug mode
        ]);
    }

    public static function render(string $name, array $context = [])
    {
        $context["version"] = "0.0.1";
        $context["appName"] = "Redline CMS";

        if (!isset($context["context"])) {
            $context["context"] = App::resolve(ViewContext::class);
        }

        return static::create()->render($name, $context);
    }
}
