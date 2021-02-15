<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Converter;

use Crell\ApiProblem\ApiProblem;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Throwable;
use function array_key_first;
use function array_key_last;

final class ExceptionConverter implements StatusCodeInterface
{
    /** @var array<int,string> Map of standard HTTP status code/reason phrases */
    private const REASON_PHRASES = [
        self::STATUS_CONTINUE => 'Continue',
        self::STATUS_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_OK => 'OK',
        self::STATUS_CREATED => 'Created',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::STATUS_NO_CONTENT => 'No Content',
        self::STATUS_RESET_CONTENT => 'Reset Content',
        self::STATUS_PARTIAL_CONTENT => 'Partial Content',
        self::STATUS_MULTI_STATUS => 'Multi-status',
        self::STATUS_ALREADY_REPORTED => 'Already Reported',
        self::STATUS_MULTIPLE_CHOICES => 'Multiple Choices',
        self::STATUS_MOVED_PERMANENTLY => 'Moved Permanently',
        self::STATUS_FOUND => 'Found',
        self::STATUS_SEE_OTHER => 'See Other',
        self::STATUS_NOT_MODIFIED => 'Not Modified',
        self::STATUS_USE_PROXY => 'Use Proxy',
        self::STATUS_RESERVED => 'Switch Proxy',
        self::STATUS_TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::STATUS_BAD_REQUEST => 'Bad Request',
        self::STATUS_UNAUTHORIZED => 'Unauthorized',
        self::STATUS_PAYMENT_REQUIRED => 'Payment Required',
        self::STATUS_FORBIDDEN => 'Forbidden',
        self::STATUS_NOT_FOUND => 'Not Found',
        self::STATUS_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::STATUS_NOT_ACCEPTABLE => 'Not Acceptable',
        self::STATUS_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::STATUS_REQUEST_TIMEOUT => 'Request Time-out',
        self::STATUS_CONFLICT => 'Conflict',
        self::STATUS_GONE => 'Gone',
        self::STATUS_LENGTH_REQUIRED => 'Length Required',
        self::STATUS_PRECONDITION_FAILED => 'Precondition Failed',
        self::STATUS_PAYLOAD_TOO_LARGE => 'Request Entity Too Large',
        self::STATUS_URI_TOO_LONG => 'Request-URI Too Large',
        self::STATUS_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::STATUS_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
        self::STATUS_EXPECTATION_FAILED => 'Expectation Failed',
        self::STATUS_IM_A_TEAPOT => 'I\'m a teapot',
        self::STATUS_UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::STATUS_LOCKED => 'Locked',
        self::STATUS_FAILED_DEPENDENCY => 'Failed Dependency',
        self::STATUS_TOO_EARLY => 'Unordered Collection',
        self::STATUS_UPGRADE_REQUIRED => 'Upgrade Required',
        self::STATUS_PRECONDITION_REQUIRED => 'Precondition Required',
        self::STATUS_TOO_MANY_REQUESTS => 'Too Many Requests',
        self::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::STATUS_UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::STATUS_NOT_IMPLEMENTED => 'Not Implemented',
        self::STATUS_BAD_GATEWAY => 'Bad Gateway',
        self::STATUS_SERVICE_UNAVAILABLE => 'Converter Unavailable',
        self::STATUS_GATEWAY_TIMEOUT => 'Gateway Time-out',
        self::STATUS_VERSION_NOT_SUPPORTED => 'HTTP Version not supported',
        self::STATUS_VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::STATUS_INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::STATUS_LOOP_DETECTED => 'Loop Detected',
        self::STATUS_NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
    ];

    public const ADD_TRACING = 1;
    public const REMOVE_TRACING = 0;

    public function __construct(
        private string $apiProblemType = 'about:blank',
        private int $tracing = self::REMOVE_TRACING
    ) {
    }

    public function toApiProblem(Throwable $exception): ApiProblem
    {
        $status = $this->getStatusCode($exception);

        $problem = new ApiProblem(self::REASON_PHRASES[$status] ?? 'Unknown', $this->apiProblemType);
        $problem->setStatus($status);
        $problem->setDetail($exception->getMessage());
        $this->addTracing($problem, $exception);

        return $problem;
    }

    private function getStatusCode(Throwable $throwable): int
    {
        $code = (int) $throwable->getCode();

        return match (true) {
            $throwable instanceof InvalidArgumentException => self::STATUS_BAD_REQUEST,
            ($code >= array_key_first(self::REASON_PHRASES) && $code <= array_key_last(self::REASON_PHRASES)) => $code,
            default => self::STATUS_INTERNAL_SERVER_ERROR,
        };
    }

    private function addTracing(ApiProblem $problem, Throwable $throwable): void
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
    private function serializeException(Throwable $throwable): array
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
