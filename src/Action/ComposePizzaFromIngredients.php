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
use function array_filter;
use function array_slice;

final class ComposePizzaFromIngredients implements StatusCodeInterface
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private IngredientConverter $converter
    ) {
    }

    /**
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $ingredients = $this->getIngredientsFromUri($request->getUri());
        $pizza = $this->pizzaiolo->composeClassicPizzaFromIngredients($ingredients);

        return $this->converter->dishToJsonResponse($response, $pizza, 'custom pizza')
            ->withStatus(self::STATUS_OK);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnableToHandleIngredient
     *
     * @return array<string>
     */
    private function getIngredientsFromUri(UriInterface $uri): array
    {
        if ('' === $uri->getQuery()) {
            throw new InvalidArgumentException('query string is missing or contains no data.');
        }

        $query = Query::createFromUri($uri);
        $sauce = $query->get('sauce');
        $cheese = $query->get('cheese');
        /** @var array<string> $meats */
        $meats = array_filter(
            array_slice(array: $query->getAll('meat'), offset: 0, length: 2, preserve_keys: false),
            fn (string|null $value): bool => null !== $value && null !== Meat::findName($value)
        );

        return match (true) {
            null === $sauce => throw UnableToHandleIngredient::dueToMissingIngredient('sauce'),
            null === $cheese => throw UnableToHandleIngredient::dueToMissingIngredient('cheese'),
            null === Sauce::findName($sauce) => throw UnableToHandleIngredient::dueToUnknownVariety($sauce, 'sauce'),
            null === Cheese::findName($cheese) => throw UnableToHandleIngredient::dueToUnknownVariety($cheese, 'cheese'),
            default => [$sauce, $cheese, ...$meats],
        };
    }
}
