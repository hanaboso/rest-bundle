<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model\Decoder;

use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
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
     * XmlDecoder constructor.
     *
     * @param XmlEncoder $encoder
     */
    public function __construct(private XmlEncoder $encoder)
    {
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
            throw new XmlDecoderException($throwable->getMessage(), DecoderExceptionAbstract::ERROR, $throwable);
        }
    }

}
