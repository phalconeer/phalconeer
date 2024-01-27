<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class ListenerConfig extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected ?string $parameterClass;

    protected int $priority = 0;

    protected int $repeatInterval = 0;
}