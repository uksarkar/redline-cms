<?php

namespace RedlineCms\Service;

use RedlineCms\Core\Http\Request;

class Paginator
{
    const MAX_LIMIT = 1000;
    const MIN_LIMIT = 10;

    public int $page;
    public int $limit;
    public int $offset;

    public function __construct(private readonly Request $request)
    {
        $this->page = max(1, (int) $request->getQuery("page", 1));
        $this->limit = max(static::MIN_LIMIT, min((int) $request->getQuery("limit"), static::MAX_LIMIT));
        $this->offset = ($this->page - 1) * $this->limit;
    }
}
