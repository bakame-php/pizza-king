<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

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
    public function composePizzaFromIngredients(array $names, Euro $basePrice = null): Pizza
    {
        return Pizza::fromIngredients(array_map([$this, 'getIngredientFromAlias'], $names), $this->ingredientPrice('pizza', $basePrice));
    }

    public function getIngredientFromAlias(string $alias, Euro $price = null): Ingredient
    {
        return match (true) {
            null !== Cheese::findName($alias) => Cheese::fromAlias($alias, $this->ingredientPrice(Cheese::findName($alias), $price)),
            null !== Sauce::findName($alias) => Sauce::fromAlias($alias, $this->ingredientPrice(Sauce::findName($alias), $price)),
            null !== Meat::findName($alias) => Meat::fromAlias($alias, $this->ingredientPrice(Meat::findName($alias), $price)),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($alias),
        };
    }

    private function ingredientPrice(string|null $name, Euro|null $price): Euro|null
    {
        return match (true) {
            null === $name || !isset($this->priceList[$name]) => $price,
            default => Euro::fromCents($this->priceList[$name]),
        };
    }

    public function composePizzaFromName(string $name, Euro $basePrice = null): Pizza
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
        return $this->composePizzaFromIngredients(['tomato', 'jambon', 'mozzarella'], $basePrice);
    }

    public function composeNapolitana(Euro $basePrice = null): Pizza
    {
        return $this->composePizzaFromIngredients(['tomato', 'mozzarella'], $basePrice);
    }

    public function composeGoat(Euro $basePrice = null): Pizza
    {
        return $this->composePizzaFromIngredients(['tomato', 'chevre'], $basePrice);
    }

    public function composeCarnivore(Euro $basePrice = null): Pizza
    {
        return $this->composePizzaFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni'], $basePrice);
    }
}
