<?php
namespace Phalconeer\CacheControl\Bo;

use Phalcon\Http;
use Phalconeer\Cache;
use Phalconeer\CacheControl as This;

class CacheControlBo
{
    protected Http\Request $request;

    protected ?Cache\Data\CacheSettings $cacheSettings = null;

    protected $cacheHeaders = [];

    protected function readHeader()
    {
        $headers = $this->request->getHeaders();
        if (!array_key_exists(This\Helper\CacheControlHelper::CACHE_CONTROL, $headers)) {
            return [];
        }
        $cacheControl = explode(',', $headers[This\Helper\CacheControlHelper::CACHE_CONTROL]);
        return array_map('trim', $cacheControl);
    }

    protected function getReadCache()
    {
        return !in_array(This\Helper\CacheControlHelper::NO_CACHE, $this->cacheHeaders);
    }

    protected function getWriteCache()
    {
        return !in_array(This\Helper\CacheControlHelper::NO_CACHE, $this->cacheHeaders)
                && !in_array(This\Helper\CacheControlHelper::NO_STORE, $this->cacheHeaders);
    }

    public function __construct(Http\Request $request)
    {
        $this->request = $request;

        $this->cacheHeaders = $this->readHeader();
        $this->cacheSettings = new Cache\Data\CacheSettings([
            'readCache'     => $this->getReadCache(),
            'writeCache'    => $this->getWriteCache()
        ]);
    }

    public function getCacheControl() : ?Cache\Data\CacheSettings
    {
        return $this->cacheSettings;
    }

    public function isCacheReadEnabled()
    {
        return $this->cacheSettings->readCache();
    }

    public function isCacheWriteEnabled()
    {
        return $this->cacheSettings->writeCache();
    }
}