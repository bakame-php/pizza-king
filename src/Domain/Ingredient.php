<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

interface Ingredient
{
    public function name(): string;

    public function alias(): string;

    public function price(): Euro;
}
