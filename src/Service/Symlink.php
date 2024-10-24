<?php

namespace RedlineCms\Service;

class Symlink
{
    public static function createSymlink($sourceDir, $targetDir)
    {
        if (!is_dir($sourceDir)) {
            throw new \Exception("Symlink source directory '$sourceDir' does not exist.");
        }

        if (!file_exists($targetDir)) {
            symlink($sourceDir, $targetDir);
        }

        return true;
    }
}
