<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use PHPUnit\Framework\TestCase;

final class PizzaKingTest extends TestCase
{
    /**
     * @param array<string> $ingredients
     */
    public function createPizza(array $ingredients): Pizza
    {
        return Pizza::fromIngredientsName(...$ingredients);
    }

    public function testReine(): void
    {
        $pizza = $this->createPizza(['sauce tomate', 'jambon', 'mozzarella']);

        self::assertCount(3, $pizza->ingredients());
        self::assertEquals(Euro::fromCents(1000), $pizza->price());
    }

    public function testNapolitana(): void
    {
        $pizza = $this->createPizza(['sauce tomate', 'mozzarella']);

        self::assertCount(2, $pizza->ingredients());
        self::assertEquals(Euro::fromCents(800), $pizza->price());
    }

    public function testChevre(): void
    {
        $pizza = $this->createPizza(['chevre', 'tomato']);

        self::assertCount(2, $pizza->ingredients());
        self::assertEquals(Euro::fromCents(700), $pizza->price());
    }

    public function testCarnivore(): void
    {
        $pizza = $this->createPizza(['creme', 'mozzarella', 'jambon', 'pepperoni']);

        self::assertCount(4, $pizza->ingredients());
        self::assertEquals(Euro::fromCents(1400), $pizza->price());
    }

    // Manque la sauce !
    public function testErreurSauceManquante(): void
    {
        $this->expectException(\Exception::class);

        $this->createPizza(['mozzarella']);
    }

    // Manque le fromage !
    public function testErreurFromageManquant(): void
    {
        $this->expectException(\Exception::class);

        $this->createPizza(['creme']);
    }

    // C'est interdit le double fromage !
    public function testErreurDoubleFromageInterdit(): void
    {
        $this->expectException(\Exception::class);

        $this->createPizza(['sauce tomate', 'mozzarella', 'chevre']);
    }

    // N'importe quoi !
    public function testErreurIngredientsInconnus(): void
    {
        $this->expectException(\Exception::class);

        $this->createPizza(['frites', 'ananas']);
    }

    public function testErreurTripleViandeInterdit(): void
    {
        $this->expectException(\Exception::class);

        $this->createPizza(['mozzarella', 'sauce tomate', 'pepperoni', 'jambon', 'jambon']);
    }
}
