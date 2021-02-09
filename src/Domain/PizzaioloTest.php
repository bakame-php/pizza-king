<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use PHPUnit\Framework\TestCase;

final class PizzaioloTest extends TestCase
{
    private Pizzaiolo $pizzaiolo;

    public function setUp(): void
    {
        parent::setUp();

        $this->pizzaiolo = new Pizzaiolo();
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_without_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`sauce` is missing.');

        $this->pizzaiolo->composePizzaFromIngredients(['jambon', 'mozzarella']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`sauce` can not be used with the following quantity `2`.');

        $this->pizzaiolo->composePizzaFromIngredients(['cream', 'tomato', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_without_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` is missing.');

        $this->pizzaiolo->composePizzaFromIngredients(['jambon', 'tomato']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` can not be used with the following quantity `2`.');

        $this->pizzaiolo->composePizzaFromIngredients(['jambon', 'mozzarella', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_meat(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        $this->pizzaiolo->composePizzaFromIngredients(['mozzarella', 'tomato', 'jambon', 'pepperoni', 'pepperoni']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_ingredients_name(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $this->pizzaiolo->composePizzaFromIngredients(['mozzarella', 'tomato', 'ananas']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_pizza_name(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $this->pizzaiolo->composePizzaFromName('frites');
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_empty_pizza_name(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $this->pizzaiolo->composePizzaFromName('      ');
    }
}
