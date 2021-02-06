<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\CanNotProcessOrder;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Service\IngredientTransformer;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function gettype;
use function is_string;
use function json_encode;

final class GetPizzaIngredientByName implements StatusCodeInterface
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private IngredientTransformer $transformer,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $name = $request->getAttribute('name');
        if (!is_string($name)) {
            throw new InvalidArgumentException('The ingredient name should be a string; '.gettype($name).' given.');
        }

        $ingredient = $this->pizzaiolo->getIngredientFromName($name);
        $presentation = $this->transformer->ingredientToArray($ingredient);
        /** @var string $body */
        $body = json_encode($presentation);

        return $response
            ->withStatus(self::STATUS_OK)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($body));
    }
}
