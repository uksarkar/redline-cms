<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Core\Support\ThemeManager;

/**
 * @method \RedlineCms\Entity\ThemeMeta|null findByPK(int $id)
 */
class ThemeMetaRepository extends Select\Repository
{
    /**
     * @return \RedlineCms\Entity\ThemeMeta|null
     */
    public function findByName(string $name)
    {
        return $this->select()
            ->where("name", $name)
            ->where("theme", ThemeManager::getTheme()->dir)
            ->fetchOne();
    }

    public function getAll(string $name): iterable
    {
        return $this->select()
            ->where("name", $name)
            ->where("theme", ThemeManager::getTheme()->dir)
            ->fetchAll();
    }
}
