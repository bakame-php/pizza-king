<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Model;

final class UnableToHandleIngredient extends \InvalidArgumentException implements CanNotProcessOrder
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function dueToMissingIngredient(string $ingredient): self
    {
        return new self('`'.$ingredient.'` is missing.');
    }

    public static function dueToWrongQuantity(int $quantity, string $ingredient): self
    {
        return new self('`'.$ingredient.'` can not be used with the following quantity `'.$quantity.'`.');
    }

    public static function dueToWrongPrice(Euro $price, string $ingredient): self
    {
        return new self('`'.$ingredient.'` can not be priced at `'.$price->toString().'`.');
    }

    public static function dueToUnSupportedIngredient(): self
    {
        return new self('An unknown or unsupported ingredient has been detected.');
    }

    public static function dueToUnknownIngredient(string $ingredient): self
    {
        return new self('`'.$ingredient.'` is an invalid or an unknown ingredient.');
    }

    public static function dueToUnknownVariety(string $name, string $ingredient): self
    {
        return new self('`'.$name.'` is an invalid or an unknown name for `'.$ingredient.'`.');
    }
}
