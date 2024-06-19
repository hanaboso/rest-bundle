<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Exception;

use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class DecoderExceptionTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Exception
 */
#[CoversClass(DecoderException::class)]
final class DecoderExceptionTest extends KernelTestCaseAbstract
{

    /**
     * @return void
     */
    public function testGetExceptions(): void
    {
        self::assertEmpty((new DecoderException('', 0))->getExceptions());
    }

}
