<?php

namespace RedlineCms\Core\Support;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use RedlineCms\Core\Http\UploadFile;

class Storage
{
    private static $instance;

    private Filesystem $driver;

    private function __construct()
    {
        $adapter = new LocalFilesystemAdapter(Path::storage("public"));
        $this->driver = new Filesystem($adapter);
    }

    private static function getInstance(): self
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function upload(string $path, string $name, UploadFile $file)
    {
        $stream = fopen($file->path, 'r+');
        $path = Path::join($path, $name);

        static::getInstance()->driver->writeStream($path, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $path;
    }
}
