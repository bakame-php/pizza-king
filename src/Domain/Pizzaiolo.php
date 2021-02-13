<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function array_filter;
use function array_map;
use function count;
use function is_int;
use function reset;

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
    public function composeClassicPizzaFromIngredients(array $aliases): Pizza
    {
        /** @var array<Ingredient> $ingredients */
        $ingredients = array_map([$this, 'getIngredientFromAlias'], $aliases);

        /** @var Cheese[] $cheeses */
        $cheeses = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Cheese);
        /** @var Sauce[] $sauces */
        $sauces = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Sauce);
        /** @var Meat[] $meats */
        $meats = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Meat);

        $nbCheese = count($cheeses);
        $nbSauce = count($sauces);
        $nbMeat = count($meats);

        /** @var Cheese $cheese */
        $cheese = match ($nbCheese) {
            0 => throw UnableToHandleIngredient::dueToMissingIngredient('cheese'),
            1 => reset($cheeses),
            default => throw UnableToHandleIngredient::dueToWrongQuantity($nbCheese, 'cheese'),
        };

        /** @var Sauce $sauce */
        $sauce = match ($nbSauce) {
            0 => throw UnableToHandleIngredient::dueToMissingIngredient('sauce'),
            1 => reset($sauces),
            // no break
            default => throw UnableToHandleIngredient::dueToWrongQuantity($nbSauce, 'sauce'),
       };

        if (2 < $nbMeat) {
            throw UnableToHandleIngredient::dueToWrongQuantity($nbMeat, 'meats');
        }

        return Pizza::fromIngredients([$sauce, $cheese, ...$meats], $this->ingredientPrice('pizza'));
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
        return $this->composeClassicPizzaFromIngredients(['tomato', 'jambon', 'mozzarella']);
    }

    public function composeNapolitana(): Pizza
    {
        return $this->composeClassicPizzaFromIngredients(['tomato', 'mozzarella']);
    }

    public function composeGoat(): Pizza
    {
        return $this->composeClassicPizzaFromIngredients(['tomato', 'chevre']);
    }

    public function composeCarnivore(): Pizza
    {
        return $this->composeClassicPizzaFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni']);
    }
}
