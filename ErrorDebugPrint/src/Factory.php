<?php
namespace Phalconeer\ErrorDebugPrint;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ErrorDebugPrint as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'errorDebugPrint';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request'
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/error_handler_config.php',
    ];

    protected function configure()
    {
        $request = $this->di->get('request');

        return new This\Bo\ErrorDebugPrintBo(
            $request
        );
    }
}