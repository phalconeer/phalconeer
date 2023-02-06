<?php
namespace Phalconeer\Cache;

use Phalconeer\Cache as This;
/**
 * Subset of Phalcon\Cache\BackendInterface
 */
interface CacheAdapterInterfaceWithIgnore extends This\CacheAdapterInterface
{
    public function ignoreCache();

    public function resetIgnoreCache();
}
