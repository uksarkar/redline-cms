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
}
