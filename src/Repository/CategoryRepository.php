<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Traits\HasSlug;
use RedlineCms\Traits\SelectBuilder;
use RedlineCms\Traits\SoftDeleteBuilder;

class CategoryRepository extends Select\Repository
{
    use SoftDeleteBuilder, SelectBuilder, HasSlug;

    /**
     * @return \RedlineCms\Entity\Category
     */
    public function findBySlug(string $slug): object|null
    {
        return $this->withoutTrashed()->where('slug', $slug)->fetchOne();
    }
}
