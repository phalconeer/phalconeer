<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Id;
use Phalconeer\Auth as This;

class UserAuthenticationBo
{
    protected \ArrayObject $authenticators;

    public function __construct()
    {
        $this->authenticators = new \ArrayObject();
    }

    public function addAuthenticator (This\AuthenticatorInterface $authenticator)
    {
        $this->authenticators->offsetSet($authenticator->getMethodName(), $authenticator);
    }

    protected function useAuthenticator(This\AuthenticatorInterface $authenticator, This\Data\AuthenticationRequest $authenticationRequest) : This\Data\AuthenticationResponse
    {
        if (!empty($authenticationRequest->method())
            && $authenticationRequest->method() !== $authenticator->getMethodName()) {
            return new This\Data\AuthenticationResponse();
        }
        return $authenticator->authenticate($authenticationRequest);

    }

    protected function getAuthenticationRequest($username, $password, $method) : This\Data\AuthenticationRequest
    {
        $request = [
            'requestId'     => Id\Helper\IdHelper::getUuidv4(),
            'requestTime'   => new \DateTime()
        ];

        if (!is_null($username)) {
            $request['username'] = $username;
        }

        if (!is_null($password)) {
            $request['password'] = $password;
        }

        if (!is_null($method)) {
            $request['method'] = $method;
        }

        return new AuthenticationRequest($request);
    }

    public function authenticate($username, $password, $method)
    {
        $authenticationRequest = $this->getAuthenticationRequest($username, $password, $method);
        if (!empty($authenticationRequest->method()) 
            && !$this->authenticators->offsetExists($authenticationRequest->method())) {
            throw new This\Exception\AuthenticatorNotFoundException($authenticationRequest->method());
        }

        $authenticationResponse = new This\Data\AuthenticationResponse();
        $authenticators = (empty($authenticationRequest->method()))
            ? $this->authenticators
            : [$this->authenticators->offsetGet($authenticationRequest->method())];

        foreach ($authenticators as $authenticator) {
            if (is_null($authenticationResponse->sessionValid())) {
                $authenticationResponse = $this->useAuthenticator($authenticator, $authenticationRequest);
                if ($authenticationResponse->sessionValid() === true) {
                    break;
                }
            }
        }

        return $authenticationResponse;
    }
}