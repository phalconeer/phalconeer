<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class StringForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'stringField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_STRING,
            ]
        ]
    ];
}