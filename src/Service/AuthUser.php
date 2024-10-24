<?php

namespace RedlineCms\Service;

use Cycle\ORM\EntityManager;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Support\App;
use RedlineCms\Entity\User;
use RedlineCms\Repository\DbSessionRepository;
use RedlineCms\Repository\UserRepository;

class AuthUser
{
    /** @var User */
    private object|null $user;

    /** @var \RedlineCms\Entity\DbSession */
    private object|null $session;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly DbSessionRepository $sessionRepository,
        private readonly Request $request
    ) {
        $this->user = null;
        $this->session = null;
        $this->refreshUser();
    }

    public function getUser(): object | null
    {
        return $this->user;
    }

    public function refreshUser(): void
    {
        if ($hash = $this->request->getCookie("session_id")) {
            $this->session = $this->sessionRepository->findByHash($hash);
        }

        if ($this->session) {
            $this->user = $this->userRepository->findByPK($this->session->getUserId());
        }
    }

    public static function getInstance(): self
    {
        return App::getInstance()->container->get(static::class);
    }

    public static function check(): bool
    {
        return static::user() !== null;
    }

    /**
     * @return \RedlineCms\Entity\User|null
     */
    public static function user(): object|null
    {
        return static::getInstance()->getUser();
    }

    public static function logout() {
        if(!$session = static::getInstance()->session) {
            return;
        }

        $manager = App::getInstance()->container->get(EntityManager::class);
        $manager->persist($session->softDelete());
        $manager->run();
    }
}
