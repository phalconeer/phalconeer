<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data\Helper\ParseValueHelper as PVH;
use Phalconeer\FormValidator as This;

class Regex extends This\Data\FieldCheck
{
    protected static array $properties = [
        'value'         => PVH::TYPE_STRING
    ];
}