<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\Session;
use RedlineCms\Entity\Category;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Request\CategoryStoreRequest;
use RedlineCms\Service\PaginatedItems;
use RedlineCms\Service\Paginator;

class AdminCategoryController extends Controller
{
    public function __construct(protected CategoryRepository $repo) {}

    public function index(Request $request)
    {
        $paginated = new Paginator($request);

        $categories = $this->repo
            ->withoutTrashed()
            ->offset($paginated->offset)
            ->limit($paginated->limit)
            ->fetchAll();

        $total = $this->repo->withoutTrashed()->count();

        $response = new PaginatedItems(
            items: $categories,
            total: $total,
            page: $paginated->page,
            limit: $paginated->limit
        );

        $errors = Session::flashOut("errors");
        $inputs = Session::flashOut("inputs");

        return Response::view("pages/category_index.html", ["categories" => $response, "errors" => $errors, "inputs" => $inputs]);
    }

    public function store(CategoryStoreRequest $request, EntityManager $manager)
    {
        if (!$request->isValid()) {
            return Response::redirect("/admin/categories")->with(["errors" => $request->getErrors(), "inputs" => $request->body()]);
        }

        $name = $request->getBody("name");
        $slug = $request->getBody("slug");

        if (!trim($slug ?? "")) {
            $slug = $this->repo->makeUniqueSlug($name);
        }

        $category = new Category($name, "", $slug);

        $manager->persist($category);
        $manager->run();

        return Response::redirect("/admin/categories")->with(["message" => "Category created"]);
    }

    public function edit(int $id)
    {
        $category = $this->repo->findByPK($id);

        if (!$category) {
            return Response::notFound("Category not found");
        }

        return Response::view("pages/category_edit.html", compact("category"));
    }

    public function update(int $id, CategoryStoreRequest $request, EntityManager $manager)
    {

        $category = $this->repo->findByPK($id);

        if (!$category) {
            return Response::notFound("Category not found");
        }

        if (!$request->isValid()) {
            return Response::view("pages/category_edit.html", [
                "category" => $category,
                "errors" => $request->getErrors(),
                "inputs" => $request->body()
            ]);
        }

        $category->setName($request->getBody("name"));
        $category->setSlug($request->getBody("slug"));

        $manager->persist($category);
        $manager->run();

        return Response::redirect("/admin/categories")->with(["message" => "The category was updated"]);
    }

    public function delete(int $id, EntityManager $manager)
    {
        $category = $this->repo->findByPK($id);

        if (!$category) {
            return Response::notFound("Category not found");
        }

        $manager->persist($category->softDelete());
        $manager->run();

        return Response::redirect("/admin/categories")->with(["message" => "The category was deleted"]);
    }

    public function restore(int $id, EntityManager $manager)
    {
        $category = $this->repo->findByPK($id);

        if (!$category) {
            return Response::notFound("Category not found");
        }

        $manager->persist($category->restore());
        $manager->run();

        return Response::redirect("/admin/categories")->with(["message" => "The category was restored"]);
    }
}
