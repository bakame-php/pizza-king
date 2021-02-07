<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Bakame\PizzaKing\Model\Dish;
use Bakame\PizzaKing\Model\Euro;
use Bakame\PizzaKing\Model\Ingredient;
use function array_map;
use function array_pop;
use function explode;
use function strtolower;
use function trim;

final class IngredientTransformer
{
    public function dishToArray(Dish $dish, string|null $name = null): array
    {
        $path = explode('\\', $dish::class);
        $data = [
            'type' => strtolower(array_pop($path)),
            'name' => $dish->name(),
            'basePrice' => $this->priceToArray($dish->basePrice()),
            'ingredients' => array_map(fn (Ingredient $ingredient): array => $this->ingredientToArray($ingredient), $dish->ingredients()),
            'price' => $this->priceToArray($dish->price()),
        ];

        if (null !== $name) {
            $data['name'] = strtolower(trim($name));
        }

        return $data;
    }

    public function priceToArray(Euro $euro): array
    {
        return [
            'currency' => $euro->currency(),
            'amount' => sprintf('%.2f', $euro->cents() / 100),
        ];
    }

    public function ingredientToArray(Ingredient $ingredient): array
    {
        $path = explode('\\', $ingredient::class);

        return [
            'type' => strtolower(array_pop($path)),
            'name' => $ingredient->name(),
            'price' => $this->priceToArray($ingredient->price()),
        ];
    }
}
