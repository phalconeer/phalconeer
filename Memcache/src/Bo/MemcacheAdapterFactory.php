<?php
namespace Phalconeer\Memcache\Bo;

use Phalcon\Cache;
use Phalconeer\Memcache as This;

class MemcacheAdapterFactory extends Cache\AdapterFactory
{
    /**
     * Returns the available services
     */
    protected function getServices() : array
    {
        $services = parent::getServices();
        $services[This\Helper\MemcacheHelper::ADAPTER_TYPE_MEMCACHE_WITH_IGNORE] = MemcacheAdapter::class;
        return $services;
    }
}