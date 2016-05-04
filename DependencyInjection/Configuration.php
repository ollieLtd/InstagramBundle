<?php

namespace Oh\InstagramBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oh_instagram');

        $rootNode
            ->children()
                ->arrayNode('instaphp')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('config')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('version')->defaultValue('v1')->end()
                                ->scalarNode('log_path')->defaultValue('%kernel.logs_dir%/instaphp-%kernel.environment%.log')->end()
                                ->scalarNode('endpoint')->defaultValue('https://api.instagram.com')->end()
                                ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('scope')->defaultValue('comments+likes+relationships')->end()
                                ->scalarNode('oauth_path')->defaultValue('/oauth/authorize/?client_id={client_id}&amp;response_type=code&amp;redirect_uri={redirect_uri}')->end()
                                ->scalarNode('oauth_token_path')->defaultValue('/oauth/access_token/')->end()
                                ->scalarNode('redirect_route')->defaultValue('OhInstagramBundle_callback')->end()
                                ->booleanNode('no_verify_peer')->defaultFalse()->end()
                                ->booleanNode('log_enabled')->defaultTrue()->end()
                                ->integerNode('http_timeout')->defaultValue(6)->end()
                                ->integerNode('http_connect_timeout')->defaultValue(2)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
