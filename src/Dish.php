<?php

declare(strict_types=1);

namespace Bakame\PizzaKing;

interface Dish extends Ingredient
{
    /**
     * @return iterable<Ingredient>
     */
    public function ingredients(): iterable;
}
