<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

interface Dish extends Ingredient
{
    /**
     * @return array<Ingredient>
     */
    public function ingredients(): array;
}
