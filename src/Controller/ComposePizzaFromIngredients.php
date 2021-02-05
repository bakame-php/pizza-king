<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\CanNotProcessOrder;
use Bakame\PizzaKing\Model\Cheese;
use Bakame\PizzaKing\Model\Meat;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\Sauce;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function array_filter;
use function array_values;
use function count;
use function gettype;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class ComposePizzaFromIngredients implements StatusCodeInterface
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $input = (string) $request->getBody();
        $pizza = $this->pizzaiolo->composeFromIngredients($this->parseBody($input));

        /** @var string $body */
        $body = json_encode(['price' => $pizza->price()->toString()]);

        return $this->responseFactory->createResponse(self::STATUS_OK)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($body));
    }

    /**
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws UnableToHandleIngredient
     *
     * @return array<string>
     */
    private function parseBody(string $body): array
    {
        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['meats'])) {
            $data['meats'] = [];
        }

        if (!is_array($data['meats'])) {
            throw new InvalidArgumentException('The meats should be specify using a list.');
        }

        $filter = fn (mixed $value): bool => (is_string($value) && Meat::isKnown($value));
        if ($data['meats'] !== array_filter($data['meats'], $filter)) {
            throw UnableToHandleIngredient::dueToUnknownIngredient('meats');
        }

        /** @var array<string> $ingredients */
        $ingredients = array_values($data['meats']);

        return match (true) {
            3 !== count($data) => throw UnableToHandleIngredient::dueToUnSupportedIngredient(),
            !isset($data['sauce']) || !is_string($data['sauce']) => throw new InvalidArgumentException('The sauce name should be a string; '.gettype($data['sauce']).' given.'),
            !isset($data['cheese']) || !is_string($data['cheese']) => throw new InvalidArgumentException('The cheese name should be a string; '.gettype($data['sauce']).' given.'),
            2 < count($data['meats']) => throw new InvalidArgumentException('The meats should be specify in a list with maximum 2 varieties given.'),
            !Sauce::isKnown($data['sauce']) => throw UnableToHandleIngredient::dueToUnknownIngredient($data['sauce']),
            !Cheese::isKnown($data['cheese']) => throw UnableToHandleIngredient::dueToUnknownIngredient($data['cheese']),
            default => [$data['sauce'], $data['cheese'], ...$ingredients],
        };
    }
}
