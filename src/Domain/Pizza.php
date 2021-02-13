<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use function array_reduce;

final class Pizza implements Dish
{
    private const NAME = 'pizza';
    private const DEFAULT_PRICE = 4_00;

    private Euro $basePrice;
    /** @var array<Ingredient> */
    private array $ingredients;
    private Euro $price;

    private function __construct(Euro $basePrice, Ingredient ...$ingredients)
    {
        if (0 > $basePrice->toCents()) {
            throw UnableToHandleIngredient::dueToWrongBasePrice($basePrice, self::NAME);
        }

        $this->basePrice = $basePrice;
        $this->ingredients = $ingredients;
        $this->price = array_reduce(
            $ingredients,
            fn (Euro $price, Ingredient $ingredient): Euro => $price->add($ingredient->price()),
            $this->basePrice
        );
    }

    /**
     * @param array<Ingredient> $ingredients
     */
    public static function fromIngredients(array $ingredients, Euro $basePrice = null): self
    {
        return new self($basePrice ?? Euro::fromCents(self::DEFAULT_PRICE), ...$ingredients);
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
        return $this->ingredients;
    }

    public function basePrice(): Euro
    {
        return $this->basePrice;
    }

    public function price(): Euro
    {
        return $this->price;
    }
}
