<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Bakame\PizzaKing\Model\Euro;
use Bakame\PizzaKing\Model\Ingredient;
use Bakame\PizzaKing\Model\Pizza;
use function array_map;
use function strtolower;
use function trim;

final class IngredientTransformer
{
    public function pizzaToArray(Pizza $pizza, string|null $name = null): array
    {
        $data = $this->ingredientToArray($pizza);
        if (null !== $name) {
            $data['name'] = strtolower(trim($name));
        }
        $data['ingredients'] = array_map(fn (Ingredient $ingredient): array => $this->ingredientToArray($ingredient), $pizza->ingredients());

        return $data;
    }

    private function getIngredientType(Ingredient $ingredient): string
    {
        $path = explode('\\', $ingredient::class);

        $name = array_pop($path);

        return strtolower($name);
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
        return [
            'type' => $this->getIngredientType($ingredient),
            'name' => $ingredient->name(),
            'price' => $this->priceToArray($ingredient->price()),
        ];
    }
}
