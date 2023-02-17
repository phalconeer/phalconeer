<?php
namespace Phalconeer\LiveSessionMemcache\Bo;

use Phalconeer\Cache;
use Phalconeer\LiveSession;

class LiveSessionMemcacheBo implements LiveSession\LiveSessionAdapterInterface
{

    public function __construct(
        protected Cache\CacheAdapterInterfaceWithIgnore $adapter,
    )
    {
    }

    public function createSession(LiveSession\Data\LiveSession $liveSession) : bool
    {
        return $this->adapter->set($liveSession->id(), $liveSession->toJsonCopy());
    }

    public function isValid(string $sessionId) : bool
    {
        return $this->adapter->has($sessionId);
    }

    public function getSession(string $sessionId = null) : ?LiveSession\Data\LiveSession
    {
        if (is_null($sessionId)) {
            return null;
        }
        $sessionData = $this->adapter->get($sessionId);
        if (!$sessionData) {
            return null;
        }

        $liveSession = new LiveSession\Data\LiveSession(json_decode($sessionData, true));
        return $liveSession;
    }

    public function deleteSession(string $sessionId) : bool
    {
        return $this->adapter->delete($sessionId);
    }
}