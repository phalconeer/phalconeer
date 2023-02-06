<?php
namespace Phalconeer\Crypt;

use Phalcon\Encryption;
use Phalconeer\Bootstrap;
use Phalconeer\Config;

/**
 * This module is needed to enable cookie encryption
 */
class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'crypt';
    
    protected static $reqioredModules = [
        Config\Factory::MODULE_NAME,
    ];
    protected static $configFiles = [
        __DIR__ . '/_config/crypt_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->crypt;

        $crypt = new Encryption\Crypt();
        $crypt->setCipher('aes-256-ctr');
        $crypt->setKey($config->key);

        return $crypt;
    }
}
