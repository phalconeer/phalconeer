<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data\Helper\ParseValueHelper as PVH;
use Phalconeer\FormValidator as This;

class MaxValue extends This\Data\FieldCheck  implements This\StrictableInterface
{
    use This\Trait\StrictableRule;

    protected static array $properties = [
        'value'         => PVH::TYPE_INT
    ];
}