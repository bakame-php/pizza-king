<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Controller;

use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Model\UnableToHandleIngredient;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Response;
use function json_encode;

final class ComposePizzaFromIngredientsTest extends TestCase
{
    /** @test */
    public function it_returns_the_price_of_a_pizza(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $responseFactory->method('createResponse')->willReturn(new Response());
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->will(
            self::returnCallback([new StreamFactory(), 'createStream'])
        );

        $data = [
            'sauce' => 'creme',
            'cheese' => 'mozzarella',
            'meats' => ['jambon', 'pepperoni'],
        ];
        /** @var string $jsonData */
        $jsonData = json_encode($data);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn((new StreamFactory())->createStream($jsonData));

        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);
        $response = $controller($request);

        self::assertSame('application/json', $response->getHeader('Content-Type')['0']);
        self::assertSame('{"price":"14.00 EUR"}', $response->getBody()->__toString());
    }

    /** @test */
    public function it_fails_if_the_request_body_is_not_parsable(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn((new StreamFactory())->createStream(''));

        $this->expectException(JsonException::class);

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_meat_list_is_invalid(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $data = [
            'sauce' => 'creme',
            'cheese' => 'mozzarella',
            'meats' => 'jambon',
        ];
        /** @var string $jsonData */
        $jsonData = json_encode($data);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn((new StreamFactory())->createStream($jsonData));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The meats should be specify using a list.');

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_meat_list_contains_invalid_ingredients(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $data = [
            'sauce' => 'creme',
            'cheese' => 'mozzarella',
            'meats' => ['jambon', 'ananas'],
        ];

        /** @var string $jsonData */
        $jsonData = json_encode($data);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn((new StreamFactory())->createStream($jsonData));

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('`meats` is an invalid or an unknown ingredient.');

        $controller($request);
    }

    /** @test */
    public function it_fails_if_the_request_body_contains_invalid_data(): void
    {
        $responseFactory = $this->createStub(ResponseFactoryInterface::class);
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $controller = new ComposePizzaFromIngredients(new Pizzaiolo(), $responseFactory, $streamFactory);

        $data = ['creme', 'mozzarella', 'jambon', 'pepperoni'];
        /** @var string $jsonData */
        $jsonData = json_encode($data);
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn((new StreamFactory())->createStream($jsonData));

        $this->expectException(UnableToHandleIngredient::class);
        $this->expectExceptionMessage('An unknown or unsupported ingredient has been detected.');

        $controller($request);
    }
}
