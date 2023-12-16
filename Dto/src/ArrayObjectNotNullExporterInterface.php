<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto as This;

interface ArrayObjectNotNullExporterInterface extends This\DtoExporterInterface
{
    public function toArrayObjectWithoutNulls();
}