<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Domain;

use Countable;
use Iterator;
use IteratorAggregate;
use function array_map;
use function array_reduce;
use const ARRAY_FILTER_USE_BOTH;

final class IngredientList implements Countable, IteratorAggregate
{
    /** @var Ingredient[] */
    private array $ingredients;

    public function __construct(Ingredient ...$ingredients)
    {
        $this->ingredients = $ingredients;
    }

    /**
     * @param iterable<Ingredient> $ingredients
     */
    public static function fromList(iterable $ingredients): self
    {
        $instance = new self();
        foreach ($ingredients as $ingredient) {
            $instance = $instance->withIngredient($ingredient);
        }

        return $instance;
    }

    public function count(): int
    {
        return count($this->ingredients);
    }

    public function getIterator(): Iterator
    {
        foreach ($this->ingredients as $ingredient) {
            yield $ingredient;
        }
    }

    public function withIngredient(Ingredient $ingredient): self
    {
        $clone = clone $this;
        $clone->ingredients[] = $ingredient;

        return $clone;
    }

    public function withoutIngredient(Ingredient $ingredient): self
    {
        $newIngredientList = $this->ingredients;
        foreach ($newIngredientList as $key => $item) {
            if ($item::class === $ingredient::class && $ingredient->name() === $item->name()) {
                unset($newIngredientList[$key]);

                return new self(...$newIngredientList);
            }
        }

        return $this;
    }

    /**
     * @psalm-param callable(Ingredient=):bool $callable
     */
    public function filter(callable $callable): self
    {
        return new self(...array_filter($this->ingredients, $callable, ARRAY_FILTER_USE_BOTH));
    }

    public function map(callable $callable): mixed
    {
        return array_map($callable, $this->ingredients);
    }

    public function price(): Euro
    {
        return array_reduce(
            $this->ingredients,
            fn (Euro $price, Ingredient $ingredient): Euro => $price->add($ingredient->price()),
            Euro::fromCents(0)
        );
    }
}
