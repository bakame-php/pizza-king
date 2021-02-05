<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\CanNotProcessOrder;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use function gettype;
use function json_encode;

final class ComposePizzaFromName
{
    public function __construct(
        private Pizzaiolo $pizzaiolo,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws CanNotProcessOrder
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $name = $request->getAttribute('name');
        if (!is_string($name)) {
            throw new InvalidArgumentException('The pizza name should be a string; '.gettype($name).' given.');
        }

        $pizza = $this->pizzaiolo->composeFromName($name);

        /** @var string $body */
        $body = json_encode(['price' => $pizza->price()->toString()]);

        return $this->responseFactory->createResponse(StatusCodeInterface::STATUS_OK)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($body))
            ;
    }
}
