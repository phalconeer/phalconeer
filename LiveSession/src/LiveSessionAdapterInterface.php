<?php
namespace Phalconeer\LiveSession;

use Phalconeer\LiveSession as This;

interface LiveSessionAdapterInterface
{
    public function createSession(This\Data\LiveSession $liveSession) : bool;

    public function isValid(string $sessionId) : bool;

    public function getSession(string $sessionId = null) : ?This\Data\LiveSession;

    public function deleteSession(string $sessionId) : bool;
}