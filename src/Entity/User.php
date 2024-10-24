<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Table\Index;
use RedlineCms\Entity\Enums\UserRole;
use RedlineCms\Entity\Enums\UserStatus;
use RedlineCms\Traits\Timestamp;
use RedlineCms\Traits\SoftDelete;

#[Entity(repository: \RedlineCms\Repository\UserRepository::class)]
#[Index(columns: ['username'], unique: true)]
class User extends BaseEntity
{
    use Timestamp, SoftDelete;

    #[Column(type: 'primary')]
    protected int $id;

    #[Column(type: 'string')]
    protected string $username;

    #[Column(type: 'string')]
    protected string $email;

    #[Column(type: 'string')]
    private string $password;

    #[Column(type: 'integer', default: 1)]
    protected int $status;

    #[Column(type: 'integer', default: 0)]
    protected int $role;

    public function __construct(string $username, string $email, string $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->status = UserStatus::ACTIVE->value;
        $this->role = UserRole::ADMIN->value;
        $this->initTimestamps();
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): UserRole
    {
        return UserRole::from($this->role);
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getStatus(): UserStatus
    {
        return UserStatus::from($this->status);
    }
}
