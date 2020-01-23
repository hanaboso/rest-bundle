<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\DependencyInjection\CompilerPass;

use Exception;
use Hanaboso\RestBundle\DependencyInjection\Configuration;
use Hanaboso\RestBundle\Model\Decoder\DecoderInterface;
use Hanaboso\RestBundle\Model\Decoder\JsonDecoder;
use Hanaboso\RestBundle\Model\Decoder\XmlDecoder;
use Hanaboso\RestBundle\Model\EventSubscriber;
use Hanaboso\RestBundle\RestBundle;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * Class RestCompilerPass
 *
 * @package Hanaboso\RestBundle\DependencyInjection\CompilerPass
 */
final class RestCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $config       = $container->getParameter(RestBundle::KEY);
        $decoders     = [];
        $decoderNames = [];

        $container->setDefinition($this->createKey('encoder.xml'), new Definition(XmlEncoder::class));
        $container->setDefinition($this->createKey('decoder.json'), new Definition(JsonDecoder::class));
        $container->setDefinition(
            $this->createKey('decoder.xml'),
            new Definition(XmlDecoder::class, [new Reference($this->createKey('encoder.xml'))])
        );

        foreach ($config[Configuration::DECODERS] as $key => $value) {
            if (!$container->has($value)) {
                throw new LogicException(sprintf("Service '%s' not found!", $value));
            }

            /** @var object $service */
            $service = $container->get($value);

            if (!in_array(DecoderInterface::class, class_implements(get_class($service)), TRUE)) {
                throw new LogicException(
                    sprintf("Service '%s' does not implement %s!", $value, DecoderInterface::class)
                );
            }

            $decoderNames[] = $key;
        }

        foreach ($config[Configuration::ROUTES] as $key => $value) {
            $missing      = array_diff($value, $decoderNames);
            $missingCount = count($missing);

            if ($missingCount === 1) {
                throw new LogicException(sprintf("Decoder '%s' not found!", $missing[0]));
            } else if ($missingCount > 1) {
                throw new LogicException(sprintf("Decoders '%s' not found!", implode("', '", $missing)));
            }
        }

        foreach ($config[Configuration::DECODERS] as $key => $value) {
            $decoders[$key] = new Reference($value);
        }

        $container->setDefinition(
            $this->createKey('subscriber'),
            (new Definition(
                EventSubscriber::class,
                [
                    $config[Configuration::ROUTES],
                    $decoders,
                    $config[Configuration::CORS],
                    $config[Configuration::STRICT],
                ]
            ))->addTag('kernel.event_subscriber')
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function createKey(string $name): string
    {
        return sprintf('%s.%s', RestBundle::KEY, $name);
    }

}
