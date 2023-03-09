<?php
namespace Phalconeer\AuthenticateBearer\Bo;

use Phalconeer\Auth;
use Phalconeer\AuthMethod;
use Phalconeer\AuthenticateBearer as This;
use Phalconeer\LiveSession;

class AuthenticateBearerBo implements Auth\AuthenticatorInterface
{
    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession)
    {
    }

    public function authenticate (
        AuthMethod\Data\AuthenticationRequest $authenticationRequest
    ) : AuthMethod\Data\AuthenticationResponse
    {
        $session = $this->liveSession->getSession($authenticationRequest->password());
        if (is_null($session)) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => $this->getMethodName(),
                'error'             => This\Helper\ExceptionHelper::AUTHENTICATE__SESSION_NOT_FOUND
            ]);
        }

        $diff = $session->timeToExpire();

        if ($diff->invert === 1) {
            return AuthMethod\Data\AuthenticationResponse::fromArray([
                'method'            => $this->getMethodName(),
                'error'             => This\Helper\ExceptionHelper::AUTHENTICATE__SESSION_EXPIRED
            ]);
        }

        return AuthMethod\Data\AuthenticationResponse::fromArray([
            'userId'            => $session->userId(),
            'sessionValid'      => true,
            'sessionId'         => $session->id(),
            'scopes'            => $session->scopes(),
            'method'            => $this->getMethodName()
        ]);
    }

    public function getMethodName() : string
    {
        return This\Factory::MODULE_NAME;
    }
}