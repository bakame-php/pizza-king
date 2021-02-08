<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use Bakame\PizzaKing\Service\IngredientRenderer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use function json_encode;

final class GetPizzaIngredientByNameTest extends TestCase
{
    /** @test */
    public function it_returns_the_ingredient(): void
    {
        $pizzaiolo = new Pizzaiolo();

        $renderer = new IngredientRenderer();
        $result = $renderer->ingredientToArray($pizzaiolo->getIngredientFromName('jambon'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('JAMBon');

        $controller = new GetPizzaIngredientByName($pizzaiolo, $renderer);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_ingredient_name_is_given(): void
    {
        $controller = new GetPizzaIngredientByName(new Pizzaiolo(), new IngredientRenderer());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(['carnivore']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The ingredient name should be a string; array given.');

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_ingredient_name_is_unknown(): void
    {
        $controller = new GetPizzaIngredientByName(new Pizzaiolo(), new IngredientRenderer());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('frites');

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`frites` is an invalid or an unknown ingredient.');

        $controller($request, new Response());
    }
}
