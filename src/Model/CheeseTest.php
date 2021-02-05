<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use PHPUnit\Framework\TestCase;

final class CheeseTest extends TestCase
{
    /** @test */
    public function it_can_create_mozzarella_cheese(): void
    {
        $cheese = Cheese::fromName('mozzarella');

        self::assertSame('mozzarella', $cheese->name());
        self::assertEquals(Euro::fromCents(3_00), $cheese->price());
    }

    /** @test */
    public function it_can_create_mozzarella_cheese_with_specified_price(): void
    {
        $price = Euro::fromCents(6_00);
        $cheese = Cheese::fromName('mozzarella', $price);

        self::assertSame('mozzarella', $cheese->name());
        self::assertSame($price, $cheese->price());
    }

    /** @test */
    public function it_fails_creating_a_cheese_with_invalid_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Cheese::fromName('mozzarella', Euro::fromCents(-1));
    }

    /** @test */
    public function it_fails_creating_a_cheese_with_invalid_variety(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Cheese::fromName('mascarpone', Euro::fromCents(2_00));
    }

    /** @test */
    public function it_can_create_goat_cheese(): void
    {
        $cheese = Cheese::fromName('goat');

        self::assertSame('goat', $cheese->name());
        self::assertEquals(Euro::fromCents(2_00), $cheese->price());
    }

    /** @test */
    public function it_can_create_goat_cheese_with_specified_price(): void
    {
        $price = Euro::fromCents(6_00);
        $cheese = Cheese::fromName('goat', $price);

        self::assertSame('goat', $cheese->name());
        self::assertSame($price, $cheese->price());
    }
}
