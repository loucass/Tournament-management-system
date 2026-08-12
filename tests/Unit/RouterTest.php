<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exceptions\RouteNotFoundException;
use App\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testItRegistersGetRoutes(): void
    {
        $router = new Router();
        $router->get('/home', fn () => 'home');

        self::assertArrayHasKey('get', $router->routes());
        self::assertSame('/home', array_key_first($router->routes()['get']));
    }

    public function testItRegistersPostRoutes(): void
    {
        $router = new Router();
        $router->post('/logIn', fn () => 'login');

        self::assertArrayHasKey('post', $router->routes());
        self::assertSame('/logIn', array_key_first($router->routes()['post']));
    }

    public function testItIsChainable(): void
    {
        $router = new Router();
        $result = $router->get('/a', fn () => 'a')->post('/b', fn () => 'b');

        self::assertInstanceOf(Router::class, $result);
        self::assertCount(1, $router->routes()['get']);
        self::assertCount(1, $router->routes()['post']);
    }

    public function testItResolvesCallableActions(): void
    {
        $router = new Router();
        $router->get('/ping', fn () => 'pong');

        self::assertSame('pong', $router->resolve('/ping', 'get'));
    }

    public function testItResolvesControllerActions(): void
    {
        $router = new Router();
        $router->get('/hello', [StubController::class, 'hello']);

        self::assertSame('hello world', $router->resolve('/hello', 'get'));
    }

    public function testItIgnoresQueryStringWhenResolving(): void
    {
        $router = new Router();
        $router->get('/search', fn () => 'results');

        self::assertSame('results', $router->resolve('/search?q=php', 'get'));
    }

    public function testItThrowsWhenRouteDoesNotExist(): void
    {
        $router = new Router();

        $this->expectException(RouteNotFoundException::class);

        $router->resolve('/nope', 'get');
    }

    public function testItThrowsWhenMethodDoesNotMatch(): void
    {
        $router = new Router();
        $router->get('/home', fn () => 'home');

        $this->expectException(RouteNotFoundException::class);

        $router->resolve('/home', 'post');
    }

    public function testItThrowsWhenControllerMethodDoesNotExist(): void
    {
        $router = new Router();
        $router->get('/broken', [StubController::class, 'missing']);

        $this->expectException(RouteNotFoundException::class);

        $router->resolve('/broken', 'get');
    }
}

final class StubController
{
    public function hello(): string
    {
        return 'hello world';
    }
}