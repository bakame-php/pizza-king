<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Service;

use Crell\ApiProblem\ApiProblem;
use InvalidArgumentException;
use function array_key_first;
use function array_key_last;

/**
 * @SuppressWarnings(PHPMD.UndefinedVariable)
 */
final class ExceptionToProblemConverter
{
    /** @var array<int,string> Map of standard HTTP status code/reason phrases */
    private const REASON_PHRASES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
    public const ADD_TRACING = 1;
    public const REMOVE_TRACING = 0;

    public function __construct(private string $apiProblemType = 'about:blank', private int $tracing = self::REMOVE_TRACING)
    {
    }

    public function toApiProblem(\Throwable $exception): ApiProblem
    {
        $status = $this->getStatusCode($exception);

        $problem = new ApiProblem(self::REASON_PHRASES[$status] ?? 'Unknown', $this->apiProblemType);
        $problem->setStatus($status);
        $problem->setDetail($exception->getMessage());
        $problem['code'] = $exception->getCode();

        $this->addTracing($problem, $exception);

        return $problem;
    }

    private function getStatusCode(\Throwable $throwable): int
    {
        $code = (int) $throwable->getCode();

        return match (true) {
            $throwable instanceof InvalidArgumentException => 400,
            ($code >= array_key_first(self::REASON_PHRASES) && $code <= array_key_last(self::REASON_PHRASES)) => $code,
            default => 500,
        };
    }

    private function addTracing(ApiProblem $problem, \Throwable $throwable): void
    {
        if (self::ADD_TRACING !== $this->tracing) {
            return;
        }

        $previousMessages = [];
        $previous = $throwable;
        while ($previous = $previous->getPrevious()) {
            $previousMessages[] = $this->serializeException($previous);
        }

        $problem['tracing'] = ['previous' => $previousMessages] + $this->serializeException($throwable);
    }

    /**
     * @return array{code:int, file:string, line:int, message:string, trace:array<array-key, string>, type:string}
     */
    private function serializeException(\Throwable $throwable): array
    {
        return [
            'type' => $throwable::class,
            'message' => $throwable->getMessage(),
            'code' => (int) $throwable->getCode(),
            'line' => $throwable->getLine(),
            'file' => $throwable->getFile(),
            'trace' => explode(PHP_EOL, $throwable->getTraceAsString()),
        ];
    }
}
