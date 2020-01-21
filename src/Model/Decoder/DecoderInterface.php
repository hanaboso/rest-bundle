<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model\Decoder;

use Hanaboso\RestBundle\Exception\DecoderException;

/**
 * Interface DecoderInterface
 *
 * @package Hanaboso\RestBundle\Model\Decoder
 */
interface DecoderInterface
{

    /**
     * @param string $content
     *
     * @return mixed[]
     * @throws DecoderException
     */
    public function decode(string $content): array;

}
