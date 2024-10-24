<?php

namespace RedlineCms\Repository;

use Cycle\ORM\Select;
use RedlineCms\Entity\Enums\PostStatus;
use RedlineCms\Entity\Enums\PostType;
use RedlineCms\Traits\HasSlug;
use RedlineCms\Traits\SelectBuilder;
use RedlineCms\Traits\SoftDeleteBuilder;

/**
 * @method \RedlineCms\Entity\Post|null findByPK
 */
class PostRepository extends Select\Repository
{
    use SoftDeleteBuilder, SelectBuilder, HasSlug;

    /**
     * @return \RedlineCms\Entity\Post|null
     */
    public function findPostBySlug(string $slug): object|null
    {
        return $this->publishedPosts()->where("slug", $slug)->fetchOne();
    }

    /**
     * @return \RedlineCms\Entity\Post|null
     */
    public function findPageBySlug(string $slug): object|null
    {
        return $this->publishedPages()->where("slug", $slug)->fetchOne();
    }

    public function publishedPosts(): Select
    {
        return $this->withoutTrashed()
            ->where("status", PostStatus::PUBLISHED)
            ->where("type", PostType::POST);
    }

    public function publishedPages(): Select
    {
        return $this->withoutTrashed()
            ->where("status", PostStatus::PUBLISHED)
            ->where("type", PostType::PAGE);
    }
}
