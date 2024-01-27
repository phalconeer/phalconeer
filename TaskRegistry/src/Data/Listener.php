<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\TaskRegistry as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class Listener extends Dto\ImmutableDto
{
    use Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected ListenerConfig $config;

    protected This\Handler\HandlerBase $module;
}