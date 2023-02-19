<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Auth as This;
use Phalconeer\AuthMethod;
use Phalconeer\LiveSession;
use Phalconeer\Scope;

class AuthenticationBo
{
    protected \ArrayObject $authenticators;

    protected \ArrayObject $loginHandlers;

    protected \ArrayObject $logoutHandlers;

    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession,
        protected Scope\ScopeAdapterInterface $scope
    )
    {
        $this->authenticators = new \ArrayObject();
        $this->loginHandlers = new \ArrayObject();
        $this->logoutHandlers = new \ArrayObject();
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

    protected function loadSession(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : ?LiveSession\Data\LiveSession
    {
        return $this->liveSession->getSession($authenticationResponse->sessionId());
    }

    protected function createSession(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : ?LiveSession\Data\LiveSession
    {
        $session = $this->liveSession->createSession(
            LiveSession\Data\LiveSession::fromArray([
                'userId'                => $authenticationResponse->userId(),
                'scopes'                => $this->scope->getAllowedScopes($authenticationResponse),
                'deniedPermissions'     => $this->scope->getDeniedScopes($authenticationResponse),
            ])
        );

        return $session;
    }

    protected function handleAuthenticationFailure(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : bool
    {
        return false;
    }

    protected function handleAuthenticationResponse(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : bool
    {
        return ($authenticationResponse->sessionValid())
            ? true
            : $this->handleAuthenticationFailure($authenticationResponse);
    }

    public function authenticate(AuthMethod\Data\AuthenticationRequest $authenticationRequest) : ?LiveSession\Data\LiveSession
    {
        if (!empty($authenticationRequest->method()) 
            && !$this->authenticators->offsetExists($authenticationRequest->method())) {
            throw new This\Exception\AuthenticatorNotFoundException($authenticationRequest->method());
        }

        $authenticationResponse = new AuthMethod\Data\AuthenticationResponse();
        $authenticators = (empty($authenticationRequest->method()))
            ? $this->authenticators
            : [$this->authenticators->offsetGet($authenticationRequest->method())];

        foreach ($authenticators as $authenticator) {
            if (is_null($authenticationResponse->sessionValid())) {
                if (empty($authenticationRequest->method())
                    || $authenticationRequest->method() === $authenticator->getMethodName()) {
                    $authenticationResponse = $authenticator->authenticate($authenticationRequest);
                }
                // TODO: figure out if there is a use case for this
                // if ($authenticationResponse->sessionValid() === true) {
                //     break;
                // }
            }
        }

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