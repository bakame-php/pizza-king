<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Action;

use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Bakame\PizzaKing\Domain\UnableToHandleIngredient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Response;
use function json_encode;

final class ComposePizzaFromIngredientsTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $pizzaiolo = new Pizzaiolo();
        $renderer = new IngredientConverter();
        $result = $renderer->dishToArray($pizzaiolo->composePizzaFromIngredients([
            'creme', 'mozzarella', 'jambon', 'pepperoni',
        ]), 'custom pizza');

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=pepperoni');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $renderer);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_query_string_is_present(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(InvalidArgumentException::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_invalid(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=ananas');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_too_big(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=jambon&meat=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_meat_value_is_null(): void
    {
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=goat&meat');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_sauce_is_not_supported(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=red&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_sauce_value_is_null(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }


    /** @test */
    public function it_fails_if_there_is_too_many_sauce(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=tomato&sauce=sauce%20tomate&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_null(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_invalid(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_there_is_too_many_cheese(): void
    {
        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=goat&cheese=goat');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientConverter());
        $controller($request, new Response());
    }
}
