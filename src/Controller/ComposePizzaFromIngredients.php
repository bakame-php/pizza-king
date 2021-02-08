<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\CanNotProcessOrder;
use Bakame\PizzaKing\Model\Cheese;
use Bakame\PizzaKing\Model\Meat;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\Sauce;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use Bakame\PizzaKing\Service\IngredientRenderer;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use JsonException;
use League\Uri\Components\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function array_reduce;
use function count;
use function reset;

final class ComposePizzaFromIngredients implements StatusCodeInterface
{
    public function __construct(private Pizzaiolo $pizzaiolo, private IngredientRenderer $renderer)
    {
    }

    /**
     * @throws JsonException
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ingredients = $this->parseQuery($request->getUri());
        $pizza = $this->pizzaiolo->composeFromIngredients($ingredients);

        $response = $response
            ->withStatus(self::STATUS_OK)
            ->withHeader('Content-Type', 'application/json');

        $body = $response->getBody();
        $body->write($this->renderer->dishToJson($pizza, 'custom pizza'));
        $body->rewind();

        return $response;
    }

    /**
     * @throws UnableToHandleIngredient
     *
     * @return array<string>
     */
    private function parseQuery(UriInterface $uri): array
    {
        $query = Query::createFromUri($uri);
        if (0 === count($query)) {
            throw new InvalidArgumentException('query string is missing.');
        }

        $reducer = function (array $carry, string|null $value): array {
            if (null === $value) {
                throw UnableToHandleIngredient::dueToMissingIngredient('meat');
            }

            if (null === Meat::fetchAlias($value)) {
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
            null === Sauce::fetchAlias($sauce) => throw UnableToHandleIngredient::dueToUnknownVariety($sauce, 'sauce'),
            null === Cheese::fetchAlias($cheese) => throw UnableToHandleIngredient::dueToUnknownVariety($cheese, 'cheese'),
            default => [$sauce, $cheese, ...$meats],
        };
    }
}
