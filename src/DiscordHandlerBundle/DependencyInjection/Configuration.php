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
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('discord_handler');
        $treeBuilder->getRootNode()
                    ->children()
                        ->scalarNode('webhook')->isRequired()->end()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('subName')->defaultValue(':facepalm:')->end()
                        ->scalarNode('level')->defaultValue('notice')->end()
                        ->booleanNode('bubble')->defaultValue(true)->end()
                    ->end();

        return $treeBuilder;
    }
}
