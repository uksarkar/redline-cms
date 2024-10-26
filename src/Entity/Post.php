<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Parsedown;
use RedlineCms\Core\Support\App;
use RedlineCms\Entity\Enums\PostStatus;
use RedlineCms\Entity\Enums\PostType;
use RedlineCms\Repository\CategoryRepository;
use RedlineCms\Traits\Timestamp;
use RedlineCms\Traits\SoftDelete;

#[Entity(repository: \RedlineCms\Repository\PostRepository::class)]
class Post
{
    use Timestamp, SoftDelete;

    #[Column(type: 'primary')]
    protected int $id;

    #[Column(type: 'string')]
    protected string $title;

    #[Column(type: 'string')]
    protected string $slug;

    #[Column(type: 'string')]
    protected string $image;

    #[Column(type: 'text')]
    protected string $content;

    #[Column(type: 'integer')]
    protected int $userId;

    #[Column(type: 'integer', default: PostType::POST->value)]
    protected int $type;

    #[Column(type: 'integer', default: PostStatus::DRAFTED->value)]
    protected int $status;

    #[Column(type: 'string', nullable: true)]
    protected ?string $template;

    #[BelongsTo(target: Category::class, nullable: true)]
    public ?Category $category;

    public function __construct(
        string $title,
        string $image,
        string $content,
        string $slug,
        int $userId,
        Category $category = null,
        PostType $type = PostType::POST,
        PostStatus $status = PostStatus::DRAFTED,
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->userId = $userId;
        $this->image = $image;
        $this->slug = $slug;
        $this->type = $type->value;
        $this->status = $status->value;
        $this->category = $category;
        $this->category = null;
        $this->initTimestamps();
    }


    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRawContent(): string
    {
        return $this->content;
    }

    public function getContent(): string
    {
        $parsedown = new Parsedown();
        return $parsedown->text($this->content);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCategoryId(): ?int
    {
        return $this->category?->getId();
    }

    public function getCategoryName(): ?string
    {
        return $this->category?->getName();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getType(): PostType
    {
        return PostType::from($this->type);
    }

    public function getTemplate(): string
    {
        return $this->template ?? "default";
    }

    public function getStatus(): PostStatus
    {
        return PostStatus::from($this->status);
    }

    public function isDrafted(): bool
    {
        return $this->getStatus() === PostStatus::DRAFTED;
    }

    public function isPublished(): bool
    {
        return $this->getStatus() === PostStatus::PUBLISHED;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getImagePath(): ?string
    {
        return $this->image;
    }

    public function getFeaturedImage(): string
    {
        if ($this->image) {
            return sprintf("/storage%s", $this->image);
        }

        return "https://placehold.co/600x400?text=Image";
    }

    public function setStatus(int|PostStatus $status)
    {
        $this->status = is_int($status) ? $status : $status->value;
    }

    public function setCategoryId(?int $id)
    {
        $this->category = $id ? App::resolve(CategoryRepository::class)->findById($id)->fetchOne() : null;
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    public function setImage(string $path)
    {
        $this->image = $path;
    }

    public function updateContent(string $content): void
    {
        $this->content = $content;
        $this->updated_at = new \DateTimeImmutable();
    }

    public function isCategory(?int $categoryId = null): bool
    {
        if (!$categoryId || !$this->getCategoryId()) {
            return false;
        }

        return $this->getCategoryId() === $categoryId;
    }

    public function getShortDescription(int $limit = 150): string
    {
        $description = strip_tags($this->getContent());

        if (mb_strlen($description) > $limit) {
            $description = substr($description, 0, $limit) . "...";
        }

        return $description;
    }
}
