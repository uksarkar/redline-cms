<?php

namespace RedlineCms\Service;

use RedlineCms\Core\Http\Request;
use RedlineCms\Core\Support\App;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Repository\MetaDataRepository;
use RedlineCms\Repository\PostRepository;

class ViewContext
{
    public string $appName;
    public string $version;

    public function __construct(
        private readonly PostRepository $postRepo,
        private readonly CategoryRepository $catRepo,
        private readonly MetaDataRepository $metaRepo,
        private readonly Request $request,
        public readonly AppConfig $appConfig,
    ) {
        $this->appName = $appConfig->config->getAppName() ?? App::getInstance()->container->get("appName");
        $this->version = App::getInstance()->container->get("version");
    }

    public function getLatestPosts(int $limit = 10): iterable
    {
        return $this->postRepo
            ->publishedPosts()
            ->orderBy("created_at", "DESC")
            ->limit($limit)
            ->fetchAll();
    }

    public function getCategoryPosts(int $categoryId): PaginatedItems
    {
        $paginator = new Paginator(new Request);
        $items = $this->postRepo
            ->publishedPosts()
            ->where("category_id", $categoryId)
            ->offset($paginator->offset)
            ->limit($paginator->limit)
            ->fetchAll();

        $total = $this->postRepo
            ->publishedPosts()
            ->where("category_id", $categoryId)
            ->count();

        return new PaginatedItems(
            items: $items,
            total: $total,
            page: $paginator->page,
            limit: $paginator->limit,
        );
    }

    public function getPages(): iterable
    {
        return $this->postRepo->publishedPages()->fetchAll();
    }

    public function getCategory(int|null $categoryId): object|null
    {
        if (!$categoryId) {
            return null;
        }

        return $this->catRepo->withoutTrashed()
            ->where("id", $categoryId)
            ->fetchOne();
    }

    public function getCategories(): iterable
    {
        return $this->catRepo->withoutTrashed()->fetchAll();
    }

    public function getConfigs()
    {
        return $this->appConfig->config;
    }

    public function isActiveSlug($checkSlug): bool
    {
        $slug = $this->request->getParam("slug");

        if (!$slug) {
            return false;
        }

        return $slug === $checkSlug;
    }

    public function isPath($path): bool
    {
        return $this->request->currentPath() === $path;
    }

    public function isParent($path): bool
    {
        return substr($this->request->currentPath(), 0, strlen($path)) === $path;
    }

    /**
     * @return \RedlineCms\Entity\User|null
     */
    public function user(): object|null
    {
        return App::resolve(AuthUser::class)->getUser();
    }

    public function getMetaData(string $name, string $provider = null)
    {
        return $this->metaRepo->findByName($name, $provider);
    }

    public function strLimit(string $str, int $limit = 100): string
    {
        if(strlen($str) <= $limit) {
            return $str;
        }

        return substr($str, $limit) . "...";
    }
}
