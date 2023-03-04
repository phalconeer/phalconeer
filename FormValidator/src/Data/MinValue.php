<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data\Helper\ParseValueHelper as PVH;
use Phalconeer\FormValidator as This;

class MinValue extends This\Data\FieldCheck implements This\StrictableInterface
{
    use This\Trait\StrictableRule;

    protected static array $_properties = [
        'value'         => PVH::TYPE_INT
    ];
}