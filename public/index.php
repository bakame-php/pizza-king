<?php

declare(strict_types=1);

use Bakame\PizzaKing\Action\ComposePizzaFromIngredients;
use Bakame\PizzaKing\Action\ComposePizzaFromName;
use Bakame\PizzaKing\Action\GetIngredientFromAlias;
use Bakame\PizzaKing\Converter\ExceptionConverter;
use Bakame\PizzaKing\Converter\IngredientConverter;
use Bakame\PizzaKing\Domain\Pizzaiolo;
use Crell\ApiProblem\HttpConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

require dirname(__DIR__).'/vendor/autoload.php';

/** @var array<string,int> $priceList */
$priceList = require dirname(__DIR__).'/config/priceList.php';
$pizzaiolo = new Pizzaiolo($priceList);
$renderer = new IngredientConverter();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(displayErrorDetails: true, logErrors: true, logErrorDetails: true);
$errorMiddleware->setDefaultErrorHandler(fn (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    LoggerInterface|null $logger = null,
): ResponseInterface => (new HttpConverter(new ResponseFactory()))
    ->toJsonResponse((new ExceptionConverter())->toApiProblem($exception)));

$app->get('/pizzas', new ComposePizzaFromIngredients($pizzaiolo, $renderer));
$app->get('/pizzas/{name:[\w\s]+}', new ComposePizzaFromName($pizzaiolo, $renderer));
$app->get('/ingredients/{alias:[\w\s]+}', new GetIngredientFromAlias($pizzaiolo, $renderer));
$app->run();
