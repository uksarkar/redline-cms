<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use RedlineCms\Core\Support\ThemeManager;

#[Entity(repository: \RedlineCms\Repository\ThemeMetaRepository::class)]
class ThemeMeta extends BaseEntity
{
    private null|string|array $parsedData = null;

    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'string')]
    private string $theme;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'text')]
    private string $data;

    public function __construct(string $name, array|string $data)
    {
        $this->name = $name;
        $this->data = is_array($data) ? json_encode($data) : $data;
        $this->theme = ThemeManager::getTheme()->dir;
        $this->parsedData = $data;
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): string|array
    {
        if ($this->parsedData) {
            return $this->parsedData;
        }

        $data = json_decode($this->data, true);
        if ($data) {
            $this->parsedData = $data;
        } else {
            $this->parsedData = $data;
        }

        return $this->parsedData;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    // Setters
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setData(string|array $data)
    {
        if (is_array($data)) {
            $this->data = json_encode($data);
        }

        $this->parsedData = $data;
    }
}
