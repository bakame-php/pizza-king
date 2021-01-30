<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use PHPUnit\Framework\TestCase;

final class EuroTest extends TestCase
{
    /** @test */
    public function it_can_give_insight_on_its_status(): void
    {
        $money = Euro::fromCents(4900);

        self::assertSame(4900, $money->cents());
        self::assertSame('EUR', $money->currency());
        self::assertSame('49.00 EUR', $money->toString());
    }

    /** @test */
    public function it_can_be_added(): void
    {
        self::assertEquals(Euro::fromCents(3000), Euro::fromCents(2000)->add(Euro::fromCents(1000)));
    }

    /** @test */
    public function it_can_be_subtract(): void
    {
        self::assertEquals(Euro::fromCents(100), Euro::fromCents(2000)->subtract(Euro::fromCents(1900)));
    }

    /** @test */
    public function it_can_be_multiply(): void
    {
        self::assertEquals(Euro::fromCents(50), Euro::fromCents(10)->multiply(5));
    }
}
