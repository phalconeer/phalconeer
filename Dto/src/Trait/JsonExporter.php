<?php
namespace Phalconeer\Dto\Trait;

use Phalconeer\Dto as This;

trait JsonExporter
{
    use This\Trait\ArrayExporter;

    public function toJson() : string
    {
        return json_encode($this->toArray());
    }
}