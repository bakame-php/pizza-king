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

final class SauceTest extends TestCase
{
    /** @test */
    public function it_can_create_tomato_sauce(): void
    {
        $sauce = Sauce::tomato();

        self::assertSame('tomato', $sauce->name());
        self::assertEquals(Euro::fromCents(100), $sauce->price());
    }

    /** @test */
    public function it_can_create_tomato_sauce_with_specified_price(): void
    {
        $price = Euro::fromCents(400);
        $sauce = Sauce::tomato($price);

        self::assertSame('tomato', $sauce->name());
        self::assertSame($price, $sauce->price());
    }

    /** @test */
    public function it_fails_creating_a_tomato_sauce_with_invalid_unit_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Sauce::tomato(price: Euro::fromCents(-1));
    }

    /** @test */
    public function it_can_create_cream_sauce(): void
    {
        $sauce = Sauce::cream();

        self::assertSame('cream', $sauce->name());
        self::assertEquals(Euro::fromCents(100), $sauce->price());
    }

    /** @test */
    public function it_can_create_cream_sauce_with_specified_unit_price(): void
    {
        $price = Euro::fromCents(400);
        $sauce = Sauce::cream($price);

        self::assertSame('cream', $sauce->name());
        self::assertSame($price, $sauce->price());
    }

    /** @test */
    public function it_fails_creating_a_cream_sauce_with_invalid_unit_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Sauce::cream(Euro::fromCents(-1));
    }
}
