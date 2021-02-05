<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use PHPUnit\Framework\TestCase;

final class EuroTest extends TestCase
{
    /** @test */
    public function it_can_give_insight_on_its_status(): void
    {
        $money = Euro::fromCents(49_00);

        self::assertSame(49_00, $money->cents());
        self::assertSame('EUR', $money->currency());
        self::assertSame('49.00 EUR', $money->toString());
    }

    /** @test */
    public function it_can_be_added(): void
    {
        self::assertEquals(Euro::fromCents(30_00), Euro::fromCents(20_00)->add(Euro::fromCents(10_00)));
    }

    /** @test */
    public function it_can_be_subtract(): void
    {
        self::assertEquals(Euro::fromCents(1_00), Euro::fromCents(20_00)->subtract(Euro::fromCents(19_00)));
    }

    /** @test */
    public function it_can_be_multiply(): void
    {
        self::assertEquals(Euro::fromCents(50), Euro::fromCents(10)->multiply(5));
    }
}
