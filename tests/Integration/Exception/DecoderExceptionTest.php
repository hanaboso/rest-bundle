<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Exception;

use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;

/**
 * Class DecoderExceptionTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Exception
 *
 * @covers  \Hanaboso\RestBundle\Exception\DecoderException
 */
final class DecoderExceptionTest extends KernelTestCaseAbstract
{

    /**
     * @covers \Hanaboso\RestBundle\Exception\DecoderException::getExceptions
     */
    public function testGetExceptions(): void
    {
        self::assertEmpty((new DecoderException('', 0))->getExceptions());
    }

}
