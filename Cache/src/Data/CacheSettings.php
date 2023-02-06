<?php
namespace Phalconeer\Cache\Data;

use Phalconeer\Data;

class CacheSettings extends Data\ImmutableData
{
    protected static array $_properties = [
        'ignoreCache'           => Data\Helper\ParseValueHelper::TYPE_BOOL,
        'readCache'             => Data\Helper\ParseValueHelper::TYPE_BOOL,
        'ttl'                   => Data\Helper\ParseValueHelper::TYPE_INT,
        'writeCache'            => Data\Helper\ParseValueHelper::TYPE_BOOL,
    ];
    
    protected bool $ignoreCache = false;

    protected bool $readCache = false;

    protected int $ttl = 0;

    protected bool $writeCache = false;

    public function ignoreCache() : bool
    {
        return $this->getValue('ignoreCache');
    }

    /**
     * Returns if the cache reading is enabled
     */
    public function readCache() : bool
    {
        return !$this->ignoreCache && $this->readCache;
    }

    /**
     * Returns if the cache writing is enabled
     */
    public function writeCache() : bool
    {
        return !$this->ignoreCache && $this->writeCache;
    }

    public function ttl() : int
    {
        return $this->getValue('ttl');
    }

    public function setIgnoreCache(bool $ignoreCache) : self
    {
        return $this->setValueByKey('ignoreCache', $ignoreCache);
    }
}

