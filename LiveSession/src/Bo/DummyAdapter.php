<?php
namespace Phalconeer\LiveSession\Bo;

use Phalconeer\LiveSession as This;

class DummyAdapter implements This\LiveSessionAdapterInterface
{
    public function adapterNotSet()
    {
        throw new This\Exception\UndefinedLiveSessionAdapterException(
            '',
            This\Helper\ExceptionHelper::LIVE_SESSION__ADAPTER_NOTSET
        );
    }

    public function createSession(This\Data\LiveSession $liveSession) : bool
    {
        $this->adapterNotSet();
    }

    public function isValid(string $sessionId) : bool
    {
        $this->adapterNotSet();
    }

    public function getSession(string $sessionId = null) : ?This\Data\LiveSession
    {
        $this->adapterNotSet();
    }

    public function deleteSession(string $sessionId) : bool
    {
        $this->adapterNotSet();
    }
}