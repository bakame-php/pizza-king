<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Bakame\PizzaKing\Model\Dish;
use Bakame\PizzaKing\Model\Euro;
use Bakame\PizzaKing\Model\Ingredient;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use function array_map;
use function array_pop;
use function explode;
use function json_encode;
use function strtolower;
use function trim;

final class IngredientRenderer
{
    public function __construct(private StreamFactoryInterface $streamFactory)
    {
    }

    public function dishToStream(Dish $dish, string|null $name = null): StreamInterface
    {
        /** @var string $json */
        $json = json_encode($this->dishToArray($dish, $name));

        return $this->streamFactory->createStream($json);
    }

    public function ingredientToStream(Ingredient $ingredient): StreamInterface
    {
        /** @var string $json */
        $json = json_encode($this->ingredientToArray($ingredient));

        return $this->streamFactory->createStream($json);
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
