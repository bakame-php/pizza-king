<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use Bakame\PizzaKing\Service\IngredientTransformer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;
use function json_encode;

final class ComposePizzaFromIngredientsTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $pizzaiolo = new Pizzaiolo();

        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->will(
            self::returnCallback([new StreamFactory(), 'createStream'])
        );

        $transformer = new IngredientTransformer($streamFactory);
        $result = $transformer->dishToArray($pizzaiolo->composeFromIngredients([
            'creme', 'mozzarella', 'jambon', 'pepperoni',
        ]), 'custom pizza');

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=pepperoni');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $transformer);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_query_string_is_present(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(InvalidArgumentException::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_request_body_is_not_parsable(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(InvalidArgumentException::class);

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_invalid(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=ananas');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_too_big(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=jambon&meat=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        $controller($request, new Response());
    }


    /** @test */
    public function it_fails_if_the_meat_value_is_null(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

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
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=red&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_sauce_value_is_null(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_null(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_invalid(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_there_is_too_many_cheese(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), new IngredientTransformer($streamFactory));

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=goat&cheese=goat');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request, new Response());
    }
}
