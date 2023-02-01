<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface DtoLoaderInterface
{
    public function load(array $input = null, \ArrayObject $inputObject = null) : Data\DataInterface;
}