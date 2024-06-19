<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model\Decoder;

use Exception;
use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
use Hanaboso\RestBundle\Exception\JsonDecoderException;
use Hanaboso\RestBundle\Model\Decoder\JsonDecoder;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class JsonDecoderTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Model\Decoder
 */
#[CoversClass(JsonDecoder::class)]
final class JsonDecoderTest extends KernelTestCaseAbstract
{

    /**
     * @var JsonDecoder
     */
    private JsonDecoder $decoder;

    /**
     * @throws Exception
     */
    public function testDecode(): void
    {
        self::assertEquals([], $this->decoder->decode('{}'));
    }

    /**
     * @throws Exception
     */
    public function testDecodeException(): void
    {
        self::assertException(JsonDecoderException::class, DecoderExceptionAbstract::ERROR, 'Syntax error');

        self::assertEquals([], $this->decoder->decode('Unknown'));
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->decoder = self::getContainer()->get('json');
    }

}
