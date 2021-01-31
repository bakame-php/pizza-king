<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use function sprintf;

final class Euro
{
    private function __construct(private int $cents)
    {
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function currency(): string
    {
        return 'EUR';
    }

    public function toString(): string
    {
        return sprintf('%.2f', ($this->cents / 100)).' '.$this->currency();
    }

    public function add(Euro $euro): self
    {
        return new self($this->cents + $euro->cents());
    }

    public function subtract(Euro $money): self
    {
        return new self($this->cents - $money->cents());
    }

    public function multiply(int $multiplier): self
    {
        return new self($this->cents * $multiplier);
    }

    public function compare(Euro $money): int
    {
        return $this->cents <=> $money->cents;
    }

    public function equals(Euro $money): bool
    {
        return 0 === $this->compare($money);
    }

    public function greaterThan(Euro $money): bool
    {
        return 1 === $this->compare($money);
    }

    public function lessThan(Euro $money): bool
    {
        return -1 === $this->compare($money);
    }
}
