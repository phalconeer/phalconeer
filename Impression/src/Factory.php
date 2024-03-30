<?php
namespace Phalconeer\Impression;

use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Impression as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'impression';
    
    protected static array $requiredModules = [
        Config\Factory::MODULE_NAME,
        'request',
    ];

    protected ?This\Bo\ImpressionBo $bo = null;

    protected function setupAdapters()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);

        if ($config->has('adapters')) {
            $iterator = $config->adapters->getIterator();
            while ($iterator->valid()) {
                $this->bo->addAdapter($this->di->get($iterator->current()));
                $iterator->next();
            }
        }
    }

    protected function setup()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME, Config\Helper\ConfigHelper::$dummyConfig);

        $this->bo = new This\Bo\ImpressionBo(
            $this->di->get('request'),
            $config
        );

        $this->setupAdapters();
    }

    protected function configure()
    {
        if (is_null($this->bo)) {
            $this->setup();
        }

        return $this->bo;
    }
}
