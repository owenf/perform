<?php

namespace Perform\Base\Installer;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Perform\Base\Util\BundleSearcher;

/**
 * SettingsInstaller.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SettingsInstaller implements InstallerInterface
{
    public function install(ContainerInterface $container, LoggerInterface $logger)
    {
        $searcher = new BundleSearcher($container);
        $importer = $container->get('admin_base.settings_importer');
        foreach ($searcher->findResourcesAtPath('config/settings.yml') as $file) {
            $logger->info('Importing '.$file);
            $importer->importYamlFile($file);
        }
    }
}
