<?php declare(strict_types=1);

namespace Hanaboso\RestBundleTests\app;

use Exception;
use Hanaboso\RestBundle\RestBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class Kernel
 *
 * @package Hanaboso\RestBundleTests\app
 */
final class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{yaml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        foreach ([FrameworkBundle::class, RestBundle::class] as $bundle) {
            yield new $bundle();
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container;

        $loader->load(sprintf('%s/*%s', $this->getConfigDir(), self::CONFIG_EXTS), 'glob');
        $loader->load(sprintf('%s/{packages}/*%s', $this->getConfigDir(), self::CONFIG_EXTS), 'glob');
    }

    /**
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes;
    }

    /**
     * @return string
     */
    private function getConfigDir(): string
    {
        return sprintf('%s/tests/app/config', $this->getProjectDir());
    }

}
