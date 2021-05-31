<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model\Decoder;

use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\RestBundle\Exception\XmlDecoderException;
use Hanaboso\RestBundle\Model\Decoder\XmlDecoder;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * Class XmlDecoderTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Model\Decoder
 *
 * @covers  \Hanaboso\RestBundle\Model\Decoder\XmlDecoder
 */
final class XmlDecoderTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    private const XML = '<?xml version="1.0" encoding="UTF-8"?><parent><one>One</one><two>Two</two></parent>';

    /**
     * @var XmlDecoder
     */
    private XmlDecoder $decoder;

    /**
     *
     */
    public function testCreate(): void
    {
        new XmlDecoder(new XmlEncoder());

        self::assertFake();
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\Decoder\XmlDecoder::decode
     */
    public function testDecode(): void
    {
        self::assertEquals(['one' => 'One', 'two' => 'Two'], $this->decoder->decode(self::XML));
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\Decoder\XmlDecoder::decode
     */
    public function testDecodeException(): void
    {
        self::assertException(XmlDecoderException::class, XmlDecoderException::ERROR, "Start tag expected, '<");

        self::assertEquals([], $this->decoder->decode('Unknown'));
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->decoder = self::getContainer()->get('xml');
    }

}
