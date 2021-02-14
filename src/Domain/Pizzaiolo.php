<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function array_map;
use function is_int;
use function strtolower;

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
        $ingredients = IngredientList::fromList(array_map([$this, 'getIngredientFromAlias'], $aliases));

        $nbCheese = $ingredients->filter(fn (Ingredient $ingredient): bool => $ingredient instanceof Cheese)->count();
        if (1 !== $nbCheese) {
            throw UnableToHandleIngredient::dueToWrongQuantity($nbCheese, 'cheese');
        }

        $nbSauce = $ingredients->filter(fn (Ingredient $ingredient): bool => $ingredient instanceof Sauce)->count();
        if (1 !== $nbSauce) {
            throw UnableToHandleIngredient::dueToWrongQuantity($nbSauce, 'sauce');
        };

        $nbMeat = $ingredients->filter(fn (Ingredient $ingredient): bool => $ingredient instanceof Meat)->count();
        if (2 < $nbMeat) {
            throw UnableToHandleIngredient::dueToWrongQuantity($nbMeat, 'meats');
        }

        return Pizza::fromIngredients($ingredients, $this->ingredientPrice('pizza'));
    }

    public function getIngredientFromAlias(string $alias): Ingredient
    {
        $normalizedAlias = trim(strtolower($alias));

        return match ($normalizedAlias) {
            'mozzarella', 'goat', 'chevre' => Cheese::fromAlias($normalizedAlias, $this->ingredientPrice(Cheese::findName($normalizedAlias))),
            'sauce tomate', 'tomato', 'cream', 'creme' => Sauce::fromAlias($normalizedAlias, $this->ingredientPrice(Sauce::findName($normalizedAlias))),
            'jambon', 'ham', 'pepperoni' => Meat::fromAlias($normalizedAlias, $this->ingredientPrice(Meat::findName($normalizedAlias))),
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

        return match ($name) {
            '' => throw UnableToHandleIngredient::dueToMissingIngredient('pizza name'),
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
