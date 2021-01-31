<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

final class Cheese implements Ingredient
{
    public const MOZZARELLA = 'mozzarella';
    public const GOAT = 'goat';

    private Euro $price;

    private function __construct(private string $name, Euro $price)
    {
        if (0 > $price->cents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->price = $price;
    }

    public static function goat(Euro $price = null): self
    {
        return new self(self::GOAT, $price ?? Euro::fromCents(2_00));
    }

    public static function mozzarella(Euro $price = null): self
    {
        return new self(self::MOZZARELLA, $price ?? Euro::fromCents(3_00));
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
