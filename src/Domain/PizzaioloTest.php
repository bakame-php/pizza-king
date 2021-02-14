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

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['jambon', 'mozzarella']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`sauce` can not be used with the following quantity `2`.');

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['cream', 'tomato', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_without_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` is missing.');

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['jambon', 'tomato']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`cheese` can not be used with the following quantity `2`.');

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['jambon', 'mozzarella', 'goat']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_too_much_meat(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` can not be used with the following quantity `3`.');

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['mozzarella', 'tomato', 'jambon', 'pepperoni', 'pepperoni']);
    }

    /** @test */
    public function it_fails_to_compose_a_pizza_with_unknown_ingredients_name(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $this->pizzaiolo->composeClassicPizzaFromIngredients(['mozzarella', 'tomato', 'ananas']);
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

    /** @test */
    public function it_fails_to_be_instantiated_with_a_wrong_price_list(): void
    {
        $priceList = [
            'foobar' => '1 euro',
        ];

        $this->expectException(UnableToHandleIngredient::class);

        new Pizzaiolo($priceList);
    }

    /** @test */
    public function it_fails_to_be_instantiated_with_a_negative_ingredient_price(): void
    {
        $priceList = [
            'foobar' => -1,
        ];

        $this->expectException(UnableToHandleIngredient::class);

        new Pizzaiolo($priceList);
    }

    /** @test */
    public function it_can_compose_a_pizza_queen(): void
    {
        $pizza = $this->pizzaiolo->composeQueen();

        self::assertCount(3, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromAlias('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromAlias('jambon'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromAlias('mozzarella'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(10_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composePizzaFromName('queen'));
    }

    /** @test */
    public function it_can_compose_a_pizza_napolitana(): void
    {
        $pizza = $this->pizzaiolo->composeNapolitana();

        self::assertCount(2, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromAlias('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromAlias('mozzarella'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(8_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composePizzaFromName('napolitana'));
    }

    /** @test */
    public function it_can_compose_a_pizza_chevre(): void
    {
        $pizza = $this->pizzaiolo->composeGoat();

        self::assertCount(2, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromAlias('tomato'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromAlias('chevre'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(7_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composePizzaFromName('chevre'));
    }

    /** @test */
    public function it_can_compose_a_pizza_carnivore(): void
    {
        $pizza = $this->pizzaiolo->composeCarnivore();

        self::assertCount(4, $pizza->ingredients());
        self::assertContainsEquals(Sauce::fromAlias('creme'), $pizza->ingredients());
        self::assertContainsEquals(Cheese::fromAlias('mozzarella'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromAlias('jambon'), $pizza->ingredients());
        self::assertContainsEquals(Meat::fromAlias('pepperoni'), $pizza->ingredients());
        self::assertEquals(Euro::fromCents(14_00), $pizza->price());
        self::assertEquals($pizza, $this->pizzaiolo->composePizzaFromName('carnivore'));
    }
}
