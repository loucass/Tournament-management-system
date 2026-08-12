<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Paginator;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    public function testItExposesCoreMetadata(): void
    {
        $p = new Paginator(['a', 'b'], 42, 3, 10);

        self::assertSame(['a', 'b'], $p->items);
        self::assertSame(42, $p->total);
        self::assertSame(3, $p->currentPage);
        self::assertSame(10, $p->perPage);
        self::assertSame(5, $p->lastPage);
        self::assertSame(20, $p->offset);
    }

    public function testItComputesLastPageFromCeiling(): void
    {
        self::assertSame(4, (new Paginator([], 31, 1, 10))->lastPage);
        self::assertSame(4, (new Paginator([], 40, 1, 10))->lastPage);
        self::assertSame(1, (new Paginator([], 0, 1, 10))->lastPage);
    }

    public function testItHasNextWhenNotOnLastPage(): void
    {
        $p = new Paginator([], 100, 3, 10);

        self::assertTrue($p->hasNext());
        self::assertTrue($p->hasPrevious());
        self::assertSame(4, $p->nextPage());
        self::assertSame(2, $p->previousPage());
    }

    public function testItDoesNotHaveNextOnLastPage(): void
    {
        $p = new Paginator([], 100, 10, 10);

        self::assertFalse($p->hasNext());
        self::assertTrue($p->hasPrevious());
    }

    public function testItDoesNotHavePreviousOnFirstPage(): void
    {
        $p = new Paginator([], 100, 1, 10);

        self::assertFalse($p->hasPrevious());
        self::assertSame(0, $p->previousPage());
    }

    public function testItClampsInvalidInput(): void
    {
        $p = new Paginator([], -5, 0, 0);

        self::assertSame(0, $p->total);
        self::assertSame(1, $p->currentPage);
        self::assertSame(1, $p->perPage);
    }

    public function testToArrayIncludesRenderedMetadata(): void
    {
        $data = (new Paginator(['x'], 25, 2, 10))->toArray();

        self::assertSame(['x'], $data['items']);
        self::assertSame(2, $data['currentPage']);
        self::assertSame(3, $data['lastPage']);
        self::assertSame(10, $data['perPage']);
        self::assertSame(25, $data['total']);
        self::assertTrue($data['hasPrevious']);
        self::assertTrue($data['hasNext']);
        self::assertSame(1, $data['previousPage']);
        self::assertSame(3, $data['nextPage']);
    }
}