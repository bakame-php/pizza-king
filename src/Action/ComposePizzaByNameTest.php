<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Action;

use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Bakame\PizzaKing\Domain\UnableToHandleIngredient;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use function json_encode;

final class ComposePizzaByNameTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $pizzaiolo = new Pizzaiolo();

        $renderer = new IngredientConverter();
        $result = $renderer->dishToArray($pizzaiolo->composePizzaFromName('carnivore'), 'carnivore');

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('CarNiVore');

        $controller = new ComposePizzaByName($pizzaiolo, $renderer);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_pizza_name_is_given(): void
    {
        $controller = new ComposePizzaByName(new Pizzaiolo(), new IngredientConverter());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(['carnivore']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The pizza name should be a string; array given.');

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_pizza_name_is_unknown(): void
    {
        $controller = new ComposePizzaByName(new Pizzaiolo(), new IngredientConverter());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('frites');

        $this->expectException(UnableToHandleIngredient::class);

        $controller($request, new Response());
    }
}
