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
        return Pizza::fromIngredients(array_map([$this, 'getIngredientFromName'], $names), $basePrice);
    }

    public function getIngredientFromName(string $name, Euro $price = null): Ingredient
    {
        return match (true) {
            Cheese::isKnown($name) => Cheese::fromName($name, $price),
            Sauce::isKnown($name) => Sauce::fromName($name, $price),
            Meat::isKnown($name) => Meat::fromName($name, $price),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($name),
        };
    }

    public function composeFromName(string $name, Euro $basePrice = null): Pizza
    {
        $name = strtolower(trim($name));
        if ('' === $name) {
            throw UnableToHandleIngredient::dueToMissingIngredient('pizza name');
        }

        return match (strtolower($name)) {
            'reine', 'queen' => $this->composeQueen($basePrice),
            'napolitaine', 'napolitana' => $this->composeNapolitana($basePrice),
            'carnivore' => $this->composeCarnivore($basePrice),
            'chevre', 'goat' => $this->composeGoat($basePrice),
            default => throw UnableToHandleIngredient::dueToUnknownVariety($name, 'pizza'),
        };
    }

    public function composeQueen(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['tomato', 'jambon', 'mozzarella'], $basePrice);
    }

    public function composeNapolitana(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['tomato', 'mozzarella'], $basePrice);
    }

    public function composeGoat(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['tomato', 'chevre'], $basePrice);
    }

    public function composeCarnivore(Euro $basePrice = null): Pizza
    {
        return $this->composeFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni'], $basePrice);
    }
}
