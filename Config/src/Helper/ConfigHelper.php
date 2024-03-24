<?php
namespace Phalconeer\Config\Helper;

use Phalcon\Config;

class ConfigHelper
{
    public static $dummyConfig = null;

    public static function loadConfigFile(string $path) : Config\Config
    {
        $factory  = new Config\ConfigFactory();
        return $factory->newInstance('php', $path);
    }
}