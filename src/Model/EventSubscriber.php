<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\Model;

use Hanaboso\RestBundle\DependencyInjection\Configuration;
use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriber
 *
 * @package Hanaboso\RestBundle\Model
 */
final class EventSubscriber implements EventSubscriberInterface
{

    private const PATTERN     = '~%s~';
    private const ORIGIN      = 'Access-Control-Allow-Origin';
    private const HEADERS     = 'Access-Control-Allow-Headers';
    private const METHODS     = 'Access-Control-Allow-Methods';
    private const CREDENTIALS = 'Access-Control-Allow-Credentials';
    private const MAX_AGE     = 'Access-Control-Max-Age';

    /**
     * EventSubscriber constructor.
     *
     * @param mixed[]            $config
     * @param DecoderInterface[] $decoders
     * @param mixed[]            $cors
     * @param mixed[]            $security
     * @param bool               $strict
     */
    public function __construct(
        private array $config,
        private array $decoders,
        private array $cors,
        private array $security,
        private bool $strict
    )
    {
    }

    /**
     * @param RequestEvent $requestEvent
     *
     * @throws DecoderException
     */
    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response(NULL, 204);

            $this->processCorsHeaders($response, $request);
            $this->processSecurityHeaders($response, $request);

            $requestEvent->setResponse($response);
        }

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

                break;
            }
        }
    }

    /**
     * @param ResponseEvent $responseEvent
     */
    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $this->processCorsHeaders($responseEvent->getResponse(), $responseEvent->getRequest());
        $this->processSecurityHeaders($responseEvent->getResponse(), $responseEvent->getRequest());
    }

    /**
     * @return array<string, array<int|string, array<int|string, int|string>|int|string>|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onKernelRequest', 250],
            KernelEvents::RESPONSE => ['onKernelResponse', 250],
        ];
    }

    /**
     * @param Response $response
     * @param Request  $request
     */
    private function processCorsHeaders(Response $response, Request $request): void
    {
        foreach ($this->cors as $route => $cors) {
            if (preg_match(sprintf(self::PATTERN, $route), $request->getRequestUri())) {
                $this->setCorsHeaders($response, $cors, $request->headers->get('Origin', '*'));

                break;
            }
        }
    }

    /**
     * @param Response $response
     * @param Request  $request
     */
    private function processSecurityHeaders(Response $response, Request $request): void
    {
        foreach ($this->security as $route => $security) {
            if (preg_match(sprintf(self::PATTERN, $route), $request->getRequestUri())) {
                $this->setSecurityHeaders($response, $security);

                break;
            }
        }
    }

    /**
     * @param Response $response
     * @param mixed[]  $headers
     * @param string   $requestOrigin
     */
    private function setCorsHeaders(Response $response, array $headers, string $requestOrigin): void
    {
        $responseOrigin = reset($headers[Configuration::ORIGIN]);

        if ($responseOrigin === '*' || @preg_match($responseOrigin, $requestOrigin)) {
            $responseOrigin = $requestOrigin;
        }

        $response->headers->set(self::ORIGIN, $responseOrigin);
        $response->headers->set(self::METHODS, implode(', ', $headers[Configuration::METHODS]));
        $response->headers->set(self::HEADERS, implode(', ', $headers[Configuration::HEADERS]));
        $response->headers->set(self::CREDENTIALS, $headers[Configuration::CREDENTIALS] ? 'true' : 'false');
        $response->headers->set(self::MAX_AGE, '3600');
    }

    /**
     * @param Response $response
     * @param mixed[]  $headers
     */
    private function setSecurityHeaders(Response $response, array $headers): void
    {
        foreach ($headers as $key => $value) {
            $response->headers->set(str_replace('_', '-', $key), $value);
        }
    }

}
