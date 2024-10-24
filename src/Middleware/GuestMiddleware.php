<?php

namespace RedlineCms\Middleware;

use RedlineCms\Core\Http\Middleware;
use RedlineCms\Core\Http\Response;
use RedlineCms\Service\AuthUser;

class GuestMiddleware implements Middleware
{
    public function __construct(private string $redirectTo) {}

    public function handle(callable $next)
    {
        if(AuthUser::check()) {
            return Response::redirect($this->redirectTo);
        }

        return $next();
    }
}
