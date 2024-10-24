<?php

namespace RedlineCms\Core\Http;

interface Middleware
{
    public function handle(callable $next);
}
