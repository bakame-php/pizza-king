<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

interface Ingredient
{
    public function name(): string;

    public function alias(): string;

    public function price(): Euro;
}
