<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use function array_map;

final class Pizzaiolo
{
    /**
     * @param array<string,int> $priceList
     */
    public function __construct(private array $priceList = [])
    {
    }

    /**
     * @param array<string> $names
     */
    public function composeFromIngredients(array $names, Euro $basePrice = null): Pizza
    {
        return Pizza::fromIngredients(array_map([$this, 'getIngredientFromName'], $names), $this->ingredientPrice('pizza', $basePrice));
    }

    public function getIngredientFromName(string $name, Euro $price = null): Ingredient
    {
        return match (true) {
            null !== Cheese::fetchAlias($name) => Cheese::fromAlias($name, $this->ingredientPrice(Cheese::fetchAlias($name), $price)),
            null !== Sauce::fetchAlias($name) => Sauce::fromAlias($name, $this->ingredientPrice(Sauce::fetchAlias($name), $price)),
            null !== Meat::fetchAlias($name) => Meat::fromAlias($name, $this->ingredientPrice(Meat::fetchAlias($name), $price)),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($name),
        };
    }

    private function ingredientPrice(string|null $name, Euro|null $price): Euro|null
    {
        return match (true) {
            null === $name || !isset($this->priceList[$name]) => $price,
            default => Euro::fromCents($this->priceList[$name]),
        };
    }

    public function composeFromName(string $name, Euro $basePrice = null): Pizza
    {
        $name = strtolower(trim($name));
        if ('' === $name) {
            throw UnableToHandleIngredient::dueToMissingIngredient('pizza name');
        }

        return match ($name) {
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
