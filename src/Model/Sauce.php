<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

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
    private string $alias;

    private function __construct(private string $name, Euro $price, string|null $alias)
    {
        if (0 > $price->cents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->price = $price;
        $this->alias = strtolower($alias ?? $this->name);
    }

    public static function fetchAlias(string $name): string|null
    {
        return self::I18N[strtolower($name)] ?? null;
    }

    public static function fromAlias(string $name, Euro $price = null): self
    {
        if (null === self::fetchAlias($name)) {
            throw UnableToHandleIngredient::dueToUnknownIngredient($name);
        }

        return new self(
            self::I18N[strtolower($name)],
            $price ?? Euro::fromCents(self::PRICES[self::I18N[strtolower($name)]]),
            $name
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function alias(): string
    {
        return $this->alias;
    }

    public function price(): Euro
    {
        return $this->price;
    }
}
