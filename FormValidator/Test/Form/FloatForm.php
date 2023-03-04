<?php
namespace Phalconeer\FormValidator\Test\Form;

use Phalconeer\FormValidator\Test as This;
use Phalconeer\FormValidator;
use Phalconeer\FormValidator\Helper\FormValidatorHelper as FVH;

class FloatForm  extends FormValidator\Data\Form
{
    protected static array $fields = [
        'floatField'         => [
            FVH::CHECK_TYPE        => [
                FVH::KEY_VALUE              => FVH::TYPE_FLOAT,
                FVH::KEY_EXCEPTION_CODE     => This\Helper\ExceptionHelper::FLOAT_EXCEPTION
            ]
        ]
    ];
}