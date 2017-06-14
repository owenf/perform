<?php

namespace Perform\BaseBundle\Tests\Fixtures\ExtendEntities;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * ExtendEntitiesKernel
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExtendEntitiesKernel extends Kernel
{
    protected $dir;
    protected $entityBundles;

    public function __construct($dir, array $entityBundles)
    {
        parent::__construct('dev', true);
        $this->rootDir = $dir;
        $this->entityBundles = $entityBundles;
    }

    public function registerBundles()
    {
        return array_merge([
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Knp\Bundle\MenuBundle\KnpMenuBundle(),

            new \Perform\BaseBundle\PerformBaseBundle(),
            new \Perform\NotificationBundle\PerformNotificationBundle(),
        ], $this->entityBundles);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }
}
