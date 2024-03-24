<?php
namespace Phalconeer\Memcache;

use Phalcon\Storage;
use Phalconeer\Application;
use Phalconeer\Bootstrap;
use Phalconeer\Cache;
use Phalconeer\CacheControl;
use Phalconeer\Config;
use Phalconeer\Memcache as This;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'memcache';
    
    protected static array $requiredModules = [
        Application\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
        Cache\Factory::MODULE_NAME,
        CacheControl\Factory::MODULE_NAME
    ];

    /**
     * from https://github.com/phalcon/cphalcon/blob/v4.0.0/phalcon/Cache/AdapterFactory.zep
     * @var array $defaultConfig  = [
     *     'servers' => [
     *         [
     *             'host' => 'localhost',
     *             'port' => 11211,
     *             'weight' => 1,
     *         ]
     *     ],
     *     'host' => '127.0.0.1',
     *     'port' => 6379,
     *     'persistent' => false,
     *     'auth' => '',
     *     'socket' => '',
     *     'defaultSerializer' => 'Php',
     *     'lifetime' => 3600,
     *     'serializer' => null,
     *     'prefix' => 'phalcon',
     *     'storageDir' => ''
     * ]
     */

    protected function configure()
    {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME);
        $applicationConfig = $this->di->get(Config\Factory::MODULE_NAME)->get('application');
        $cacheControl = $this->di->get(CacheControl\Factory::MODULE_NAME);
        return function (
            $connectionType,
            $prefix = null,
            Cache\Data\CacheSettings $cacheControlInstance = null
        ) use ($applicationConfig, $cacheControl, $config) {
            if (is_null($config)
                || !$config->offsetExists('connections')
                || !$config->connections->offsetExists($connectionType)) {
                throw new This\Exception\InvalidMemcacheTypeException(
                    'Connection configuration for memcache type `' . $connectionType . '` not found.',
                    This\Helper\ExceptionHelper::MEMCACHE__CONNECTION_CONFIG_NOT_FOUND
                );
            }
            $adapterFactory    = new This\Bo\MemcacheAdapterFactory(
                new Storage\SerializerFactory()
            );
            if (is_null($cacheControlInstance)) {
                $cacheControlInstance = $cacheControl->getCacheControl();
            }
            $options = array_merge_recursive(
                $config->get('default', Config\Helper\ConfigHelper::$dummyConfig)->toArray(),
                $config->connections->get($connectionType, Config\Helper\ConfigHelper::$dummyConfig)->toArray(),
                [
                    'cacheSettings'     => $cacheControlInstance,
                    'prefix'            => implode('_', array_filter([
                        $applicationConfig->name,
                        $prefix
                    ]))
                ]
            );

            return $adapterFactory->newInstance(
                This\Helper\MemcacheHelper::ADAPTER_TYPE_MEMCACHE_WITH_IGNORE,
                $options
            );
        };
    }
}