<?php

namespace RedlineCms\Service;

class PaginatedItems
{
    public function __construct(
        public readonly iterable $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $limit,
    ) {}

    public function nextPage(): int|null
    {
        $maxPage = $this->maxPage();

        if ($maxPage === 0) {
            return null;
        }

        if ($maxPage > $this->page) {
            return $this->page + 1;
        }

        return null;
    }

    public function prevPage(): int|null
    {
        if ($this->page > 1) {
            return $this->page - 1;
        }

        return null;
    }

    public function maxPage(): int
    {
        return $this->total === 0 ? 0 : ceil($this->total / $this->limit);
    }

    public function pages(): array
    {
        $maxPage = $this->maxPage();

        if ($maxPage === 0) {
            return [];
        }

        // Determine the range of pages to display
        $halfRange = 2; // Number of pages to show on either side of the current page
        $startPage = max(1, $this->page - $halfRange);
        $endPage = min($maxPage, $this->page + $halfRange);

        // Adjust the start and end pages if they are too close to the boundaries
        if ($startPage === 1) {
            $endPage = min($maxPage, $startPage + 4); // Show 5 pages total if at the beginning
        } elseif ($endPage === $maxPage) {
            $startPage = max(1, $endPage - 4); // Show 5 pages total if at the end
        }

        return range($startPage, $endPage);
    }

    public function isActive($page): bool
    {
        return (int) $page === $this->page;
    }
}
