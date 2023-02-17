<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Auth as This;
use Phalconeer\Id;
use Phalconeer\LiveSession;

class AuthenticationBo
{
    protected \ArrayObject $authenticationCreators;

    protected \ArrayObject $authenticators;

    protected \ArrayObject $loginHandlers;

    protected \ArrayObject $logoutHandlers;

    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession,
    )
    {
        $this->authenticationCreators = new \ArrayObject();
        $this->authenticators = new \ArrayObject();
        $this->loginHandlers = new \ArrayObject();
        $this->logoutHandlers = new \ArrayObject();
    }

    public function addAuthenticationCreator (This\AuthenticationCreatorInterface $authenticationCreator)
    {
        $this->authenticationCreators->offsetSet(null, $authenticationCreator);
    }

    public function addAuthenticator (This\AuthenticatorInterface $authenticator)
    {
        $this->authenticators->offsetSet(null, $authenticator);
    }

    public function addLoginHandler (callable $handler)
    {
        $this->loginHandlers->offsetSet(null, $handler);
    }

    public function addLogoutHandler (callable $handler)
    {
        $this->logoutHandlers->offsetSet(null, $handler);
    }

    protected function handleLogin(LiveSession\Data\LiveSession $sessionObject)
    {
        $iterator = $this->loginHandlers->getIterator();
        while ($iterator->valid()) {
            $iterator->current()($sessionObject);
            $iterator->next();
        }
    }

    protected function handleLogout(string $sessionId)
    {
        $iterator = $this->logoutHandlers->getIterator();
        while ($iterator->valid()) {
            $iterator->current()($sessionId);
            $iterator->next();
        }
    }

    protected function loadSession(This\Data\AuthenticationResponse $authenticationResponse) : ?LiveSession\Data\LiveSession
    {
        return $this->liveSession->getSession($authenticationResponse->sessionId());
    }

    protected function createSession(This\Data\AuthenticationResponse $authenticationResponse) : ?LiveSession\Data\LiveSession
    {
        if (is_null($authenticationResponse->scopes())) {
            $roles = $this->roleRepository->getApplicationRoles(
                $authenticationResponse->userId()
            );
            $roles->merge(
                $this->roleRepository->getUserRoles(
                    $authenticationResponse->userId()
                )
            );
            $privileges = $this->privilegeRepository
                    ->getPrivilegesByRoles($roles)
                    ->merge($this->privilegeRepository->getPrivilegesByUser($authenticationResponse->userId()));

            $scopeNames = $this->scopeRepository->getScopeNames($privileges->getFieldValues('privilegeId', true));
            $authenticationResponse = $authenticationResponse->setScopes(
                $privileges->getScopes($scopeNames)
            )->setDeniedPermissions(
                $privileges->getDeniedPermissions($scopeNames)
            );
        }

        $session = $this->liveSession->createSession(
            LiveSession\Data\LiveSession::fromArray([
                'userId'                => $authenticationResponse->userId(),
                'scopes'                => $authenticationResponse->scopes(),
                'deniedPermissions'     => $authenticationResponse->deniedPermissions(),
            ])
        );

        return $session;
    }

    protected function handleAuthenticationFailure(This\Data\AuthenticationResponse $authenticationResponse) : bool
    {
        return false;
    }

    protected function handleAuthenticationResponse(This\Data\AuthenticationResponse $authenticationResponse) : bool
    {
        return ($authenticationResponse->sessionValid())
            ? true
            : $this->handleAuthenticationFailure($authenticationResponse);
    }

    public function authenticate(This\Data\AuthenticationRequest $authenticationRequest) : ?LiveSession\Data\LiveSession
    {
        $authenticationResponse = $this->authenticationBo->authenticate(
            $authenticationRequest->username(),
            $authenticationRequest->password(),
            $authenticationRequest->method(),
        );

        if (!$this->handleAuthenticationResponse($authenticationResponse)) {
            return null;
        }
        $session = (is_null($authenticationResponse->sessionId()))
            ? $this->createSession($authenticationResponse)
            : $this->loadSession($authenticationResponse);

        if (!is_null($authenticationResponse->userId())) {
            $this->handleLogin($session);
        }

        return $session;
    }

    public function logout($sessionId) : void
    {
        $this->liveSession->deleteSession($sessionId);
        $this->handleLogout($sessionId);
    }
}