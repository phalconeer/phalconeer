<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Auth as This;

class UserAuthenticationCreatorBo
{
    protected \ArrayObject $authenticationCreators;

    public function __construct()
    {
        $this->authenticationCreators = new \ArrayObject();
    }

    public function addAuthenticationCreator (This\AuthenticationCreatorInterface $authenticationCreator)
    {
        $this->authenticationCreators->offsetSet($authenticationCreator->getMethodName(), $authenticationCreator);
    }

    public function create(This\Data\AuthenticationRequest $authenticationRequest) : bool
    {
        $method = $authenticationRequest->method();
        if (!$this->authenticationCreators->offsetExists($method)) {
            throw new This\Exception\AuthenticationCreatorNotFoundException($method);
        }

        return $this->authenticationCreators->offsetGet($method)->create($authenticationRequest);
    }
}