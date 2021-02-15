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

final class ComposePizzaFromName implements StatusCodeInterface
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
        $name = $request->getAttribute('name', '');
        if (!is_string($name)) {
            throw new InvalidArgumentException('The pizza name should be a string.');
        }

        $pizza = $this->pizzaiolo->composePizzaFromName($name);

        return $this->converter->dishToJsonResponse($response, $pizza, $name)
            ->withStatus(self::STATUS_OK);
    }
}
