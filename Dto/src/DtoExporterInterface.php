<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface DtoExporterInterface
{
    public function exportWithTransformers(
        array $transformers = [],
        \ArrayObject $parameters = null
    );

    public function export(\ArrayObject $parameters = null);
}