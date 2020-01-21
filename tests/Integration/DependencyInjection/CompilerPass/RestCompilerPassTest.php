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
            sprintf("Service 'unknown' does not implement %s!", DecoderInterface::class)
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
            $this->prepareContainerBuilder([Configuration::ROUTES => ['^/' => ['Unknown', 'Another Unknown']]])
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
