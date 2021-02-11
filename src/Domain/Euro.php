<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function sprintf;

final class Euro
{
    private const CURRENCY = 'EUR';

    private function __construct(private int $amountInCents)
    {
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function toCents(): int
    {
        return $this->amountInCents;
    }

    public function amount(): float
    {
        return $this->amountInCents / 100;
    }

    public function currency(): string
    {
        return self::CURRENCY;
    }

    public function toString(): string
    {
        return sprintf('%.2f', $this->amountInCents / 100).' '.self::CURRENCY;
    }

    public function add(Euro $euro): self
    {
        return new self($this->amountInCents + $euro->amountInCents);
    }

    public function subtract(Euro $money): self
    {
        return new self($this->amountInCents - $money->amountInCents);
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->amountInCents * $multiplier);
    }
}
