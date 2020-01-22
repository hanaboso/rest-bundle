<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model\Decoder;

use Exception;
use Hanaboso\RestBundle\Exception\JsonDecoderException;
use Hanaboso\RestBundle\Model\Decoder\JsonDecoder;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;

/**
 * Class JsonDecoderTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Model\Decoder
 *
 * @covers  \Hanaboso\RestBundle\Model\Decoder\JsonDecoder
 */
final class JsonDecoderTest extends KernelTestCaseAbstract
{

    /**
     * @var JsonDecoder
     */
    private JsonDecoder $decoder;

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\Decoder\JsonDecoder::decode
     */
    public function testDecode(): void
    {
        self::assertEquals([], $this->decoder->decode('{}'));
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\Decoder\JsonDecoder::decode
     */
    public function testDecodeException(): void
    {
        self::assertException(JsonDecoderException::class, JsonDecoderException::ERROR, 'Syntax error');

        self::assertEquals([], $this->decoder->decode('Unknown'));
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->decoder = self::$container->get('json');
    }

}
