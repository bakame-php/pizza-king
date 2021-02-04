<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use function strtolower;

final class Meat implements Ingredient
{
    private const HAM = 'ham';
    private const PEPPERONI = 'pepperoni';
    private const I18N = [
        self::PEPPERONI => self::PEPPERONI,
        self::HAM => self::HAM,
        'jambon' => self::HAM,
    ];

    private const PRICES = [
        self::HAM => 2_00,
        self::PEPPERONI => 4_00,
    ];

    private string $name;
    private Euro $price;

    private function __construct(string $name, Euro $price)
    {
        if (0 > $price->cents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->name = strtolower($name);
        $this->price = $price;
    }

    public static function isKnown(string $name): bool
    {
        return isset(self::I18N[strtolower($name)]);
    }

    public static function fromName(string $name, Euro $price = null): self
    {
        if (!self::isKnown($name)) {
            throw UnableToHandleIngredient::dueToUnknownIngredient($name);
        }

        return new self($name, $price ?? Euro::fromCents(self::PRICES[self::I18N[strtolower($name)]]));
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
