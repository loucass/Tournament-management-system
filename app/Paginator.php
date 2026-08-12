<?php

declare(strict_types=1);

namespace App;

/**
 * Paginator — Simple pagination helper.
 *
 * Wraps a result set with page metadata so views can render Previous/Next
 * links and page indicators without duplicating logic in every controller.
 *
 * Usage:
 *   $paginator = new Paginator($items, $total, $currentPage, 10);
 *   $paginator->items;        // sliced array
 *   $paginator->hasNext();     // bool
 *   $paginator->nextPage();    // int
 *   $paginator->toArray();     // full payload for View::make()
 */
class Paginator
{
    public int $currentPage;
    public int $perPage;
    public int $total;
    public int $lastPage;
    public int $offset;
    public array $items;

    /**
     * @param array $items Items for the CURRENT page (already sliced).
     * @param int   $total Total number of records across ALL pages.
     */
    public function __construct(array $items, int $total, int $currentPage, int $perPage = 10)
    {
        $this->items       = $items;
        $this->total       = max(0, $total);
        $this->currentPage = max(1, $currentPage);
        $this->perPage     = max(1, $perPage);
        $this->lastPage    = max(1, (int) ceil($this->total / $this->perPage));
        $this->offset      = ($this->currentPage - 1) * $this->perPage;
    }

    public function hasPrevious(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function previousPage(): int
    {
        return $this->currentPage - 1;
    }

    public function nextPage(): int
    {
        return $this->currentPage + 1;
    }

    /**
     * Return an associative array suitable for passing to View::make().
     */
    public function toArray(): array
    {
        return [
            'items'             => $this->items,
            'currentPage'       => $this->currentPage,
            'lastPage'          => $this->lastPage,
            'perPage'           => $this->perPage,
            'total'             => $this->total,
            'hasPrevious'       => $this->hasPrevious(),
            'hasNext'           => $this->hasNext(),
            'previousPage'      => $this->previousPage(),
            'nextPage'          => $this->nextPage(),
        ];
    }
}