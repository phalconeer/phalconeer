<?php
namespace Phalconeer\Bootstrap;

use Phalcon\CLI;
use Phalconeer\Bootstrap as This;
use Phalconeer\Exception;
use Phalconeer\Router;

/**
 * Bootstraps the application.
 *
 * @package    Phalconeer
 */
abstract class BootstrapMicro extends This\Bootstrap
{
    protected function runApplication()
    {
        $this->addNamespaceToLoader(Exception\NotFound\DependencyNotFoundException::class);
        if (!class_exists(Exception\NotFound\DependencyNotFoundException::class)) {
            throw new Exception\NotFoundException('Autoloader is not configured', Exception\Helper\ExceptionHelper::AUTOLOADER_NOT_CONFIGURED);
        }

        if (!$this->di->has('router')) {
            throw new Exception\NotFound\DependencyNotFoundException(
                'router',
                Exception\Helper\ExceptionHelper::MODULE_NOT_LOADED
            );
        }

        $console = new CLI\Console();
        $console->setDI($this->di);
        return $console->handle($this->di[Router\Factory::MODULE_NAME]->getArguments());
    }
}
