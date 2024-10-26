<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Core\Support\ThemeManager;

/**
 * @method \RedlineCms\Entity\MetaData|null findByPK(int $id)
 */
class MetaDataRepository extends Select\Repository
{
    /**
     * @return \RedlineCms\Entity\MetaData|null
     */
    public function findByName(string $name, string $provider = null)
    {
        return $this->select()
            ->where("name", $name)
            ->where("provider", $provider ?? ThemeManager::getTheme()->dir)
            ->fetchOne();
    }

    public function getAll(string $name, string $provider = null): iterable
    {
        return $this->select()
            ->where("name", $name)
            ->where("provider", $provider ?? ThemeManager::getTheme()->dir)
            ->fetchAll();
    }
}
