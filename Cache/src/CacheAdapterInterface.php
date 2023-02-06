<?php
namespace Phalconeer\Cache;

/**
 * Subset of Phalcon\Storage\Adapter\AdapterInterface
 */
interface CacheAdapterInterface
{
    public function get(string $key, $defaultValue = null);

    public function set(string $key, $value, $ttl = null) : bool;

    public function delete(string $key) : bool;

    public function has(string $key) : bool;
}