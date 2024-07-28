<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto as This;

interface ArrayNotNullExporterInterface extends This\DtoExporterInterface
{
    public function toArrayWithoutNulls();
}