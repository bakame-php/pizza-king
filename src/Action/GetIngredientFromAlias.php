<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Action;

use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\CanNotProcessOrder;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function gettype;
use function is_string;

final class GetIngredientFromAlias implements StatusCodeInterface
{
    public function __construct(private Pizzaiolo $pizzaiolo, private IngredientConverter $renderer)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $name = $request->getAttribute('name');
        if (!is_string($name)) {
            throw new InvalidArgumentException('The ingredient name should be a string; '.gettype($name).' given.');
        }

        $ingredient = $this->pizzaiolo->getIngredientFromAlias($name);

        return $this->renderer->ingredientToJsonResponse($response, $ingredient)
            ->withStatus(self::STATUS_OK);
    }
}
