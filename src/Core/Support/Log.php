<?php

namespace RedlineCms\Core\Support;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Log
{
    private static self $instance;

    private Logger $logger;

    private function __construct()
    {
        $this->logger = new Logger("Redline-CMS");
        $this->logger->pushHandler(new StreamHandler(Path::storage("logs/info.log"), Level::Info));
    }

    private static function getInstance(): Log
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function getLogger(): Logger
    {
        return static::getInstance()->logger;
    }

    public static function info(string|\Stringable $message, array $context = [])
    {
        return static::getLogger()->info($message, $context);
    }
}
