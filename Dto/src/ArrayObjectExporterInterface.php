<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto as This;

interface ArrayObjectExporterInterface extends This\DtoExporterInterface
{
    public function toArrayObject();
}