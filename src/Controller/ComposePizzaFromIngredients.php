<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\CanNotProcessOrder;
use Bakame\PizzaKing\Model\Cheese;
use Bakame\PizzaKing\Model\Meat;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\Sauce;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use Bakame\PizzaKing\Service\IngredientTransformer;
use Fig\Http\Message\StatusCodeInterface;
use League\Uri\Components\Query;
use League\Uri\Contracts\QueryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function array_reduce;
use function count;
use function json_encode;
use function reset;

final class ComposePizzaFromIngredients implements StatusCodeInterface
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private IngredientTransformer $transformer,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = Query::createFromUri($request->getUri());
        $ingredients = $this->parseQuery($query);
        $pizza = $this->pizzaiolo->composeFromIngredients($ingredients);
        $presentation = $this->transformer->dishToArray($pizza, 'customized');

        /** @var string $body */
        $body = json_encode($presentation);

        return $response
            ->withStatus(self::STATUS_OK)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($body));
    }

    /**
     * @throws UnableToHandleIngredient
     *
     * @return array<string>
     */
    private function parseQuery(QueryInterface $query): array
    {
        $reducer = function (array $carry, string|null $value): array {
            if (null === $value) {
                throw UnableToHandleIngredient::dueToMissingIngredient('meat');
            }

            if (!Meat::isKnown($value)) {
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
            !Sauce::isKnown($sauce) => throw UnableToHandleIngredient::dueToUnknownVariety($sauce, 'sauce'),
            !Cheese::isKnown($cheese) => throw UnableToHandleIngredient::dueToUnknownVariety($cheese, 'cheese'),
            default => [$sauce, $cheese, ...$meats],
        };
    }
}
