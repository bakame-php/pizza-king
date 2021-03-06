<?php

declare(strict_types=1);

namespace Bakame\PizzaKing\Converter;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionConverterTest extends TestCase
{
    /** @test */
    public function it_converts_a_throwable_error_into_an_api_problem_and_keep_the_error_code(): void
    {
        $converter = new ExceptionConverter();
        $exceptionCode = 42;
        $exception = new Exception('foobar', $exceptionCode);
        $problem = $converter->toApiProblem($exception);

        self::assertSame(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR, $problem->getStatus());
        self::assertSame('Internal Server Error', $problem->getTitle());
        self::assertSame($exception->getMessage(), $problem->getDetail());
        self::assertArrayNotHasKey('tracing', $problem);
    }

    /** @test */
    public function it_converts_a_throwable_error_into_an_api_problem_and_preserve_its_code(): void
    {
        $converter = new ExceptionConverter();
        $exceptionCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        $exception = new Exception('foobar', $exceptionCode);
        $problem = $converter->toApiProblem($exception);

        self::assertSame($exceptionCode, $problem->getStatus());
        self::assertSame('Internal Server Error', $problem->getTitle());
        self::assertSame($exception->getMessage(), $problem->getDetail());
        self::assertArrayNotHasKey('tracing', $problem);
    }

    /** @test */
    public function it_converts_a_throwable_error_into_an_api_problem_and_exposes_the_full_trace(): void
    {
        $converter = new ExceptionConverter('about:blank', ExceptionConverter::ADD_TRACING);
        $previous = new Exception('barbaz');
        $exceptionCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        $exception = new RuntimeException('foobar', $exceptionCode, $previous);
        $problem = $converter->toApiProblem($exception);

        self::assertSame($exceptionCode, $problem->getStatus());
        self::assertSame('Bad Request', $problem->getTitle());
        self::assertSame($exception->getMessage(), $problem->getDetail());
        self::assertArrayHasKey('tracing', $problem);
        self::assertIsArray($problem['tracing']);
    }
}
