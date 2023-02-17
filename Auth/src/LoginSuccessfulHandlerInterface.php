<?php
namespace Phalconeer\Auth;

interface LoginSuccessfulHandlerInterface
{
    public function handleLogin() : callable;
}