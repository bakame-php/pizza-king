<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

interface Dish extends Ingredient
{
    public function basePrice(): Euro;

    /**
     * @return array<Ingredient>
     */
    public function ingredients(): array;
}