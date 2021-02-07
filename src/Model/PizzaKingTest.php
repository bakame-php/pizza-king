<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use PHPUnit\Framework\TestCase;

final class PizzaKingTest extends TestCase
{
    private Pizzaiolo $pizzaiolo;

    public function setUp(): void
    {
        parent::setUp();

        $this->pizzaiolo = new Pizzaiolo();
    }

    /**
     * @param array<string> $ingredients
     */
    public function createPizza(array $ingredients): Pizza
    {
        return $this->pizzaiolo->composeFromIngredients($ingredients);
    }

    public function testReine(): void
    {
        $pizza = $this->pizzaiolo->composeQueen();

        self::assertCount(3, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromName('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromName('jambon'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromName('mozzarella'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(10_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composeFromName('queen'));
    }

    public function testNapolitana(): void
    {
        $pizza = $this->pizzaiolo->composeNapolitana();

        self::assertCount(2, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromName('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromName('mozzarella'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(8_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composeFromName('napolitana'));
    }

    public function testChevre(): void
    {
        $pizza = $this->pizzaiolo->composeGoat();

        self::assertCount(2, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromName('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromName('chevre'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(7_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composeFromName('chevre'));
    }

    public function testCarnivore(): void
    {
        $pizza = $this->pizzaiolo->composeCarnivore();

        self::assertCount(4, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromName('creme'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromName('mozzarella'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromName('jambon'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromName('pepperoni'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(14_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composeFromName('carnivore'));
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
