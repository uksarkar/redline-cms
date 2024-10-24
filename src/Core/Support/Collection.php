<?php

namespace RedlineCms\Core\Support;

use \Countable;
use \Iterator;
use RedlineCms\Core\Http\Response;

class Collection implements Countable, Iterator
{
    private array $data = [];
    private int $cursor = 0;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->cursor = 0; // Initialize the cursor to 0
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function next(): void
    {
        $this->cursor++;
    }

    public function rewind(): void
    {
        $this->cursor = 0;
    }

    public function current(): mixed
    {
        return $this->data[$this->cursor] ?? null;
    }

    public function key(): int
    {
        return $this->cursor;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->cursor]);
    }

    public function intoEntityArray(): self
    {
        $this->data = array_map(fn($entity) => is_object($entity) && method_exists($entity, "toArray") ? $entity->toArray() : $entity, $this->data);
        return $this;
    }

    public function toResponse(): Response
    {
        $this->intoEntityArray();
        return Response::fromArray($this->data);
    }
}
