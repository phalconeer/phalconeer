<?php
namespace Phalconeer\AuthAdmin\Bo;

use Phalconeer\AuthAdmin as This;
use Phalconeer\AuthMethod;
use Phalconeer\LiveSession;
use Phalconeer\Scope;

class AuthenticationAdminBo
{
    protected \ArrayObject $authenticationCreators;

    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession,
        protected Scope\ScopeAdapterInterface $scope
    )
    {
        $this->authenticationCreators = new \ArrayObject();
    }

    public function addAuthenticationCreator (This\AuthenticationCreatorInterface $authenticationCreator)
    {
        $this->authenticationCreators->offsetSet(
            $authenticationCreator->getMethodName(),
            $authenticationCreator
        );
    }

    public function createCredentials(AuthMethod\Data\AuthenticationRequest $authenticationRequest)
    {
        if (!$this->authenticationCreators->offsetExists($authenticationRequest->method())) {
            throw new This\Exception\AuthenticationCreatorNotFoundException(
                $authenticationRequest->method(),
                This\Helper\ExceptionHelper::AUTHENTICATION_ADMIN__USER_CREATOR_NOT_FOUND
            );
        }
        
        if (!$this->authenticationCreators->offsetGet($authenticationRequest->method())
            ->create($authenticationRequest)) {
            throw new This\Exception\FailedToSaveUserException(
                '',
                This\Helper\ExceptionHelper::AUTHENTICATION_ADMIN__GENERIC_USER_CREATION_ERROR
            );
        }
    }
}