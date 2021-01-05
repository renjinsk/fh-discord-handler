<?php

namespace RenjiNSK\DiscordHandlerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class DiscordHandlerExtension
 *
 * @package RenjiNSK\FHDiscordHandlerBundle\DependencyInjection
 * @author  Kostas Rentzikas <krentzikas@ferryhopper.com>
 */
class DiscordHandlerExtension extends Extension
{

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        $loader->load('services.yml');

    }
    
    public function getAlias()
    {
        return 'discord_handler';
    }
}
