<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

interface Dish extends Ingredient
{
    public function basePrice(): Euro;

    public function ingredients(): IngredientList;
}
