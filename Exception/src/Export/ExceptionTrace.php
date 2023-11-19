<?php
namespace Phalconeer\Exception\Export;

use Phalconeer\Data;
use Phalconeer\Dto;

class ExceptionTrace extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayLoader,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected array $arguments;

    protected string $class;
    
    protected string $file;
    
    protected string $function;

    protected int $line;
    
    protected string $type;
}