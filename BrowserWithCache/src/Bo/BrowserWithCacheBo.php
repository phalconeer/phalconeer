<?php
namespace Phalconeer\BrowserWithCache\Bo;

use Phalcon\Config;
use Phalconeer\Browser;
use Phalconeer\Cache;
use Phalconeer\BrowserWithCache as This;
use Phalconeer\CurlClient;
use Phalconeer\Middleware;
use Psr;

class BrowserWithCacheBo extends Browser\Bo\BrowserBo
{
    protected Cache\CacheAdapterInterface $cache;

    protected Cache\Data\CacheSettings $cacheSettings;

    protected Config\Config $config;

    /**
     * The constructor.
     *
     */
    public function __construct(
        CurlClient\CurlClientInterface $client,
        array $requestMiddlewares = [],
        array $responseMiddlewares = [],
        Cache\CacheAdapterInterface $cache,
        Cache\Data\CacheSettings $cacheSettings,
        Config\Config $config = null
    )
    {
        parent::__construct(
            $client,
            $requestMiddlewares,
            $responseMiddlewares,
        );
        $this->cache = $cache;
        $this->cacheSettings = $cacheSettings;
        $this->config = $config ?? new Config\COnfig;
    }

    protected function getCacheKey(Psr\Http\Message\RequestInterface $request) : string
    {
        return implode('_', [
            $this->config->get('cacheName', ''),
            $request->getMethod(),
            $request->getUri(),
            md5(serialize($request->getBody())),
            md5(implode(',', $request->getHeader('Authorization')))
        ]);
    }

    public function call(
        Psr\Http\Message\RequestInterface $request,
        Cache\Data\CacheSettings $cacheSettings = null) : Psr\Http\Message\ResponseInterface
    {
        $cacheSettings = $cacheSettings ?? $this->cacheSettings;
        $cacheKey = $this->getCacheKey($request);
        if (!$cacheSettings->readCache()) {
            $responseJson = $this->cache->get($cacheKey);
            if (is_null($responseJson)) {
                $response = parent::call($request);
                if ($cacheSettings->writeCache()) {
                    $this->cache->set(
                        $cacheKey,
                        json_encode($response),
                        $cacheSettings->ttl()
                    );
                }
            } else {
                $response = new CurlClient\Data\CurlResponse(json_decode($responseJson, 1));
            }
        }

        return $response;
    }
}