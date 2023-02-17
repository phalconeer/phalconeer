<?php
namespace Phalconeer\Scope\Bo;

use Phalconeer\Config;
use Phalconeer\Auth;

class ScopeBo implements Auth\ScopeAdapterInterface
{
    public function __construct(
        protected Config\Config $config
    )
    {
    }

    /**
     * Returns a list of scopes which are enabled for the session
     */
    public function getScopeNames(Auth\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject
    {
        return $this->config->get('allowedScopes', Config\Helper\ConfigHelper::$dummyConfig)->toArray();
    }
}