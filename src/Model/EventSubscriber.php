<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model;

use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriber
 *
 * @package Hanaboso\RestBundle\Model
 */
final class EventSubscriber implements EventSubscriberInterface
{

    private const PATTERN = '~%s~';

    /**
     * @var mixed[]
     */
    private array $config;

    /**
     * @var DecoderInterface[]
     */
    private array $decoders;

    /**
     * @var bool
     */
    private bool $strict;

    /**
     * EventSubscriber constructor.
     *
     * @param mixed[]            $config
     * @param DecoderInterface[] $decoders
     * @param bool               $strict
     */
    public function __construct(array $config, array $decoders, bool $strict)
    {
        $this->decoders = $decoders;
        $this->config   = $config;
        $this->strict   = $strict;
    }

    /**
     * @param RequestEvent $requestEvent
     *
     * @throws DecoderException
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        foreach ($this->config as $route => $decoders) {
            if (preg_match(sprintf(self::PATTERN, $route), $request->getRequestUri())) {
                $exceptions = [];

                foreach ($decoders as $decoder) {
                    try {
                        $request->request = new ParameterBag($this->decoders[$decoder]->decode($request->getContent()));

                        break;
                    } catch (DecoderExceptionAbstract $exception) {
                        $exceptions[] = $exception;
                    }
                }

                if ($this->strict && count($exceptions) !== 0 && count($exceptions) === count($decoders)) {
                    throw new DecoderException(
                        'Cannot decode given content!',
                        DecoderException::ERROR,
                        NULL,
                        $exceptions
                    );
                }
            }

            break;
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

}
