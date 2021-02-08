<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

use function array_filter;
use function array_reduce;
use function count;
use function reset;

final class Pizza implements Dish
{
    private const NAME = 'pizza';

    private const DEFAULT_PRICE = 4_00;

    private Euro $basePrice;

    /** @var array<Meat> */
    private array $meats;

    private function __construct(
        Euro $basePrice,
        private Sauce $sauce,
        private Cheese $cheese,
        Meat ...$meats
    ) {
        $nbMeat = count($meats);
        if (2 < $nbMeat) {
            throw UnableToHandleIngredient::dueToWrongQuantity($nbMeat, 'meats');
        }

        if (0 > $basePrice->cents()) {
            throw UnableToHandleIngredient::dueToWrongBasePrice($basePrice, self::NAME);
        }

        $this->meats = $meats;
        $this->basePrice = $basePrice;
    }

    /**
     * @param array<Ingredient> $ingredients
     */
    public static function fromIngredients(array $ingredients, Euro $basePrice = null): self
    {
        /** @var Cheese[] $cheeses */
        $cheeses = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Cheese);
        /** @var Sauce[] $sauces */
        $sauces = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Sauce);
        /** @var Meat[] $meats */
        $meats = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Meat);

        $nbCheese = count($cheeses);
        $nbSauce = count($sauces);
        if (($nbCheese + $nbSauce + count($meats)) !== count($ingredients)) {
            throw UnableToHandleIngredient::dueToUnSupportedIngredient();
        }

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

        return new self($basePrice ?? Euro::fromCents(self::DEFAULT_PRICE), $sauce, $cheese, ...$meats);
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function alias(): string
    {
        return self::NAME;
    }

    public function ingredients(): array
    {
        return [$this->cheese, $this->sauce, ...$this->meats];
    }

    public function basePrice(): Euro
    {
        return $this->basePrice;
    }

    public function price(): Euro
    {
        return array_reduce(
            $this->ingredients(),
            fn (Euro $price, Ingredient $ingredient): Euro => $price->add($ingredient->price()),
            $this->basePrice
        );
    }
}
