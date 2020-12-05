<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 *
 * @package App
 */
class Config
{
    /**
     * @var array
     */
    private static $config;

    /**
     * Config constructor.
     *
     */
    private function __construct()
    {
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (null === self::$config) {
            self::$config = Yaml::parseFile(__DIR__ . '/../../resources/config.yaml');
        }

        return self::$config;
    }


    public function getComision()
    {
//        return $this->config
    }
}
