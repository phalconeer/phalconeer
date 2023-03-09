<?php
namespace Phalconeer\LiveSession\Adapter;

use Phalconeer\Cache;
use Phalconeer\LiveSession as This;

class MemcacheAdapter implements This\LiveSessionAdapterInterface
{

    public function __construct(
        protected Cache\CacheAdapterInterfaceWithIgnore $adapter,
    )
    {
    }

    public function createSession(This\Data\LiveSession $liveSession) : bool
    {
        return $this->adapter->set($liveSession->id(), $liveSession->toJson());
    }

    public function isValid(string $sessionId) : bool
    {
        return $this->adapter->has($sessionId);
    }

    public function getSession(string $sessionId = null) : ?This\Data\LiveSession
    {
        if (is_null($sessionId)) {
            return null;
        }
        $sessionData = $this->adapter->get($sessionId);
        if (!$sessionData) {
            return null;
        }

        $liveSession = new This\Data\LiveSession(json_decode($sessionData, true));
        return $liveSession;
    }

    public function deleteSession(string $sessionId) : bool
    {
        return $this->adapter->delete($sessionId);
    }
}