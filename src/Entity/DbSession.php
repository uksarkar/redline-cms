<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use DateTimeImmutable;
use RedlineCms\Traits\SoftDelete;
use RedlineCms\Traits\Timestamp;

#[Entity(repository: \RedlineCms\Repository\DbSessionRepository::class)]
class DbSession
{
    use SoftDelete, Timestamp;

    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'string')]
    private string $hash;

    #[Column(type: 'integer')]
    private int $userId;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $expire_at;

    public function __construct(string $hash, int $userId, DateTimeImmutable $expire_at)
    {
        $this->userId = $userId;
        $this->hash = $hash;
        $this->expire_at = $expire_at;
        $this->initTimestamps();
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
