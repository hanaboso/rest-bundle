<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Exception;

use Exception;

/**
 * Class DecoderExceptionAbstract
 *
 * @package Hanaboso\RestBundle\Exception
 */
abstract class DecoderExceptionAbstract extends Exception
{

    public const int ERROR = 1;

}
