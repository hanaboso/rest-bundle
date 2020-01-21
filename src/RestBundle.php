<?php declare(strict_types=1);

namespace Hanaboso\RestBundle;

use Hanaboso\RestBundle\DependencyInjection\CompilerPass\RestCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RestBundle
 *
 * @package Hanaboso\RestBundle
 *
 * @codeCoverageIgnore
 */
final class RestBundle extends Bundle
{

    public const KEY = 'rest';

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RestCompilerPass());
    }

}
