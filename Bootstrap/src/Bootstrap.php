<?php
namespace Phalconeer\Bootstrap;

/**
 * Exceptions possible before the Phalcon Autoloader is initiated
 */

use Phalconeer\Exception;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Mvc;
use Phalcon\Support;

/**
 * Bootstraps the application.
 *
 * @package    Phalconeer
 */
abstract class Bootstrap
{
    /**
     * The dependency injector.
     */
    protected Di\DiInterface $di;

    /**
     * List of options supplied for the Bootstrap to initialize the application
     */
    protected array $options = [];

    /**
     * Contains the application sepcific configurations.
     */
    protected Config\Config $config;

    /**
     * Constructor.
     *
     */
    public function __construct(Di\DiInterface $di)
    {
        // Prepend all additional autoloaders as this will not work on
        // normal namespaces without the /src directory
        spl_autoload_register([$this, 'autoloader']);

        // if (DEBUG_ON) {
        //     $debug = new Support\Debug();

        //     $debug->listen();
        // }
        $this->di = $di;
    }

    public function autoloader($class)
    {
        /**
         * Loader takes care of the autolaod behavior. Before it is loaded, the modules need to be loaded manually.
         */
        // TODO: decide if it is worth manipulating the autolaoder to deal with the /src bacuase of composer
        // if ($this->di->offsetExists('loader')) {
        //     return false;
        // } 
        $classPieces = explode('\\', $class);
        $namespace = array_shift($classPieces);
        if ($namespace !== 'Phalconeer') {
            return false;
            // TODO: log these errors, as per PSR-4 no exceptions are to be thrown during autoload
            // throw new Exception\InvalidArgumentException(
            //     "Basic autoloader can only load Phalconeer classes, " . $class,
            //     Exception\Helper\ExceptionHelper::INVALID_NAMESPACE
            // );
        }
        $moduleName = array_shift($classPieces);
        array_unshift($classPieces, PHALCONEER_SOURCE_PATH, $moduleName, 'src');

        $fileName = implode('/', $classPieces) . '.php';
        if (!file_exists($fileName)) {
            return false;
            // TODO: log these errors, as per PSR-4 no exceptions are to be thrown during autoload
            // throw new Exception\NotFound\FileNotFoundException($fileName, Exception\Helper\ExceptionHelper::FILE_NOT_FOUND);
        }
        require_once $fileName;
    }

    protected function addNamespaceToLoader($moduleInitiator)
    {
        if (!$this->di->offsetExists('loader')) {
            return;
        }
        $loader = $this->di->get('loader');
        $classPieces = explode('\\', $moduleInitiator);
        $namespace = implode(
            '\\',
            [
                $classPieces[0],
                $classPieces[1]
            ]
        );
        $folder = implode(
            '/',
            [
                PHALCONEER_SOURCE_PATH,
                $classPieces[1],
                'src'
            ]
        );
        $loader->addNamespace(
            $namespace,
            $folder,
            true
        );
    }

    /**
     * Runs the application performing all initializations
     */
    public function run(array $options = array()) : string
    {
        $this->options = array_merge_recursive($this->options, $options);
        $this->config  = new Config\Config($this->options);
        $this->loadServices();

        return $this->runApplication();
    }

    /**
     * Initializes the services.
     */
    protected function loadServices() : void
    {
        foreach ($this->config->loaders as $moduleInitiator) {
            $this->loadService($moduleInitiator);
        }
    }

    /**
     * Tries to load a Bootstrap module.
     */
    protected function loadService(string $moduleInitiator)
    {
        $this->addNamespaceToLoader($moduleInitiator);
        if (!class_exists($moduleInitiator)) {
            throw new Exception\NotFound\ClassNotFoundException($moduleInitiator, Exception\Helper\ExceptionHelper::CLASS_NOT_FOUND);
        }
        (new $moduleInitiator($this->di, $this->config))->injectModule();
    }

    /**
     * Runs the application.
     *
     * @return string   The HTTP response body.
     */
    protected function runApplication()
    {
        $this->addNamespaceToLoader(Exception\NotFound\DependencyNotFoundException::class);
        if (!class_exists(Exception\NotFound\DependencyNotFoundException::class)) {
            throw new Exception\NotFoundException('Autoloader is not configured', Exception\Helper\ExceptionHelper::AUTOLOADER_NOT_CONFIGURED);
        }

        if (!$this->di->has('view')) {
            throw new Exception\NotFound\DependencyNotFoundException('view', Exception\Helper\ExceptionHelper::MODULE_NOT_LOADED);
        }
        $application = new Mvc\Application();
        $application->setDI($this->di);

        return $application->handle($this->di->get('request')->getServer('REQUEST_URI'))->getContent();
    }
}
