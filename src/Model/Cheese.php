<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use function strtolower;

final class Cheese implements Ingredient
{
    private const MOZZARELLA = 'mozzarella';
    private const GOAT = 'goat';
    private const I18N = [
        self::MOZZARELLA => self::MOZZARELLA,
        self::GOAT => self::GOAT,
        'chevre' => self::GOAT,
    ];
    private const PRICES = [
        self::MOZZARELLA => 3_00,
        self::GOAT => 2_00,
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
