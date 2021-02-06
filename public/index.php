<?php

declare(strict_types=1);

use Bakame\PizzaKing\Controller\ComposePizzaByName;
use Bakame\PizzaKing\Controller\ComposePizzaFromIngredients;
use Bakame\PizzaKing\Model\Pizzaiolo;
use Bakame\PizzaKing\Service\ExceptionToProblemConverter;
use Crell\ApiProblem\HttpConverter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

require dirname(__DIR__).'/vendor/autoload.php';

$pizzaiolo = new Pizzaiolo();
$responseFactory = new ResponseFactory();
$streamFactory = new StreamFactory();
$exceptionConverter = new ExceptionToProblemConverter();
$httpConverter = new HttpConverter($responseFactory);

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
): ResponseInterface => $httpConverter->toJsonResponse($exceptionConverter->toApiProblem($exception)));

$app->get('/compose', new ComposePizzaFromIngredients($pizzaiolo, $responseFactory, $streamFactory));
$app->get('/pizza/{name}', new ComposePizzaByName($pizzaiolo, $responseFactory, $streamFactory));
$app->run();
