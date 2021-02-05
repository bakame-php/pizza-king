<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use function array_map;

final class Pizzaiolo
{
    /**
     * @param array<string> $names
     */
    public function composeFromIngredients(array $names, Euro $basePrice = null): Pizza
    {
        $converter = fn (string $name): Ingredient => match (true) {
            Cheese::isKnown($name) => Cheese::fromName($name),
            Sauce::isKnown($name) => Sauce::fromName($name),
            Meat::isKnown($name) => Meat::fromName($name),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($name),
        };

        return Pizza::fromIngredients(array_map($converter, $names), $basePrice);
    }

    public function composeFromName(string $name, Euro $basePrice = null): Pizza
    {
        $name = strtolower(trim($name));
        if ('' === $name) {
            throw UnableToHandleIngredient::dueToMissingIngredient('pizza name');
        }

        return match (strtolower($name)) {
            'reine', 'queen' => $this->composeReine($basePrice),
            'napolitaine', 'napolitana' => $this->composeNapolitana($basePrice),
            'carnivore' => $this->composeCarnivore($basePrice),
            'chevre', 'goat' => $this->composeGoat($basePrice),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($name),
        };
    }

    public function composeReine(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['sauce tomate', 'jambon', 'mozzarella'], $basePrice);
    }

    public function composeNapolitana(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['sauce tomate', 'mozzarella'], $basePrice);
    }

    public function composeGoat(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['chevre', 'tomato'], $basePrice);
    }

    public function composeCarnivore(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni'], $basePrice);
    }
}
