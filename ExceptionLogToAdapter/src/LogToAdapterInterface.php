<?php
namespace Phalconeer\ExceptionLogToAdapter;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Exception;

interface LogToAdapterInterface
{
    public function save(
        Exception\Export\Exception $exception,
        PhalconConfig\Config $exceptionDescriptors
    );
}