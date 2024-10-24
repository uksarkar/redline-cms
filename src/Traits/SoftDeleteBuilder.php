<?php

namespace RedlineCms\Traits;

use Cycle\ORM\Select;

/**
 * @method Select select
 */
trait SoftDeleteBuilder
{
    public function trashed(): Select
    {
        return $this->select()->where("deleted_at", "is not", null);
    }

    public function withoutTrashed(): Select
    {
        return $this->select()->where("deleted_at", "is", null);
    }

    public function findById(int $id): Select
    {
        return $this->withoutTrashed()->where("id", $id);
    }
}
