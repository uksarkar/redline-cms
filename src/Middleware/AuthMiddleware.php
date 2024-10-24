<?php

namespace RedlineCms\Middleware;

use RedlineCms\Core\Http\Middleware;
use RedlineCms\Core\Http\Response;
use RedlineCms\Service\AuthUser;

class AuthMiddleware implements Middleware
{
    public function handle(callable $next)
    {
        if (AuthUser::check()) {
            return $next();
        }

        return Response::redirect("/admin/login");
    }
}
