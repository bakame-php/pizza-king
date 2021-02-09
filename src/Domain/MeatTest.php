<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use PHPUnit\Framework\TestCase;

final class MeatTest extends TestCase
{
    /** @test */
    public function it_can_create_pepperoni_meat(): void
    {
        $sauce = Meat::fromAlias('PePpeROnI');

        self::assertSame('pepperoni', $sauce->name());
        self::assertEquals(Euro::fromCents(400), $sauce->price());
    }

    /** @test */
    public function it_can_create_pepperoni_meat_with_specified_price(): void
    {
        $price = Euro::fromCents(10_00);
        $sauce = Meat::fromAlias('PEPPERONI', $price);

        self::assertSame('pepperoni', $sauce->name());
        self::assertSame('pepperoni', $sauce->alias());
        self::assertSame($price, $sauce->price());
    }

    /** @test */
    public function it_fails_creating_a_pepperoni_meat_with_invalid_price(): void
    {
        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`pepperoni` can not be priced at `-0.01 EUR`.');

        Meat::fromAlias('pepperoni', Euro::fromCents(-1));
    }

    /** @test */
    public function it_fails_creating_a_meat_with_invalid_variety(): void
    {
        $this->expectException(UnableToHandleIngredient::class);

        Meat::fromAlias('pork', Euro::fromCents(2_00));
    }

    /** @test */
    public function it_can_create_ham_meat(): void
    {
        $sauce = Meat::fromAlias('JaMbOn');

        self::assertSame('ham', $sauce->name());
        self::assertSame('jambon', $sauce->alias());
        self::assertEquals(Euro::fromCents(2_00), $sauce->price());
    }

    /** @test */
    public function it_can_create_ham_meat_with_specified_unit_price(): void
    {
        $price = Euro::fromCents(4_00);
        $sauce = Meat::fromAlias('JAMBON', $price);

        self::assertSame('ham', $sauce->name());
        self::assertSame('jambon', $sauce->alias());
        self::assertSame($price, $sauce->price());
    }
}
