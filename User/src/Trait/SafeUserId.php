<?php
namespace Phalconeer\User\Trait;

trait SafeUserId
{
    protected string $safeUserId;

    public function setSafeUserId(string $safeUserId) : self
    {
        return $this->setKeyValue('safeUserId', $safeUserId);
    }
}