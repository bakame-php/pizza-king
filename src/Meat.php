<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

final class Meat implements Ingredient
{
    public const HAM = 'ham';
    public const PEPPERONI = 'pepperoni';

    private Euro $price;

    private function __construct(private string $name, Euro $price)
    {
        if (0 > $price->cents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->price = $price;
    }

    public static function ham(Euro $price = null): self
    {
        return new self(self::HAM, $price ?? Euro::fromCents(2_00));
    }

    public static function pepperoni(Euro $price = null): self
    {
        return new self(self::PEPPERONI, $price ?? Euro::fromCents(4_00));
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
