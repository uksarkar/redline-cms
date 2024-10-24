<?php

namespace RedlineCms\Entity;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity]
class Comment
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'text')]
    private string $content;

    #[Column(type: 'integer')]
    private int $postId; // The ID of the post the comment belongs to

    #[Column(type: 'integer')]
    private int $userId; // The ID of the user who created the comment

    #[Column(type: 'datetime')]
    private \DateTime $created_at;

    public function __construct(string $content, int $postId, int $userId)
    {
        $this->content = $content;
        $this->postId = $postId;
        $this->userId = $userId;
        $this->created_at = new \DateTime();
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }
}
