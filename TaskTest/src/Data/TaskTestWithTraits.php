<?php
namespace Phalconeer\TaskTest\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\TaskRegistry;

class TaskTestWithTraits extends TaskRegistry\Data\TaskParameters implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;
        
    protected ?string $message;
}