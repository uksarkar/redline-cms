<?php

namespace RedlineCms\Controller\Admin;

use Cycle\ORM\EntityManager;
use RedlineCms\Controller\Controller;
use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Http\UploadFile;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\Storage;
use RedlineCms\Entity\Enums\PostType;
use RedlineCms\Entity\Post;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Request\StorePostRequest;
use RedlineCms\Service\AuthUser;
use RedlineCms\Service\PaginatedItems;
use RedlineCms\Service\Paginator;

class AdminPostController extends Controller
{
    public function __construct(protected PostRepository $postRepository) {}

    public function index(Request $request)
    {
        $paginated = new Paginator($request);

        $posts = $this->postRepository
            ->withoutTrashed()
            ->with('category')
            ->where('type', PostType::POST)
            ->offset($paginated->offset)
            ->limit($paginated->limit)
            ->fetchAll();

        $total = $this->postRepository->withoutTrashed()->where('type', PostType::POST)->count();

        $response = new PaginatedItems(
            items: $posts,
            total: $total,
            page: $paginated->page,
            limit: $paginated->limit
        );

        return Response::view("pages/post_index.html", ["posts" => $response]);
    }

    public function create()
    {
        $categories = App::resolve(CategoryRepository::class)->withoutTrashed()->fetchAll();

        return Response::view("pages/post_action.html", compact("categories"));
    }

    public function store(StorePostRequest $request, EntityManager $manager)
    {
        $isPage = !empty($request->getBody("type"));

        if (!$request->isValid()) {
            return Response::view("pages/post_action.html", [
                "errors" => $request->getErrors(),
                "inputs" => $request->body(),
                "isPage" => $isPage,
            ]);
        }

        $title = $request->getBody("title");
        $content = $request->getBody("content");
        $userId = AuthUser::user()->getId();
        $categoryId = $request->getBody("category_id");
        $status = $request->getBody("status");
        $slug = $request->getBody("slug");

        if (!trim($slug)) {
            $slug = $this->postRepository->makeUniqueSlug($title);
        }

        $image = UploadFile::get("image");
        if ($image) {
            $path = Storage::upload("/images", sprintf("%s.%s", $slug, $image->ext), $image);
        }

        $type = PostType::POST;

        if ($isPage) {
            $type = PostType::PAGE;
        }

        $post = new Post(
            title: $title,
            image: $path ?? "",
            content: $content,
            userId: $userId,
            slug: $slug,
            type: $type,
            status: $status
        );

        $post->setCategoryId($categoryId);

        $manager->persist($post);
        $manager->run();

        $to = $isPage ? "/admin/pages" : "/admin/posts";

        return Response::redirect($to)->with(["message" => "Post created"]);
    }

    public function edit(int $id)
    {
        $post = $this->postRepository->findByPK($id);

        if (!$post) {
            return Response::notFound("Post not found");
        }

        $categories = App::resolve(CategoryRepository::class)->withoutTrashed()->fetchAll();

        return Response::view("pages/post_action.html", compact("post", "categories"));
    }

    public function update(int $id, StorePostRequest $request, EntityManager $manager)
    {
        $isPage = !empty($request->getBody("type"));
        $post = $this->postRepository->findByPK($id);

        if (!$post) {
            return Response::notFound("Post not found");
        }

        if (!$request->isValid()) {
            $categories = App::resolve(CategoryRepository::class)->withoutTrashed()->fetchAll();

            return Response::view("pages/post_action.html", [
                "post" => $post,
                "errors" => $request->getErrors(),
                "categories" => $categories,
                "inputs" => $request->body(),
                "isPage" => $isPage,
            ]);
        }

        $post->setSlug($request->getBody("slug"));

        $image = UploadFile::get("image");
        if ($image) {
            $path = Storage::upload("/images", sprintf("%s.%s", $post->getSlug(), $image->ext), $image);
            Storage::delete($post->getImagePath());

            $post->setImage($path);
        }

        $post->setTitle($request->getBody("title"));
        $post->setCategoryId($request->getBody("category_id"));
        $post->setStatus($request->getBody("status"));
        $post->updateContent($request->getBody("content"));

        $manager->persist($post);
        $manager->run();

        $to = $isPage ? "/admin/pages" : "/admin/posts";

        return Response::redirect($to)->with(["message" => "The post was updated"]);
    }

    public function delete(int $id, EntityManager $manager)
    {
        $post = $this->postRepository->findByPK($id);

        if (!$post) {
            return Response::notFound("Post not found");
        }

        $manager->persist($post->softDelete());
        $manager->run();

        return Response::redirect("/admin/posts")->with(["message" => "The post was deleted"]);
    }

    public function restore(int $id, EntityManager $manager)
    {
        $post = $this->postRepository->findByPK($id);

        if (!$post) {
            return Response::notFound("Post not found");
        }

        $manager->persist($post->restore());
        $manager->run();

        return Response::redirect("/admin/posts")->with(["message" => "The post was restored"]);
    }
}
