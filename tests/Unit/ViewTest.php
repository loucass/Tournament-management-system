<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exceptions\ViewNotFoundException;
use App\View;
use PHPUnit\Framework\TestCase;

final class ViewTest extends TestCase
{
    public function testItRendersATemplateWithParams(): void
    {
        $html = View::make('_test_greeting', ['name' => 'World'])->render();

        self::assertSame('Hello World', $html);
    }

    public function testItExposesParamsThroughMagicGetter(): void
    {
        $view = View::make('_test_greeting', ['name' => 'PHP']);

        self::assertSame('PHP', $view->name);
        self::assertNull($view->missing);
    }

    public function testToStringRendersTemplate(): void
    {
        self::assertSame('Hello PHP', (string) View::make('_test_greeting', ['name' => 'PHP']));
    }

    public function testItThrowsWhenViewDoesNotExist(): void
    {
        $this->expectException(ViewNotFoundException::class);

        View::make('does_not_exist')->render();
    }
}