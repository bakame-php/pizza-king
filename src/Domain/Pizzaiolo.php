<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function array_map;
use function is_int;

final class Pizzaiolo
{
    /**
     * @var array<string,Euro>
     */
    private array $priceList;

    public function __construct(iterable $priceList = [])
    {
        $this->priceList = [];
        foreach ($priceList as $ingredient => $cents) {
            if (!is_string($ingredient) || !is_int($cents)) {
                throw UnableToHandleIngredient::dueToInvalidPriceList();
            }

            $amount = Euro::fromCents($cents);
            if (0 > $amount->toCents()) {
                throw UnableToHandleIngredient::dueToWrongPrice($amount, $ingredient);
            }

            $this->priceList[$ingredient] = $amount;
        }
    }

    /**
     * @param array<string> $aliases
     */
    public function composePizzaFromIngredients(array $aliases): Pizza
    {
        return Pizza::fromIngredients(array_map([$this, 'getIngredientFromAlias'], $aliases), $this->ingredientPrice('pizza'));
    }

    public function getIngredientFromAlias(string $alias): Ingredient
    {
        return match (true) {
            null !== Cheese::findName($alias) => Cheese::fromAlias($alias, $this->ingredientPrice(Cheese::findName($alias))),
            null !== Sauce::findName($alias) => Sauce::fromAlias($alias, $this->ingredientPrice(Sauce::findName($alias))),
            null !== Meat::findName($alias) => Meat::fromAlias($alias, $this->ingredientPrice(Meat::findName($alias))),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($alias),
        };
    }

    private function ingredientPrice(string|null $name): Euro|null
    {
        return $this->priceList[$name] ?? null;
    }

    public function composePizzaFromName(string $name): Pizza
    {
        $name = strtolower(trim($name));
        if ('' === $name) {
            throw UnableToHandleIngredient::dueToMissingIngredient('pizza name');
        }

        return match ($name) {
            'reine', 'queen' => $this->composeQueen(),
            'napolitaine', 'napolitana' => $this->composeNapolitana(),
            'carnivore' => $this->composeCarnivore(),
            'chevre', 'goat' => $this->composeGoat(),
            default => throw UnableToHandleIngredient::dueToUnknownVariety($name, 'pizza'),
        };
    }

    public function composeQueen(): Pizza
    {
        return $this->composePizzaFromIngredients(['tomato', 'jambon', 'mozzarella']);
    }

    public function composeNapolitana(): Pizza
    {
        return $this->composePizzaFromIngredients(['tomato', 'mozzarella']);
    }

    public function composeGoat(): Pizza
    {
        return $this->composePizzaFromIngredients(['tomato', 'chevre']);
    }

    public function composeCarnivore(): Pizza
    {
        return $this->composePizzaFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni']);
    }
}
