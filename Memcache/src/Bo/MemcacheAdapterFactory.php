<?php
namespace Phalconeer\Memcache\Bo;

use Phalcon\Cache;
use Phalconeer\Memcache as This;

class MemcacheAdapterFactory extends Cache\AdapterFactory
{
    /**
     * Returns the available adapters
     */
    protected function getAdapters() : array
    {
        $adapters = parent::getServices();
        $adapters[This\Helper\MemcacheHelper::ADAPTER_TYPE_MEMCACHE_WITH_IGNORE] = MemcacheAdapter::class;
        return $adapters;
    }
}