<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use RedlineCms\Core\Support\Path;

#[Entity(repository: \RedlineCms\Repository\ConfigRepository::class)]
class Config
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'string')]
    private string $appName;

    #[Column(type: 'string')]
    private string $favicon;

    #[Column(type: 'string')]
    private string $logo;

    #[Column(type: 'integer', default: 1)]
    private int $status;

    #[Column(type: 'string', default: 'default')]
    private ?string $theme;

    public function __construct(string $appName, string $favicon, string $logo, int $status = 1)
    {
        $this->appName = $appName;
        $this->favicon = $favicon;
        $this->logo = $logo;
        $this->status = $status;
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getAppName(): string
    {
        return $this->appName;
    }

    public function getFavicon(): string
    {
        return Path::join("/storage", $this->favicon);
    }

    public function getLogo(): string
    {
        return Path::join("/storage", $this->logo);
    }

    public function getFaviconPath(): string
    {
        return $this->favicon;
    }

    public function getLogoPath(): string
    {
        return $this->logo;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getTheme(): string
    {
        return $this->theme ?? "default";
    }

    public function setFavicon(string $path)
    {
        $this->favicon = $path;
    }

    public function setLogo(string $path)
    {
        $this->logo = $path;
    }

    public function setAppName(string $name)
    {
        $this->appName = $name;
    }

    public function setTheme(string $theme)
    {
        $this->theme = $theme;
    }
}
