<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model\Decoder;

use Hanaboso\RestBundle\Exception\XmlDecoderException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Throwable;

/**
 * Class XmlDecoder
 *
 * @package Hanaboso\RestBundle\Model\Decoder
 */
final class XmlDecoder implements DecoderInterface
{

    /**
     * @var XmlEncoder
     */
    private XmlEncoder $encoder;

    /**
     * XmlDecoder constructor.
     *
     * @param XmlEncoder $encoder
     */
    public function __construct(XmlEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param string $content
     *
     * @return mixed[]
     * @throws XmlDecoderException
     */
    public function decode(string $content): array
    {
        try {
            return $this->encoder->decode($content, XmlEncoder::FORMAT);
        } catch (Throwable $throwable) {
            throw new XmlDecoderException($throwable->getMessage(), XmlDecoderException::ERROR, $throwable);
        }
    }

}
