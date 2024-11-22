<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Core\Support\ThemeManager;
use RedlineCms\Entity\MetaData;

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

    /**
     * Try to get the first meta by the name or return a new instance with the provided values
     */
    public function firstOrNew(string $name, array $meta, string $provider = null)
    {
        if($dbMeta = $this->findByName($name, $provider)) {
            return $dbMeta;
        }

        return new MetaData($name, $meta);
    }
}
