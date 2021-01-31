<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

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

    /** @test */
    public function it_can_be_compare_with_each_other(): void
    {
        $money1 = Euro::fromCents(50);
        $money2 = Euro::fromCents(1_00);
        $money3 = Euro::fromCents(1_00);

        self::assertTrue($money1->lessThan($money2));
        self::assertTrue($money2->greaterThan($money1));
        self::assertTrue($money3->equals($money2));
    }
}
