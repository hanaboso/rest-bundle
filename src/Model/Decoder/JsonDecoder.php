<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model\Decoder;

use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\Utils\String\Json;
use Throwable;

/**
 * Class JsonDecoder
 *
 * @package Hanaboso\RestBundle\Model\Decoder
 */
final class JsonDecoder implements DecoderInterface
{

    /**
     * @param string $content
     *
     * @return mixed[]
     * @throws DecoderException
     */
    public function decode(string $content): array
    {
        try {
            return Json::decode($content);
        } catch (Throwable $throwable) {
            throw new DecoderException($throwable->getMessage(), DecoderException::ERROR, $throwable);
        }
    }

}
