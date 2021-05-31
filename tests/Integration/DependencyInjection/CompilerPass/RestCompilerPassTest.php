<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\Integration\DependencyInjection\CompilerPass;

use Exception;
use Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass;
use Hanaboso\RestBundle\DependencyInjection\Configuration;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Hanaboso\RestBundle\RestBundle;
use Hanaboso\RestBundleTests\KernelTestCaseAbstract;
use LogicException;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class RestCompilerPassTest
 *
 * @package Hanaboso\RestBundleTests\Integration\DependencyInjection\CompilerPass
 *
 * @covers  \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass
 */
final class RestCompilerPassTest extends KernelTestCaseAbstract
{

    private const PARAMETERS = [
        Configuration::STRICT   => FALSE,
        Configuration::ROUTES   => [
            '^/api' => ['json', 'xml'],
        ],
        Configuration::DECODERS => [
            'json' => 'rest.decoder.json',
            'xml'  => 'rest.decoder.xml',
        ],
        Configuration::CORS     => [
            '^/api' => [
                Configuration::ORIGIN      => ['*'],
                Configuration::METHODS     => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                Configuration::HEADERS     => ['Content-Type'],
                Configuration::CREDENTIALS => TRUE,
            ],
        ],
        Configuration::SECURITY => [
            '^/api' => [
                Configuration::X_FRAME_OPTIONS           => 'sameorigin',
                Configuration::X_XSS_PROTECTION          => '1; mode=block',
                Configuration::X_CONTENT_TYPE_OPTIONS    => 'nosniff',
                Configuration::CONTENT_SECURITY_POLICY   => "default-src * data: blob: 'unsafe-inline' 'unsafe-eval'",
                Configuration::STRICT_TRANSPORT_SECURITY => 'max-age=31536000; includeSubDomains; preload',
                Configuration::REFERER_POLICY            => 'strict-origin-when-cross-origin',
                Configuration::FEATURE_POLICY            => "accelerometer 'self'; ambient-light-sensor 'self'; autoplay 'self'; camera 'self'; cookie 'self'; docwrite 'self'; domain 'self'; encrypted-media 'self'; fullscreen 'self'; geolocation 'self'; gyroscope 'self'; magnetometer 'self'; microphone 'self'; midi 'self'; payment 'self'; picture-in-picture 'self'; speaker 'self'; sync-script 'self'; sync-xhr 'self'; unsized-media 'self'; usb 'self'; vertical-scroll 'self'; vibrate 'self'; vr 'self'",
                Configuration::EXPECT_CT                 => 'max-age=3600',
            ],
        ],
    ];

    /**
     * @var RestCompilerPass
     */
    private RestCompilerPass $compilerPass;

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass::process
     */
    public function testProcess(): void
    {
        $builder = $this->prepareContainerBuilder();

        $this->compilerPass->process($builder);

        self::assertTrue($builder->has('rest.subscriber'));
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass::process
     */
    public function testProcessMissingDecoderService(): void
    {
        self::assertException(LogicException::class, 0, "Service 'Unknown' not found!");

        $this->compilerPass->process($this->prepareContainerBuilder([Configuration::DECODERS => ['Unknown']]));
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass::process
     */
    public function testProcessMissingDecoderInterface(): void
    {
        self::assertException(
            LogicException::class,
            0,
            sprintf("Service 'unknown' does not implement %s!", DecoderInterface::class),
        );

        $builder = $this->prepareContainerBuilder([Configuration::DECODERS => ['unknown']]);
        $builder->setDefinition('unknown', new Definition(stdClass::class));

        $this->compilerPass->process($builder);
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass::process
     */
    public function testProcessMissingDecoder(): void
    {
        self::assertException(LogicException::class, 0, "Decoder 'Unknown' not found!");

        $this->compilerPass->process($this->prepareContainerBuilder([Configuration::ROUTES => ['^/' => ['Unknown']]]));
    }

    /**
     * @throws Exception
     *
     * @covers \Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass::process
     */
    public function testProcessMissingDecoders(): void
    {
        self::assertException(LogicException::class, 0, "Decoders 'Unknown', 'Another Unknown' not found!");

        $this->compilerPass->process(
            $this->prepareContainerBuilder([Configuration::ROUTES => ['^/' => ['Unknown', 'Another Unknown']]]),
        );
    }

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->compilerPass = new RestCompilerPass();
    }

    /**
     * @param mixed[] $parameters
     *
     * @return ContainerBuilder
     */
    private function prepareContainerBuilder(array $parameters = self::PARAMETERS): ContainerBuilder
    {
        return new ContainerBuilder(new ParameterBag([RestBundle::KEY => array_merge(self::PARAMETERS, $parameters)]));
    }

}
