<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

use function array_filter;
use function array_map;
use function array_reduce;
use function count;
use function reset;
use function strtolower;

final class Pizza implements Dish
{
    private const NAME = 'pizza';

    /** @var array<Meat> */
    private array $meats;

    private function __construct(private Sauce $sauce, private Cheese $cheese, Meat ...$meats)
    {
        $this->meats = $meats;
    }

    public static function fromIngredientsByName(string ...$names): self
    {
        $converter = fn (string $name): Ingredient => match (strtolower($name)) {
            Cheese::MOZZARELLA => Cheese::mozzarella(),
            Cheese::GOAT, 'chevre' => Cheese::goat(),
            Sauce::TOMATO, 'sauce tomate' => Sauce::tomato(),
            Sauce::CREAM, 'creme' => Sauce::cream(),
            Meat::PEPPERONI => Meat::pepperoni(),
            Meat::HAM, 'jambon' => Meat::ham(),
            default => throw UnableToHandleIngredient::dueToUnknownIngredient($name),
        };

        return self::fromIngredients(...array_map($converter, $names));
    }

    public static function fromIngredients(Ingredient ...$ingredients): self
    {
        /** @var Cheese[] $cheeses */
        $cheeses = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Cheese);
        $nbCheese = count($cheeses);

        /** @var Sauce[] $sauces */
        $sauces = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Sauce);
        $nbSauce = count($sauces);

        /** @var Meat[] $meats */
        $meats = array_filter($ingredients, fn (Ingredient $ingredient): bool => $ingredient instanceof Meat);
        $nbMeat = count($meats);

        if (($nbMeat + $nbCheese + $nbSauce) !== count($ingredients)) {
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

        return match ($nbMeat) {
            0 => new self($sauce, $cheese),
            1, 2 => new self($sauce, $cheese, ...$meats),
            default => throw UnableToHandleIngredient::dueToWrongQuantity($nbMeat, 'meats'),
        };
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function cheese(): Cheese
    {
        return $this->cheese;
    }

    public function sauce(): Sauce
    {
        return $this->sauce;
    }

    /**
     * @return array<Meat>
     */
    public function meats(): array
    {
        return $this->meats;
    }

    public function ingredients(): array
    {
        return [$this->cheese, $this->sauce, ...$this->meats];
    }

    public function price(): Euro
    {
        return array_reduce(
            $this->ingredients(),
            fn (Euro $price, Ingredient $ingredient): Euro => $price->add($ingredient->price()),
            Euro::fromCents(400)
        );
    }
}
