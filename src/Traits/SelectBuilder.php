<?php

namespace RedlineCms\Traits;

use Cycle\ORM\Select;

/**
 * @method Select select
 */
trait SelectBuilder
{
    public function whereNull(string $column): Select
    {
        return $this->select()->where($column, "is", null);
    }

    public function whereNotNull(string $column): Select
    {
        return $this->select()->where($column, "is not", null);
    }

    public function first(): object|null
    {
        return $this->select()->orderBy("id")->fetchOne();
    }
}
