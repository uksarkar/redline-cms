<?php

namespace RedlineCms\Controller;

use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Service\ViewContext;

class CategoryController extends Controller
{
    public function __construct(protected CategoryRepository $repo) {}

    public function view(string $slug)
    {
        $category = $this->repo->findBySlug($slug);

        if (!$category) {
            return Response::notFound(heading: "Category not found");
        }

        return Response::view("@themes/default/category.html", [
            "category" => $category,
            "context" => App::resolve(ViewContext::class)
        ]);
    }
}
