<?php declare(strict_types=1);

namespace Hanaboso\RestBundle\DependencyInjection;

use Hanaboso\RestBundle\RestBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
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

    public const string STRICT                    = 'strict';
    public const string ROUTES                    = 'routes';
    public const string DECODERS                  = 'decoders';
    public const string CORS                      = 'cors';
    public const string SECURITY                  = 'security';
    public const string ORIGIN                    = 'origin';
    public const string METHODS                   = 'methods';
    public const string HEADERS                   = 'headers';
    public const string CREDENTIALS               = 'credentials';
    public const string X_FRAME_OPTIONS           = 'X_Frame_Options';
    public const string X_XSS_PROTECTION          = 'X_XSS_Protection';
    public const string X_CONTENT_TYPE_OPTIONS    = 'X_Content_Type_Options';
    public const string STRICT_TRANSPORT_SECURITY = 'Strict_Transport_Security';
    public const string REFERER_POLICY            = 'Referrer_Policy';
    public const string CONTENT_SECURITY_POLICY   = 'Content_Security_Policy';
    public const string EXPECT_CT                 = 'Expect_CT';
    public const string FEATURE_POLICY            = 'Feature_Policy';

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

        /** @var ArrayNodeDefinition $origin */
        $origin = (new ArrayNodeDefinition(self::ORIGIN))->scalarPrototype()->end();
        /** @var ArrayNodeDefinition $methods */
        $methods = (new ArrayNodeDefinition(self::METHODS))->scalarPrototype()->end();
        /** @var ArrayNodeDefinition $headers */
        $headers = (new ArrayNodeDefinition(self::HEADERS))->scalarPrototype()->end();

        $rootNode
            ->children()
            ->arrayNode(self::CORS)
            ->arrayPrototype()
            ->append($origin)
            ->append($methods)
            ->append($headers)
            ->append(new BooleanNodeDefinition(self::CREDENTIALS));

        $rootNode
            ->children()
            ->arrayNode(self::SECURITY)
            ->arrayPrototype()
            ->append(new ScalarNodeDefinition(self::X_FRAME_OPTIONS))
            ->append(new ScalarNodeDefinition(self::X_XSS_PROTECTION))
            ->append(new ScalarNodeDefinition(self::X_CONTENT_TYPE_OPTIONS))
            ->append(new ScalarNodeDefinition(self::CONTENT_SECURITY_POLICY))
            ->append(new ScalarNodeDefinition(self::STRICT_TRANSPORT_SECURITY))
            ->append(new ScalarNodeDefinition(self::REFERER_POLICY))
            ->append(new ScalarNodeDefinition(self::FEATURE_POLICY))
            ->append(new ScalarNodeDefinition(self::EXPECT_CT));

        return $treeBuilder;
    }

}
