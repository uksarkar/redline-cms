<?php

namespace RedlineCms\Core\Model;

use RedlineCms\Core\Support\Path;
use RedlineCms\Core\Support\ThemeManager;

class Theme
{
    public function __construct(
        public readonly string $dir,
        public readonly string $name,
        public readonly string $template_path,
        public readonly string $screenshot,
        public readonly ?string $description = null,
        public readonly ?string $author = null,
    ) {}

    public function getTemplate(string $name): string
    {
        return ltrim(Path::join("@themes", $this->dir, $this->template_path, $name), "/");
    }

    public function isActive(): bool
    {
        return ThemeManager::getTheme()->dir === $this->dir;
    }
}
