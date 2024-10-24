<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use RedlineCms\Traits\SoftDelete;
use RedlineCms\Traits\Timestamp;

#[Entity(repository: \RedlineCms\Repository\CategoryRepository::class)]
class Category extends BaseEntity
{
    use SoftDelete, Timestamp;

    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'integer', nullable: true, default: null)]
    private ?int $parent_id;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'string')]
    private string $description;

    #[Column(type: 'string')]
    private string $slug;

    public function __construct(string $name, string $description, string $slug)
    {
        $this->name = $name;
        $this->description = $description;
        $this->slug = $slug;
        $this->initTimestamps();
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
        $this->updateTimestamps();
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;
        $this->updateTimestamps();
    }
}
