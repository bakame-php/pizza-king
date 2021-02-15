<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Action;

use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\CanNotProcessOrder;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function is_string;

final class GetIngredientFromAlias implements StatusCodeInterface
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private IngredientConverter $converter
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $alias = $request->getAttribute('alias', '');
        if (!is_string($alias)) {
            throw new InvalidArgumentException('The ingredient alias should be a string.');
        }

        $ingredient = $this->pizzaiolo->getIngredientFromAlias($alias);

        return $this->converter->ingredientToJsonResponse($response, $ingredient)
            ->withStatus(self::STATUS_OK);
    }
}
