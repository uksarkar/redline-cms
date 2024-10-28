<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\Session;
use RedlineCms\Entity\User;
use RedlineCms\Repository\UserRepository;
use RedlineCms\Request\UserStoreRequest;
use RedlineCms\Request\UserUpdateRequest;
use RedlineCms\Service\PaginatedItems;
use RedlineCms\Service\Paginator;

class UserController extends Controller
{
    public function __construct(protected UserRepository $repo) {}

    public function index(Paginator $paginator)
    {
        $items = $this->repo->withoutTrashed()
            ->limit($paginator->limit)
            ->offset($paginator->offset)
            ->fetchAll();

        $total = $this->repo->withoutTrashed()->count();

        $users = new PaginatedItems(
            items: $items,
            total: $total,
            page: $paginator->page,
            limit: $paginator->limit,
        );
        return Response::view("pages/users.html", compact("users"));
    }

    public function view(int $id)
    {
        $user = $this->repo->findById($id)->fetchOne();
        if (!$user) {
            return Response::notFound("User not found");
        }

        $inputs = Session::flashOut("inputs");
        $errors = Session::flashOut("errors");

        return Response::view("pages/user_view.html", compact("user", "inputs", "errors"));
    }

    public function store(UserStoreRequest $request, EntityManager $manager)
    {
        $user = new User(...$request->only(["username", "email", "password"]));
        $manager->persist($user);
        $manager->run();

        return Response::back()->with(["message" => "User created"]);
    }

    public function update(int $id, UserUpdateRequest $request, EntityManager $manager)
    {
        if (!$request->isValid()) {
            return Response::redirect("/admin/users/" . $id)->with(["inputs" => $request->body(), "errors" => $request->getErrors()]);
        }

        /** @var \RedlineCms\Entity\User */
        $user = $this->repo->findById($id)->fetchOne();
        if (!$user) {
            return Response::notFound("User not found");
        }

        $user->update($request->only(["username", "email", "password"], true));
        $manager->persist($user);
        $manager->run();

        return Response::back()->with(["message" => "User updated"]);
    }
}
