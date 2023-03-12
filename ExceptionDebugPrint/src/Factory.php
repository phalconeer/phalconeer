<?php
namespace Phalconeer\ExceptionDebugPrint;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\ExceptionDebugPrint as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'exceptionDebugPrint';

    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request'
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/dispatcher_config.php',
    ];

    protected function configure()
    {
        $request = $this->di->get('request');
        $exceptionDescriptors = $this->di->get(Config\Factory::MODULE_NAME)
            ->get('exceptionDescriptors', Config\Helper\ConfigHelper::$dummyConfig);

        return new This\Bo\ExceptionDebugPrintBo(
            $request,
            $exceptionDescriptors
        );
    }
}