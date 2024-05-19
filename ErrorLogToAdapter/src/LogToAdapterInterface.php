<?php
namespace Phalconeer\ExceptionLogToAdapter;

use Phalconeer\ErrorHandler;

interface LogToAdapterInterface
{
    public function save(
        ErrorHandler\Data\Error $error,
    );
}