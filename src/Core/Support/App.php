<?php

namespace RedlineCms\Core\Support;

use DI\Container;
use DI\ContainerBuilder;

final class App
{
    private static App $instance;
    
    private array $params;

    private function __construct(public readonly Container $container) {
        $this->params = [];
    }

    public static function setParams(array $params)
    {
        static::$instance->params += $params;
    }

    public static function getParams(): array
    {
        if(!isset(static::$instance)) {
            return [];
        }
        
        return static::$instance->params;
    }

    public static function init(array $dependencyDefinitions): void
    {
        if (isset(static::$instance)) {
            throw new \Exception("Multiple attempt to init the application");
        }

        Session::start();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            "appName" => "Redline CMS",
            "version" => "0.0.0",
            ...$dependencyDefinitions
        ]);

        static::$instance = new static($containerBuilder->build());
    }

    public static function getInstance(): self
    {
        return static::$instance;
    }

    public static function getContainer(): Container
    {
        return static::getInstance()->container;
    }

    /**
     * @param string|class-string<T> $id — Entry name or a class name.
     * 
     * @return mixed|T
     */
    public static function resolve(string $id)
    {
        return static::getContainer()->get($id);
    }
}
