<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Bakame\PizzaKing\Model\Dish;
use Bakame\PizzaKing\Model\Euro;
use Bakame\PizzaKing\Model\Ingredient;
use JsonSerializable;
use function array_map;
use function array_pop;
use function explode;
use function json_encode;
use function strtolower;
use function trim;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class IngredientRenderer
{
    public function dishToJson(Dish $dish, string|null $name = null): string
    {
        return $this->toJson($this->dishToArray($dish, $name));
    }

    private function toJson(array|JsonSerializable $data): string
    {
        /** @var string $json */
        $json = json_encode(
            $data,
            JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES
        );

        return $json;
    }

    public function ingredientToJson(Ingredient $ingredient): string
    {
        return $this->toJson($this->ingredientToArray($ingredient));
    }

    public function dishToArray(Dish $dish, string|null $name = null): array
    {
        $path = explode('\\', $dish::class);
        $data = [
            'type' => strtolower(array_pop($path)),
            'name' => $dish->alias(),
            'basePrice' => $this->euroToArray($dish->basePrice()),
            'ingredients' => array_map(fn (Ingredient $ingredient): array => $this->ingredientToArray($ingredient), $dish->ingredients()),
            'price' => $this->euroToArray($dish->price()),
        ];

        if (null !== $name) {
            $data['name'] = strtolower(trim($name));
        }

        return $data;
    }

    public function euroToArray(Euro $euro): array
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
            'name' => $ingredient->alias(),
            'price' => $this->euroToArray($ingredient->price()),
        ];
    }
}
