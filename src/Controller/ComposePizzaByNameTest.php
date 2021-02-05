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
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;

final class ComposePizzaByNameTest extends TestCase
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

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('carnivore');

        $controller = new ComposePizzaByName(new Pizzaiolo(), $responseFactory, $streamFactory);
        $response = $controller($request);

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame('{"price":"14.00 EUR"}', $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_pizza_name_is_given(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaByName(new Pizzaiolo(), $responseFactory, $streamFactory);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(['carnivore']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The pizza name should be a string; array given.');

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_pizza_name_is_unknown(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaByName(new Pizzaiolo(), $responseFactory, $streamFactory);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('frites');

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`frites` is an invalid or an unknown ingredient.');

        $controller($request);
    }
}
