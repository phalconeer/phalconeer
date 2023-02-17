<?php
namespace Phalconeer\Auth;

interface LogoutHandlerInterface
{
    public function handleLogout(string $sessionId) : void;
}