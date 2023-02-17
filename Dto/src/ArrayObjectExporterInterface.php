<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto\DtoExporterInterface as DtoDtoExporterInterface;

interface ArrayObjectExporterInterface extends DtoDtoExporterInterface
{
    public function toArrayObject();
}