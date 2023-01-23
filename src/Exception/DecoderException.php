<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Exception;

use Exception;
use Throwable;

/**
 * Class DecoderException
 *
 * @package Hanaboso\RestBundle\Exception
 */
final class DecoderException extends Exception
{

    public const ERROR = 1;

    /**
     * DecoderException constructor.
     *
     * @param string                     $message
     * @param int                        $code
     * @param Throwable|null             $previous
     * @param DecoderExceptionAbstract[] $exceptions
     */
    public function __construct(
        string $message,
        int $code,
        ?Throwable $previous = NULL,
        private readonly array $exceptions = [],
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return DecoderExceptionAbstract[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

}
