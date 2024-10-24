<?php

namespace RedlineCms\Traits;

use Cycle\Annotated\Annotation\Column;
use \DateTimeImmutable;
use RedlineCms\Service\AppDate;

trait Timestamp
{
    #[Column(type: 'datetime')]
    private DateTimeImmutable $created_at;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $updated_at;

    public function initTimestamps()
    {
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
    }

    public function getCreatedAt(): AppDate
    {
        return new AppDate($this->created_at);
    }

    public function getUpdatedAt(): AppDate
    {
        return new AppDate($this->updated_at);
    }

    public function updateTimestamps(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }
}
