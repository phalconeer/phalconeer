<?php
namespace Phalconeer\Bootstrap;

use Phalcon\Config;
use Phalcon\Di;
use Phalconeer\Bootstrap as This;
use Phalconeer\Config as PhalconeerConfig;
use Phalconeer\Exception;

/**
 *
 */
abstract class Factory
{
    const MODULE_NAME = '';

    /**
     * List of bootstrap modules required to initializes this module.
     */
    protected static array $requiredModules = [];

    /**
     * List off all the module specific configuration files.
     */
    protected static array $configFiles = [];
    
    public function __construct(
        protected Di\DiInterface $di,
        protected Config\Config $config,
        protected This\Bootstrap $bootstrap
    ) {
        $this->checkRequiredModules();
        $this->loadAdditionalConfig();
    }

    /**
     * Loads all required modules, cascading through all parents
     */
    public static function getRequiredModules() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge($parentClassName::getRequiredModules(), static::$requiredModules)
            : static::$requiredModules;
    }

    /**
     * Check if all modules required are loaded.
     */
    protected function checkRequiredModules() : void
    {
        foreach (static::getRequiredModules() as $requiredModule) {
            if (!isset($this->di[$requiredModule])) {
                /**
                 * Try autoloading the default Phalconeer module
                 * It only works of the module name is the same as the DI key with first letter being ujppercase
                 */
                $this->bootstrap->loadService('Phalconeer\\' . ucfirst($requiredModule) . '\\Factory');
                if (!isset($this->di[$requiredModule])) {
                    throw new Exception\NotFound\ModuleNotFoundException(
                        '`' . $requiredModule . '` required for ' . get_called_class(),
                        Exception\Helper\ExceptionHelper::MODULE_NOT_LOADED
                    );
                }
            }
        }
    }

    /**
     * Loads all configuration files recursively, cascading through all parents
     */
    public static function getAdditionalConfig() : array
    {
        $parentClassName = get_parent_class(static::class);
        return ($parentClassName
            && method_exists($parentClassName, __FUNCTION__))
            ? array_merge($parentClassName::getAdditionalConfig(), static::$configFiles)
            : static::$configFiles;
    }

    protected function loadAdditionalConfig() : void
    {
        $configFiles = static::getAdditionalConfig();

        if (count($configFiles) > 0) {
            foreach ($configFiles as $configFile) {
                if (!file_exists($configFile)) {
                    throw new Exception\NotFound\ConfigNotFoundException(
                        $configFile,
                        Exception\Helper\ExceptionHelper::CONFIG_FILE_NOT_FOUND
                    );
                }
                $configArray = include $configFile;
                if (!is_array($configArray)) {
                    $pathParts = explode('/', $configFile);
                    throw new Exception\InvalidConfigDataException(
                        array_pop($pathParts) . ', ' . get_class($this) . ', ' . $configFile,
                        Exception\Helper\ExceptionHelper::INVLIAD_CONFIG_FILE_CONTENT
                    );
                }
                $this->di->get(PhalconeerConfig\Factory::MODULE_NAME)->merge(
                    new Config\Config($configArray)
                );
                // $merged = array_replace_recursive($this->di->get('config')->toArray(), $configArray);
                // $this->di->set('config', new Config($merged));
            }
        }
    }
    
    /**
     * Adds the configured module to the dependency injector.
     */
    public function injectModule()
    {
        $module = $this->configure();
        if (is_null($module)) {
            return;
        }

        if (is_array($module)) {
            foreach ($module as $subName => $realModule) {
                switch ($subName) {
                    case '0':
                    case 'default':
                        $this->di->set($this->getModuleName(), $realModule);
                        break;
                    default:
                        $this->di->set(
                            implode('.', [$this->getModuleName(), $subName]),
                            $realModule,
                        );
                }
            }
        } else {
            $this->di->set($this->getModuleName(), $module);
        }

    }

    /**
     * Configures the Bootstrap module
     */
    abstract protected function configure();

    /**
     * Returns the container name of the Bootstrap module
     * @return string
     */
    protected function getModuleName() : string
    {
        if (empty(static::MODULE_NAME)) {
            throw new Exception\InvalidConfigDataException('Module name is not set, ' . get_class($this));
        }
        return static::MODULE_NAME;
    }

    public function __toString()
    {
        return $this->getModuleName();
    }
}

