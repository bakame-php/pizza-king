<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use PHPUnit\Framework\TestCase;

final class SauceTest extends TestCase
{
    /** @test */
    public function it_can_create_tomato_sauce(): void
    {
        $sauce = Sauce::fromAlias('tomato');

        self::assertSame('tomato', $sauce->name());
        self::assertEquals(Euro::fromCents(1_00), $sauce->price());
    }

    /** @test */
    public function it_can_create_tomato_sauce_with_specified_price(): void
    {
        $price = Euro::fromCents(4_00);
        $sauce = Sauce::fromAlias('SaUCe ToMATe', $price);

        self::assertSame('tomato', $sauce->name());
        self::assertSame('sauce tomate', $sauce->alias());
        self::assertSame($price, $sauce->price());
    }

    /** @test */
    public function it_fails_creating_a_tomato_sauce_with_invalid_unit_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Sauce::fromAlias('tomato', Euro::fromCents(-1));
    }

    /** @test */
    public function it_fails_creating_a_sauce_with_invalid_variety(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Sauce::fromAlias('béarnaise', Euro::fromCents(2_00));
    }

    /** @test */
    public function it_can_create_cream_sauce(): void
    {
        $sauce = Sauce::fromAlias('cream');

        self::assertSame('cream', $sauce->name());
        self::assertEquals(Euro::fromCents(1_00), $sauce->price());
    }

    /** @test */
    public function it_can_create_cream_sauce_with_specified_unit_price(): void
    {
        $price = Euro::fromCents(4_00);
        $sauce = Sauce::fromAlias('cream', $price);

        self::assertSame('cream', $sauce->name());
        self::assertSame($price, $sauce->price());
    }
}
