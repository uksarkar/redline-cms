<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use DateTimeImmutable;
use RedlineCms\Traits\SelectBuilder;
use RedlineCms\Traits\SoftDeleteBuilder;

class DbSessionRepository extends Select\Repository
{
    use SoftDeleteBuilder, SelectBuilder;

    /**
     * @return \RedlineCms\Entity\DbSession|null
     */
    public function findByHash(string $hash)
    {
        return $this->withoutTrashed()
            ->where("hash", $hash)
            ->where("expire_at", ">", new DateTimeImmutable())
            ->fetchOne();
    }
}
