<?php
namespace Phalconeer\Impression;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Impression as This;
use Phalcon;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'impression';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request',
    ];

    protected ?This\Bo\ImpressionBo $bo = null;

    protected ?Phalcon\Config\Config $moduleConfig = null;

    protected function setupAdapters()
    {
        if ($this->moduleConfig->has('adapters')) {
            $iterator = $this->moduleConfig->adapters->getIterator();
            while ($iterator->valid()) {
                $this->bo->addAdapter($this->di->get($iterator->current()));
                $iterator->next();
            }
        }
    }

    protected function setup()
    {
        $this->bo = new This\Bo\ImpressionBo(
            $this->di->get('request'),
            $this->moduleConfig
        );
    }

    protected function configure()
    {
        if (is_null($this->moduleConfig)) {
            $this->moduleConfig = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);
        }

        if (is_null($this->bo)) {
            $this->setup();
            $this->setupAdapters();
        }

        return $this->bo;
    }
}
