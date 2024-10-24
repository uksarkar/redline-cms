<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;

class ConfigRepository extends Select\Repository
{
    /**
     * @return \RedlineCms\Entity\Config|null
     */
    public function first(): object|null
    {
        return $this->select()->orderBy("id")->fetchOne();
    }
}
