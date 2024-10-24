<?php

namespace RedlineCms\Controller;

use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Service\ViewContext;

class PostController extends Controller
{
    public function __construct(protected PostRepository $postRepository) {}

    public function view(string $slug)
    {
        // find the post
        $post = $this->postRepository->findPostBySlug($slug);

        if (!$post) {
            return Response::notFound(heading: "Post not found");
        }

        return Response::view("@themes/default/post.html", [
            "post" => $post,
            "context" => App::resolve(ViewContext::class)
        ]);
    }
}
