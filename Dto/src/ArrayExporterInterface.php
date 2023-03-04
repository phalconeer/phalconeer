<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto as This;

interface ArrayExporterInterface extends This\DtoExporterInterface
{
    public function toArray();
}