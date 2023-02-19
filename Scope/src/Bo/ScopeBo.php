<?php
namespace Phalconeer\Scope\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\AuthMethod;
use Phalconeer\Config;
use Phalconeer\Scope as This;

/**
 * This is a simple implementation which returns the simplest possible privilege setting
 * Auth uses allowed and denied scopes, to decide if the playes has access to a specific function
 */
class ScopeBo implements This\ScopeAdapterInterface
{
    public function __construct(
        protected PhalconConfig\Config $config
    )
    {
    }

    /**
     * Returns a list of scopes which are enabled for the session
     */
    public function getAllowedScopes(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject
    {
        return $this->config->get('allowedScopes', Config\Helper\ConfigHelper::$dummyConfig)->toArray();
    }

    /**
     * Returns a list of scopes which are enabled for the session
     */
    public function getDeniedScopes(AuthMethod\Data\AuthenticationResponse $authenticationResponse) : \ArrayObject
    {
        return $this->config->get('deniedScopes', Config\Helper\ConfigHelper::$dummyConfig)->toArray();
    }
}