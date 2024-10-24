<?php

namespace RedlineCms\Core\Support;

class CSRF
{
    public static function generateToken(): string
    {
        if (!Session::has('_token')) {
            Session::set('_token', bin2hex(random_bytes(32)));
        }

        return Session::get('_token');
    }

    public static function verifyToken(string $token): bool
    {
        return hash_equals(Session::get('_token'), $token);
    }
}
