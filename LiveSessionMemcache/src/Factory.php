<?php
namespace Phalconeer\LiveSessionMemcache;

use Phalconeer\Bootstrap;
use Phalconeer\Cache;
use Phalconeer\CacheControl;
use Phalconeer\Config;
use Phalconeer\LiveSession;
use Phalconeer\LiveSessionMemcache as This;
use Phalconeer\Memcache;

class Factory extends Bootstrap\Factory
{
    const MODULE_NAME = 'liveSessionMemcache';
    
    protected static array $requiredModules = [
        Cache\Factory::MODULE_NAME,
        Config\Factory::MODULE_NAME,
        LiveSession\Factory::MODULE_NAME,
        Memcache\Factory::MODULE_NAME,
    ];

    protected static array $configFiles = [
        __DIR__ . '/_config/session_config.php'
    ];

    protected function configure() {
        $config = $this->di->get(Config\Factory::MODULE_NAME)->get(static::MODULE_NAME);

        $adapter = new This\Bo\LiveSessionMemcacheBo(
            $this->di->get(Memcache\Factory::MODULE_NAME, [
                Memcache\Helper\MemcacheHelper::TYPE_SHARED,
                $config->get('sessionMemcachePrefix', 'token_'),
                new Cache\Data\CacheSettings(new \ArrayObject([
                    'readCache'     => true,
                    'writeCache'    => true
                ]))
            ]),
            $this->di->get(CacheControl\Factory::MODULE_NAME)
        );

        $this->di->get(LiveSession\Factory::MODULE_NAME)->setAdapter($adapter);

        return Bootstrap\Helper\BootstrapHelper::MODULE_LOADED;
    }
}