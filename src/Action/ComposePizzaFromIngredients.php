<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Action;

use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\CanNotProcessOrder;
use Bakame\PizzaKing\Domain\Cheese;
use Bakame\PizzaKing\Domain\Meat;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Bakame\PizzaKing\Domain\Sauce;
use Bakame\PizzaKing\Domain\UnableToHandleIngredient;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use League\Uri\Components\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_reduce;
use function count;
use function reset;

final class ComposePizzaFromIngredients implements StatusCodeInterface
{
    public function __construct(private Pizzaiolo $pizzaiolo, private IngredientConverter $renderer)
    {
    }

    /**
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ingredients = $this->getIngredientsFromUri($request->getUri());
        $pizza = $this->pizzaiolo->composePizzaFromIngredients($ingredients);

        return $this->renderer->dishToJsonResponse($response, $pizza, 'custom pizza')
            ->withStatus(self::STATUS_OK);
    }

    /**
     * @throws UnableToHandleIngredient
     *
     * @return array<string>
     */
    private function getIngredientsFromUri(UriInterface $uri): array
    {
        $query = Query::createFromUri($uri);
        if (0 === count($query)) {
            throw new InvalidArgumentException('query string is missing or contains no data.');
        }

        $reducer = function (array $carry, string|null $value): array {
            if (null === $value) {
                throw UnableToHandleIngredient::dueToMissingIngredient('meat');
            }

            if (null === Meat::findName($value)) {
                throw UnableToHandleIngredient::dueToUnknownVariety($value, 'meat');
            }

            $carry[] = $value;

            return $carry;
        };

        /** @var array<string> $meats */
        $meats = array_reduce($query->getAll('meat'), $reducer, []);
        $sauces = $query->getAll('sauce');
        $cheeses = $query->getAll('cheese');

        if (1 !== count($sauces)) {
            throw UnableToHandleIngredient::dueToWrongQuantity(count($sauces), 'sauce');
        }

        if (1 !== count($cheeses)) {
            throw UnableToHandleIngredient::dueToWrongQuantity(count($cheeses), 'cheese');
        }

        /** @var string|null $sauce */
        $sauce = reset($sauces);
        /** @var string|null $cheese */
        $cheese = reset($cheeses);

        return match (true) {
            null === $sauce => throw UnableToHandleIngredient::dueToMissingIngredient('sauce'),
            null === $cheese => throw UnableToHandleIngredient::dueToMissingIngredient('cheese'),
            null === Sauce::findName($sauce) => throw UnableToHandleIngredient::dueToUnknownVariety($sauce, 'sauce'),
            null === Cheese::findName($cheese) => throw UnableToHandleIngredient::dueToUnknownVariety($cheese, 'cheese'),
            default => [$sauce, $cheese, ...$meats],
        };
    }
}
