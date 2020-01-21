<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\DependencyInjection;

use Hanaboso\RestBundle\RestBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Hanaboso\RestBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 */
final class Configuration implements ConfigurationInterface
{

    public const STRICT   = 'strict';
    public const ROUTES   = 'routes';
    public const DECODERS = 'decoders';

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(RestBundle::KEY);
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()->booleanNode(self::STRICT)->defaultValue(FALSE);
        $rootNode->children()->arrayNode(self::ROUTES)->arrayPrototype()->scalarPrototype();
        $rootNode->children()->arrayNode(self::DECODERS)->scalarPrototype();

        return $treeBuilder;
    }

}
