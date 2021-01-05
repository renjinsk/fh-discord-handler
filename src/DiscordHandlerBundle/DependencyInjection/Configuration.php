<?php

namespace RenjiNSK\DiscordHandlerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package RenjiNSK\DiscordHandlerBundle\DependencyInjection
 * @author  Kostas Rentzikas <krentzikas@ferryhopper.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('discord_handler');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('options')->children()
                    ->scalarNode('webhook')->isRequired()->end()
                    ->scalarNode('name')->isRequired()->end()
                    ->scalarNode('subname')->defaultValue(':facepalm:')->end()
                    ->scalarNode('level')->defaultValue('notice')->end()
                    ->booleanNode('buble')->defaultValue(true)->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
