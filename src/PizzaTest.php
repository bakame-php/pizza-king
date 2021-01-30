<?php

/**
 * Pizza King (https://github.com/bakame-php/pizza-king/)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bakame\PizzaKing;

use PHPUnit\Framework\TestCase;

final class PizzaTest extends TestCase
{
    /** @test */
    public function it_can_bake_a_pizza(): void
    {
        $sauce = Sauce::tomato();
        $cheese = Cheese::mozzarella();
        $meat = Meat::pepperoni();
        $pizza = Pizza::fromIngredients($sauce, $cheese, $meat);

        self::assertSame('pizza', $pizza->name());
        self::assertSame($sauce, $pizza->sauce());
        self::assertSame($cheese, $pizza->cheese());
        self::assertSame([$meat], $pizza->meats());
        self::assertEquals(Euro::fromCents(1200), $pizza->price());

        $ingredients = $pizza->toIngredients();

        self::assertContainsOnlyInstancesOf(Ingredient::class, $ingredients);
        self::assertContains($cheese, $ingredients);
        self::assertContains($sauce, $ingredients);
        self::assertContains($meat, $ingredients);
        self::assertCount(3, $ingredients);
    }

    /** @test */
    public function it_can_bake_a_pizza_without_meat(): void
    {
        $sauce = Sauce::tomato();
        $cheese = Cheese::mozzarella();
        $pizza = Pizza::fromIngredients($sauce, $cheese);

        self::assertSame('pizza', $pizza->name());
        self::assertSame($sauce, $pizza->sauce());
        self::assertSame($cheese, $pizza->cheese());
        self::assertEquals([], $pizza->meats());
        self::assertEquals(Euro::fromCents(800), $pizza->price());
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_without_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('mozzarella', 'jambon');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_with_too_much_sauce(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('tomato', 'cream', 'jambon', 'mozzarella');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_without_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('tomato', 'jambon');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_with_too_much_cheese(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('tomato', 'jambon', 'mozzarella', 'goat');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_with_too_much_meat(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('tomato', 'mozzarella', 'jambon', 'jambon', 'pepperoni');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_with_unknown_ingredients(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Pizza::fromIngredientsName('tomato', 'mozzarella', 'foobar', 'jambon', 'pepperoni');
    }

    /** @test */
    public function it_fails_to_bake_a_pizza_with_unknown_ingredient(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        $ananas = $this->createStub(Ingredient::class);
        $ananas->method('name')->willReturn('ananas');
        $ananas->method('price')->willReturn(Euro::fromCents(3000));

        Pizza::fromIngredients(Sauce::tomato(), Cheese::goat(), $ananas);
    }
}
