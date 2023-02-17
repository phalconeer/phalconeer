<?php
namespace Phalconeer\LiveSession;

use Phalconeer\LiveSession as This;

interface LiveSessionInterface
{
    public function createSession(This\Data\LiveSession $sessionData) : ?This\Data\LiveSession;

    public function isValid(string $sessionId) : bool;

    public function getSession(string $sessionId = null) : ?This\Data\LiveSession;

    public function deleteSession(string $sessionId) : bool;
}