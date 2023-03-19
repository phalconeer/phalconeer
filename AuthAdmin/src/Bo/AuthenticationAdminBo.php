<?php
namespace Phalconeer\AuthAdmin\Bo;

use Phalconeer\AuthAdmin as This;
use Phalconeer\AuthMethod;
use Phalconeer\Id;
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

    public function createCredentials(array $userData, string $method)
    {
        if (!$this->authenticationCreators->offsetExists($method)) {
            throw new This\Exception\AuthenticationCreatorNotFoundException(
                $method,
                This\Helper\ExceptionHelper::AUTHENTICATION_ADMIN__USER_CREATOR_NOT_FOUND
            );
        }
        

        $authenticationRequest = new AuthMethod\Data\AuthenticationRequest(new \ArrayObject([
            'requestId'     => Id\Helper\IdHelper::getUuidv4(),
            'requestTime'   => new \DateTime(),
            'userId'        => $userData['id'] ?? null,
            'username'      => $userData['username'] ?? '',
            'password'      => $userData['password'] ?? '',
            'method'        => $method
        ]));
        
        if (!$this->authenticationCreators->offsetGet($method)
            ->create($authenticationRequest)) {
            throw new This\Exception\FailedToSaveUserException(
                '',
                This\Helper\ExceptionHelper::AUTHENTICATION_ADMIN__GENERIC_USER_CREATION_ERROR
            );
        }
    }
}