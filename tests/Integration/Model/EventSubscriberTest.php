<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model;

use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundle\Exception\DecoderExceptionAbstract;
use Hanaboso\RestBundle\Exception\JsonDecoderException;
use Hanaboso\RestBundle\Exception\XmlDecoderException;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Hanaboso\RestBundle\Model\EventSubscriber;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriberTest
 *
 * @package Hanaboso\RestBundleTests\Integration\Model
 *
 * @covers  \Hanaboso\RestBundle\Model\EventSubscriber
 */
final class EventSubscriberTest extends KernelTestCaseAbstract
{

    use PrivateTrait;

    /**
     * @var EventSubscriber
     */
    private EventSubscriber $subscriber;

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\EventSubscriber::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $requestEvent = $this->prepareRequestEvent();

        $this->subscriber->onKernelRequest($requestEvent);
        /** @var Response $response */
        $response = $requestEvent->getResponse();

        self::assertEquals(['one' => 'One'], $requestEvent->getRequest()->request->all());
        self::assertEquals(
            [
                'access-control-allow-credentials' => ['true'],
                'access-control-allow-headers'     => ['Content-Type'],
                'access-control-allow-methods'     => ['GET, POST, PUT, DELETE, OPTIONS'],
                'access-control-allow-origin'      => ['https://example.com'],
                'access-control-max-age'           => ['3600'],
                'cache-control'                    => ['no-cache, private',],
                'content-security-policy'          => ["default-src * data: blob: 'unsafe-inline' 'unsafe-eval'"],
                'date'                             => [$response->headers->get('date')],
                'expect-ct'                        => ['max-age=3600'],
                'feature-policy'                   => ["accelerometer 'self'; ambient-light-sensor 'self'; autoplay 'self'; camera 'self'; cookie 'self'; docwrite 'self'; domain 'self'; encrypted-media 'self'; fullscreen 'self'; geolocation 'self'; gyroscope 'self'; magnetometer 'self'; microphone 'self'; midi 'self'; payment 'self'; picture-in-picture 'self'; speaker 'self'; sync-script 'self'; sync-xhr 'self'; unsized-media 'self'; usb 'self'; vertical-scroll 'self'; vibrate 'self'; vr 'self'"],
                'referrer-policy'                  => ['strict-origin-when-cross-origin'],
                'strict-transport-security'        => ['max-age=31536000; includeSubDomains; preload'],
                'x-content-type-options'           => ['nosniff'],
                'x-frame-options'                  => ['sameorigin'],
                'x-xss-protection'                 => ['1; mode=block'],
            ],
            $response->headers->all(),
        );
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\EventSubscriber::onKernelRequest
     */
    public function testOnKernelRequestException(): void
    {
        $this->setProperty($this->subscriber, 'strict', TRUE);
        $this->setProperty(
            $this->subscriber,
            'decoders',
            [
                'json' => new class implements DecoderInterface {

                    /**
                     * @param string $content
                     *
                     * @return mixed[]
                     */
                    public function decode(string $content): array
                    {
                        $content;

                        throw new JsonDecoderException(
                            'Something gone terribly wrong!',
                            DecoderExceptionAbstract::ERROR,
                            new Exception('Unknown JSON error!'),
                        );
                    }

                },
                'xml'  => new class implements DecoderInterface {

                    /**
                     * @param string $content
                     *
                     * @return mixed[]
                     */
                    public function decode(string $content): array
                    {
                        $content;

                        throw new XmlDecoderException(
                            'Something gone terribly wrong!',
                            DecoderExceptionAbstract::ERROR,
                            new Exception('Unknown XML error!'),
                        );
                    }

                },
            ],
        );

        try {
            $this->subscriber->onKernelRequest($this->prepareRequestEvent());

            self::fail('Must throw exception!');
        } catch (DecoderException $exception) {
            self::assertEquals('Cannot decode given content!', $exception->getMessage());
            self::assertEquals(DecoderException::ERROR, $exception->getCode());

            $exceptions = $exception->getExceptions();
            self::assertCount(2, $exceptions);

            $jsonException = $exception->getExceptions()[0];
            $xmlException  = $exception->getExceptions()[1];
            self::assertInstanceOf(JsonDecoderException::class, $jsonException);
            self::assertInstanceOf(XmlDecoderException::class, $xmlException);

            self::assertEquals('Something gone terribly wrong!', $jsonException->getMessage());
            self::assertEquals(DecoderExceptionAbstract::ERROR, $jsonException->getCode());
            self::assertEquals('Something gone terribly wrong!', $xmlException->getMessage());
            self::assertEquals(DecoderExceptionAbstract::ERROR, $xmlException->getCode());

            $jsonPreviousException = $jsonException->getPrevious();
            $xmlPreviousException  = $xmlException->getPrevious();

            if ($jsonPreviousException && $xmlPreviousException) {
                self::assertEquals('Unknown JSON error!', $jsonPreviousException->getMessage());
                self::assertEquals('Unknown XML error!', $xmlPreviousException->getMessage());
            }

        }
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\Model\EventSubscriber::onKernelResponse
     */
    public function testOnKernelResponse(): void
    {
        $responseEvent = $this->prepareResponseEvent();

        $this->subscriber->onKernelResponse($responseEvent);

        self::assertEquals(
            [
                'access-control-allow-credentials' => ['true'],
                'access-control-allow-headers'     => ['Content-Type'],
                'access-control-allow-methods'     => ['GET, POST, PUT, DELETE, OPTIONS'],
                'access-control-allow-origin'      => ['https://example.com'],
                'access-control-max-age'           => ['3600'],
                'cache-control'                    => ['no-cache, private',],
                'content-security-policy'          => ["default-src * data: blob: 'unsafe-inline' 'unsafe-eval'"],
                'date'                             => [$responseEvent->getResponse()->headers->get('date')],
                'expect-ct'                        => ['max-age=3600'],
                'feature-policy'                   => ["accelerometer 'self'; ambient-light-sensor 'self'; autoplay 'self'; camera 'self'; cookie 'self'; docwrite 'self'; domain 'self'; encrypted-media 'self'; fullscreen 'self'; geolocation 'self'; gyroscope 'self'; magnetometer 'self'; microphone 'self'; midi 'self'; payment 'self'; picture-in-picture 'self'; speaker 'self'; sync-script 'self'; sync-xhr 'self'; unsized-media 'self'; usb 'self'; vertical-scroll 'self'; vibrate 'self'; vr 'self'"],
                'referrer-policy'                  => ['strict-origin-when-cross-origin'],
                'strict-transport-security'        => ['max-age=31536000; includeSubDomains; preload'],
                'x-content-type-options'           => ['nosniff'],
                'x-frame-options'                  => ['sameorigin'],
                'x-xss-protection'                 => ['1; mode=block'],
            ],
            $responseEvent->getResponse()->headers->all(),
        );
    }

    /**
     * @covers \Hanaboso\RestBundle\Model\EventSubscriber::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [
                KernelEvents::REQUEST  => ['onKernelRequest', 250],
                KernelEvents::RESPONSE => ['onKernelResponse', 250],
            ],
            EventSubscriber::getSubscribedEvents(),
        );
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriber = self::getContainer()->get('subscriber');
    }

    /**
     * @return RequestEvent
     * @throws Exception
     */
    private function prepareRequestEvent(): RequestEvent
    {
        $request = self::createMock(Request::class);
        $request->method('getMethod')->willReturn('OPTIONS');
        $request->method('getContent')->willReturn('{"one":"One"}');
        $request->method('getRequestUri')->willReturn('/api/example');

        $this->setProperty($request, 'headers', new HeaderBag(['Origin' => 'https://example.com']));

        $requestEvent = self::createPartialMock(RequestEvent::class, ['getRequest']);
        $requestEvent->method('getRequest')->willReturn($request);

        return $requestEvent;
    }

    /**
     * @return ResponseEvent
     * @throws Exception
     */
    private function prepareResponseEvent(): ResponseEvent
    {
        $request = self::createMock(Request::class);
        $request->method('getRequestUri')->willReturn('/api/example');
        $this->setProperty($request, 'headers', new HeaderBag(['Origin' => 'https://example.com']));

        $requestEvent = self::createPartialMock(ResponseEvent::class, ['getRequest', 'getResponse']);
        $requestEvent->method('getRequest')->willReturn($request);
        $requestEvent->method('getResponse')->willReturn(new Response());

        return $requestEvent;
    }

}
