<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use DateTimeImmutable;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Repository\UserRepository;
use RedlineCms\Core\Support\Session;
use RedlineCms\Entity\DbSession;
use RedlineCms\Service\AuthUser;

class AuthController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loginView()
    {
        return Response::view("pages/auth/login.html");
    }

    public function login(Request $request, EntityManager $entityManager)
    {
        $username = $request->getBody("username");
        $password = $request->getBody("password");

        if (!$username || !$password) {
            return Response::view("pages/auth/login.html", ["error" => "Please provide valid username and password."]);
        }

        /** @var \RedlineCms\Entity\User */
        $user = $this->userRepository->findByUsernameOrEmail($username);

        if (!$user || !$user->checkPassword($password)) {
            return Response::view("pages/auth/login.html", ["error" => "The credential doesn't match."]);
        }

        $session = new DbSession(
            hash: uniqid("redlin-cms-"),
            userId: $user->getId(),
            expire_at: new DateTimeImmutable("+1 day")
        );

        $entityManager->persist($session);
        $entityManager->run();

        // Redirect to admin dashboard
        return Response::redirect("/admin")->setCookie(
            name: "session_id",
            value: $session->getHash(),
            httponly: true
        );
    }

    public function logout()
    {
        // Destroy the session
        Session::destroy();
        AuthUser::logout();

        // Redirect to login page
        return Response::redirect("/admin/login")->setCookie(
            name: "session_id",
            value: "logged-out",
            expires: 1,
            httponly: true
        );
    }
}
