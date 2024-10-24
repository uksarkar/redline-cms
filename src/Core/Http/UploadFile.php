<?php

namespace RedlineCms\Core\Http;

class UploadFile
{
    public readonly string $path;
    public readonly string $name;
    public readonly string $ext;

    public function __construct(array $postFile)
    {
        $this->path = $postFile['tmp_name'];
        $this->name = basename($postFile['name']);
        $this->ext = pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public static function get(string $file): ?self
    {
        $postFile = $_FILES[$file] ?? null;
        if ($postFile && isset($postFile['tmp_name']) && !empty($postFile['tmp_name'])) {
            return new static($postFile);
        }

        return null;
    }
}
