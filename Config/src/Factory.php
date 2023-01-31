<?php
namespace Phalconeer\Config;

use Phalconeer\Bootstrap;
use Phalcon\Config;
use Phalconeer\Exception\NotFound\ConfigNotFoundException;
use Phalconeer\Exception\InvalidArgumentException;

/**
 * Initializes the config. Reads it from its location and stores it in the Di container for easier access.
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'config';
    
    /**
     * Configures the Bootstrap module
     * @return Phalcon\Config\Config
     * @throws InvalidArgumentException   If the $this->option parameter doesn't have a non-empty 'configFiles' entry array.
     * @throws ConfigNotFoundException
     */
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

        return new Config\Config($config);
    }
}
