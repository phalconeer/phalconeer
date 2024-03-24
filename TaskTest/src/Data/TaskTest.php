<?php
namespace Phalconeer\TaskTest\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\TaskRegistry;

class TaskTest extends TaskRegistry\Data\TaskParameters implements Dto\ArrayObjectExporterInterface
{
    use Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter;

    protected static array $properties = [
        'message'                  => Data\Helper\ParseValueHelper::TYPE_STRING,
    ];

    protected ?string $message;

    public function message() : ?string
    {
        return $this->message;
    }
}