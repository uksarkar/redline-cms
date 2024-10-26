<?php

namespace RedlineCms\Controller;

use RedlineCms\Core\Http\Response;
use RedlineCms\Core\Support\App;
use RedlineCms\Core\Support\ThemeManager;
use RedlineCms\Repository\PostRepository;
use RedlineCms\Service\ViewContext;

class PageController extends Controller
{
    public function __construct(protected PostRepository $postRepository) {}

    public function view(string $slug)
    {
        // find the page
        $page = $this->postRepository->findPageBySlug($slug);

        if (!$page) {
            return Response::notFound(heading: "Page not found");
        }

        return Response::view(ThemeManager::getTheme()->getTemplate("page.html"), [
            "page" => $page,
            "context" => App::resolve(ViewContext::class)
        ]);
    }
}
