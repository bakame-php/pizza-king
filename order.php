<?php

declare(strict_types=1);

use Bakame\PizzaKing\Model\Ingredient;
use Bakame\PizzaKing\Model\Pizzaiolo;

require 'vendor/autoload.php';

$pizza = (new Pizzaiolo())->composeFromIngredients(['creme', 'mozzarella', 'jambon', 'pepperoni']);
echo "The pizza ingredients are : ",
    implode(', ', array_map(fn (Ingredient $ingredient): string => $ingredient->name(), $pizza->ingredients())),
    PHP_EOL,
    "----",
    PHP_EOL,
    "The pizza price is : ", $pizza->price()->toString(), PHP_EOL;
