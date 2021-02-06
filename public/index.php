<?php

declare(strict_types=1);

use Bakame\PizzaKing\Controller\ComposePizzaByName;
use Bakame\PizzaKing\Controller\ComposePizzaFromIngredients;
use Bakame\PizzaKing\Controller\GetPizzaIngredientByName;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Service\ExceptionToProblemConverter;
use Bakame\PizzaKing\Service\IngredientTransformer;
use Crell\ApiProblem\HttpConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

require dirname(__DIR__).'/vendor/autoload.php';

$pizzaiolo = new Pizzaiolo();
$streamFactory = new StreamFactory();
$transformer = new IngredientTransformer();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(displayErrorDetails: true, logErrors: true, logErrorDetails: true);
$errorMiddleware->setDefaultErrorHandler(fn (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null,
): ResponseInterface => (new HttpConverter(new ResponseFactory()))
    ->toJsonResponse((new ExceptionToProblemConverter())->toApiProblem($exception)));

$app->get('/compose', new ComposePizzaFromIngredients($pizzaiolo, $transformer, $streamFactory));
$app->get('/pizza/{name}', new ComposePizzaByName($pizzaiolo, $transformer, $streamFactory));
$app->get('/ingredient/{name}', new GetPizzaIngredientByName($pizzaiolo, $transformer, $streamFactory));
$app->run();
