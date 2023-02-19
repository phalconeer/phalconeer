<?php
namespace Phalconeer\LiveSession\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Application;
use Phalconeer\Id;
use Phalconeer\LiveSession as This;

class LiveSessionBo implements This\LiveSessionInterface
{
    public function __construct(
        protected ?This\LiveSessionAdapterInterface $adapter = null,
        protected Application\ApplicationInterface $application,
        protected PhalconConfig\Config $config
    )
    {
    }

    public function setAdapter(This\LiveSessionAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    protected function getSessionExpiration() : \DateTime
    {
        return new \DateTime('+' . $this->config->sessionDuration . ' seconds');
    }

    public function createSession(This\Data\LiveSession $sessionObject) : ?This\Data\LiveSession
    {
        if (is_null($this->adapter)) {
            throw new This\Exception\UndefinedLiveSessionAdapterException(
                '',
                This\Helper\ExceptionHelper::LIVE_SESSION__ADAPTER_NOTSET
            );
        }
        if (is_null($sessionObject->id())) {
            $sessionObject = $sessionObject->setId(
                Id\Helper\IdHelper::generate($this->config->get('sessionIdLength', 24))
            );
        }

        if (is_null($sessionObject->expires())) {
            $sessionObject = $sessionObject->setExpires($this->getSessionExpiration());
        }

        if ($this->adapter->createSession($sessionObject)) {
            return $sessionObject;
        }

        return null;
    }

    public function refreshSessionExpiration(string $sessionId) : ?This\Data\LiveSession
    {

        $newSession = $this->getSession($sessionId)->setExpires($this->getSessionExpiration());
        if ($this->adapter->createSession($newSession)) {
            return $newSession;
        }

        return null;

    }

    public function isValid(string $sessionId) : bool
    {
        return $this->adapter->isValid($sessionId);
    }

    public function hasScope(
        string $sessionId,
        string $scope,
        array $restriction = []
    ) : bool
    {
        $scopeFullName = implode('.', 
                array_filter([
                    $this->application->getPrivilegeScheme(),
                    $scope
                ])
            );
        $resourcePermission = implode('.', 
                array_reduce(
                    array_keys($restriction),
                    function ($aggregator, $key) use ($restriction) {
                        $aggregator[] = $key;
                        $aggregator[] = $restriction[$key];
                        return $aggregator;
                    },
                    [$scopeFullName]
                )
            );

        $session = $this->getSession($sessionId);
        if (is_null($session)) {
            return false;
        }
        /**
         * Permission is granted if the player has unrestricted or restricted permission for a resource.
         * Restricted permission always overwrites unrestricted if they have different status (allowed / denied)
         * If there are no restricted permissions defined, the resourcePermission value is equal to the scopeFullName
         */
        return
            in_array($resourcePermission, $session->scopes())
            || (
                in_array($scopeFullName, $session->scopes())
                && (
                    $scopeFullName == $resourcePermission
                    || !in_array($resourcePermission, $session->deniedPermissions())
                    )
                )
            || (
                !in_array($scopeFullName, $session->deniedPermissions())
                && !in_array($resourcePermission, $session->deniedPermissions()
                    )
                );
    }

    public function getSession(string $sessionId = null) : ?This\Data\LiveSession
    {
        if (is_null($sessionId)) {
            return null;
        }
        return $this->adapter->getSession($sessionId);
    }

    public function deleteSession(string $sessionId) : bool
    {
        return $this->adapter->deleteSession($sessionId);
    }
}