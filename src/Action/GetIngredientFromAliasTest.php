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

final class GetIngredientFromAliasTest extends TestCase
{
    /** @test */
    public function it_returns_the_ingredient(): void
    {
        $pizzaiolo = new Pizzaiolo();

        $renderer = new IngredientConverter();
        $result = $renderer->ingredientToArray($pizzaiolo->getIngredientFromAlias('jambon'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('JAMBon');

        $controller = new GetIngredientFromAlias($pizzaiolo, $renderer);
        $response = $controller($request, new Response());

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame(json_encode($result), $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_no_ingredient_name_is_given(): void
    {
        $controller = new GetIngredientFromAlias(new Pizzaiolo(), new IngredientConverter());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(['carnivore']);

        $this->expectException(InvalidArgumentException::class);

        $controller($request, new Response());
    }

    /** @test */
    public function it_fails_if_the_ingredient_name_is_unknown(): void
    {
        $controller = new GetIngredientFromAlias(new Pizzaiolo(), new IngredientConverter());

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn('frites');

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`frites` is an invalid or an unknown ingredient.');

        $controller($request, new Response());
    }
}
