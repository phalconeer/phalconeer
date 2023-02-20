<?php
namespace Phalconeer\Impression;

use Phalconeer\Application;
use Phalconeer\Bootstrap;
use Phalconeer\Config;
use Phalconeer\Impression as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'impression';
    
    protected static $instances = [];

    protected static $requiredModules = [
        Application\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
        'request',
        'elasticAdapter'
    ];

    protected static $configFiles = [
        __DIR__ . '/_config/impression_config.php'
    ];

    /**
     * Configures the Bootstrap module
     * @return Phalcon\Config
     */
    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::class, Config\Helper\ConfigHelper::$dummyConfig);
        $di = $this->di;
        $application = $this->di->get(Application\Factory::MODULE_NAME);
        $request = $this->di->get('request');

        return function ($indexName = null) use ($application, $config, $di, $request) {
            $indexName = $indexName ?? $config->offsetGet('indexName');
            $impressionBo = $config->get('impressionBo', ImpressionBo::class);
            $impressionDao = $config->get('impressionDao', ImpressionsDao::class);
            $adapter = $di->get('elasticAdapter');
            $adapter->offsetSet('indexName', $indexName);

            if (!array_key_exists($indexName, self::$instances)) {
                self::$instances[$indexName] = new $impressionBo(
                    new $impressionDao($adapter),
                    $request,
                    $application
                );
                self::$instances[$indexName]->initImpression();
            }

            return self::$instances[$indexName];
        };
    }
}
