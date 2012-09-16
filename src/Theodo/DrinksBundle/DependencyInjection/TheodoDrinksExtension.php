<?php

namespace Theodo\DrinksBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * TheodoDrinksExtension class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class TheodoDrinksExtension extends Extension
{
    /**
     * Load drinks bundle configuration
     *
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->loadControllers($loader);
    }

    /**
     * Load controller services.
     *
     * @param \Symfony\Component\DependencyInjection\Loader\YamlFileLoader $loader
     */
    public function loadControllers(YamlFileLoader $loader)
    {
        $loader->load('controllers.yml');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'drinks';
    }
}
