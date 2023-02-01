<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface DtoLoaderInterface
{
    public function export() : string | array | \ArrayObject | Data\DataInterface;
}