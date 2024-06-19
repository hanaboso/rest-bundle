<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model\Decoder;

use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\CustomAssertTrait;
use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
use Hanaboso\RestBundle\Exception\XmlDecoderException;
use Hanaboso\RestBundle\Model\Decoder\XmlDecoder;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Class XmlDecoderTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Model\Decoder
 */
#[CoversClass(XmlDecoder::class)]
final class XmlDecoderTest extends KernelTestCaseAbstract
{

    use CustomAssertTrait;

    private const XML = '<?xml version="1.0" encoding="UTF-8"?><parent><one>One</one><two>Two</two></parent>';

    /**
     * @var XmlDecoder
     */
    private XmlDecoder $decoder;

    /**
     * @throws Exception
     */
    public function testDecode(): void
    {
        self::assertEquals(['one' => 'One', 'two' => 'Two'], $this->decoder->decode(self::XML));
    }

    /**
     * @throws Exception
     */
    public function testDecodeException(): void
    {
        self::assertException(XmlDecoderException::class, DecoderExceptionAbstract::ERROR, "Start tag expected, '<");

        self::assertEquals([], $this->decoder->decode('Unknown'));
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->decoder = self::getContainer()->get('xml');
    }

}
