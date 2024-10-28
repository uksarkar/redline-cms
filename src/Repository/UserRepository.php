<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Entity\Enums\UserStatus;
use RedlineCms\Traits\SelectBuilder;
use RedlineCms\Traits\SoftDeleteBuilder;

/**
 * @method \RedlineCms\Entity\Category|null findByPK
 */
class UserRepository extends Select\Repository
{
    use SoftDeleteBuilder, SelectBuilder;

    public function findActive(): Select
    {
        return $this->select()->where('status', UserStatus::ACTIVE);
    }

    public function findByUsernameOrEmail(string $username)
    {
        return $this->select()
            ->where("username", $username)
            ->orWhere("email", $username)
            ->fetchOne();
    }

    public function emailExists(string $email, int $except = null): bool
    {
        $q = $this->select()
            ->orWhere("email", $email);

        if($except) {
            $q = $q->where("id", "!=", $except);
        }

        return $q->count() > 0;
    }

    public function usernameExists(string $username, int $except = null): bool
    {
        $q = $this->select()
            ->orWhere("username", $username);

        if($except) {
            $q = $q->where("id", "!=", $except);
        }

        return $q->count() > 0;
    }
}
