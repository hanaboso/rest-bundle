<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class KernelTestCaseAbstract
 *
 * @package Hanaboso\RestBundleTests
 */
abstract class KernelTestCaseAbstract extends KernelTestCase
{

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    /**
     * @phpstan-param class-string<\Throwable> $exception
     *
     * @param string      $exception
     * @param int|null    $exceptionCode
     * @param string|null $exceptionMessage
     */
    protected function assertException(
        string $exception,
        ?int $exceptionCode = NULL,
        ?string $exceptionMessage = NULL
    ): void
    {
        self::expectException($exception);

        if ($exceptionCode) {
            self::expectExceptionCode($exceptionCode);
        }

        if ($exceptionMessage) {
            self::expectExceptionMessage($exceptionMessage);
        }
    }

}
