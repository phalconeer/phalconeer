<?php
namespace Phalconeer\LiveSession\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Application;
use Phalconeer\Id;
use Phalconeer\LiveSession as This;

class LiveSessionBo implements This\LiveSessionInterface
{
    public function __construct(
        protected ?\ArrayObject $adapters = null,
        protected Application\ApplicationInterface $application,
        protected PhalconConfig\Config $config
    )
    {
        if (is_null($this->adapters)) {
            $this->adapters = new \ArrayObject();
        }
    }

    public function addAdapter(This\LiveSessionAdapterInterface $adapter, ?string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT)
    {
        $this->adapters->offsetSet(
            $type,
            $adapter
        );
    }

    protected function getSessionExpiration() : \DateTime
    {
        return new \DateTime('+' . $this->config->sessionDuration . ' seconds');
    }

    public function createSession(This\Data\LiveSession $sessionObject, string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT) : ?This\Data\LiveSession
    {
        if (is_null($this->adapters)
            || !$this->adapters->offsetExists($type)) {
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

        if ($this->adapters->offsetGet($type)->createSession($sessionObject)) {
            return $sessionObject;
        }
        throw new This\Exception\AdapterFailedSessionCreationException(
            get_class($this->adapters->offsetGet($type)),
            This\Helper\ExceptionHelper::LIVE_SESSION__ADAPTER_NOT_WORKING
        );
    }

    public function refreshSessionExpiration(string $sessionId, string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT) : ?This\Data\LiveSession
    {

        $newSession = $this->getSession($sessionId, $type)->setExpires($this->getSessionExpiration());
        if ($this->adapters->offsetGet($type)->createSession($newSession)) {
            return $newSession;
        }

        return null;

    }

    public function isValid(string $sessionId, string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT) : bool
    {
        return $this->adapters->offsetGet($type)->isValid($sessionId);
    }

    public function hasScope(
        string $sessionId,
        string $scope,
        array $restriction = [],
        string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT
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

        $session = $this->getSession($sessionId, $type);
        if (is_null($session)) {
            return false;
        }
        /**
         * Permission is granted if the player has unrestricted or restricted permission for a resource.
         * Restricted permission always overwrites unrestricted if they have different status (allowed / denied)
         * If there are no restricted permissions defined, the resourcePermission value is equal to the scopeFullName
         */
        $hasResourcePermission = in_array($resourcePermission, $session->scopes());
        $hasUnrestrictedPermission = in_array($scopeFullName, $session->scopes())
            && $scopeFullName == $resourcePermission
                || !in_array($resourcePermission, $session->deniedPermissions());
        $permissionIsNotDenied = (!in_array($scopeFullName, $session->deniedPermissions())
            && !in_array($resourcePermission, $session->deniedPermissions()));
        return $hasResourcePermission
            || $hasUnrestrictedPermission
            || $permissionIsNotDenied;
    }

    public function getSession(string $sessionId = null, string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT) : ?This\Data\LiveSession
    {
        if (is_null($sessionId)) {
            return null;
        }
        return $this->adapters->offsetGet($type)->getSession($sessionId);
    }

    public function deleteSession(string $sessionId, string $type = This\Helper\LiveSessionHelper::LIVE_SESSION_TYPE_DEFAULT) : bool
    {
        return $this->adapters->offsetGet($type)->deleteSession($sessionId);
    }
}