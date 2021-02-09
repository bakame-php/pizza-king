<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Renderer;

use Bakame\PizzaKing\Domain\Dish;
use Bakame\PizzaKing\Domain\Euro;
use Bakame\PizzaKing\Domain\Ingredient;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
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
    public function dishToJsonResponse(ResponseInterface $response, Dish $dish, string|null $name = null): ResponseInterface
    {
        return $this->toJsonResponse($this->dishToArray($dish, $name), $response);
    }

    public function ingredientToJsonResponse(ResponseInterface $response, Ingredient $ingredient): ResponseInterface
    {
        return $this->toJsonResponse($this->ingredientToArray($ingredient), $response);
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

    private function toJsonResponse(array|JsonSerializable $data, ResponseInterface $response): ResponseInterface
    {
        /** @var string $json */
        $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

        $response = $response->withHeader('Content-Type', 'application/json');

        $body = $response->getBody();
        $body->write($json);
        $body->rewind();

        return $response;
    }
}
