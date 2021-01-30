<?php

declare(strict_types=1);

use Bakame\PizzaKing\Ingredient;
use Bakame\PizzaKing\Pizza;

require 'vendor/autoload.php';

$pizza = Pizza::fromIngredientsName('creme', 'mozzarella', 'jambon', 'pepperoni');
echo "The pizza ingredients are : ",
    implode(', ', array_map(fn (Ingredient $ingredient): string => $ingredient->name(), $pizza->toIngredients())),
    PHP_EOL,
    "----",
    PHP_EOL,
    "The pizza price is : ", $pizza->price()->toString(), PHP_EOL;

