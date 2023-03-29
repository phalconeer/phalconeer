<?php
namespace Phalconeer\FormValidator;

use Phalcon\Config as PhalconConfig;
use Phalcon\Filter;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\FormValidator as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'formValidator';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/form_validator_config.php'
    ];

    protected function configure() {
        $di = $this->di;

        return function (
            This\Data\Form $form,
            PhalconConfig\Config $config = null,
            Filter\FilterInterface $filter = null,
            bool $strictMode = false
        ) use ($di) {
            if (is_null($config)) {
                $config = $di->get(Config\Factory::MODULE_NAME)->get(self::MODULE_NAME); //static points to Phalcon\Di\FactoryDefault
            }
            return new This\Bo\FormValidatorBo(
                $form,
                $config,
                $filter,
                $strictMode
            );
        };
    }
}