<?php

namespace Perform\AnalyticsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Perform\Licensing\Licensing;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class PerformAnalyticsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        Licensing::validateProject($container);
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $twigExtension = $container->getDefinition('perform_analytics.twig.analytics');
        $twigExtension->addArgument($config['enabled']);
        $twigExtension->addArgument($config['vendors']);

        $settingsPanel = $container->getDefinition('perform_analytics.settings.analytics');
        $settingsPanel->addArgument($config['enabled']);
        $settingsPanel->addArgument($config['vendors']);
    }
}
