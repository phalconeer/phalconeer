<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Data\Helper\ParseValueHelper as PVH;
use Phalconeer\FormValidator as This;

class Required extends This\Data\FieldCheck
{
    protected static array $_properties = [
        'value'         => PVH::TYPE_BOOL
    ];
}