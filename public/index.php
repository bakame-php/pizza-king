<?php

declare(strict_types=1);

use Bakame\PizzaKing\Controller\ComposePizzaByName;
use Bakame\PizzaKing\Controller\ComposePizzaFromIngredients;
use Bakame\PizzaKing\Controller\GetPizzaIngredientByName;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Service\ExceptionToProblemConverter;
use Bakame\PizzaKing\Service\IngredientRenderer;
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
$renderer = new IngredientRenderer();

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
    ->toJsonResponse((new ExceptionToProblemConverter())->toApiProblem($exception)));

$app->get('/pizzas', new ComposePizzaFromIngredients($pizzaiolo, $renderer));
$app->get('/pizzas/{name}', new ComposePizzaByName($pizzaiolo, $renderer));
$app->get('/ingredients/{name}', new GetPizzaIngredientByName($pizzaiolo, $renderer));
$app->run();
