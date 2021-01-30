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

final class Meat implements Ingredient
{
    public const HAM = 'ham';
    public const PEPPERONI = 'pepperoni';

    private Euro $price;

    private function __construct(private string $name, Euro $unitPrice)
    {
        if (0 > $unitPrice->cents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($unitPrice, $name);
        }

        $this->price = $unitPrice;
    }

    public static function ham(Euro $price = null): self
    {
        return new self(self::HAM, $price ?? Euro::fromCents(200));
    }

    public static function pepperoni(Euro $price = null): self
    {
        return new self(self::PEPPERONI, $price ?? Euro::fromCents(400));
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): Euro
    {
        return $this->price;
    }
}
