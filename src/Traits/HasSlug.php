<?php

namespace RedlineCms\Traits;

use RedlineCms\Service\Str;

trait HasSlug
{
    public function makeUniqueSlug(string $name): string
    {
        $slug = Str::url_safe_string($name);
        $count = 1;

        while ($this->select()->where("slug", $slug)->count()) {
            $slug .= "-$count";
            $count++;
        }

        return $slug;
    }

    public function slugExists(string $slug, int $except = null): bool
    {
        $q = $this->select()->where("slug", $slug);

        if ($except) {
            $q = $q->where("id", "!=", $except);
        }

        return $q->count() > 0;
    }
}
