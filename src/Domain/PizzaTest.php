<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use PHPUnit\Framework\TestCase;

final class PizzaTest extends TestCase
{
    /** @test */
    public function it_composes_a_pizza(): void
    {
        $sauce = Sauce::fromAlias('tomato');
        $cheese = Cheese::fromAlias('mozzarella');
        $meat = Meat::fromAlias('pepperoni');
        $pizza = Pizza::fromIngredients([$sauce, $cheese, $meat]);
        $ingredients = $pizza->ingredients();

        self::assertSame('pizza', $pizza->name());
        self::assertEquals(Euro::fromCents(4_00), $pizza->basePrice());
        self::assertEquals(Euro::fromCents(12_00), $pizza->price());
        self::assertContainsOnlyInstancesOf(Ingredient::class, $ingredients);
        self::assertContains($cheese, $ingredients);
        self::assertContains($sauce, $ingredients);
        self::assertContains($meat, $ingredients);
        self::assertCount(3, $ingredients);
    }

    /** @test */
    public function it_composes_a_pizza_without_meat(): void
    {
        $sauce = Sauce::fromAlias('tomato');
        $cheese = Cheese::fromAlias('mozzarella');
        $pizza = Pizza::fromIngredients([$sauce, $cheese]);
        $ingredients = $pizza->ingredients();
        self::assertCount(2, $ingredients);
        self::assertContains($cheese, $ingredients);
        self::assertContains($sauce, $ingredients);
        self::assertEquals(Euro::fromCents(8_00), $pizza->price());
    }

    /** @test */
    public function it_composes_a_pizza_with_a_difference_base_price(): void
    {
        $sauce = Sauce::fromAlias('tomato');
        $cheese = Cheese::fromAlias('mozzarella');
        $pizza = Pizza::fromIngredients([$sauce, $cheese]);
        $altPizza = Pizza::fromIngredients([$sauce, $cheese], Euro::fromCents(10_00));
        self::assertEquals(Euro::fromCents(4_00), $pizza->basePrice());
        self::assertEquals(Euro::fromCents(8_00), $pizza->price());
        self::assertEquals(Euro::fromCents(10_00), $altPizza->basePrice());
        self::assertEquals(Euro::fromCents(14_00), $altPizza->price());
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_a_negative_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`pizza` can not have a base price of `-5.00 EUR`.');

        Pizza::fromIngredients(
            [Sauce::fromAlias('tomato'), Cheese::fromAlias('mozzarella')],
            Euro::fromCents(-5_00)
        );
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_ingredient(): void
    {
        $ananas = $this->createStub(Ingredient::class);
        $ananas->method('name')->willReturn('ananas');
        $ananas->method('price')->willReturn(Euro::fromCents(30_00));

        $pizza = Pizza::fromIngredients([
            Cheese::fromAlias('chevre'),
            Sauce::fromAlias('sauce TomAte'),
            $ananas,
        ]);

        self::assertContains($ananas, $pizza->ingredients());
    }
}
