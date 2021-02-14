<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

final class Pizza implements Dish
{
    private const NAME = 'pizza';
    private const DEFAULT_PRICE = 4_00;

    private Euro $basePrice;

    private function __construct(Euro $basePrice, private IngredientList $ingredients)
    {
        if (0 > $basePrice->toCents()) {
            throw UnableToHandleIngredient::dueToWrongBasePrice($basePrice, self::NAME);
        }

        $this->basePrice = $basePrice;
    }

    public static function fromIngredients(IngredientList $ingredients, Euro $basePrice = null): self
    {
        return new self($basePrice ?? Euro::fromCents(self::DEFAULT_PRICE), $ingredients);
    }

    public function name(): string
    {
        return self::NAME;
    }

    public function alias(): string
    {
        return self::NAME;
    }

    public function ingredients(): IngredientList
    {
        return $this->ingredients;
    }

    public function basePrice(): Euro
    {
        return $this->basePrice;
    }

    public function price(): Euro
    {
        return $this->basePrice->add($this->ingredients->price());
    }
}
