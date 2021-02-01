<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use function strtolower;

final class Sauce implements Ingredient
{
    private const TOMATO = 'tomato';
    private const CREAM = 'cream';
    private const I18N = [
        self::TOMATO => self::TOMATO,
        'sauce tomate' => self::TOMATO,
        self::CREAM => self::CREAM,
        'creme' => self::CREAM,
    ];

    private const PRICES = [
        self::TOMATO => 1_00,
        self::CREAM => 1_00,
    ];

    private Euro $price;

    private function __construct(private string $name, Euro $price)
    {
        if ($price->cents() < 0) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

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

        $variety = self::I18N[strtolower($name)];

        return new self($variety, $price ?? Euro::fromCents(self::PRICES[$variety]));
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
