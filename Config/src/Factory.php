<?php
namespace Phalconeer\Config;

use Phalconeer\Bootstrap;
use Phalcon\Config as PhalconConfig;
use Phalconeer\Config as This;
use Phalconeer\Exception\NotFound\ConfigNotFoundException;
use Phalconeer\Exception\InvalidArgumentException;

/**
 * Initializes the config. Reads it from its location and stores it in the Di container for easier access.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'config';
    
    protected function configure() {
        if (!$this->config->offsetExists('configFiles') || count($this->config->configFiles) == 0) {
            throw new InvalidArgumentException('The bootstrap parameter \'configFiles\' does not exist or is empty.');
        }

        $config = array();
        foreach ($this->config->configFiles as $configFile) {
            if (!file_exists($configFile)) {
                throw new ConfigNotFoundException('Config file not found:' . $configFile);
            }
            $config = array_merge_recursive($config, include_once $configFile);
        }

        This\Helper\ConfigHelper::$dummyConfig = new PhalconConfig\Config();

        return new PhalconConfig\Config($config);
    }
}
