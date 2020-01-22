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
     * @var DecoderExceptionAbstract[]
     */
    private array $exceptions;

    /**
     * DecoderException constructor.
     *
     * @param string                     $message
     * @param int                        $code
     * @param Throwable|null             $previous
     * @param DecoderExceptionAbstract[] $exceptions
     */
    public function __construct(string $message, int $code, ?Throwable $previous = NULL, array $exceptions = [])
    {
        parent::__construct($message, $code, $previous);

        $this->exceptions = $exceptions;
    }

    /**
     * @return DecoderExceptionAbstract[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

}
