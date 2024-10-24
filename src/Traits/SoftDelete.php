<?php

/**
 * 
 * Redline CMS
 * 
 * A simple and lightweight CMS
 * @author connect@utpal.io <Utpal Sarkar>
 */

namespace RedlineCms\Traits;

use Cycle\Annotated\Annotation\Column;
use \DateTimeImmutable;
use RedlineCms\Service\AppDate;

trait SoftDelete
{
    #[Column(type: 'datetime', nullable: true, default: null)]
    private ?DateTimeImmutable $deleted_at = null;

    public function softDelete(): self
    {
        $this->deleted_at = new DateTimeImmutable();
        return $this;
    }

    public function restore(): self
    {
        $this->deleted_at = null;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function getDeletedAt(): ?AppDate
    {
        return $this->deleted_at ? new AppDate($this->deleted_at) : null;
    }
}
