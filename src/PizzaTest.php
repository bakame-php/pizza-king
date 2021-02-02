<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use PHPUnit\Framework\TestCase;

final class PizzaTest extends TestCase
{
    /** @test */
    public function it_composes_a_pizza(): void
    {
        $sauce = Sauce::fromName('tomato');
        $cheese = Cheese::fromName('mozzarella');
        $meat = Meat::fromName('pepperoni');
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
        $sauce = Sauce::fromName('tomato');
        $cheese = Cheese::fromName('mozzarella');
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
        $sauce = Sauce::fromName('tomato');
        $cheese = Cheese::fromName('mozzarella');
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
        $this->expectExceptionMessage('`pizza` can not be priced at `-1.00 EUR`.');

        Pizza::fromIngredients(
            [Sauce::fromName('tomato'), Cheese::fromName('mozzarella')],
            Euro::fromCents(-5_00)
        );
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_without_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`sauce` is missing.');

        Pizza::fromIngredientsByName(['jambon', 'mozzarella']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`sauce` can not be used with the following quantity `2`.');

        Pizza::fromIngredientsByName(['cream', 'tomato', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_without_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` is missing.');

        Pizza::fromIngredientsByName(['jambon', 'tomato']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` can not be used with the following quantity `2`.');

        Pizza::fromIngredientsByName(['jambon', 'mozzarella', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_meat(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        Pizza::fromIngredientsByName(['mozzarella', 'tomato', 'jambon', 'pepperoni', 'pepperoni']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_ingredients_name(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsByName(['mozzarella', 'tomato', 'ananas']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_ingredient(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $ananas = $this->createStub(Ingredient::class);
        $ananas->method('name')->willReturn('ananas');
        $ananas->method('price')->willReturn(Euro::fromCents(30_00));

        Pizza::fromIngredients([
            Cheese::fromName('chevre'),
            Sauce::fromName('sauce TomAte'),
            $ananas,
        ]);
    }
}
