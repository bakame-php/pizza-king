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
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;
use function json_encode;

final class ComposePizzaByNameTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $pizzaiolo = new Pizzaiolo();
        $transformer = new IngredientTransformer();
        $result = $transformer->dishToArray($pizzaiolo->composeFromName('carnivore'), 'carnivore');

        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->will(
            self::returnCallback([new StreamFactory(), 'createStream'])
        );

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('CarNiVore');

        $controller = new ComposePizzaByName($pizzaiolo, $transformer, $streamFactory);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_pizza_name_is_given(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaByName(new Pizzaiolo(), new IngredientTransformer(), $streamFactory);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(['carnivore']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The pizza name should be a string; array given.');

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_pizza_name_is_unknown(): void
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaByName(new Pizzaiolo(), new IngredientTransformer(), $streamFactory);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('frites');

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`frites` is an invalid or an unknown ingredient.');

        $controller($request, new Response());
    }
}
