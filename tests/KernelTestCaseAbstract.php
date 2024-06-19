<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests;

use Hanaboso\PhpCheckUtils\PhpUnit\Traits\RestoreErrorHandlersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

/**
 * Class KernelTestCaseAbstract
 *
 * @package Hanaboso\RestBundleTests
 */
abstract class KernelTestCaseAbstract extends KernelTestCase
{

    use RestoreErrorHandlersTrait;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    /**
     * @phpstan-param class-string<Throwable> $exception
     *
     * @param string      $exception
     * @param int|null    $exceptionCode
     * @param string|null $exceptionMessage
     */
    protected function assertException(
        string $exception,
        ?int $exceptionCode = NULL,
        ?string $exceptionMessage = NULL,
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

    /**
     * @return void
     */
    protected function tearDown(): void {
        parent::tearDown();

        $this->restoreErrorHandler();
        $this->restoreExceptionHandler();
    }

}
