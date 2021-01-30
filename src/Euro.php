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
}
