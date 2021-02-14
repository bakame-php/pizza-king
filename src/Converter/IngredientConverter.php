<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Converter;

use Bakame\PizzaKing\Domain\Dish;
use Bakame\PizzaKing\Domain\Euro;
use Bakame\PizzaKing\Domain\Ingredient;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use function json_encode;
use function strtolower;
use function trim;
use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class IngredientConverter
{
    public function dishToJsonResponse(ResponseInterface $response, Dish $dish, string $name): ResponseInterface
    {
        return $this->toJsonResponse($this->dishToArray($dish, $name), $response);
    }

    public function ingredientToJsonResponse(ResponseInterface $response, Ingredient $ingredient): ResponseInterface
    {
        return $this->toJsonResponse($this->ingredientToArray($ingredient), $response);
    }

    public function dishToArray(Dish $dish, string|null $name = null): array
    {
        $data = [
            'name' => $dish->name(),
            'alias' => $dish->alias(),
            'basePrice' => $this->euroToArray($dish->basePrice()),
            'ingredients' => $dish->ingredients()->map(fn (Ingredient $ingredient): array => $this->ingredientToArray($ingredient)),
            'price' => $this->euroToArray($dish->price()),
        ];

        if (null !== $name) {
            $data['alias'] = strtolower(trim($name));
        }

        return $data;
    }

    public function euroToArray(Euro $euro): array
    {
        return [
            'currency' => $euro->currency(),
            'amount' => sprintf('%.2f', $euro->amount()),
        ];
    }

    public function ingredientToArray(Ingredient $ingredient): array
    {
        return [
            'name' => $ingredient->name(),
            'alias' => $ingredient->alias(),
            'price' => $this->euroToArray($ingredient->price()),
        ];
    }

    private function toJsonResponse(array|JsonSerializable $data, ResponseInterface $response): ResponseInterface
    {
        /** @var string $json */
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

        $response = $response->withHeader('Content-Type', 'application/json');
        $body = $response->getBody();
        $body->write($json);

        return $response;
    }
}
