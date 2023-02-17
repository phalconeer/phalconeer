<?php
namespace Phalconeer\Auth\Bo;

use Phalconeer\Auth as This;
use Phalconeer\Id;
use Phalconeer\LiveSession;

class AuthenticationBo
{
    protected \ArrayObject $loginHandlers;

    protected \ArrayObject $logoutHandlers;

    public function __construct(
        protected LiveSession\LiveSessionInterface $liveSession,
        protected This\Bo\UserAuthenticationBo $authenticationBo,
        protected This\Bo\UserAuthenticationCreatorBo $authenticationCreatorBo,
        protected This\RoleRepositoryInterface $roleRepository,
        protected This\PrivilegeRepositoryInterface $privilegeRepository,
        protected This\ScopeRepositoryInterface $scopeRepository
    )
    {
        $this->loginHandlers = new \ArrayObject();
        $this->logoutHandlers = new \ArrayObject();
    }

    public function addAuthenticator (This\AuthenticatorInterface $authenticator)
    {
        $this->authenticationBo->addAuthenticator($authenticator);
    }

    public function addAuthenticationCreator (This\AuthenticationCreatorInterface $authenticationCreator)
    {
        $this->authenticationCreatorBo->addAuthenticationCreator($authenticationCreator);
    }

    public function addLoginHandler ($handler)
    {
        if (!is_callable($handler)) {
            throw new This\Exception\LoginHandlerIsNotCallableException(
                '',
                This\Helper\ExceptionHelper::AUTHENTICATION__LOGIN_HANDLER_NOT_CALLABLE);
        }
        $this->loginHandlers->offsetSet(null, $handler);
    }

    public function addLogoutHandler ($handler)
    {
        if (!is_callable($handler)) {
            throw new This\Exception\LogoutHandlerIsNotCallableException(
                '',
                This\Helper\ExceptionHelper::AUTHENTICATION__LOGOUT_HANDLER_NOT_CALLABLE);
        }
        $this->logoutHandlers->offsetSet(null, $handler);
    }

    protected function handleLogin(LiveSession\Data\LiveSession $sessionObject)
    {
        $iterator = $this->loginHandlers->getIterator();
        iterator_apply(
            $iterator,
            function ($iterator)  use ($sessionObject) {
                $iterator->current()($sessionObject);
                return true;
            },
            [$iterator]
        );
    }

    protected function handleLogout(string $sessionId)
    {
        $iterator = $this->logoutHandlers->getIterator();
        iterator_apply(
            $iterator,
            function ($iterator) use ($sessionId) {
                $iterator->current()($sessionId);
                return true;
            },
            [$iterator]
        );
    }

    protected function handleAuthenticationFailure(This\Data\AuthenticationResponse $authenticationResponse)
    {
        throw new This\Exception\UnauthorizedException(
            '',
            (empty($authenticationResponse->error()))
                ? This\Helper\ExceptionHelper::AUTHENTICATION__GENERIC_FAILURE
                : $authenticationResponse->error()
            );
    }

    protected function handleAuthenticationResponse(This\Data\AuthenticationResponse $authenticationResponse) : bool
    {
        if (!$authenticationResponse->sessionValid()) {
            // $this->handleAuthenticationFailure($authenticationResponse);
            return false;
        }
        return true;
    }

    protected function loadSession(This\Data\AuthenticationResponse $authenticationResponse) : ?LiveSession\Data\LiveSession
    {
        $session = $this->liveSession->getSession($authenticationResponse->sessionId());
        return $session;
    }

    public function getScopeNames(array $privileges, string $privilegeScheme = null) : \ArrayObject
    {
        return $this->scopeRepository->getScopeNames($privileges, $privilegeScheme);
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

    public function authenticate(
        $username,
        $password,
        $method
    ) : ?LiveSession\Data\LiveSession
    {
        $authenticationResponse = $this->authenticationBo->authenticate(
            $username,
            $password,
            $method
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

    public function createCredentials(array $userData, string $method)
    {
        $request = [
            'requestId'     => Id\Helper\IdHelper::getUuidv4(),
            'requestTime'   => new \DateTime(),
            'userId'        => $userData['id'] ?? null,
            'username'      => $userData['username'] ?? '',
            'password'      => $userData['password'] ?? '',
            'method'        => $method
        ];

        $authenticationRequest = new This\Data\AuthenticationRequest($request);
        if (!$this->authenticationCreatorBo->create($authenticationRequest)) {
            throw new This\Exception\FailedToSaveUserRolesException(
                $this->dao->getLastErrorInfo()
            );
        }
    }

    public function logout($sessionId) : void
    {
        $this->liveSession->deleteSession($sessionId);
        $this->handleLogout($sessionId);
    }

    public function getSessionId() : ?string
    {
        return $this->sessionId;
    }
}