<?php
namespace Phalconeer\Exception;

use Phalconeer\Exception as This;

class Exception extends \Exception implements This\ExceptionInterface
{
    protected string $component = 'unknown';

    public function getComponent() : string
    {
        return $this->component;
    }

    public function setComponent(string $component) : self
    {
        $this->component = $component;
        return $this;
    }

    public static function withComponent(
        string $component,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) : self
    {
        $exception = new static($message, $code, $previous);
        return $exception->setComponent($component);
    }
}
