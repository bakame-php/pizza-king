<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;

final class ComposePizzaFromIngredientsTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $responseFactory->method('createResponse')->willReturn(new Response());
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->will(
            self::returnCallback([new StreamFactory(), 'createStream'])
        );

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=pepperoni');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);
        $response = $controller($request);

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame('{"price":"14.00 EUR"}', $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_the_request_body_is_not_parsable(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(InvalidArgumentException::class);

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_invalid(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=ananas');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`ananas` is an invalid or an unknown ingredient.');

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_too_big(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=creme&cheese=mozzarella&meat=jambon&meat=jambon&meat=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        $controller($request);
    }


    /** @test */
    public function it_fails_if_the_meat_value_is_null(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=goat&meat');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_sauce_is_not_supported(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=red&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`red` is an invalid or an unknown ingredient.');

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_sauce_value_is_null(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce&cheese=mozzarella');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_null(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_cheese_value_is_invalid(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=jambon');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request);
    }

    /** @test */
    public function it_fails_if_there_is_too_many_cheese(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $uri = $this->createStub(UriInterface::class);
        $uri->method('getQuery')->willReturn('sauce=cream&cheese=goat&cheese=goat');
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);

        $this->expectException(UnableToHandleIngredient::class);
        $controller($request);
    }
}
