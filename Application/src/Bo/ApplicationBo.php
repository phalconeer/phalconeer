<?php
namespace Phalconeer\Application\Bo;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Application as This;

class ApplicationBo implements This\ApplicationInterface
{
    protected This\Data\Application $appData;

    public function __construct(
        protected PhalconConfig\Config $config
    )
    {
        if (!$this->config->offsetExists('id')) {
            throw new This\Exception\ApplicationIdNotSet();
        }
        $appData = array_filter([
            'id'                => $this->config->id,
            'name'              => $this->config->get('name'),
            'privilegeScheme'   => $this->config->get('privilegeScheme'),
            'version'           => APP_VERSION
        ]);

        $this->appData = new This\Data\Application(new \ArrayObject($appData));
    }

    /**
     * Returns application id
     */
    public function getId() : int
    {
        return $this->appData->id();
    }

    /**
     * Returns application name.
     */
    public function getName() : ?string
    {
        return $this->appData->name();
    }

    /**
     * Returns application priviliege scheme.
     * Privileges schemes are used to share privilige "namespaces" between applications.
     */
    public function getPrivilegeScheme() : ?string
    {
        if (is_null($this->appData->privilegeScheme())) {
            return $this->getName();
        }
        return $this->appData->privilegeScheme();
    }

    /**
     * Returns application name.
     */
    public function getVersion() : ?string
    {
        return $this->appData->version();
    }
}