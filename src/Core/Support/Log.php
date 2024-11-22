<?php

namespace RedlineCms\Core\Support;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Log
{
    private static self $instance;

    // the current active channel
    private null|string $channel;

    // the default logger
    private Logger $logger;

    /**
     * @var array<Logger> secondary loggers
     */
    private array $loggers = [];

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

    private static function getLogger(bool $fallback = false): ?Logger
    {
        if (static::getInstance()->channel) {
            $logger = static::getInstance()->loggers[static::getInstance()->channel] ?? null;
            return $logger ? $logger : ($fallback ? static::getInstance()->logger : null);
        }

        return static::getInstance()->logger;
    }

    private static function onChannel(?string $channel, callable $cb)
    {
        static::getInstance()->channel = $channel;
        $result = $cb();
        static::getInstance()->channel = null;

        return $result;
    }

    public static function info(string|\Stringable $message, array $context = [])
    {
        static::getLogger()->info($message, $context);
    }

    public static function addLogger(string $channel, string $name)
    {
        $logger = new Logger($name);
        static::getInstance()->loggers[$channel] = $logger;
    }

    public static function addHandler(string $channel, HandlerInterface $handler)
    {
        static::onChannel($channel, function () use ($handler) {
            static::getInstance()->getLogger()?->pushHandler($handler);
        });
    }

    public static function logger(string $channel): ?Logger
    {
        return static::onChannel($channel, fn() => static::getLogger());
    }
}
