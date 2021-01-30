<?php

/**
 * Pizza King (https://github.com/bakame-php/pizza-king/)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bakame\PizzaKing;

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

    public static function dueToUnknownIngredient(string $ingredient = ''): self
    {
        return new self('`'.$ingredient.'` is an invalid or an unknown ingredient.');
    }
}
