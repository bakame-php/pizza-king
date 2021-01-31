<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

final class Sauce implements Ingredient
{
    public const TOMATO = 'tomato';
    public const CREAM = 'cream';

    private Euro $price;

    private function __construct(private string $name, Euro $price)
    {
        if ($price->cents() < 0) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->price = $price;
    }

    public static function tomato(Euro $price = null): self
    {
        return new self(self::TOMATO, $price ?? Euro::fromCents(1_00));
    }

    public static function cream(Euro $price = null): self
    {
        return new self(self::CREAM, $price ?? Euro::fromCents(1_00));
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
