<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function strtolower;

final class Cheese implements Ingredient
{
    private const MOZZARELLA = 'mozzarella';
    private const GOAT = 'goat';
    private const ALIASES = [
        self::MOZZARELLA => self::MOZZARELLA,
        self::GOAT => self::GOAT,
        'chevre' => self::GOAT,
    ];
    private const PRICES = [
        self::MOZZARELLA => 3_00,
        self::GOAT => 2_00,
    ];

    private Euro $price;
    private string $alias;

    private function __construct(
        private string $name,
        Euro $price,
        string|null $alias
    ) {
        if (0 > $price->toCents()) {
            throw UnableToHandleIngredient::dueToWrongPrice($price, $name);
        }

        $this->price = $price;
        $this->alias = strtolower($alias ?? $this->name);
    }

    public static function findName(string $alias): string|null
    {
        return self::ALIASES[strtolower($alias)] ?? null;
    }

    public static function fromAlias(string $alias, Euro $price = null): self
    {
        $name = self::findName($alias);
        if (null === $name) {
            throw UnableToHandleIngredient::dueToUnknownIngredient($alias);
        }

        return new self(
            $name,
            $price ?? Euro::fromCents(self::PRICES[$name]),
            $alias
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
