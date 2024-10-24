<?php

namespace RedlineCms\Controller\Admin;

use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Entity\Enums\PostType;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Service\PaginatedItems;
use RedlineCms\Service\Paginator;

class AdminPageController extends Controller
{
    public function __construct(protected PostRepository $postRepository) {}

    public function index(Request $request)
    {
        $paginated = new Paginator($request);

        $posts = $this->postRepository
            ->withoutTrashed()
            ->where("type", PostType::PAGE)
            ->offset($paginated->offset)
            ->limit($paginated->limit)
            ->fetchAll();

        $total = $this->postRepository->withoutTrashed()->where("type", PostType::PAGE)->count();

        $response = new PaginatedItems(
            items: $posts,
            total: $total,
            page: $paginated->page,
            limit: $paginated->limit
        );

        return Response::view("pages/post_index.html", ["posts" => $response, "isPage" => true]);
    }

    public function create()
    {
        $isPage = true;

        return Response::view("pages/post_create.html", compact("isPage"));
    }

    public function edit(int $id)
    {
        $post = $this->postRepository->findByPK($id);

        if (!$post) {
            return Response::notFound("Post not found");
        }
        $isPage = true;

        return Response::view("pages/post_edit.html", compact("post", "isPage"));
    }
}
