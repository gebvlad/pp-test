<?php

/**
 *
 */
declare(strict_types=1);


namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class Container
 *
 * @package App
 */
class Container
{
    /**
     * @var ContainerBuilder
     */
    private static $container;

    /**
     * Container constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $service
     *
     * @return object|null
     * @throws \Exception
     */
    public static function get(string $service)
    {
        return self::getInstance()->get($service);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     * @throws \Exception
     */
    private static function getInstance(): ContainerBuilder
    {
        if (null === self::$container) {
            self::$container = new ContainerBuilder();
            $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
            $loader->load(__DIR__ . '/../../resources/services.yaml');
        }

        return self::$container;
    }
}
