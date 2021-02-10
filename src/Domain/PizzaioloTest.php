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

    /** @test */
    public function it_uses_a_price_list_to_calculate_price(): void
    {
        $priceList = [
            'pizza' => 1_00,
            'tomato' => 1_00,
            'cream' => 1_00,
            'mozzarella' => 1_00,
            'goat' => 1_00,
            'ham' => 1_00,
            'pepperoni' => 1_00,
        ];

        $pizzaiolo = new Pizzaiolo($priceList);

        self::assertNotEquals(
            $pizzaiolo->composeGoat()->price(),
            $this->pizzaiolo->composeGoat()->price()
        );
    }
}
