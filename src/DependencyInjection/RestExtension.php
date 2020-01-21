<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\DependencyInjection;

use Exception;
use Hanaboso\RestBundle\RestBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class RestExtension
 *
 * @package Hanaboso\RestBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 */
final class RestExtension extends Extension
{

    /**
     * @param mixed[]          $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container->setParameter(RestBundle::KEY, $this->processConfiguration(new Configuration(), $configs));
    }

}
