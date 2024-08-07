<?php

namespace RenjiNSK\DiscordHandlerBundle\DependencyInjection;

use Monolog\Level;
use RenjiNSK\DiscordHandlerBundle\Services\DiscordMonologHandlerService;
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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(DiscordMonologHandlerService::class);
        $definition->replaceArgument(0, $config['webhook']);
        $definition->replaceArgument(1, $config['name']);
        $definition->replaceArgument(2, $config['subName']);
        $definition->replaceArgument(3, $this->loggingLevelDecider($config['level']));
        $definition->replaceArgument(4, $config['bubble']);
    }

    /**
     * @param string $level
     *
     * @return int
     */
    private function loggingLevelDecider(string $level): int
    {
        return Level::fromName($level)->value;
    }
}
