<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\Model;

use Exception;
use Hanaboso\PhpCheckUtils\PhpUnit\Traits\PrivateTrait;
use Hanaboso\RestBundle\Exception\DecoderException;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Hanaboso\RestBundle\Model\EventSubscriber;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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

        self::assertEquals(['one' => 'One'], $requestEvent->getRequest()->request->all());
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

                        throw new DecoderException('Something gone terribly wrong!', DecoderException::ERROR);
                    }

                },
            ]
        );

        self::assertException(DecoderException::class, DecoderException::ERROR, 'Something gone terribly wrong!');

        $this->subscriber->onKernelRequest($this->prepareRequestEvent());
    }

    /**
     * @covers \Hanaboso\RestBundle\Model\EventSubscriber::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals([KernelEvents::REQUEST => 'onKernelRequest'], EventSubscriber::getSubscribedEvents());
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriber = self::$container->get('subscriber');
    }

    /**
     * @return RequestEvent
     */
    private function prepareRequestEvent(): RequestEvent
    {
        /** @var Request|MockObject $request */
        $request = self::createMock(Request::class);
        $request->method('getContent')->willReturn('{"one":"One"}');
        $request->method('getRequestUri')->willReturn('/api/example');

        /** @var RequestEvent|MockObject $requestEvent */
        $requestEvent = self::createMock(RequestEvent::class);
        $requestEvent->method('getRequest')->willReturn($request);

        return $requestEvent;
    }

}
